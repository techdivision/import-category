<?php

/**
 * TechDivision\Import\Category\Services\CategoryUrlRewriteProcessor
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
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Category\Services;

use TechDivision\Import\Dbal\Actions\ActionInterface;
use TechDivision\Import\Dbal\Connection\ConnectionInterface;
use TechDivision\Import\Repositories\UrlRewriteRepositoryInterface;
use TechDivision\Import\Category\Repositories\CategoryRepositoryInterface;

/**
 * The category URL rewrite processor implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */
class CategoryUrlRewriteProcessor implements CategoryUrlRewriteProcessorInterface
{

    /**
     * A PDO connection initialized with the values from the Doctrine EntityManager.
     *
     * @var \TechDivision\Import\Dbal\Connection\ConnectionInterface
     */
    protected $connection;

    /**
     * The action for URL rewrite CRUD methods.
     *
     * @var \TechDivision\Import\Dbal\Actions\ActionInterface
     */
    protected $urlRewriteAction;

    /**
     * The repository to load the categories with.
     *
     * @var \TechDivision\Import\Category\Repositories\CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * The repository to load the URL rewrites with.
     *
     * @var \TechDivision\Import\Repositories\UrlRewriteRepositoryInterface
     */
    protected $urlRewriteRepository;

    /**
     * Initialize the processor with the necessary assembler and repository instances.
     *
     * @param \TechDivision\Import\Dbal\Connection\ConnectionInterface               $connection           The connection to use
     * @param \TechDivision\Import\Category\Repositories\CategoryRepositoryInterface $categoryRepository   The category repository to use
     * @param \TechDivision\Import\Repositories\UrlRewriteRepositoryInterface        $urlRewriteRepository The URL rewrite repository to use
     * @param \TechDivision\Import\Dbal\Actions\ActionInterface                      $urlRewriteAction     The URL rewrite action to use
     */
    public function __construct(
        ConnectionInterface $connection,
        CategoryRepositoryInterface $categoryRepository,
        UrlRewriteRepositoryInterface $urlRewriteRepository,
        ActionInterface $urlRewriteAction
    ) {
        $this->setConnection($connection);
        $this->setCategoryRepository($categoryRepository);
        $this->setUrlRewriteRepository($urlRewriteRepository);
        $this->setUrlRewriteAction($urlRewriteAction);
    }

    /**
     * Set's the passed connection.
     *
     * @param \TechDivision\Import\Dbal\Connection\ConnectionInterface $connection The connection to set
     *
     * @return void
     */
    public function setConnection(ConnectionInterface $connection) : void
    {
        $this->connection = $connection;
    }

    /**
     * Return's the connection.
     *
     * @return \TechDivision\Import\Dbal\Connection\ConnectionInterface The connection instance
     */
    public function getConnection() : ConnectionInterface
    {
        return $this->connection;
    }

    /**
     * Turns off autocommit mode. While autocommit mode is turned off, changes made to the database via the PDO
     * object instance are not committed until you end the transaction by calling ProductProcessor::commit().
     * Calling ProductProcessor::rollBack() will roll back all changes to the database and return the connection
     * to autocommit mode.
     *
     * @return boolean Returns TRUE on success or FALSE on failure
     * @link http://php.net/manual/en/pdo.begintransaction.php
     */
    public function beginTransaction() : bool
    {
        return $this->connection->beginTransaction();
    }

    /**
     * Commits a transaction, returning the database connection to autocommit mode until the next call to
     * ProductProcessor::beginTransaction() starts a new transaction.
     *
     * @return boolean Returns TRUE on success or FALSE on failure
     * @link http://php.net/manual/en/pdo.commit.php
     */
    public function commit() : bool
    {
        return $this->connection->commit();
    }

    /**
     * Rolls back the current transaction, as initiated by ProductProcessor::beginTransaction().
     *
     * If the database was set to autocommit mode, this function will restore autocommit mode after it has
     * rolled back the transaction.
     *
     * Some databases, including MySQL, automatically issue an implicit COMMIT when a database definition
     * language (DDL) statement such as DROP TABLE or CREATE TABLE is issued within a transaction. The implicit
     * COMMIT will prevent you from rolling back any other changes within the transaction boundary.
     *
     * @return boolean Returns TRUE on success or FALSE on failure
     * @link http://php.net/manual/en/pdo.rollback.php
     */
    public function rollBack() : bool
    {
        return $this->connection->rollBack();
    }

