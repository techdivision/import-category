<?php

/**
 * TechDivision\Import\Category\Observers\CategoryPathObserver
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
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Category\Observers;

use TechDivision\Import\Category\Utils\ColumnKeys;
use TechDivision\Import\Category\Utils\MemberNames;
use TechDivision\Import\Category\Services\CategoryBunchProcessorInterface;

/**
 * Observer that update's the categories path.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */
class CategoryPathObserver extends AbstractCategoryImportObserver
{

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
     * Return's the category bunch processor instance.
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
        $this->persistCategory($this->initializeCategory($this->prepareAttributes()));
    }

    /**
     * Prepare the attributes of the entity that has to be persisted.
     *
     * @return array The prepared attributes
     */
    protected function prepareAttributes()
    {

        // initialize the values that has to be updated
        $updatedAt = date('Y-m-d H:i:s');
        $path = $this->getValue(ColumnKeys::PATH);

        // prepare and return the array with the updated values
        return array(
            MemberNames::PATH           => $path,
            MemberNames::UPDATED_AT     => $updatedAt
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
        return $this->mergeEntity($this->loadCategory($this->getPrimaryKey()), $attr);
    }

    /**
     * Return's the primary key of the category to load.
     *
     * @return integer The primary key of the category
     */
    protected function getPrimaryKey()
    {
        return $this->getValue(ColumnKeys::ENTITY_ID);
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
}
