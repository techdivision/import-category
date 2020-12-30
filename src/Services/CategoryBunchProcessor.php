<?php

/**
 * TechDivision\Import\Category\Services\CategoryBunchProcessor
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

namespace TechDivision\Import\Category\Services;

use TechDivision\Import\Loaders\LoaderInterface;
use TechDivision\Import\Actions\ActionInterface;
use TechDivision\Import\Connection\ConnectionInterface;
use TechDivision\Import\Repositories\UrlRewriteRepositoryInterface;
use TechDivision\Import\Repositories\EavEntityTypeRepositoryInterface;
use TechDivision\Import\Repositories\EavAttributeRepositoryInterface;
use TechDivision\Import\Repositories\EavAttributeOptionValueRepositoryInterface;
use TechDivision\Import\Category\Assembler\CategoryAssemblerInterface;
use TechDivision\Import\Category\Repositories\CategoryRepositoryInterface;
use TechDivision\Import\Category\Repositories\CategoryIntRepositoryInterface;
use TechDivision\Import\Category\Repositories\CategoryTextRepositoryInterface;
use TechDivision\Import\Category\Assembler\CategoryAttributeAssemblerInterface;
use TechDivision\Import\Category\Repositories\CategoryVarcharRepositoryInterface;
use TechDivision\Import\Category\Repositories\CategoryDecimalRepositoryInterface;
use TechDivision\Import\Category\Repositories\CategoryDatetimeRepositoryInterface;

/**
 * The category bunch processor implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */
class CategoryBunchProcessor implements CategoryBunchProcessorInterface
{

    /**
     * A PDO connection initialized with the values from the Doctrine EntityManager.
     *
     * @var \TechDivision\Import\Connection\ConnectionInterface
     */
    protected $connection;

    /**
     * The category assembler instance.
     *
     * @var \TechDivision\Import\Category\Assembler\CategoryAssemblerInterface
     */
    protected $categoryAssembler;

    /**
     * The repository to access EAV attributes.
     *
     * @var \TechDivision\Import\Repositories\EavAttributeRepositoryInterface
     */
    protected $eavAttributeRepository;

    /**
     * The repository to access EAV attributes.
     *
     * @var \TechDivision\Import\Repositories\EavEntityTypeRepositoryInterface
     */
    protected $eavEntityTypeRepository;

    /**
     * The repository to access EAV attribute option values.
     *
     * @var \TechDivision\Import\Repositories\EavAttributeOptionValueRepositoryInterface
     */
    protected $eavAttributeOptionValueRepository;

    /**
     * The action for category CRUD methods.
     *
     * @var \TechDivision\Import\Actions\ActionInterface
     */
    protected $categoryAction;

    /**
     * The action for category varchar attribute CRUD methods.
     *
     * @var \TechDivision\Import\Actions\ActionInterface
     */
    protected $categoryVarcharAction;

    /**
     * The action for category text attribute CRUD methods.
     *
     * @var \TechDivision\Import\Actions\ActionInterface
     */
    protected $categoryTextAction;

    /**
     * The action for category int attribute CRUD methods.
     *
     * @var \TechDivision\Import\Actions\ActionInterface
     */
    protected $categoryIntAction;

    /**
     * The action for category decimal attribute CRUD methods.
     *
     * @var \TechDivision\Import\Actions\ActionInterface
     */
    protected $categoryDecimalAction;

    /**
     * The action for category datetime attribute CRUD methods.
     *
     * @var \TechDivision\Import\Actions\ActionInterface
     */
    protected $categoryDatetimeAction;

    /**
     * The action for URL rewrite CRUD methods.
     *
     * @var \TechDivision\Import\Actions\ActionInterface
     */
    protected $urlRewriteAction;

    /**
     * The repository to load the categories with.
     *
     * @var \TechDivision\Import\Category\Repositories\CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * The repository to load the category datetime attribute with.
     *
     * @var \TechDivision\Import\Category\Repositories\CategoryDatetimeRepositoryInterface
     */
    protected $categoryDatetimeRepository;

    /**
     * The repository to load the category decimal attribute with.
     *
     * @var \TechDivision\Import\Category\Repositories\CategoryDecimalRepositoryInterface
     */
    protected $categoryDecimalRepository;

    /**
     * The repository to load the category integer attribute with.
     *
     * @var \TechDivision\Import\Category\Repositories\CategoryIntRepositoryInterface
     */
    protected $categoryIntRepository;

    /**
     * The repository to load the product text attribute with.
     *
     * @var \TechDivision\Import\Category\Repositories\CategoryTextRepositoryInterface
     */
    protected $categoryTextRepository;

