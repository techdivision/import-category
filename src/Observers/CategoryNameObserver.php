<?php

/**
 * TechDivision\Import\Category\Observers\CategoryNameObserver
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

/**
 * Observer that extracts the category name from the path and adds a new column
 * with the name as value.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */
class CategoryNameObserver extends AbstractCategoryImportObserver
{

    /**
     * Process the observer's business logic.
     *
     * @return void
     */
    protected function process()
    {

        // query whether or not the row has the path value set
        if ($path = $this->getValue(ColumnKeys::PATH)) {
            // explode the category names from the path
            $categories = $this->explode($path, '/');

            // try to load the appropriate key for the value
            if (!$this->hasHeader(ColumnKeys::NAME)) {
                $this->addHeader(ColumnKeys::NAME);
            }

            // append the category name
            $this->setValue(ColumnKeys::NAME, end($categories));
        }
    }
}
