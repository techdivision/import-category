<?php

/**
 * TechDivision\Import\Category\Repositories\CategoryIntRepository
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

use TechDivision\Import\Category\Utils\ParamNames;
use TechDivision\Import\Category\Utils\SqlStatementKeys;
use TechDivision\Import\Dbal\Collection\Repositories\AbstractRepository;

/**
 * Repository implementation to load category integer attribute data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */
class CategoryIntRepository extends AbstractRepository implements CategoryIntRepositoryInterface
{

    /**
     * The prepared statement to load the existing category integer attributes with the passed entity/store ID.
     *
     * @var \PDOStatement
     */
    protected $categoryIntsStmt;

    /**
     * The prepared statement to load the existing category integer attributes with the passed entity/store ID, extended with the attribute code.
     *
     * @var \PDOStatement
     */
    protected $categoryIntsByPkAndStoreIdStmt;

    /**
     * Initializes the repository's prepared statements.
     *
     * @return void
     */
    public function init()
    {

        // initialize the prepared statements
        $this->categoryIntsStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::CATEGORY_INTS));
        $this->categoryIntsByPkAndStoreIdStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::CATEGORY_INTS_BY_PK_AND_STORE_ID));
    }

    /**
     * Load's and return's the integer attributes with the passed primary key/store ID.
     *
     * @param integer $pk      The primary key of the attributes
     * @param integer $storeId The store ID of the attributes
     *
     * @return array The integer attributes
     */
    public function findAllByPrimaryKeyAndStoreId($pk, $storeId)
    {

        // prepare the params
        $params = array(
            ParamNames::PK        => $pk,
            ParamNames::STORE_ID  => $storeId
        );

        // load and return the category integer attributes with the passed primary key/store ID
        $this->categoryIntsStmt->execute($params);
        return $this->categoryIntsStmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Load's and return's the integer attributes with the passed primary key/store ID, extended with the attribute code.
     *
     * @param integer $pk      The primary key of the attributes
     * @param integer $storeId The store ID of the attributes
     *
     * @return array The integer attributes
     */
    public function findAllByPrimaryKeyAndStoreIdExtendedWithAttributeCode($pk, $storeId)
    {

        // prepare the params
        $params = array(
            ParamNames::PK        => $pk,
            ParamNames::STORE_ID  => $storeId
        );

        // load and return the category integer attributes with the passed primary key/store ID
        $this->categoryIntsByPkAndStoreIdStmt->execute($params);
        return $this->categoryIntsByPkAndStoreIdStmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
