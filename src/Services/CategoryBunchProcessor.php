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
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Category\Services;

use TechDivision\Import\Assembler\CategoryAssembler;
use TechDivision\Import\Actions\UrlRewriteAction;
use TechDivision\Import\Category\Actions\CategoryVarcharAction;
use TechDivision\Import\Category\Actions\CategoryTextAction;
use TechDivision\Import\Category\Actions\CategoryIntAction;
use TechDivision\Import\Category\Actions\CategoryDecimalAction;
use TechDivision\Import\Repositories\UrlRewriteRepository;
use TechDivision\Import\Repositories\EavAttributeRepository;
use TechDivision\Import\Repositories\EavAttributeOptionValueRepository;
use TechDivision\Import\Category\Repositories\CategoryVarcharRepository;
use TechDivision\Import\Category\Repositories\CategoryTextRepository;
use TechDivision\Import\Category\Repositories\CategoryIntRepository;
use TechDivision\Import\Category\Repositories\CategoryDecimalRepository;
use TechDivision\Import\Category\Repositories\CategoryDatetimeRepository;
use TechDivision\Import\Category\Repositories\CategoryRepository;
use TechDivision\Import\Category\Actions\CategoryDatetimeAction;
use TechDivision\Import\Category\Actions\CategoryAction;

/**
 * The category bunch processor implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */
class CategoryBunchProcessor implements CategoryBunchProcessorInterface
{

    /**
     * A PDO connection initialized with the values from the Doctrine EntityManager.
     *
     * @var \PDO
     */
    protected $connection;

    /**
     * The category assembler instance.
     *
     * @var \TechDivision\Import\Assembler\CategoryAssembler
     */
    protected $categoryAssembler;

    /**
     * The repository to access EAV attributes.
     *
     * @var \TechDivision\Import\Repositories\EavAttributeRepository
     */
    protected $eavAttributeRepository;

    /**
     * The repository to access EAV attribute option values.
     *
     * @var \TechDivision\Import\Product\Repositories\EavAttributeOptionValueRepository
     */
    protected $eavAttributeOptionValueRepository;

    /**
     * The action for category CRUD methods.
     *
     * @var \TechDivision\Import\Category\Actions\CategoryAction
     */
    protected $categoryAction;

    /**
     * The action for category varchar attribute CRUD methods.
     *
     * @var \TechDivision\Import\Category\Actions\CategoryVarcharAction
     */
    protected $categoryVarcharAction;

    /**
     * The action for category text attribute CRUD methods.
     *
     * @var \TechDivision\Import\Category\Actions\CategoryTextAction
     */
    protected $categoryTextAction;

    /**
     * The action for category int attribute CRUD methods.
     *
     * @var \TechDivision\Import\Category\Actions\CategoryIntAction
     */
    protected $categoryIntAction;

    /**
     * The action for category decimal attribute CRUD methods.
     *
     * @var \TechDivision\Import\Category\Actions\CategoryDecimalAction
     */
    protected $categoryDecimalAction;

    /**
     * The action for category datetime attribute CRUD methods.
     *
     * @var \TechDivision\Import\Category\Actions\CategoryDatetimeAction
     */
    protected $categoryDatetimeAction;

    /**
     * The action for URL rewrite CRUD methods.
     *
     * @var \TechDivision\Import\Product\Actions\UrlRewriteAction
     */
    protected $urlRewriteAction;

    /**
     * The repository to load the categories with.
     *
     * @var \TechDivision\Import\Category\Repositories\CategoryRepository
     */
    protected $categoryRepository;

    /**
     * The repository to load the category datetime attribute with.
     *
     * @var \TechDivision\Import\Category\Repositories\CategoryDatetimeRepository
     */
    protected $categoryDatetimeRepository;

    /**
     * The repository to load the category decimal attribute with.
     *
     * @var \TechDivision\Import\Category\Repositories\CategoryDecimalRepository
     */
    protected $categoryDecimalRepository;

