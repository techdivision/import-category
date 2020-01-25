<?php

/**
 * TechDivision\Import\Category\Observers\CategoryObserver
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

namespace TechDivision\Import\Category\Observers;

use TechDivision\Import\Category\Utils\ColumnKeys;
use TechDivision\Import\Category\Utils\MemberNames;
use TechDivision\Import\Category\Services\CategoryBunchProcessorInterface;

/**
 * Observer that create's the category itself.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */
class CategoryObserver extends AbstractCategoryImportObserver
{

    /**
     * The artefact type.
     *
     * @var string
     */
    const ARTEFACT_TYPE = 'category-path';

    /**
     * The array with the parent category IDs.
     *
     * @var array
     */
    protected $categoryIds = array();

    /**
     * The processor to read/write the necessary category data.
     *
     * @var \TechDivision\Import\Category\Services\CategoryBunchProcessorInterface
     */
    protected $categoryBunchProcessor;

    /**
     * Initialize the subject instance.
     *
     * @param \TechDivision\Import\Category\Services\CategoryBunchProcessorInterface $categoryBunchProcessor The category bunch processor instance
     */
    public function __construct(CategoryBunchProcessorInterface $categoryBunchProcessor)
    {
        $this->categoryBunchProcessor = $categoryBunchProcessor;
    }

    /**
     * Returns the category bunch processor instance.
     *
     * @return \TechDivision\Import\Category\Services\CategoryBunchProcessorInterface The category bunch processor instance
     */
    protected function getCategoryBunchProcessor()
    {
        return $this->categoryBunchProcessor;
    }

    /**
     * Process the observer's business logic.
     *
     * @return void
     */
    protected function process()
    {

        // explode the path into the category names
        if ($categories = $this->explode($this->getValue(ColumnKeys::PATH), '/')) {
            // initialize the artefacts and reset the category IDs
            $artefacts = array();
            $this->categoryIds = array(1);

            // iterate over the category names and try to load the category therefore
            for ($i = 0; $i < sizeof($categories); $i++) {
                // prepare the expected category name
                $categoryPath = implode('/', array_slice($categories, 0, $i + 1));

                try {
                    // load the existing category
                    if ($category = $this->loadCategory($this->mapPath($categoryPath))) {
                        // prepend the ID the array with the category IDs
                        array_push($this->categoryIds, (integer) $category[MemberNames::ENTITY_ID]);
                        // temporary persist primary keys
                        $this->updatePrimaryKeys($category);
                    } else {
                        throw new \Exception(sprintf('Can\'t load category %s from database', $categoryPath));
                    }
                } catch (\Exception $e) {
                    // log a message that requested category is NOT available
                    $this->getSystemLogger()->debug(sprintf('Can\'t load category %s, create a new one', $categoryPath));

                    // prepare the static entity values, insert the entity and set the entity ID
                    $category = $this->initializeCategory($this->prepareAttributes());

                    // update the persisted category with the entity ID
                    $category[$this->getPkMemberName()] = $this->persistCategory($category);

                    // update the persisted category with the additional attribute values
                    $category[MemberNames::NAME] = $this->getValue(ColumnKeys::NAME);
                    $category[MemberNames::URL_KEY] = $this->getValue(ColumnKeys::URL_KEY);
                    $category[MemberNames::URL_PATH] = $this->getValue(ColumnKeys::URL_PATH);
                    $category[MemberNames::IS_ACTIVE] = $this->getValue(ColumnKeys::IS_ACTIVE);
                    $category[MemberNames::IS_ANCHOR] = $this->getValue(ColumnKeys::IS_ANCHOR);
                    $category[MemberNames::INCLUDE_IN_MENU] = $this->getValue(ColumnKeys::INCLUDE_IN_MENU);

                    // append the ID of the new category to array with the IDs
                    array_push($this->categoryIds, $category[MemberNames::ENTITY_ID]);

                    // temporary persist primary keys
                    $this->updatePrimaryKeys($category);

                    // add the category by the given path as well as the path mapping
                    $this->addCategoryByPath($categoryPath, $category);
                    $this->addPathEntityIdMapping($categoryPath);
                } finally {
                    // prepare the artefact and put it on the stack
                    $artefacts[] = array(
                        $this->getPkMemberName() => $category[$this->getPkMemberName()],
                        MemberNames::PATH        => implode('/', $this->categoryIds)
                    );
                }
            }

            // add the artefacts
            $this->addArtefacts($artefacts);
        }
    }

