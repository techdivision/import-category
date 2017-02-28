<?php

/**
 * TechDivision\Import\Category\Subjects\AbstractCategorySubject
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

namespace TechDivision\Import\Category\Subjects;

use Psr\Log\LoggerInterface;
use TechDivision\Import\Utils\RegistryKeys;
use TechDivision\Import\Subjects\AbstractEavSubject;
use TechDivision\Import\Services\RegistryProcessorInterface;
use TechDivision\Import\Category\Utils\MemberNames;
use TechDivision\Import\Category\Services\CategoryProcessorInterface;
use TechDivision\Import\Configuration\SubjectConfigurationInterface;

/**
 * The abstract product subject implementation that provides basic category
 * handling business logic.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */
abstract class AbstractCategorySubject extends AbstractEavSubject
{

    /**
     * The processor to read/write the necessary category data.
     *
     * @var \TechDivision\Import\Category\Services\CategoryProcessorInterface
     */
    protected $categoryProcessor;

    /**
     * The available stores.
     *
     * @var array
     */
    protected $stores = array();

    /**
     * The available store websites.
     *
     * @var array
     */
    protected $storeWebsites = array();

    /**
     * The available tax classes.
     *
     * @var array
     */
    protected $taxClasses = array();

    /**
     * The available categories.
     *
     * @var array
     */
    protected $categories = array();

    /**
     * The ID of the product that has been created recently.
     *
     * @var string
     */
    protected $lastEntityId;

    /**
     * The path of the category that has been created recently.
     *
     * @var string
     */
    protected $lastPath;

    /**
     * The mapping for the paths to the created entity IDs.
     *
     * @var array
     */
    protected $pathEntityIdMapping = array();

    /**
     * The mapping for the paths to the store view codes.
     *
     * @var array
     */
    protected $pathStoreViewCodeMapping = array();

    /**
     * The Magento 2 configuration.
     *
     * @var array
     */
    protected $coreConfigData;

    /**
     * Mappings for attribute code => CSV column header.
     *
     * @var array
     */
    protected $headerMappings = array(
        'image_path' => 'image'
    );

    /**
     * Initialize the subject instance.
     *
     * @param \Psr\Log\LoggerInterface                                          $systemLogger      The system logger instance
     * @param \TechDivision\Import\Configuration\SubjectConfigurationInterface  $configuration     The subject configuration instance
     * @param \TechDivision\Import\Services\RegistryProcessorInterface          $registryProcessor The registry processor instance
     * @param \TechDivision\Import\Category\Services\CategoryProcessorInterface $categoryProcessor The category processor instance
     */
    public function __construct(
        LoggerInterface $systemLogger,
        SubjectConfigurationInterface $configuration,
        RegistryProcessorInterface $registryProcessor,
        CategoryProcessorInterface $categoryProcessor
    ) {

        // pass the arguments to the parent constructor
        parent::__construct($systemLogger, $configuration, $registryProcessor);

        // initialize the category processor
        $this->categoryProcessor = $categoryProcessor;
    }

    /**
     * Return's the attribute option value with the passed value and store ID.
     *
     * @param mixed   $value   The option value
     * @param integer $storeId The ID of the store
     *
     * @return array|boolean The attribute option value instance
     */
    public function getEavAttributeOptionValueByOptionValueAndStoreId($value, $storeId)
    {
        return $this->getCategoryProcessor()->getEavAttributeOptionValueByOptionValueAndStoreId($value, $storeId);
    }

    /**
     * Return's the header mappings for the actual entity.
     *
     * @return array The header mappings
     */
    public function getHeaderMappings()
    {
        return $this->headerMappings;
    }

    /**
     * Set's the category processor instance.
     *
     * @param \TechDivision\Import\Category\Services\CategoryProcessorInterface $categoryProcessor The category processor instance
     *
     * @return void
     */
    public function setCategoryProcessor(CategoryProcessorInterface $categoryProcessor)
    {
        $this->categoryProcessor = $categoryProcessor;
    }

    /**
     * Return's the category processor instance.
     *
     * @return \TechDivision\Import\Category\Services\CategoryProcessorInterface The category processor instance
     */
    public function getCategoryProcessor()
    {
        return $this->categoryProcessor;
    }

    /**
     * Set's the ID of the product that has been created recently.
     *
     * @param string $lastEntityId The entity ID
     *
     * @return void
     */
    public function setLastEntityId($lastEntityId)
    {
        $this->lastEntityId = $lastEntityId;
    }

    /**
     * Return's the ID of the product that has been created recently.
     *
     * @return string The entity Id
     */
    public function getLastEntityId()
    {
        return $this->lastEntityId;
    }

    /**
     * Set's the path of the last imported category.
     *
     * @param string $lastPath The path
     *
     * @return void
     */
    public function setLastPath($lastPath)
    {
        $this->lastPath = $lastPath;
    }

    /**
     * Return's the path of the last imported category.
     *
     * @return string|null The path
     */
    public function getLastPath()
    {
        return $this->lastPath;
    }

    /**
     * Queries whether or not the path has already been processed.
     *
     * @param string $path The path to check been processed
     *
     * @return boolean TRUE if the path has been processed, else FALSE
     */
    public function hasBeenProcessed($path)
    {
        return isset($this->pathEntityIdMapping[$path]);
    }

    /**
     * Add the passed path => entity ID mapping.
     *
     * @param string $path The path
     *
     * @return void
     */
    public function addPathEntityIdMapping($path)
    {
        $this->pathEntityIdMapping[$path] = $this->getLastEntityId();
    }

