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
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Category\Listeners;

use League\Event\EventInterface;
use League\Event\AbstractListener;
use TechDivision\Import\Subjects\SubjectInterface;
use TechDivision\Import\Services\RegistryProcessorInterface;
use TechDivision\Import\Category\Observers\CopyCategoryObserver;
use TechDivision\Import\Category\Utils\ColumnKeys;
use TechDivision\Import\Category\Utils\RegistryKeys;
use TechDivision\Import\Utils\StoreViewCodes;
use TechDivision\Import\Category\Utils\MemberNames;

/**
 * A listener implementation that sorts categories by their path.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */
class SortCategoryListener extends AbstractListener
{

    const ARTEFACT_TYPE = 'category-position';

    /**
     * The registry processor instance.
     *
     * @var \TechDivision\Import\Services\RegistryProcessorInterface
     */
    protected $registryProcessor;

    protected $existingCategories = array();

    protected $artefacts = array();

    protected $newCategories = array();

    protected $attributeSets = array();

    protected $positionCounter = 0;

    /**
     * @var \TechDivision\Import\Subjects\SubjectInterface
     */
    protected $subject;

    /**
     * @var \TechDivision\Import\Serializers\SerializerInterface
     */
    protected $serializer;

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
    public function handle(EventInterface $event, SubjectInterface $subject = null)
    {

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

            foreach ($newCategories as $newCategory) {
                $this->newCategories[$newCategory[ColumnKeys::PATH]] = $newCategory;
            }

            // sort them by the path + store_view_code
            uasort($this->newCategories, function ($a, $b) {
                return
                    strcmp($a[ColumnKeys::PATH], $b[ColumnKeys::PATH]) ?:
                    strcmp($a[ColumnKeys::STORE_VIEW_CODE], $b[ColumnKeys::STORE_VIEW_CODE]) ?:
                    strcmp($a[ColumnKeys::POSITION], $b[ColumnKeys::POSITION]);
            });

            // replace the artefacts to be exported later
            $subject->setArtefactsByType($type, array($this->newCategories));

            foreach ($this->newCategories as $category) {

                // query whether or not the category already exists
                if (isset($this->existingCategories[$category[ColumnKeys::PATH]])) {

                    // z1
                    if (isset($category[ColumnKeys::POSITION])) {
                        $this->raise($category);
                    }

                    // do nothing, because the category is already available and position should NOT change

                } else {

                    // z2
                    if (isset($category[ColumnKeys::POSITION])) {
                        $this->raise($category);
                    } else {
                        $this->last($category);
                    }
                }
            }

            // sort them by the path + store_view_code
            usort($this->artefacts, function ($a, $b) {
                return
                strcmp($a[ColumnKeys::PATH], $b[ColumnKeys::PATH]) ?:
                strcmp($a[ColumnKeys::POSITION], $b[ColumnKeys::POSITION]);
            });

            print_r($this->artefacts);

            // replace the artefacts to be exported later
            $subject->setArtefactsByType(SortCategoryListener::ARTEFACT_TYPE, array($this->artefacts));
        }
    }

    private function template(array $category)
    {

        $row = array(
            ColumnKeys::PATH               => null,
            ColumnKeys::POSITION           => null,
            ColumnKeys::ATTRIBUTE_SET_CODE => null
        );

        $keys = array_keys($row);

        foreach ($keys as $key) {
            $row[$key] = isset($category[$key]) ? $category[$key] : null;
        }

        return $row;
    }

    private function categoriesOnSameLevel(string $path) : array
    {

        $categories = array();

        $elements = $this->serializer->unserialize($path, '/');

        foreach ($this->existingCategories as $p => $category) {

            if ((int) $category[MemberNames::LEVEL] == sizeof($elements)) {

                $el = $this->serializer->unserialize($p, '/');

                $diff = array_diff(array_slice($elements, 0, sizeof($elements) - 1), array_slice($el, 0, sizeof($elements)));

                if (sizeof($diff) === 0) {
                    $categories[$p] = $category;
                }
            }
        }

        usort($categories, function ($a, $b) {

            if ($a[MemberNames::POSITION] == $b[MemberNames::POSITION]) {
                return 0;
            }

            $a[MemberNames::POSITION] > $b[MemberNames::POSITION] ? -1 : 1;

        });

        return $categories;
    }

    private function nextPosition(string $path) : int
    {

        $categoriesOnSameLevel = $this->categoriesOnSameLevel($path);

        $lastCategory = end($categoriesOnSameLevel);

        if (isset($lastCategory[MemberNames::POSITION])) {
            $position = (int) $lastCategory[MemberNames::POSITION];
        } else {
            $position = $this->positionCounter++;
        }

        return $position;
    }

    /**
     * Raise the position of ALL categories on the same level as the passed category has.
     *
     * @param array $cat The category to raise the position of the following categories
     *
     * @return void
     */
    private function raise(array $cat) : void
    {

        $categoriesOnSameLevel = $this->categoriesOnSameLevel($cat[ColumnKeys::PATH]);

        foreach ($categoriesOnSameLevel as $p => $category) {

            if ((int) $category[ColumnKeys::POSITION] < (int) $cat[ColumnKeys::POSITION]) {
                continue;
            }

            // ATTENTION: We need a template mechanism here that allows us to create
            //            a CSV structure conform version of the existing category.

            $artefact = array();

            $artefact[ColumnKeys::PATH] = $p;
            $artefact[ColumnKeys::POSITION] = (int) $category[ColumnKeys::POSITION] + 1;
            $artefact[ColumnKeys::ATTRIBUTE_SET_CODE] = $this->getAttributeSetNameById($category[MemberNames::ATTRIBUTE_SET_ID]);

            $this->artefacts[$p] = $this->template($artefact);
        }
    }

    private function last(array $cat) : void
    {

        $cat[ColumnKeys::POSITION] = $this->nextPosition($path = $cat[ColumnKeys::PATH]);

        $this->artefacts[$path] = $cat;
    }

    private function getAttributeSetNameById($attributeSetId)
    {
        foreach ($this->attributeSets as $attributeSet) {
            if ($attributeSet[MemberNames::ATTRIBUTE_SET_ID] === $attributeSetId) {
                return $attributeSet[MemberNames::ATTRIBUTE_SET_NAME];
            }
        }
    }
}