    /**
     * Prepare the attributes of the entity that has to be persisted.
     *
     * @return array The prepared attributes
     */
    protected function prepareAttributes()
    {

        // prepare the date format for the created at/updated at dates
        $createdAt = $this->getValue(ColumnKeys::CREATED_AT, date('Y-m-d H:i:s'), array($this, 'formatDate'));
        $updatedAt = $this->getValue(ColumnKeys::UPDATED_AT, date('Y-m-d H:i:s'), array($this, 'formatDate'));

        // load the product's attribute set ID
        $attributeSet = $this->getAttributeSetByAttributeSetName($this->getValue(ColumnKeys::ATTRIBUTE_SET_CODE));
        $attributeSetId = $attributeSet[MemberNames::ATTRIBUTE_SET_ID];
        $this->setAttributeSet($attributeSet);

        // initialize parent ID (the parent array entry)
        $parentId = $this->categoryIds[sizeof($this->categoryIds) - 1];

        // initialize the level
        $level = sizeof($this->categoryIds);

        // load the position, if available
        $position = $this->getValue(ColumnKeys::POSITION, 0);

        // return the prepared product
        return $this->initializeEntity(
            array(
                MemberNames::CREATED_AT       => $createdAt,
                MemberNames::UPDATED_AT       => $updatedAt,
                MemberNames::ATTRIBUTE_SET_ID => $attributeSetId,
                MemberNames::PATH             => '',
                MemberNames::PARENT_ID        => $parentId,
                MemberNames::POSITION         => $position,
                MemberNames::LEVEL            => $level,
                MemberNames::CHILDREN_COUNT   => 0
            )
        );
    }

    /**
     * Initialize the category with the passed attributes and returns an instance.
     *
     * @param array $attr The category attributes
     *
     * @return array The initialized category
     */
    protected function initializeCategory(array $attr)
    {
        return $attr;
    }

    /**
     * Add's the passed category with the given path.
     *
     * @param string $path     The path to add the category with
     * @param array  $category The catagory to add
     *
     * @return void
     */
    protected function addCategoryByPath($path, array $category)
    {
        $this->getSubject()->addCategoryByPath($path, $category);
    }

    /**
     * Return the primary key member name.
     *
     * @return string The primary key member name
     */
    protected function getPkMemberName()
    {
        return MemberNames::ENTITY_ID;
    }

    /**
     * Tmporary persist the entity ID
     *
     * @param array $category The category to update the IDs
     *
     * @return void
     */
    protected function updatePrimaryKeys(array $category)
    {
        $this->setLastEntityId($category[$this->getPkMemberName()]);
    }

    /**
     * Return's the category with the passed ID.
     *
     * @param string $id The ID of the category to return
     *
     * @return array The category
     */
    protected function loadCategory($id)
    {
        return $this->getCategoryBunchProcessor()->loadCategory($id);
    }

    /**
     * Persist's the passed category data and return's the ID.
     *
     * @param array $category The category data to persist
     *
     * @return string The ID of the persisted entity
     */
    protected function persistCategory($category)
    {
        return $this->getCategoryBunchProcessor()->persistCategory($category);
    }

    /**
     * Set's the attribute set of the product that has to be created.
     *
     * @param array $attributeSet The attribute set
     *
     * @return void
     */
    protected function setAttributeSet(array $attributeSet)
    {
        $this->getSubject()->setAttributeSet($attributeSet);
    }

    /**
     * Return's the attribute set of the product that has to be created.
     *
     * @return array The attribute set
     */
    protected function getAttributeSet()
    {
        $this->getSubject()->getAttributeSet();
    }

    /**
     * Return's the attribute set with the passed attribute set name.
     *
     * @param string $attributeSetName The name of the requested attribute set
     *
     * @return array The attribute set data
     */
    protected function getAttributeSetByAttributeSetName($attributeSetName)
    {
        return $this->getSubject()->getAttributeSetByAttributeSetName($attributeSetName);
    }

    /**
     * Set's the ID of the product that has been created recently.
     *
     * @param string $lastEntityId The entity ID
     *
     * @return void
     */
    protected function setLastEntityId($lastEntityId)
    {
        $this->getSubject()->setLastEntityId($lastEntityId);
    }

    /**
     * Add the passed category artefacts to the category with the
     * last entity ID.
     *
     * @param array $artefacts The category artefacts
     *
     * @return void
     * @uses \TechDivision\Import\Category\BunchSubject::getLastEntityId()
     */
    protected function addArtefacts(array $artefacts)
    {
        $this->getSubject()->addArtefacts(CategoryObserver::ARTEFACT_TYPE, $artefacts);
    }
}
