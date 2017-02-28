<?php

/**
 * TechDivision\Import\Category\Observers\CategoryUpdateObserver
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

/**
 * Observer that add's/update's the category itself.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */
class CategoryUpdateObserver extends CategoryObserver
{

    /**
     * Initialize the category with the passed attributes and returns an instance.
     *
     * @param array $attr The category attributes
     *
     * @return array The initialized category
     */
    protected function initializeCategory(array $attr)
    {

        // load the path of the category that has to be initialized
        $path = $this->getValue(ColumnKeys::PATH);

        try {
            // try to load the category and the entity with the passed path
            $category = $this->getCategoryByPath($path);
            $entity = $this->loadCategory($category[MemberNames::ENTITY_ID]);

            // merge it with the attributes, if we can find it
            return $this->mergeEntity($entity, $attr);

        } catch (\Exception $e) {
            $this->getSystemLogger()->debug(sprintf('Can\'t load category with path %s', $path));
        }

        // otherwise simply return the attributes
        return $attr;
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
        return $this->getSubject()->loadCategory($id);
    }
}