    /**
     * The repository to load the category integer attribute with.
     *
     * @var \TechDivision\Import\Category\Repositories\CategoryIntRepository
     */
    protected $categoryIntRepository;

    /**
     * The repository to load the product text attribute with.
     *
     * @var \TechDivision\Import\Product\Repositories\ProductTextRepository
     */
    protected $categoryTextRepository;

    /**
     * The repository to load the category varchar attribute with.
     *
     * @var \TechDivision\Import\Category\Repositories\CategoryVarcharRepository
     */
    protected $categoryVarcharRepository;

    /**
     * The repository to load the URL rewrites with.
     *
     * @var \TechDivision\Import\Repositories\UrlRewriteRepository
     */
    protected $urlRewriteRepository;

    /**
     * Initialize the processor with the necessary assembler and repository instances.
     *
     * @param \PDO                                                                        $connection                        The PDO connection to use
     * @param \TechDivision\Import\Category\Repositories\CategoryRepository               $categoryRepository                The category repository to use
     * @param \TechDivision\Import\Category\Repositories\CategoryDatetimeRepository       $categoryDatetimeRepository        The category datetime repository to use
     * @param \TechDivision\Import\Category\Repositories\CategoryDecimalRepository        $categoryDecimalRepository         The category decimal repository to use
     * @param \TechDivision\Import\Category\Repositories\CategoryIntRepository            $categoryIntRepository             The category integer repository to use
     * @param \TechDivision\Import\Category\Repositories\CategoryTextRepository           $categoryTextRepository            The category text repository to use
     * @param \TechDivision\Import\Category\Repositories\CategoryVarcharRepository        $categoryVarcharRepository         The category varchar repository to use
     * @param \TechDivision\Import\Product\Repositories\EavAttributeOptionValueRepository $eavAttributeOptionValueRepository The EAV attribute option value repository to use
     * @param \TechDivision\Import\Repositories\EavAttributeRepository                    $eavAttributeRepository            The EAV attribute repository to use
     * @param \TechDivision\Import\Repositories\UrlRewriteRepository                      $urlRewriteRepository              The URL rewrite repository to use
     * @param \TechDivision\Import\Category\Actions\CategoryDatetimeAction                $categoryDatetimeAction            The category datetime action to use
     * @param \TechDivision\Import\Category\Actions\CategoryDecimalAction                 $categoryDecimalAction             The category decimal action to use
     * @param \TechDivision\Import\Category\Actions\CategoryIntAction                     $categoryIntAction                 The category integer action to use
     * @param \TechDivision\Import\Category\Actions\CategoryAction                        $categoryAction                    The category action to use
     * @param \TechDivision\Import\Category\Actions\CategoryTextAction                    $categoryTextAction                The category text action to use
     * @param \TechDivision\Import\Category\Actions\CategoryVarcharAction                 $categoryVarcharAction             The category varchar action to use
     * @param \TechDivision\Import\Product\Actions\UrlRewriteAction                       $urlRewriteAction                  The URL rewrite action to use
     * @param \TechDivision\Import\Assembler\CategoryAssembler                            $categoryAssembler                 The category assembler to use
     */
    public function __construct(
        \PDO $connection,
        CategoryRepository $categoryRepository,
        CategoryDatetimeRepository $categoryDatetimeRepository,
        CategoryDecimalRepository $categoryDecimalRepository,
        CategoryIntRepository $categoryIntRepository,
        CategoryTextRepository $categoryTextRepository,
        CategoryVarcharRepository $categoryVarcharRepository,
        EavAttributeOptionValueRepository $eavAttributeOptionValueRepository,
        EavAttributeRepository $eavAttributeRepository,
        UrlRewriteRepository $urlRewriteRepository,
        CategoryDatetimeAction $categoryDatetimeAction,
        CategoryDecimalAction $categoryDecimalAction,
        CategoryIntAction $categoryIntAction,
        CategoryAction $categoryAction,
        CategoryTextAction $categoryTextAction,
        CategoryVarcharAction $categoryVarcharAction,
        UrlRewriteAction $urlRewriteAction,
        CategoryAssembler $categoryAssembler
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
        $this->setCategoryDatetimeAction($categoryDatetimeAction);
        $this->setCategoryDecimalAction($categoryDecimalAction);
        $this->setCategoryIntAction($categoryIntAction);
        $this->setCategoryAction($categoryAction);
        $this->setCategoryTextAction($categoryTextAction);
        $this->setCategoryVarcharAction($categoryVarcharAction);
        $this->setUrlRewriteAction($urlRewriteAction);
        $this->setCategoryAssembler($categoryAssembler);
    }

