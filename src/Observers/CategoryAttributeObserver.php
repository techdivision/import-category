<?php

/**
 * TechDivision\Import\Category\Observers\CategoryAttributeObserver
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
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Category\Observers;

use TechDivision\Import\Observers\AbstractAttributeObserver;
use TechDivision\Import\Category\Services\CategoryBunchProcessorInterface;

/**
 * Observer that creates/updates the category's attributes.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class CategoryAttributeObserver extends AbstractAttributeObserver
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
     * Persist's the passed varchar attribute.
     *
     * @param array $attribute The attribute to persist
     *
     * @return void
     */
    protected function persistVarcharAttribute($attribute)
    {
        $this->getCategoryBunchProcessor()->persistCategoryVarcharAttribute($attribute);
    }

    /**
     * Persist's the passed integer attribute.
     *
     * @param array $attribute The attribute to persist
     *
     * @return void
     */
    protected function persistIntAttribute($attribute)
    {
        $this->getCategoryBunchProcessor()->persistCategoryIntAttribute($attribute);
    }

    /**
     * Persist's the passed decimal attribute.
     *
     * @param array $attribute The attribute to persist
     *
     * @return void
     */
    protected function persistDecimalAttribute($attribute)
    {
        $this->getCategoryBunchProcessor()->persistCategoryDecimalAttribute($attribute);
    }

    /**
     * Persist's the passed datetime attribute.
     *
     * @param array $attribute The attribute to persist
     *
     * @return void
     */
    protected function persistDatetimeAttribute($attribute)
    {
        $this->getCategoryBunchProcessor()->persistCategoryDatetimeAttribute($attribute);
    }

    /**
     * Persist's the passed text attribute.
     *
     * @param array $attribute The attribute to persist
     *
     * @return void
     */
    protected function persistTextAttribute($attribute)
    {
        $this->getCategoryBunchProcessor()->persistCategoryTextAttribute($attribute);
    }
}
