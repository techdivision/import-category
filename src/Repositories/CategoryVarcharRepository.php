<?php

/**
 * TechDivision\Import\Category\Repositories\CategoryVarcharRepository
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

use TechDivision\Import\Category\Utils\CacheKeys;
use TechDivision\Import\Category\Utils\MemberNames;
use TechDivision\Import\Category\Utils\ParamNames;
use TechDivision\Import\Category\Utils\SqlStatementKeys;
use TechDivision\Import\Cache\CacheAdapterInterface;
use TechDivision\Import\Dbal\Connection\ConnectionInterface;
use TechDivision\Import\Dbal\Repositories\SqlStatementRepositoryInterface;

/**
 * Repository implementation to load category varchar attribute data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */
class CategoryVarcharRepository extends \TechDivision\Import\Repositories\CategoryVarcharRepository implements CategoryVarcharRepositoryInterface
{

    /**
     * The prepared statement to load the existing category varchar attributes with the passed entity/store ID.
     *
     * @var \PDOStatement
     */
    protected $categoryVarcharsStmt;

    /**
     * The prepared statement to load the existing category varchar attributes with the passed entity/store ID, extended with the attribute code.
     *
     * @var \PDOStatement
     */
    protected $categoryVarcharsByPkAndStoreIdStmt;

    /**
     * The prepared statement to load the existing category varchar attribute with the passed attribute code
     * entity type/store ID as well as the passed value.
     *
     * @var \PDOStatement
     */
    protected $categoryVarcharByAttributeCodeAndEntityTypeIdAndStoreIdAndValueStmt;

    /**
     * The prepared statement to load the existing category varchar attribute with the passed attribute code
     * entity type/store ID as well as the passed primary key for row_id.
     *
     * @var \PDOStatement
     */
    protected $categoryVarcharByAttributeCodeAndEntityTypeIdAndStoreIdAndPrimaryKeyStmt;

    /**
     * Initialize the repository with the passed connection and utility class name.
     * .
     * @param \TechDivision\Import\Dbal\Connection\ConnectionInterface               $connection             The connection instance
     * @param \TechDivision\Import\Dbal\Repositories\SqlStatementRepositoryInterface $sqlStatementRepository The SQL repository instance
     * @param \TechDivision\Import\Cache\CacheAdapterInterface                       $cacheAdapter           The cache adapter instance
     */
    public function __construct(
        ConnectionInterface $connection,
        SqlStatementRepositoryInterface $sqlStatementRepository,
        CacheAdapterInterface $cacheAdapter
    ) {

        // pass the connection the SQL statement repository to the parent class
        parent::__construct($connection, $sqlStatementRepository);

        // set the cache adapter instance
        $this->cacheAdapter = $cacheAdapter;
    }

    /**
     * Return's the primary key name of the entity.
     *
     * @return string The name of the entity's primary key
     */
    public function getPrimaryKeyName()
    {
        return MemberNames::VALUE_ID;
    }