    /**
     * The repository to load the category varchar attribute with.
     *
     * @var \TechDivision\Import\Category\Repositories\CategoryVarcharRepositoryInterface
     */
    protected $categoryVarcharRepository;

    /**
     * The repository to load the URL rewrites with.
     *
     * @var \TechDivision\Import\Repositories\UrlRewriteRepositoryInterface
     */
    protected $urlRewriteRepository;

    /**
     * The assembler to load the category attributes with.
     *
     * @var \TechDivision\Import\Category\Assembler\CategoryAttributeAssemblerInterface
     */
    protected $categoryAttributeAssembler;

    /**
     * The raw entity loader instance.
     *
     * @var \TechDivision\Import\Loaders\LoaderInterface
     */
    protected $rawEntityLoader;

    /**
     * Initialize the processor with the necessary assembler and repository instances.
     *
     * @param \TechDivision\Import\Connection\ConnectionInterface                            $connection                        The connection to use
     * @param \TechDivision\Import\Category\Repositories\CategoryRepositoryInterface         $categoryRepository                The category repository to use
     * @param \TechDivision\Import\Category\Repositories\CategoryDatetimeRepositoryInterface $categoryDatetimeRepository        The category datetime repository to use
     * @param \TechDivision\Import\Category\Repositories\CategoryDecimalRepositoryInterface  $categoryDecimalRepository         The category decimal repository to use
     * @param \TechDivision\Import\Category\Repositories\CategoryIntRepositoryInterface      $categoryIntRepository             The category integer repository to use
     * @param \TechDivision\Import\Category\Repositories\CategoryTextRepositoryInterface     $categoryTextRepository            The category text repository to use
     * @param \TechDivision\Import\Category\Repositories\CategoryVarcharRepositoryInterface  $categoryVarcharRepository         The category varchar repository to use
     * @param \TechDivision\Import\Repositories\EavAttributeOptionValueRepositoryInterface   $eavAttributeOptionValueRepository The EAV attribute option value repository to use
     * @param \TechDivision\Import\Repositories\EavAttributeRepositoryInterface              $eavAttributeRepository            The EAV attribute repository to use
     * @param \TechDivision\Import\Repositories\UrlRewriteRepositoryInterface                $urlRewriteRepository              The URL rewrite repository to use
     * @param \TechDivision\Import\Repositories\EavEntityTypeRepositoryInterface             $eavEntityTypeRepository           The EAV entity type repository to use
     * @param \TechDivision\Import\Actions\ActionInterface                                   $categoryDatetimeAction            The category datetime action to use
     * @param \TechDivision\Import\Actions\ActionInterface                                   $categoryDecimalAction             The category decimal action to use
     * @param \TechDivision\Import\Actions\ActionInterface                                   $categoryIntAction                 The category integer action to use
     * @param \TechDivision\Import\Actions\ActionInterface                                   $categoryAction                    The category action to use
     * @param \TechDivision\Import\Actions\ActionInterface                                   $categoryTextAction                The category text action to use
     * @param \TechDivision\Import\Actions\ActionInterface                                   $categoryVarcharAction             The category varchar action to use
     * @param \TechDivision\Import\Actions\ActionInterface                                   $urlRewriteAction                  The URL rewrite action to use
     * @param \TechDivision\Import\Category\Assembler\CategoryAssemblerInterface             $categoryAssembler                 The category assembler to use
     * @param \TechDivision\Import\Category\Assembler\CategoryAttributeAssemblerInterface    $categoryAttributeAssembler        The assembler to load the category attributes with
     * @param \TechDivision\Import\Loaders\LoaderInterface                                   $rawEntityLoader                   The raw entity loader instance
     */
    public function __construct(
        ConnectionInterface $connection,
        CategoryRepositoryInterface $categoryRepository,
        CategoryDatetimeRepositoryInterface $categoryDatetimeRepository,
        CategoryDecimalRepositoryInterface $categoryDecimalRepository,
        CategoryIntRepositoryInterface $categoryIntRepository,
        CategoryTextRepositoryInterface $categoryTextRepository,
        CategoryVarcharRepositoryInterface $categoryVarcharRepository,
        EavAttributeOptionValueRepositoryInterface $eavAttributeOptionValueRepository,
        EavAttributeRepositoryInterface $eavAttributeRepository,
        UrlRewriteRepositoryInterface $urlRewriteRepository,
        EavEntityTypeRepositoryInterface $eavEntityTypeRepository,
        ActionInterface $categoryDatetimeAction,
        ActionInterface $categoryDecimalAction,
        ActionInterface $categoryIntAction,
        ActionInterface $categoryAction,
        ActionInterface $categoryTextAction,
        ActionInterface $categoryVarcharAction,
        ActionInterface $urlRewriteAction,
        CategoryAssemblerInterface $categoryAssembler,
        CategoryAttributeAssemblerInterface $categoryAttributeAssembler,
        LoaderInterface $rawEntityLoader
    ) {
        $this->setConnection($connection);
        $this->setCategoryRepository($categoryRepository);
        $this->setCategoryDatetimeRepository($categoryDatetimeRepository);
        $this->setCategoryDecimalRepository($categoryDecimalRepository);
        $this->setCategoryIntRepository($categoryIntRepository);
        $this->setCategoryTextRepository($categoryTextRepository);
        $this->setCategoryVarcharRepository($categoryVarcharRepository);
        $this->setEavAttributeOptionValueRepository($eavAttributeOptionValueRepository);
        $this->setEavAttributeRepository($eavAttributeRepository);
        $this->setUrlRewriteRepository($urlRewriteRepository);
        $this->setEavEntityTypeRepository($eavEntityTypeRepository);
        $this->setCategoryDatetimeAction($categoryDatetimeAction);
        $this->setCategoryDecimalAction($categoryDecimalAction);
        $this->setCategoryIntAction($categoryIntAction);
        $this->setCategoryAction($categoryAction);
        $this->setCategoryTextAction($categoryTextAction);
        $this->setCategoryVarcharAction($categoryVarcharAction);
        $this->setUrlRewriteAction($urlRewriteAction);
        $this->setCategoryAssembler($categoryAssembler);
        $this->setCategoryAttributeAssembler($categoryAttributeAssembler);
        $this->setRawEntityLoader($rawEntityLoader);
    }

