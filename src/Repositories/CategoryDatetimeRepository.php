<?php

/**
 * TechDivision\Import\Category\Repositories\CategoryDatetimeRepository
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

namespace TechDivision\Import\Category\Repositories;

use TechDivision\Import\Category\Utils\ParamNames;
use TechDivision\Import\Category\Utils\SqlStatementKeys;
use TechDivision\Import\Dbal\Collection\Repositories\AbstractRepository;

/**
 * Repository implementation to load category datetime attribute data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */
class CategoryDatetimeRepository extends AbstractRepository implements CategoryDatetimeRepositoryInterface
{

    /**
     * The prepared statement to load the existing category datetime attributes with the passed entity/store ID.
     *
     * @var \PDOStatement
     */
    protected $categoryDatetimesStmt;

    /**
     * The prepared statement to load the existing category datetime attributes with the passed entity/store ID, extended with the attribute code.
     *
     * @var \PDOStatement
     */
    protected $categoryDatetimesByPkAndStoreIdStmt;

    /**
     * Initializes the repository's prepared statements.
     *
     * @return void
     */
    public function init()
    {

        // initialize the prepared statements
        $this->categoryDatetimesStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::CATEGORY_DATETIMES));
        $this->categoryDatetimesByPkAndStoreIdStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::CATEGORY_DATETIMES_BY_PK_AND_STORE_ID));
    }

    /**
     * Load's and return's the datetime attributes for the passed primary key/store ID.
     *
     * @param integer $pk      The primary key of the attributes
     * @param integer $storeId The store ID of the attributes
     *
     * @return array The datetime attributes
     */
    public function findAllByPrimaryKeyAndStoreId($pk, $storeId)
    {

        // prepare the params
        $params = array(
            ParamNames::PK        => $pk,
            ParamNames::STORE_ID  => $storeId
        );

        // load and return the category datetime attributes with the passed primary key/store ID
        $this->categoryDatetimesStmt->execute($params);
        return $this->categoryDatetimesStmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Load's and return's the datetime attributes for the passed primary key/store ID, extended with the attribute code.
     *
     * @param integer $pk      The primary key of the attributes
     * @param integer $storeId The store ID of the attributes
     *
     * @return array The datetime attributes
     */
    public function findAllByPrimaryKeyAndStoreIdExtendedWithAttributeCode($pk, $storeId)
    {

        // prepare the params
        $params = array(
            ParamNames::PK        => $pk,
            ParamNames::STORE_ID  => $storeId
        );

        // load and return the category datetime attributes with the passed primary key/store ID
        $this->categoryDatetimesByPkAndStoreIdStmt->execute($params);
        return $this->categoryDatetimesByPkAndStoreIdStmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