    /**
     * Initializes the repository's prepared statements.
     *
     * @return void
     */
    public function init()
    {

        // initialize the parend class
        parent::init();

        // initialize the prepared statements
        $this->categoryVarcharsStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::CATEGORY_VARCHARS));
        $this->categoryVarcharsByPkAndStoreIdStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::CATEGORY_VARCHARS_BY_PK_AND_STORE_ID));
        $this->categoryVarcharByAttributeCodeAndEntityTypeIdAndStoreIdAndValueStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::CATEGORY_VARCHAR_BY_ATTRIBUTE_CODE_AND_ENTITY_TYPE_ID_AND_STORE_ID_AND_VALUE));
        $this->categoryVarcharByAttributeCodeAndEntityTypeIdAndStoreIdAndPrimaryKeyStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::CATEGORY_VARCHAR_BY_ATTRIBUTE_CODE_AND_ENTITY_TYPE_ID_AND_STORE_ID_AND_PK));
    }

    /**
     * Load's and return's the varchar attributes with the passed primary key/store ID.
     *
     * @param integer $pk      The primary key of the attributes
     * @param integer $storeId The store ID of the attributes
     *
     * @return array The varchar attributes
     */
    public function findAllByPrimaryKeyAndStoreId($pk, $storeId)
    {

        // prepare the params
        $params = array(
            ParamNames::PK        => $pk,
            ParamNames::STORE_ID  => $storeId
        );

        // load and return the category varchar attributes with the passed primary key/store ID
        $this->categoryVarcharsStmt->execute($params);
        return $this->categoryVarcharsStmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Load's and return's the varchar attributes with the passed primary key/store ID, extended with the attribute code.
     *
     * @param integer $pk      The primary key of the attributes
     * @param integer $storeId The store ID of the attributes
     *
     * @return array The varchar attributes
     */
    public function findAllByPrimaryKeyAndStoreIdExtendedWithAttributeCode($pk, $storeId)
    {

        // prepare the params
        $params = array(
            ParamNames::PK        => $pk,
            ParamNames::STORE_ID  => $storeId
        );

        // load and return the category varchar attributes with the passed primary key/store ID
        $this->categoryVarcharsByPkAndStoreIdStmt->execute($params);
        return $this->categoryVarcharsByPkAndStoreIdStmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Load's and return's the varchar attribute with the passed params.
     *
     * @param integer $attributeCode The attribute code of the varchar attribute
     * @param integer $entityTypeId  The entity type ID of the varchar attribute
     * @param integer $storeId       The store ID of the varchar attribute
     * @param string  $value         The value of the varchar attribute
     *
     * @return array|null The varchar attribute
     */
    public function findOneByAttributeCodeAndEntityTypeIdAndStoreIdAndValue($attributeCode, $entityTypeId, $storeId, $value)
    {

        // prepare the params
        $params = array(
            ParamNames::ATTRIBUTE_CODE => $attributeCode,
            ParamNames::ENTITY_TYPE_ID => $entityTypeId,
            ParamNames::STORE_ID       => $storeId,
            ParamNames::VALUE          => $value
        );

        // prepare the cache key
        $cacheKey = $this->cacheAdapter->cacheKey(array(SqlStatementKeys::CATEGORY_VARCHAR_BY_ATTRIBUTE_CODE_AND_ENTITY_TYPE_ID_AND_STORE_ID_AND_VALUE => $params));

        // query whether or not the item has already been cached
        if ($this->cacheAdapter->isCached($cacheKey)) {
            return $this->cacheAdapter->fromCache($cacheKey);
        }

        // load and return the product varchar attribute with the passed parameters
        $this->categoryVarcharByAttributeCodeAndEntityTypeIdAndStoreIdAndValueStmt->execute($params);

        // query whether or not the category varchar value is available in the database
        if ($categoryVarchar = $this->categoryVarcharByAttributeCodeAndEntityTypeIdAndStoreIdAndValueStmt->fetch(\PDO::FETCH_ASSOC)) {
            // prepare the unique cache key for the EAV attribute option value
            $uniqueKey = array(CacheKeys::CATEGORY_VARCHAR => $categoryVarchar[$this->getPrimaryKeyName()]);
            // add the EAV attribute option value to the cache, register the cache key reference as well
            $this->cacheAdapter->toCache($uniqueKey, $categoryVarchar, array($cacheKey => $uniqueKey));
        }

        // finally, return it
        return $categoryVarchar;
    }

    /**
     * Load's and return's the varchar attribute with the passed params.
     *
     * @param integer $attributeCode The attribute code of the varchar attribute
     * @param integer $entityTypeId  The entity type ID of the varchar attribute
     * @param integer $storeId       The store ID of the varchar attribute
     * @param string  $pk            The primary key of the category
     *
     * @return array|null The varchar attribute
     */
    public function findOneByAttributeCodeAndEntityTypeIdAndStoreIdAndPk($attributeCode, $entityTypeId, $storeId, $pk)
    {

        // prepare the params
        $params = array(
            ParamNames::ATTRIBUTE_CODE => $attributeCode,
            ParamNames::ENTITY_TYPE_ID => $entityTypeId,
            ParamNames::STORE_ID       => $storeId,
            ParamNames::PK             => $pk
        );

        // prepare the cache key
        $cacheKey = $this->cacheAdapter->cacheKey(array(SqlStatementKeys::CATEGORY_VARCHAR_BY_ATTRIBUTE_CODE_AND_ENTITY_TYPE_ID_AND_STORE_ID_AND_PK => $params));

        // query whether or not the item has already been cached
        if ($this->cacheAdapter->isCached($cacheKey)) {
            return $this->cacheAdapter->fromCache($cacheKey);
        }

        // load and return the product varchar attribute with the passed parameters
        $this->categoryVarcharByAttributeCodeAndEntityTypeIdAndStoreIdAndPrimaryKeyStmt->execute($params);

        // query whether or not the category varchar value is available in the database
        if ($categoryVarchar = $this->categoryVarcharByAttributeCodeAndEntityTypeIdAndStoreIdAndPrimaryKeyStmt->fetch(\PDO::FETCH_ASSOC)) {
            // prepare the unique cache key for the EAV attribute option value
            $uniqueKey = array(CacheKeys::CATEGORY_VARCHAR => $categoryVarchar[$this->getPrimaryKeyName()]);
            // add the EAV attribute option value to the cache, register the cache key reference as well
            $this->cacheAdapter->toCache($uniqueKey, $categoryVarchar, array($cacheKey => $uniqueKey));
        }

        // finally, return it
        return $categoryVarchar;
    }
}