    /**
     * Set's the raw entity loader instance.
     *
     * @param \TechDivision\Import\Loaders\LoaderInterface $rawEntityLoader The raw entity loader instance to set
     *
     * @return void
     */
    public function setRawEntityLoader(LoaderInterface $rawEntityLoader)
    {
        $this->rawEntityLoader = $rawEntityLoader;
    }

    /**
     * Return's the raw entity loader instance.
     *
     * @return \TechDivision\Import\Loaders\LoaderInterface The raw entity loader instance
     */
    public function getRawEntityLoader()
    {
        return $this->rawEntityLoader;
    }

    /**
     * Set's the passed connection.
     *
     * @param \TechDivision\Import\Connection\ConnectionInterface $connection The connection to set
     *
     * @return void
     */
    public function setConnection(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Return's the connection.
     *
     * @return \TechDivision\Import\Connection\ConnectionInterface The connection instance
     */
    public function getConnection()
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
    public function beginTransaction()
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
    public function commit()
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
    public function rollBack()
    {
        return $this->connection->rollBack();
    }

    /**
     * Set's the repository to access EAV attribute option values.
     *
     * @param \TechDivision\Import\Repositories\EavAttributeOptionValueRepositoryInterface $eavAttributeOptionValueRepository The repository to access EAV attribute option values
     *
     * @return void
     */
    public function setEavAttributeOptionValueRepository(EavAttributeOptionValueRepositoryInterface $eavAttributeOptionValueRepository)
    {
        $this->eavAttributeOptionValueRepository = $eavAttributeOptionValueRepository;
    }

    /**
     * Return's the repository to access EAV attribute option values.
     *
     * @return \TechDivision\Import\Repositories\EavAttributeOptionValueRepositoryInterface The repository instance
     */
    public function getEavAttributeOptionValueRepository()
    {
        return $this->eavAttributeOptionValueRepository;
    }

    /**
     * Set's the repository to access EAV attributes.
     *
     * @param \TechDivision\Import\Repositories\EavAttributeRepositoryInterface $eavAttributeRepository The repository to access EAV attributes
     *
     * @return void
     */
    public function setEavAttributeRepository(EavAttributeRepositoryInterface $eavAttributeRepository)
    {
        $this->eavAttributeRepository = $eavAttributeRepository;
    }

    /**
     * Return's the repository to access EAV attributes.
     *
     * @return \TechDivision\Import\Repositories\EavAttributeRepositoryInterface The repository instance
     */
    public function getEavAttributeRepository()
    {
        return $this->eavAttributeRepository;
    }

    /**
     * Set's the repository to access EAV entity types.
     *
     * @param \TechDivision\Import\Repositories\EavEntityTypeRepositoryInterface $eavEntityTypeRepository The repository to access EAV entity types
     *
     * @return void
     */
    public function setEavEntityTypeRepository(EavEntityTypeRepositoryInterface $eavEntityTypeRepository)
    {
        $this->eavEntityTypeRepository = $eavEntityTypeRepository;
    }

    /**
     * Return's the repository to access EAV entity types.
     *
     * @return \TechDivision\Import\Repositories\EavEntityTypeRepositoryInterface The repository instance
     */
    public function getEavEntityTypeRepository()
    {
        return $this->eavEntityTypeRepository;
    }

    /**
     * Set's the action with the category CRUD methods.
     *
     * @param \TechDivision\Import\Actions\ActionInterface $categoryAction The action with the category CRUD methods
     *
     * @return void
     */
    public function setCategoryAction(ActionInterface $categoryAction)
    {
        $this->categoryAction = $categoryAction;
    }

    /**
     * Return's the action with the category CRUD methods.
     *
     * @return \TechDivision\Import\Actions\ActionInterface The action instance
     */
    public function getCategoryAction()
    {
        return $this->categoryAction;
    }

    /**
     * Set's the action with the category varchar attribute CRUD methods.
     *
     * @param \TechDivision\Import\Actions\ActionInterface $categoryVarcharAction The action with the category varchar attriute CRUD methods
     *
     * @return void
     */
    public function setCategoryVarcharAction(ActionInterface $categoryVarcharAction)
    {
        $this->categoryVarcharAction = $categoryVarcharAction;
    }

    /**
     * Return's the action with the category varchar attribute CRUD methods.
     *
     * @return \TechDivision\Import\Actions\ActionInterface The action instance
     */
    public function getCategoryVarcharAction()
    {
        return $this->categoryVarcharAction;
    }

    /**
     * Set's the action with the category text attribute CRUD methods.
     *
     * @param \TechDivision\Import\Actions\ActionInterface $categoryTextAction The action with the category text attriute CRUD methods
     *
     * @return void
     */
    public function setCategoryTextAction(ActionInterface $categoryTextAction)
    {
        $this->categoryTextAction = $categoryTextAction;
    }

    /**
     * Return's the action with the category text attribute CRUD methods.
     *
     * @return \TechDivision\Import\Actions\ActionInterface The action instance
     */
    public function getCategoryTextAction()
    {
        return $this->categoryTextAction;
    }

    /**
     * Set's the action with the category int attribute CRUD methods.
     *
     * @param \TechDivision\Import\Actions\ActionInterface $categoryIntAction The action with the category int attriute CRUD methods
     *
     * @return void
     */
    public function setCategoryIntAction(ActionInterface $categoryIntAction)
    {
        $this->categoryIntAction = $categoryIntAction;
    }

    /**
     * Return's the action with the category int attribute CRUD methods.
     *
     * @return \TechDivision\Import\Actions\ActionInterface The action instance
     */
    public function getCategoryIntAction()
    {
        return $this->categoryIntAction;
    }

    /**
     * Set's the action with the category decimal attribute CRUD methods.
     *
     * @param \TechDivision\Import\Actions\ActionInterface $categoryDecimalAction The action with the category decimal attriute CRUD methods
     *
     * @return void
     */
    public function setCategoryDecimalAction(ActionInterface $categoryDecimalAction)
    {
        $this->categoryDecimalAction = $categoryDecimalAction;
    }

    /**
     * Return's the action with the category decimal attribute CRUD methods.
     *
     * @return \TechDivision\Import\Actions\ActionInterface The action instance
     */
    public function getCategoryDecimalAction()
    {
        return $this->categoryDecimalAction;
    }

    /**
     * Set's the action with the category datetime attribute CRUD methods.
     *
     * @param \TechDivision\Import\Actions\ActionInterface $categoryDatetimeAction The action with the category datetime attriute CRUD methods
     *
     * @return void
     */
    public function setCategoryDatetimeAction(ActionInterface $categoryDatetimeAction)
    {
        $this->categoryDatetimeAction = $categoryDatetimeAction;
    }

    /**
     * Return's the action with the category datetime attribute CRUD methods.
     *
     * @return \TechDivision\Import\Actions\ActionInterface The action instance
     */
    public function getCategoryDatetimeAction()
    {
        return $this->categoryDatetimeAction;
    }

    /**
     * Set's the action with the URL rewrite CRUD methods.
     *
     * @param \TechDivision\Import\Actions\ActionInterface $urlRewriteAction The action with the URL rewrite CRUD methods
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
     * @return \TechDivision\Import\Actions\ActionInterface The action instance
     */
    public function getUrlRewriteAction()
    {
        return $this->urlRewriteAction;
    }

    /**
     * Set's the category assembler.
     *
     * @param \TechDivision\Import\Category\Assembler\CategoryAssemblerInterface $categoryAssembler The category assembler
     *
     * @return void
     */
    public function setCategoryAssembler(CategoryAssemblerInterface $categoryAssembler)
    {
        $this->categoryAssembler = $categoryAssembler;
    }

    /**
     * Return's the category assembler.
     *
     * @return \TechDivision\Import\Category\Assembler\CategoryAssemblerInterface The category assembler instance
     */
    public function getCategoryAssembler()
    {
        return $this->categoryAssembler;
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
    public function getCategoryRepository()
    {
        return $this->categoryRepository;
    }

    /**
     * Set's the repository to load the category datetime attribute with.
     *
     * @param \TechDivision\Import\Category\Repositories\CategoryDatetimeRepositoryInterface $categoryDatetimeRepository The repository instance
     *
     * @return void
     */
    public function setCategoryDatetimeRepository(CategoryDatetimeRepositoryInterface $categoryDatetimeRepository)
    {
        $this->categoryDatetimeRepository = $categoryDatetimeRepository;
    }

    /**
     * Return's the repository to load the category datetime attribute with.
     *
     * @return \TechDivision\Import\Category\Repositories\CategoryDatetimeRepositoryInterface The repository instance
     */
    public function getCategoryDatetimeRepository()
    {
        return $this->categoryDatetimeRepository;
    }

    /**
     * Set's the repository to load the category decimal attribute with.
     *
     * @param \TechDivision\Import\Category\Repositories\CategoryDecimalRepositoryInterface $categoryDecimalRepository The repository instance
     *
     * @return void
     */
    public function setCategoryDecimalRepository(CategoryDecimalRepositoryInterface $categoryDecimalRepository)
    {
        $this->categoryDecimalRepository = $categoryDecimalRepository;
    }

    /**
     * Return's the repository to load the category decimal attribute with.
     *
     * @return \TechDivision\Import\Category\Repositories\CategoryDecimalRepositoryInterface The repository instance
     */
    public function getCategoryDecimalRepository()
    {
        return $this->categoryDecimalRepository;
    }

    /**
     * Set's the repository to load the category integer attribute with.
     *
     * @param \TechDivision\Import\Category\Repositories\CategoryIntRepositoryInterface $categoryIntRepository The repository instance
     *
     * @return void
     */
    public function setCategoryIntRepository(CategoryIntRepositoryInterface $categoryIntRepository)
    {
        $this->categoryIntRepository = $categoryIntRepository;
    }

    /**
     * Return's the repository to load the category integer attribute with.
     *
     * @return \TechDivision\Import\Category\Repositories\CategoryIntRepositoryInterface The repository instance
     */
    public function getCategoryIntRepository()
    {
        return $this->categoryIntRepository;
    }

    /**
     * Set's the repository to load the category text attribute with.
     *
     * @param \TechDivision\Import\Category\Repositories\CategoryTextRepositoryInterface $categoryTextRepository The repository instance
     *
     * @return void
     */
    public function setCategoryTextRepository(CategoryTextRepositoryInterface $categoryTextRepository)
    {
        $this->categoryTextRepository = $categoryTextRepository;
    }

    /**
     * Return's the repository to load the category text attribute with.
     *
     * @return \TechDivision\Import\Category\Repositories\CategoryTextRepositoryInterface The repository instance
     */
    public function getCategoryTextRepository()
    {
        return $this->categoryTextRepository;
    }

    /**
     * Set's the repository to load the category varchar attribute with.
     *
     * @param \TechDivision\Import\Category\Repositories\CategoryVarcharRepositoryInterface $categoryVarcharRepository The repository instance
     *
     * @return void
     */
    public function setCategoryVarcharRepository(CategoryVarcharRepositoryInterface $categoryVarcharRepository)
    {
        $this->categoryVarcharRepository = $categoryVarcharRepository;
    }

    /**
     * Return's the repository to load the category varchar attribute with.
     *
     * @return \TechDivision\Import\Category\Repositories\CategoryVarcharRepositoryInterface The repository instance
     */
    public function getCategoryVarcharRepository()
    {
        return $this->categoryVarcharRepository;
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
    public function getUrlRewriteRepository()
    {
        return $this->urlRewriteRepository;
    }

    /**
     * Set's the assembler to load the category attributes with.
     *
     * @param \TechDivision\Import\Category\Assembler\CategoryAttributeAssemblerInterface $categoryAttributeAssembler The assembler instance
     *
     * @return void
     */
    public function setCategoryAttributeAssembler(CategoryAttributeAssemblerInterface $categoryAttributeAssembler)
    {
        $this->categoryAttributeAssembler = $categoryAttributeAssembler;
    }

    /**
     * Return's the assembler to load the category attributes with.
     *
     * @return \TechDivision\Import\Category\Assembler\CategoryAttributeAssemblerInterface The assembler instance
     */
    public function getCategoryAttributeAssembler()
    {
        return $this->categoryAttributeAssembler;
    }

    /**
     * Return's an array with the available EAV attributes for the passed is user defined flag.
     *
     * @param integer $isUserDefined The flag itself
     *
     * @return array The array with the EAV attributes matching the passed flag
     */
    public function getEavAttributesByIsUserDefined($isUserDefined = 1)
    {
        return $this->getEavAttributeRepository()->findAllByIsUserDefined($isUserDefined);
    }

    /**
     * Returns the category with the passed primary key and the attribute values for the passed store ID.
     *
     * @param string  $pk      The primary key of the category to return
     * @param integer $storeId The store ID of the category values
     *
     * @return array|null The category data
     */
    public function getCategoryByPkAndStoreId($pk, $storeId)
    {
        return $this->getCategoryAssembler()->getCategoryByPkAndStoreId($pk, $storeId);
    }

    /**
     * Returns an array with the available categories and their
     * resolved path as keys.
     *
     * @return array The array with the categories
     */
    public function getCategoriesWithResolvedPath()
    {
        return $this->getCategoryAssembler()->getCategoriesWithResolvedPath();
    }

    /**
     * Return's an array with all available categories.
     *
     * @return array The available categories
     */
    public function getCategories()
    {
        return $this->getCategoryRepository()->findAll();
    }

    /**
     * Return's an array with the root categories with the store code as key.
     *
     * @return array The root categories
     */
    public function getRootCategories()
    {
        return $this->getCategoryRepository()->findAllRootCategories();
    }

    /**
     * Returns the category varchar values for the categories with
     * the passed with the passed entity IDs.
     *
     * @param array $entityIds The array with the category IDs
     *
     * @return mixed The category varchar values
     */
    public function getCategoryVarcharsByEntityIds(array $entityIds)
    {
        return $this->getCategoryVarcharRepository()->findAllByEntityIds($entityIds);
    }

    /**
     * Return's the URL rewrites for the passed URL entity type and ID.
     *
     * @param string  $entityType The entity type to load the URL rewrites for
     * @param integer $entityId   The entity ID to laod the rewrites for
     *
     * @return array The URL rewrites
     */
    public function getUrlRewritesByEntityTypeAndEntityId($entityType, $entityId)
    {
        return $this->getUrlRewriteRepository()->findAllByEntityTypeAndEntityId($entityType, $entityId);
    }

    /**
     * Return's the URL rewrites for the passed URL entity type and ID.
     *
     * @param string  $entityType The entity type to load the URL rewrites for
     * @param integer $entityId   The entity ID to load the URL rewrites for
     * @param integer $storeId    The store ID to load the URL rewrites for
     *
     * @return array The URL rewrites
     */
    public function getUrlRewritesByEntityTypeAndEntityIdAndStoreId($entityType, $entityId, $storeId)
    {
        return $this->getUrlRewriteRepository()->findAllByEntityTypeAndEntityIdAndStoreId($entityType, $entityId, $storeId);
    }

    /**
     * Intializes the existing attributes for the entity with the passed primary key.
     *
     * @param string  $pk      The primary key of the entity to load the attributes for
     * @param integer $storeId The ID of the store view to load the attributes for
     *
     * @return array The entity attributes
     */
    public function getCategoryAttributesByPrimaryKeyAndStoreId($pk, $storeId)
    {
        return $this->getCategoryAttributeAssembler()->getCategoryAttributesByPrimaryKeyAndStoreId($pk, $storeId);
    }

    /**
     * Load's and return's a raw entity without primary key but the mandatory members only and nulled values.
     *
     * @param string $entityTypeCode The entity type code to return the raw entity for
     * @param array  $data           An array with data that will be used to initialize the raw entity with
     *
     * @return array The initialized entity
     */
    public function loadRawEntity($entityTypeCode, array $data = array())
    {
        return $this->getRawEntityLoader()->load($entityTypeCode, $data);
    }

    /**
     * Return's the children count of the category with the passed path.
     *
     * @param string $path The path of the category to count the children for
     *
     * @return integer The children count of the category with the passed path
     */
    public function loadCategoryChildrenChildrenCount($path)
    {
        return $this->getCategoryRepository()->countChildren($path);
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
     * Load's and return's the datetime attribute with the passed entity/attribute/store ID.
     *
     * @param integer $entityId    The entity ID of the attribute
     * @param integer $attributeId The attribute ID of the attribute
     * @param integer $storeId     The store ID of the attribute
     *
     * @return array|null The datetime attribute
     */
    public function loadCategoryDatetimeAttribute($entityId, $attributeId, $storeId)
    {
        return  $this->getCategoryDatetimeRepository()->findOneByEntityIdAndAttributeIdAndStoreId($entityId, $attributeId, $storeId);
    }

    /**
     * Load's and return's the decimal attribute with the passed entity/attribute/store ID.
     *
     * @param integer $entityId    The entity ID of the attribute
     * @param integer $attributeId The attribute ID of the attribute
     * @param integer $storeId     The store ID of the attribute
     *
     * @return array|null The decimal attribute
     */
    public function loadCategoryDecimalAttribute($entityId, $attributeId, $storeId)
    {
        return  $this->getCategoryDecimalRepository()->findOneByEntityIdAndAttributeIdAndStoreId($entityId, $attributeId, $storeId);
    }

    /**
     * Load's and return's the integer attribute with the passed entity/attribute/store ID.
     *
     * @param integer $entityId    The entity ID of the attribute
     * @param integer $attributeId The attribute ID of the attribute
     * @param integer $storeId     The store ID of the attribute
     *
     * @return array|null The integer attribute
     */
    public function loadCategoryIntAttribute($entityId, $attributeId, $storeId)
    {
        return $this->getCategoryIntRepository()->findOneByEntityIdAndAttributeIdAndStoreId($entityId, $attributeId, $storeId);
    }

    /**
     * Load's and return's the text attribute with the passed entity/attribute/store ID.
     *
     * @param integer $entityId    The entity ID of the attribute
     * @param integer $attributeId The attribute ID of the attribute
     * @param integer $storeId     The store ID of the attribute
     *
     * @return array|null The text attribute
     */
    public function loadCategoryTextAttribute($entityId, $attributeId, $storeId)
    {
        return $this->getCategoryTextRepository()->findOneByEntityIdAndAttributeIdAndStoreId($entityId, $attributeId, $storeId);
    }

    /**
     * Load's and return's the varchar attribute with the passed entity/attribute/store ID.
     *
     * @param integer $entityId    The entity ID of the attribute
     * @param integer $attributeId The attribute ID of the attribute
     * @param integer $storeId     The store ID of the attribute
     *
     * @return array|null The varchar attribute
     */
    public function loadCategoryVarcharAttribute($entityId, $attributeId, $storeId)
    {
        return $this->getCategoryVarcharRepository()->findOneByEntityIdAndAttributeIdAndStoreId($entityId, $attributeId, $storeId);
    }

    /**
     * Load's and return's the EAV attribute option value with the passed entity type ID, code, store ID and value.
     *
     * @param string  $entityTypeId  The entity type ID of the EAV attribute to load the option value for
     * @param string  $attributeCode The code of the EAV attribute option to load
     * @param integer $storeId       The store ID of the attribute option to load
     * @param string  $value         The value of the attribute option to load
     *
     * @return array The EAV attribute option value
     */
    public function loadAttributeOptionValueByEntityTypeIdAndAttributeCodeAndStoreIdAndValue($entityTypeId, $attributeCode, $storeId, $value)
    {
        return $this->getEavAttributeOptionValueRepository()->findOneByEntityTypeIdAndAttributeCodeAndStoreIdAndValue($entityTypeId, $attributeCode, $storeId, $value);
    }

    /**
     * Return's an EAV entity type with the passed entity type code.
     *
     * @param string $entityTypeCode The code of the entity type to return
     *
     * @return array The entity type with the passed entity type code
     */
    public function loadEavEntityTypeByEntityTypeCode($entityTypeCode)
    {
        return $this->getEavEntityTypeRepository()->findOneByEntityTypeCode($entityTypeCode);
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
    public function loadVarcharAttributeByAttributeCodeAndEntityTypeIdAndStoreIdAndValue($attributeCode, $entityTypeId, $storeId, $value)
    {
        return $this->loadCategoryVarcharAttributeByAttributeCodeAndEntityTypeIdAndStoreIdAndValue($attributeCode, $entityTypeId, $storeId, $value);
    }

    /**
     * Load's and return's the varchar attribute with the passed params.
     *
     * @param integer $attributeCode The attribute code of the varchar attribute
     * @param integer $entityTypeId  The entity type ID of the varchar attribute
     * @param integer $storeId       The store ID of the varchar attribute
     * @param string  $primaryKey    The primary key ID of the category
     *
     * @return array|null The varchar attribute
     */
    public function loadVarcharAttributeByAttributeCodeAndEntityTypeIdAndStoreIdAndPrimaryKey($attributeCode, $entityTypeId, $storeId, $primaryKey)
    {
        return $this->loadCategoryVarcharAttributeByAttributeCodeAndEntityTypeIdAndStoreIdAndPK($attributeCode, $entityTypeId, $storeId, $primaryKey);
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
    public function loadCategoryVarcharAttributeByAttributeCodeAndEntityTypeIdAndStoreIdAndValue($attributeCode, $entityTypeId, $storeId, $value)
    {
        return $this->getCategoryVarcharRepository()->findOneByAttributeCodeAndEntityTypeIdAndStoreIdAndValue($attributeCode, $entityTypeId, $storeId, $value);
    }

    /**
     * Load's and return's the varchar attribute with the passed params.
     *
     * @param integer $attributeCode The attribute code of the varchar attribute
     * @param integer $entityTypeId  The entity type ID of the varchar attribute
     * @param integer $storeId       The store ID of the varchar attribute
     * @param string  $pk            The primary key ID of the category
     *
     * @return array|null The url_key from category
     */
    public function loadCategoryVarcharAttributeByAttributeCodeAndEntityTypeIdAndStoreIdAndPk($attributeCode, $entityTypeId, $storeId, $pk)
    {
        return $this->getCategoryVarcharRepository()->findOneByAttributeCodeAndEntityTypeIdAndStoreIdAndPk($attributeCode, $entityTypeId, $storeId, $pk);
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
     * Persist's the passed category data and return's the ID.
     *
     * @param array       $category The category data to persist
     * @param string|null $name     The name of the prepared statement that has to be executed
     *
     * @return string The ID of the persisted entity
     */
    public function persistCategory($category, $name = null)
    {
        return $this->getCategoryAction()->persist($category, $name);
    }

    /**
     * Persist's the passed category varchar attribute.
     *
     * @param array       $attribute The attribute to persist
     * @param string|null $name      The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function persistCategoryVarcharAttribute($attribute, $name = null)
    {
        $this->getCategoryVarcharAction()->persist($attribute, $name);
    }

    /**
     * Persist's the passed category integer attribute.
     *
     * @param array       $attribute The attribute to persist
     * @param string|null $name      The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function persistCategoryIntAttribute($attribute, $name = null)
    {
        $this->getCategoryIntAction()->persist($attribute, $name);
    }

    /**
     * Persist's the passed category decimal attribute.
     *
     * @param array       $attribute The attribute to persist
     * @param string|null $name      The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function persistCategoryDecimalAttribute($attribute, $name = null)
    {
        $this->getCategoryDecimalAction()->persist($attribute, $name);
    }

    /**
     * Persist's the passed product datetime attribute.
     *
     * @param array       $attribute The attribute to persist
     * @param string|null $name      The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function persistCategoryDatetimeAttribute($attribute, $name = null)
    {
        $this->getCategoryDatetimeAction()->persist($attribute, $name);
    }

    /**
     * Persist's the passed category text attribute.
     *
     * @param array       $attribute The attribute to persist
     * @param string|null $name      The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function persistCategoryTextAttribute($attribute, $name = null)
    {
        $this->getCategoryTextAction()->persist($attribute, $name);
    }

    /**
     * Persist's the URL rewrite with the passed data.
     *
     * @param array       $row  The URL rewrite to persist
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return string The ID of the persisted entity
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

    /**
     * Delete's the entity with the passed attributes.
     *
     * @param array       $row  The attributes of the entity to delete
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function deleteCategory($row, $name = null)
    {
        $this->getCategoryAction()->delete($row, $name);
    }

    /**
     * Delete's the category datetime attribute with the passed attributes.
     *
     * @param array       $row  The attributes of the entity to delete
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function deleteCategoryDatetimeAttribute($row, $name = null)
    {
        $this->getCategoryDatetimeAction()->delete($row, $name);
    }

    /**
     * Delete's the category decimal attribute with the passed attributes.
     *
     * @param array       $row  The attributes of the entity to delete
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function deleteCategoryDecimalAttribute($row, $name = null)
    {
        $this->getCategoryDecimalAction()->delete($row, $name);
    }

    /**
     * Delete's the category integer attribute with the passed attributes.
     *
     * @param array       $row  The attributes of the entity to delete
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function deleteCategoryIntAttribute($row, $name = null)
    {
        $this->getCategoryIntAction()->delete($row, $name);
    }

    /**
     * Delete's the category text attribute with the passed attributes.
     *
     * @param array       $row  The attributes of the entity to delete
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function deleteCategoryTextAttribute($row, $name = null)
    {
        $this->getCategoryTextAction()->delete($row, $name);
    }

    /**
     * Delete's the category varchar attribute with the passed attributes.
     *
     * @param array       $row  The attributes of the entity to delete
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function deleteCategoryVarcharAttribute($row, $name = null)
    {
        $this->getCategoryVarcharAction()->delete($row, $name);
    }
}
