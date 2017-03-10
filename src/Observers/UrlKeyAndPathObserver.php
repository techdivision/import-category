<?php

/**
 * TechDivision\Import\Category\Observers\UrlKeyAndPathObserver
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
use TechDivision\Import\Utils\Filter\UrlKeyFilterTrait;
use TechDivision\Import\Category\Utils\MemberNames;

/**
 * Observer that extracts the URL key/path from the category path
 * and adds them as two new columns with the their values.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */
class UrlKeyAndPathObserver extends AbstractCategoryImportObserver
{

    /**
     * The trait that provides string => URL key conversion functionality.
     *
     * @var \TechDivision\Import\Utils\Filter\UrlKeyFilterTrait
     */
    use UrlKeyFilterTrait;

    /**
     * Process the observer's business logic.
     *
     * @return void
     */
    protected function process()
    {

        // initialize the URL key and array for the categories
        $urlKey = null;
        $categories = array();

        // query whether or not the URL key column has a value
        if ($this->hasValue(ColumnKeys::URL_KEY)) {
            $urlKey = $this->getValue(ColumnKeys::URL_KEY);
        } else {
            $this->setValue(
                ColumnKeys::URL_KEY,
                $urlKey = $this->convertNameToUrlKey($this->getValue(ColumnKeys::NAME))
            );
        }

        // explode the path into the category names
        if ($categories = $this->explode($this->getValue(ColumnKeys::PATH), '/')) {
            // initialize the category with the actual category's URL key
            $categoryPaths = array($urlKey);

            // iterate over the category names and try to load the category therefore
            for ($i = sizeof($categories); $i > 1; $i--) {
                try {
                    // prepare the expected category name
                    $categoryPath = implode('/', array_slice($categories, 0, $i));

                    // load the existing category and prepend the URL key the array with the category URL keys
                    $existingCategory = $this->getCategoryByPath($categoryPath);
                    if (isset($existingCategory[MemberNames::URL_KEY])) {
                        array_unshift($categoryPaths, $existingCategory[MemberNames::URL_KEY]);
                    } else {
                        $this->getSystemLogger()->debug(sprintf('Can\'t find URL key for category %s', $categoryPath));
                    }

                } catch (\Exception $e) {
                    $this->getSystemLogger()->debug(sprintf('Can\'t load parent category %s', $categoryPath));
                }
            }

            // create the header for the URL path
            if (!$this->hasHeader(ColumnKeys::URL_PATH)) {
                $this->addHeader(ColumnKeys::URL_PATH);
            }

            // set the URL path
            $this->setValue(ColumnKeys::URL_PATH, implode('/', $categoryPaths));
        }
    }

    /**
     * Return's the category with the passed path.
     *
     * @param string $path The path of the category to return
     *
     * @return array The category
     */
    protected function getCategoryByPath($path)
    {
        return $this->getSubject()->getCategoryByPath($path);
    }
}