    /**
     * Set's the repository to load the categories with.
     *
     * @param \TechDivision\Import\Category\Repositories\CategoryRepositoryInterface $categoryRepository The repository instance
     *
     * @return void
     */
    public function setCategoryRepository(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Return's the repository to load the categories with.
     *
     * @return \TechDivision\Import\Category\Repositories\CategoryRepositoryInterface The repository instance
     */
    public function getCategoryRepository() : CategoryRepositoryInterface
    {
        return $this->categoryRepository;
    }

    /**
     * Set's the action with the URL rewrite CRUD methods.
     *
     * @param \TechDivision\Import\Dbal\Actions\ActionInterface $urlRewriteAction The action with the URL rewrite CRUD methods
     *
     * @return void
     */
    public function setUrlRewriteAction(ActionInterface $urlRewriteAction)
    {
        $this->urlRewriteAction = $urlRewriteAction;
    }

    /**
     * Return's the action with the URL rewrite CRUD methods.
     *
     * @return \TechDivision\Import\Dbal\Actions\ActionInterface The action instance
     */
    public function getUrlRewriteAction() : ActionInterface
    {
        return $this->urlRewriteAction;
    }

    /**
     * Set's the repository to load the URL rewrites with.
     *
     * @param \TechDivision\Import\Repositories\UrlRewriteRepositoryInterface $urlRewriteRepository The repository instance
     *
     * @return void
     */
    public function setUrlRewriteRepository(UrlRewriteRepositoryInterface $urlRewriteRepository)
    {
        $this->urlRewriteRepository = $urlRewriteRepository;
    }

    /**
     * Return's the repository to load the URL rewrites with.
     *
     * @return \TechDivision\Import\Repositories\UrlRewriteRepositoryInterface The repository instance
     */
    public function getUrlRewriteRepository() : UrlRewriteRepositoryInterface
    {
        return $this->urlRewriteRepository;
    }
    /**
     * Return's the URL rewrites for the passed URL entity type and ID.
     *
     * @param string  $entityType The entity type to load the URL rewrites for
     * @param integer $entityId   The entity ID to load the URL rewrites for
     * @param integer $storeId    The store ID to load the URL rewrites for
     *
     * @return \Generator The URL rewrites
     */
    public function loadUrlRewritesByEntityTypeAndEntityIdAndStoreId($entityType, $entityId, $storeId) : \Generator
    {
        return $this->getUrlRewriteRepository()->findAllByEntityTypeAndEntityIdAndStoreId($entityType, $entityId, $storeId);
    }

    /**
     * Return's the category with the passed ID.
     *
     * @param string $id The ID of the category to return
     *
     * @return array The category
     */
    public function loadCategory($id)
    {
        return $this->getCategoryRepository()->load($id);
    }

    /**
     * Return's the category with the passed ID.
     *
     * @param string $path The ID of the category to return
     *
     * @return array The category
     */
    public function loadCategoriesByPath($path)
    {
        return $this->getCategoryRepository()->findAllByPath($path);
    }

    /**
     * Load's and return's the URL rewrite for the given request path and store ID
     *
     * @param string $requestPath The request path to load the URL rewrite for
     * @param int    $storeId     The store ID to load the URL rewrite for
     *
     * @return array|null The URL rewrite found for the given request path and store ID
     */
    public function loadUrlRewriteByRequestPathAndStoreId(string $requestPath, int $storeId)
    {
        return $this->getUrlRewriteRepository()->findOneByRequestPathAndStoreId($requestPath, $storeId);
    }

    /**
     * Persist's the URL rewrite with the passed data.
     *
     * @param array       $row  The URL rewrite to persist
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return int The ID of the persisted entity
     */
    public function persistUrlRewrite($row, $name = null)
    {
        return $this->getUrlRewriteAction()->persist($row, $name);
    }

    /**
     * Delete's the URL rewrite with the passed attributes.
     *
     * @param array       $row  The attributes of the entity to delete
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function deleteUrlRewrite($row, $name = null)
    {
        $this->getUrlRewriteAction()->delete($row, $name);
    }
}
