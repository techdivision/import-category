<?php

/**
 * TechDivision\Import\Category\Listeners\SortCategoryListener
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Category\Listeners;

use League\Event\EventInterface;
use League\Event\AbstractListener;
use TechDivision\Import\Utils\StoreViewCodes;
use TechDivision\Import\Subjects\SubjectInterface;
use TechDivision\Import\Services\RegistryProcessorInterface;
use TechDivision\Import\Category\Utils\ColumnKeys;
use TechDivision\Import\Category\Utils\MemberNames;
use TechDivision\Import\Category\Utils\RegistryKeys;
use TechDivision\Import\Category\Observers\CopyCategoryObserver;

/**
 * A listener implementation that sorts categories by their path and the position that has
 * been specified in the `position` column.
 *
 * This process can be quite complicated as it is possible that the import file contains
 *
 *   - only a subset of the categories
 *   - existing categories has been moved
 *   - the positions changes
 *
 * Therefore the position calculation is a very expensive operation an needs to handled
 * and refactored very carefully.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */
class SortCategoryListener extends AbstractListener
{

    /**
     * The registry processor instance.
     *
     * @var \TechDivision\Import\Services\RegistryProcessorInterface
     */
    protected $registryProcessor;

    /**
     * The array with the already existing categories.
     *
     * @var array
     */
    private $existingCategories = array();

    /**
     * The array that is used to temporary store the main rows (where we NEED to set the position for sorting).
     *
     * @var array
     */
    private $mainRows = array();

    /**
     * The array that is used to temporary store the store view specific rows.
     *
     * @var array
     */
    private $storeViewRows = array();

    /**
     * The array with the artefacts the we want to export with the appropriate position.
     *
     * @var array
     */
    private $artefacts = array();

    /**
     * The array with the existing attribute sets.
     *
     * @var array
     */
    private $attributeSets = array();

    /**
     * The actual subject instance.
     *
     * @var \TechDivision\Import\Subjects\SubjectInterface
     */
    private $subject;

    /**
     * The subject's serializer instance.
     *
     * @var \TechDivision\Import\Serializers\SerializerInterface
     */
    private $serializer;

    /**
     * Initializes the listener with the registry processor instance.
     *
     * @param \TechDivision\Import\Services\RegistryProcessorInterface $registryProcessor The registry processor instance
     */
    public function __construct(RegistryProcessorInterface $registryProcessor)
    {
        $this->registryProcessor = $registryProcessor;
    }

    /**
     * Handle an event.
     *
     * @param \League\Event\EventInterface                   $event   The event that triggered the event
     * @param \TechDivision\Import\Subjects\SubjectInterface $subject The subject instance
     *
     * @return void
     */
    public function handle(EventInterface $event, SubjectInterface $subject = null) : void
    {

        // initialize subject and serializer
        $this->subject = $subject;
        $this->serializer = $subject->getImportAdapter()->getSerializer();

        // load the status of the actual import
        $status = $this->registryProcessor->getAttribute(RegistryKeys::STATUS);

        // load the categories for the admin store view from the global data
        if (isset($status[RegistryKeys::GLOBAL_DATA][RegistryKeys::CATEGORIES])) {
            $this->existingCategories = $status[RegistryKeys::GLOBAL_DATA][RegistryKeys::CATEGORIES][StoreViewCodes::ADMIN];
            $this->attributeSets = $status[RegistryKeys::GLOBAL_DATA][RegistryKeys::ATTRIBUTE_SETS][$subject->getEntityTypeCode()];
        }

        // load the artefacts from the subject
        $artefacts = $subject->getArtefacts();

        // query whether or not the artefacts with the given type are available
        if (isset($artefacts[$type = CopyCategoryObserver::ARTEFACT_TYPE])) {
            // load the artefacts for the given type
            $newCategories = array_shift($artefacts[CopyCategoryObserver::ARTEFACT_TYPE]);
            // iterate over ALL rows found in the actual CSV file
            foreach ($newCategories as $newCategory) {
                // we only want to process main rows, so temporary persist store view specific rows
                if ($newCategory[ColumnKeys::STORE_VIEW_CODE]) {
                    $this->storeViewRows[] = $this->template($newCategory);
                } else {
                    // clean-up the path to avoid encoding/quotation specific differences
                    $path = implode('/', $this->serializer->unserialize($newCategory[ColumnKeys::PATH], '/'));
                    // add the main category to the new categories (we want to load/update the position for)
                    $this->mainRows[$path] = $newCategory;
                }
            }

            // sort the main rows by the path, store_view_code and position
            // ATTENTION: we use uasort, because we NEED to preserve the keys
            uasort($this->mainRows, function ($a, $b) {
                return
                    strcmp($a[ColumnKeys::PATH], $b[ColumnKeys::PATH]) ?:
                    strcmp($a[ColumnKeys::STORE_VIEW_CODE], $b[ColumnKeys::STORE_VIEW_CODE]) ?:
                    strcmp($a[ColumnKeys::POSITION], $b[ColumnKeys::POSITION]);
            });

            // update the position of the categories and the categories on the same level
            foreach ($this->mainRows as $path => $category) {
                $this->update($path, $category);
            }

            // merge the processed rows with the previously excluded store view
            // base rows, because in the new CSV file we want to have them both
            $this->artefacts = array_merge(array_values($this->artefacts), $this->storeViewRows);

            // sort the artefacts again, because we want to export them in the expected order
            usort($this->artefacts, function ($a, $b) {
                return
                    strcmp($a[ColumnKeys::PATH], $b[ColumnKeys::PATH]) ?:
                    strcmp($a[ColumnKeys::STORE_VIEW_CODE], $b[ColumnKeys::STORE_VIEW_CODE]);
            });

            // replace the artefacts to be exported later
            $subject->setArtefactsByType($type, array($this->artefacts));
        }
    }