    /**
     * Return's the entity ID for the passed path.
     *
     * @param string $path The path to return the entity ID for
     *
     * @return integer The mapped entity ID
     * @throws \Exception Is thrown, if the path can not be mapped
     */
    public function mapPathEntityId($path)
    {

        // query whether or not a entity ID for the passed path has been mapped
        if (isset($this->pathEntityIdMapping[$path])) {
            return $this->pathEntityIdMapping[$path];
        }

        // throw an exception if not
        throw new \Exception(sprintf('Can\'t map path %s to any entity ID', $path));
    }

    /**
     * Add the passed path => store view code mapping.
     *
     * @param string $path          The SKU
     * @param string $storeViewCode The store view code
     *
     * @return void
     */
    public function addPathStoreViewCodeMapping($path, $storeViewCode)
    {
        $this->pathStoreViewCodeMapping[$path] = $storeViewCode;
    }

    /**
     * Intializes the previously loaded global data for exactly one bunch.
     *
     * @return void
     * @see \Importer\Csv\Actions\ProductImportAction::prepare()
     */
    public function setUp()
    {

        // load the status of the actual import
        $status = $this->getRegistryProcessor()->getAttribute($this->getSerial());

        // load the global data we've prepared initially
        $this->storeWebsites =  $status[RegistryKeys::GLOBAL_DATA][RegistryKeys::STORE_WEBSITES];
        $this->stores = $status[RegistryKeys::GLOBAL_DATA][RegistryKeys::STORES];
        $this->taxClasses = $status[RegistryKeys::GLOBAL_DATA][RegistryKeys::TAX_CLASSES];
        $this->coreConfigData = $status[RegistryKeys::GLOBAL_DATA][RegistryKeys::CORE_CONFIG_DATA];

        // load the available categories
        $this->categories = $this->getCategoryProcessor()->getCategoriesWithResolvedPath();

        // prepare the callbacks
        parent::setUp();
    }

    /**
     * Clean up the global data after importing the bunch.
     *
     * @return void
     */
    public function tearDown()
    {

        // invoke the parent method
        parent::tearDown();

        // update the status
        $this->getRegistryProcessor()->mergeAttributesRecursive(
            $this->getSerial(),
            array(
                RegistryKeys::FILES => array($this->getFilename() => array(RegistryKeys::STATUS => 1))
            )
        );
    }

    /**
     * Return's the store ID of the actual row, or of the default store
     * if no store view code is set in the CSV file.
     *
     * @param string|null $default The default store view code to use, if no store view code is set in the CSV file
     *
     * @return integer The ID of the actual store
     * @throws \Exception Is thrown, if the store with the actual code is not available
     */
    public function getRowStoreId($default = null)
    {

        // initialize the default store view code, if not passed
        if ($default == null) {
            $defaultStore = $this->getDefaultStore();
            $default = $defaultStore[MemberNames::CODE];
        }

        // load the store view code the create the product/attributes for
        $storeViewCode = $this->getStoreViewCode($default);

        // query whether or not, the requested store is available
        if (isset($this->stores[$storeViewCode])) {
            return (integer) $this->stores[$storeViewCode][MemberNames::STORE_ID];
        }

        // throw an exception, if not
        throw new \Exception(
            sprintf(
                'Found invalid store view code %s in file %s on line %d',
                $storeViewCode,
                $this->getFilename(),
                $this->getLineNumber()
            )
        );
    }

    /**
     * Return's the store website for the passed code.
     *
     * @param string $code The code of the store website to return the ID for
     *
     * @return integer The store website ID
     * @throws \Exception Is thrown, if the store website with the requested code is not available
     */
    public function getStoreWebsiteIdByCode($code)
    {

        // query whether or not, the requested store website is available
        if (isset($this->storeWebsites[$code])) {
            return (integer) $this->storeWebsites[$code][MemberNames::WEBSITE_ID];
        }

        // throw an exception, if not
        throw new \Exception(
            sprintf(
                'Found invalid website code %s in file %s on line %d',
                $code,
                $this->getFilename(),
                $this->getLineNumber()
            )
        );
    }

    /**
     * Return's the category with the passed path.
     *
     * @param string $path The path of the category to return
     *
     * @return array The category
     * @throws \Exception Is thrown, if the requested category is not available
     */
    public function getCategoryByPath($path)
    {

        // query whether or not the category with the passed path exists
        if (array_key_exists($path, $this->categories)) {
            return $this->categories[$path];
        }

        // throw an exception, if not
        throw new \Exception(
            sprintf(
                'Can\'t find category with path %s in file %s on line %d',
                $path,
                $this->getFilename(),
                $this->getLineNumber()
            )
        );
    }

    /**
     * Add the passed category to the available categories.
     *
     * @param string $path     The path to register the category with
     * @param array  $category The category to add
     *
     * @return void
     */
    public function addCategory($path, $category)
    {
        $this->categories[$path] = $category;
    }

    /**
     * Return's the category with the passed ID.
     *
     * @param integer $categoryId The ID of the category to return
     *
     * @return array The category data
     * @throws \Exception Is thrown, if the category is not available
     */
    public function getCategory($categoryId)
    {

        // try to load the category with the passed ID
        foreach ($this->categories as $category) {
            if ($category[MemberNames::ENTITY_ID] == $categoryId) {
                return $category;
            }
        }

        // throw an exception if the category is NOT available
        throw new \Exception(
            sprintf(
                'Can\'t load category with ID %d in file %s on line %d',
                $categoryId,
                $this->getFilename(),
                $this->getLineNumber()
            )
        );
    }
}
