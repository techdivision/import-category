<?php

/**
 * TechDivision\Import\Category\Repositories\CategoryIntRepositoryInterface
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Category\Repositories;

use TechDivision\Import\Dbal\Repositories\RepositoryInterface;

/**
 * Interface for repositories providing functionality to load category integer attribute data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */
interface CategoryIntRepositoryInterface extends RepositoryInterface
{

    /**
     * Load's and return's the integer attributes with the passed primary key/store ID.
     *
     * @param integer $pk      The primary key of the attributes
     * @param integer $storeId The store ID of the attributes
     *
     * @return array The integer attributes
     */
    public function findAllByPrimaryKeyAndStoreId($pk, $storeId);

    /**
     * Load's and return's the integer attributes with the passed primary key/store ID, extended with the attribute code.
     *
     * @param integer $pk      The primary key of the attributes
     * @param integer $storeId The store ID of the attributes
     *
     * @return array The integer attributes
     */
    public function findAllByPrimaryKeyAndStoreIdExtendedWithAttributeCode($pk, $storeId);
}