    /**
     * Template method to create a new array based on the passed category data.
     *
     * @param array $category The category data to create the new array from
     *
     * @return array The array with the category data
     */
    private function template(array $category) : array
    {

        // initialize the array for the category data
        $row = array();

        // initialize the array's keys from the actual headers AND add the position (which is the important column we are here for)
        $keys = array_keys($row = array_replace($this->subject->getHeaders(), array(ColumnKeys::POSITION => null)));

        // initialize the category with the data from the passed category
        foreach ($keys as $key) {
            $row[$key] = isset($category[$key]) ? $category[$key] : null;
        }

        // return the row
        return $row;
    }

    /**
     * Return's an array, sorted by position, with categories on the same level as the one with
     * the passed path is and have the same parent node.
     *
     * Therefore, the we cut off the last element of the passed path and compare it with the
     * already existing categories and the categories that has been processed within the
     * actual import file.
     *
     * @param string $path The path to return the categories on the same level for
     *
     * @return array The array, sorted by position, of the categories on the same level
     */
    private function categoriesOnSameLevel(string $path) : array
    {

        // initialize the array for the categories an the same level
        $categoriesOnSameLevel = array();

        // explode the path by the category separator
        $elements = $this->serializer->unserialize($path, '/');

        // iterate over the existing categories to load the one's on the same level
        foreach ($this->existingCategories as $p => $category) {
            // initialize the counter with the size of elements
            $sizeOfElements = sizeof($elements);
            // query whether or not the level (integer) is the same as the number of elements
            // AND the category is NOT a root category. This means we virtually cut off the
            // last element of the passed category. Then we know the category is at least on
            // the same level, but NOT if it has the same parent category!!!!!
            if ((int) $category[MemberNames::LEVEL] == $sizeOfElements && $sizeOfElements > 1) {
                // extract the category's path by the category separator
                $el = $this->serializer->unserialize($p, '/');
                // diff the path of the parent category to make sure they are children of the same parent node
                $diff = array_diff(array_slice($elements, 0, sizeof($elements) - 1), array_slice($el, 0, sizeof($elements)));
                // BINGO: We found a category on the same level that has the same parent
                if (sizeof($diff) === 0) {
                    $categoriesOnSameLevel[$p] = $this->template(
                        array_merge(
                            $category,
                            array(ColumnKeys::ATTRIBUTE_SET_CODE => $this->getAttributeSetNameById($category[MemberNames::ATTRIBUTE_SET_ID]))
                        )
                    );
                }
            }
        }

        // iterate over the already processed categories and try to find the one's on the same level
        // because it's possible we've a NEW category in the CSV file which is NOT in the array with
        // the existing ones.
        foreach ($this->artefacts as $p => $artefact) {
            // extract the category's path by the category separator
            $el = $this->serializer->unserialize($p, '/');
            // query whether or not the categories are at least on the same level
            if (sizeof($el) == sizeof($elements)) {
                // diff the path of the parent category to make sure they are children of the same parent node
                $diff = array_diff(array_slice($elements, 0, sizeof($elements) - 1), array_slice($el, 0, sizeof($elements)));
                // BINGO: We found a category on the same level that has the same parent
                if (sizeof($diff) === 0) {
                    $categoriesOnSameLevel[$p] = $artefact;
                }
            }
        }

        // sor the categories by their position and KEEP the keys
        uasort($categoriesOnSameLevel, function ($a, $b) {
            // return 0 when the position is equal (should never happen)
            if ($a[MemberNames::POSITION] == $b[MemberNames::POSITION]) {
                return 0;
            }
            // return 1 or -1 if the categories position differs
            return $a[MemberNames::POSITION] > $b[MemberNames::POSITION] ? 1 : -1;
        });

        // return the sorted array with categories on the same level and the same parent node
        return $categoriesOnSameLevel;
    }