    /**
     * Set's the passed connection.
     *
     * @param \PDO $connection The connection to set
     *
     * @return void
     */
    public function setConnection(\PDO $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Return's the connection.
     *
     * @return \PDO The connection instance
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
     * @param \TechDivision\Import\Product\Repositories\EavAttributeOptionValueRepository $eavAttributeOptionValueRepository The repository to access EAV attribute option values
     *
     * @return void
     */
    public function setEavAttributeOptionValueRepository($eavAttributeOptionValueRepository)
    {
        $this->eavAttributeOptionValueRepository = $eavAttributeOptionValueRepository;
    }

    /**
     * Return's the repository to access EAV attribute option values.
     *
     * @return \TechDivision\Import\Product\Repositories\EavAttributeOptionValueRepository The repository instance
     */
    public function getEavAttributeOptionValueRepository()
    {
        return $this->eavAttributeOptionValueRepository;
    }

    /**
     * Set's the repository to access EAV attributes.
     *
     * @param \TechDivision\Import\Repositories\EavAttributeRepository $eavAttributeRepository The repository to access EAV attributes
     *
     * @return void
     */
    public function setEavAttributeRepository($eavAttributeRepository)
    {
        $this->eavAttributeRepository = $eavAttributeRepository;
    }

    /**
     * Return's the repository to access EAV attributes.
     *
     * @return \TechDivision\Import\Repositories\EavAttributeRepository The repository instance
     */
    public function getEavAttributeRepository()
    {
        return $this->eavAttributeRepository;
    }

    /**
     * Set's the action with the category CRUD methods.
     *
     * @param \TechDivision\Import\Category\Actions\CategoryAction $categoryAction The action with the category CRUD methods
     *
     * @return void
     */
    public function setCategoryAction($categoryAction)
    {
        $this->categoryAction = $categoryAction;
    }

    /**
     * Return's the action with the category CRUD methods.
     *
     * @return \TechDivision\Import\Category\Actions\CategoryAction The action instance
     */
    public function getCategoryAction()
    {
        return $this->categoryAction;
    }

    /**
     * Set's the action with the category varchar attribute CRUD methods.
     *
     * @param \TechDivision\Import\Category\Actions\CategoryVarcharAction $categoryVarcharAction The action with the category varchar attriute CRUD methods
     *
     * @return void
     */
    public function setCategoryVarcharAction($categoryVarcharAction)
    {
        $this->categoryVarcharAction = $categoryVarcharAction;
    }

    /**
     * Return's the action with the category varchar attribute CRUD methods.
     *
     * @return \TechDivision\Import\Category\Actions\CategoryVarcharAction The action instance
     */
    public function getCategoryVarcharAction()
    {
        return $this->categoryVarcharAction;
    }

    /**
     * Set's the action with the category text attribute CRUD methods.
     *
     * @param \TechDivision\Import\Category\Actions\CategoryTextAction $categoryTextAction The action with the category text attriute CRUD methods
     *
     * @return void
     */
    public function setCategoryTextAction($categoryTextAction)
    {
        $this->categoryTextAction = $categoryTextAction;
    }

    /**
     * Return's the action with the category text attribute CRUD methods.
     *
     * @return \TechDivision\Import\Category\Actions\CategoryTextAction The action instance
     */
    public function getCategoryTextAction()
    {
        return $this->categoryTextAction;
    }

    /**
     * Set's the action with the category int attribute CRUD methods.
     *
     * @param \TechDivision\Import\Category\Actions\CategoryIntAction $categoryIntAction The action with the category int attriute CRUD methods
     *
     * @return void
     */
    public function setCategoryIntAction($categoryIntAction)
    {
        $this->categoryIntAction = $categoryIntAction;
    }

    /**
     * Return's the action with the category int attribute CRUD methods.
     *
     * @return \TechDivision\Import\Category\Actions\CategoryIntAction The action instance
     */
    public function getCategoryIntAction()
    {
        return $this->categoryIntAction;
    }

    /**
     * Set's the action with the category decimal attribute CRUD methods.
     *
     * @param \TechDivision\Import\Category\Actions\CategoryDecimalAction $categoryDecimalAction The action with the category decimal attriute CRUD methods
     *
     * @return void
     */
    public function setCategoryDecimalAction($categoryDecimalAction)
    {
        $this->categoryDecimalAction = $categoryDecimalAction;
    }

    /**
     * Return's the action with the category decimal attribute CRUD methods.
     *
     * @return \TechDivision\Import\Category\Actions\CategoryDecimalAction The action instance
     */
    public function getCategoryDecimalAction()
    {
        return $this->categoryDecimalAction;
    }

    /**
     * Set's the action with the category datetime attribute CRUD methods.
     *
     * @param \TechDivision\Import\Category\Actions\CategoryDatetimeAction $categoryDatetimeAction The action with the category datetime attriute CRUD methods
     *
     * @return void
     */
    public function setCategoryDatetimeAction($categoryDatetimeAction)
    {
        $this->categoryDatetimeAction = $categoryDatetimeAction;
    }

    /**
     * Return's the action with the category datetime attribute CRUD methods.
     *
     * @return \TechDivision\Import\Category\Actions\CategoryDatetimeAction The action instance
     */
    public function getCategoryDatetimeAction()
    {
        return $this->categoryDatetimeAction;
    }

    /**
     * Set's the action with the URL rewrite CRUD methods.
     *
     * @param \TechDivision\Import\Product\Actions\UrlRewriteAction $urlRewriteAction The action with the URL rewrite CRUD methods
     *
     * @return void
     */
    public function setUrlRewriteAction($urlRewriteAction)
    {
        $this->urlRewriteAction = $urlRewriteAction;
    }

    /**
     * Return's the action with the URL rewrite CRUD methods.
     *
     * @return \TechDivision\Import\Product\Actions\UrlRewriteAction The action instance
     */
    public function getUrlRewriteAction()
    {
        return $this->urlRewriteAction;
    }

    /**
     * Set's the category assembler.
     *
     * @param \TechDivision\Import\Assembler\CategoryAssembler $categoryAssembler The category assembler
     *
     * @return void
     */
    public function setCategoryAssembler($categoryAssembler)
    {
        $this->categoryAssembler = $categoryAssembler;
    }

    /**
     * Return's the category assembler.
     *
     * @return \TechDivision\Import\Assembler\CategoryAssembler The category assembler instance
     */
    public function getCategoryAssembler()
    {
        return $this->categoryAssembler;
    }

    /**
     * Set's the repository to load the categories with.
     *
     * @param \TechDivision\Import\Category\Repositories\CategoryRepository $categoryRepository The repository instance
     *
     * @return void
     */
    public function setCategoryRepository($categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Return's the repository to load the categories with.
     *
     * @return \TechDivision\Import\Category\Repositories\CategoryRepository The repository instance
     */
    public function getCategoryRepository()
    {
        return $this->categoryRepository;
    }

    /**
     * Set's the repository to load the category datetime attribute with.
     *
     * @param \TechDivision\Import\Category\Repositories\CategoryDatetimeRepository $categoryDatetimeRepository The repository instance
     *
     * @return void
     */
    public function setCategoryDatetimeRepository($categoryDatetimeRepository)
    {
        $this->categoryDatetimeRepository = $categoryDatetimeRepository;
    }

    /**
     * Return's the repository to load the category datetime attribute with.
     *
     * @return \TechDivision\Import\Category\Repositories\CategoryDatetimeRepository The repository instance
     */
    public function getCategoryDatetimeRepository()
    {
        return $this->categoryDatetimeRepository;
    }

    /**
     * Set's the repository to load the category decimal attribute with.
     *
     * @param \TechDivision\Import\Category\Repositories\CategoryDecimalRepository $categoryDecimalRepository The repository instance
     *
     * @return void
     */
    public function setCategoryDecimalRepository($categoryDecimalRepository)
    {
        $this->categoryDecimalRepository = $categoryDecimalRepository;
    }

    /**
     * Return's the repository to load the category decimal attribute with.
     *
     * @return \TechDivision\Import\Category\Repositories\CategoryDecimalRepository The repository instance
     */
    public function getCategoryDecimalRepository()
    {
        return $this->categoryDecimalRepository;
    }

    /**
     * Set's the repository to load the category integer attribute with.
     *
     * @param \TechDivision\Import\Category\Repositories\CategoryIntRepository $categoryIntRepository The repository instance
     *
     * @return void
     */
    public function setCategoryIntRepository($categoryIntRepository)
    {
        $this->categoryIntRepository = $categoryIntRepository;
    }

    /**
     * Return's the repository to load the category integer attribute with.
     *
     * @return \TechDivision\Import\Category\Repositories\CategoryIntRepository The repository instance
     */
    public function getCategoryIntRepository()
    {
        return $this->categoryIntRepository;
    }

    /**
     * Set's the repository to load the category text attribute with.
     *
     * @param \TechDivision\Import\Category\Repositories\CategoryTextRepository $categoryTextRepository The repository instance
     *
     * @return void
     */
    public function setCategoryTextRepository($categoryTextRepository)
    {
        $this->categoryTextRepository = $categoryTextRepository;
    }

    /**
     * Return's the repository to load the category text attribute with.
     *
     * @return \TechDivision\Import\Category\Repositories\CategoryTextRepository The repository instance
     */
    public function getCategoryTextRepository()
    {
        return $this->categoryTextRepository;
    }

    /**
     * Set's the repository to load the category varchar attribute with.
     *
     * @param \TechDivision\Import\Category\Repositories\CategoryVarcharRepository $categoryVarcharRepository The repository instance
     *
     * @return void
     */
    public function setCategoryVarcharRepository($categoryVarcharRepository)
    {
        $this->categoryVarcharRepository = $categoryVarcharRepository;
    }

    /**
     * Return's the repository to load the category varchar attribute with.
     *
     * @return \TechDivision\Import\Category\Repositories\CategoryVarcharRepository The repository instance
     */
    public function getCategoryVarcharRepository()
    {
        return $this->categoryVarcharRepository;
    }

    /**
     * Set's the repository to load the URL rewrites with.
     *
     * @param \TechDivision\Import\Repositories\UrlRewriteRepository $urlRewriteRepository The repository instance
     *
     * @return void
     */
    public function setUrlRewriteRepository($urlRewriteRepository)
    {
        $this->urlRewriteRepository = $urlRewriteRepository;
    }

    /**
     * Return's the repository to load the URL rewrites with.
     *
     * @return \TechDivision\Import\Repositories\UrlRewriteRepository The repository instance
     */
    public function getUrlRewriteRepository()
    {
        return $this->urlRewriteRepository;
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
     * Load's and return's the EAV attribute option value with the passed code, store ID and value.
     *
     * @param string  $attributeCode The code of the EAV attribute option to load
     * @param integer $storeId       The store ID of the attribute option to load
     * @param string  $value         The value of the attribute option to load
     *
     * @return array The EAV attribute option value
     */
    public function loadEavAttributeOptionValueByAttributeCodeAndStoreIdAndValue($attributeCode, $storeId, $value)
    {
        return $this->getEavAttributeOptionValueRepository()->findOneByAttributeCodeAndStoreIdAndValue($attributeCode, $storeId, $value);
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
}