    /**
     * Return's the next free position for the category with the passed path.
     *
     * The calculation happens on the already exising categories on the same
     * level and the same parent not as well as the categories that already
     * have been processed.
     *
     * @param string $path The path of the category to return the position for
     *
     * @return int The next free position
     */
    private function nextPosition(string $path) : int
    {

        // load the categories, sorted by position, that have the same level
        // as well as the same parent node
        $categoriesOnSameLevel = $this->categoriesOnSameLevel($path);

        // load the last one, because this must be the one with the highest position
        $lastCategory = end($categoriesOnSameLevel);

        // raise the position counter either by loading the position of the last category
        // OR the number of categories on the same level and with the same parent node
        if (isset($lastCategory[MemberNames::POSITION])) {
            $position = (int) $lastCategory[MemberNames::POSITION] + 1;
        } else {
            $position = sizeof($categoriesOnSameLevel) + 1;
        }

        // return the next position
        return $position;
    }

    /**
     * Updates the positions (if necessary) of ALL categories on the same level as the passed one is and
     * add them to the array of categories that has to be exported with the position column filled.
     *
     * @param string $path The unqiue path ot the passed category (ATTENTION: We do NOT use the column `path`, because this can have format specific encoding or quoting)
     * @param array  $cat  The category to raise the positions on the same level for
     *
     * @return void
     */
    private function update(string $path, array $cat) : void
    {

        // query whether or not the the category with the passed path already exists
        if (isset($this->existingCategories[$path])) {
            // if yes, load the exisisting category
            $existingCategory = $this->existingCategories[$path];
            // query whether or not the passed category has a position, eventually a new and
            // different one has been set, if NOT use the position of the existing one
            if (isset($cat[ColumnKeys::POSITION]) === false) {
                $cat[ColumnKeys::POSITION] = $existingCategory[MemberNames::POSITION];
            }

            // query whether or not the new position is different from the exising one, if not we do
            // NOT have to process the categories on the same level and raise their positions as well
            if ((int) $cat[ColumnKeys::POSITION] === (int) $existingCategory[MemberNames::POSITION]) {
                $this->artefacts[$path] = $this->template($cat);
                return;
            }
        }

        // category is NEW and has NO position > load the next available one and append the category
        // on the end of the existing categories on the same level and the same parent node
        if (isset($cat[ColumnKeys::POSITION]) === false) {
            $cat[ColumnKeys::POSITION] = $this->nextPosition($path);
        } else {
            // load the categories on the same level
            $categoriesOnSameLevel = $this->categoriesOnSameLevel($path);
            // iterate over the categories on the same level and the same parent node
            foreach ($categoriesOnSameLevel as $p => $category) {
                // if the position of the existing category is lower than the one of the
                // passed category we do not have to care about raising the position
                if ((int) $category[ColumnKeys::POSITION] < (int) $cat[ColumnKeys::POSITION]) {
                    continue;
                }

                // in case the position is equal or higher, we've to raise the position
                // make sure the new category will be rendered before the existing one
                $this->artefacts[$p] = $this->template(
                    array_merge(
                        $category,
                        array(
                            ColumnKeys::PATH               => $category[MemberNames::PATH],
                            ColumnKeys::POSITION           => (int) $category[MemberNames::POSITION] + 1,
                            ColumnKeys::ATTRIBUTE_SET_CODE => $category[ColumnKeys::ATTRIBUTE_SET_CODE]
                        )
                    )
                );
            }
        }

        // finally append the passed category to the array with the artefacts
        $this->artefacts[$path] = $this->template($cat);
    }

    /**
     * Return's the attribute set name of the attribute set with the given ID.
     *
     * @param int $attributeSetId The ID of the attribute set to return the name for
     *
     * @return string The attribute set name
     * @throws \InvalidArgumentException Is thrown, if the attribute set with the passed ID is NOT available
     */
    private function getAttributeSetNameById(int $attributeSetId) : string
    {

        // try to load the attribute set with the given ID
        foreach ($this->attributeSets as $attributeSet) {
            if ((int) $attributeSet[MemberNames::ATTRIBUTE_SET_ID] === $attributeSetId) {
                return $attributeSet[MemberNames::ATTRIBUTE_SET_NAME];
            }
        }

        // throw an exception if the attribute set is NOT available
        throw new \InvalidArgumentException(sprintf('Can\'t find attribute set with ID "%s"', $attributeSetId));
    }
}
