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

use TechDivision\Import\Utils\RegistryKeys;
use TechDivision\Import\Utils\FrontendInputTypes;
use TechDivision\Import\Utils\Generators\GeneratorInterface;
use TechDivision\Import\Subjects\AbstractEavSubject;
use TechDivision\Import\Services\RegistryProcessorInterface;
use TechDivision\Import\Category\Utils\MemberNames;
use TechDivision\Import\Category\Services\CategoryBunchProcessorInterface;

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
     * @var \TechDivision\Import\Category\Services\CategoryBunchProcessorInterface
     */
    protected $categoryBunchProcessor;

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
     * The default mappings for the user defined attributes, based on the attributes frontend input type.
     *
     * @var array
     */
    protected $defaultFrontendInputCallbackMappings = array(
        FrontendInputTypes::SELECT      => 'import_category.callback.select',
        FrontendInputTypes::MULTISELECT => 'import_category.callback.multiselect',
        FrontendInputTypes::BOOLEAN     => 'import_category.callback.boolean'
    );

    /**
     * Initialize the subject instance.
     *
     * @param \TechDivision\Import\Services\RegistryProcessorInterface               $registryProcessor          The registry processor instance
     * @param \TechDivision\Import\Utils\Generators\GeneratorInterface               $coreConfigDataUidGenerator The UID generator for the core config data
     * @param array                                                                  $systemLoggers              The array with the system logger instances
     * @param \TechDivision\Import\Category\Services\CategoryBunchProcessorInterface $categoryBunchProcessor     The category processor instance
     */
    public function __construct(
        RegistryProcessorInterface $registryProcessor,
        GeneratorInterface $coreConfigDataUidGenerator,
        array $systemLoggers,
        CategoryBunchProcessorInterface $categoryBunchProcessor
    ) {

        // pass the arguments to the parent constructor
        parent::__construct($registryProcessor, $coreConfigDataUidGenerator, $systemLoggers);

        // initialize the category bunch processor
        $this->categoryBunchProcessor = $categoryBunchProcessor;
    }

    /**
     * Return's the default callback frontend input mappings for the user defined attributes.
     *
     * @return array The default frontend input callback mappings
     */
    public function getDefaultFrontendInputCallbackMappings()
    {
        return $this->defaultFrontendInputCallbackMappings;
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
     * Return's the category bunch processor instance.
     *
     * @return \TechDivision\Import\Category\Services\CategoryBunchProcessorInterface The category bunch processor instance
     */
    public function getCategoryBunchProcessor()
    {
        return $this->categoryBunchProcessor;
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
     * @param string $serial The serial of the actual import
     *
     * @return void
     * @see \Importer\Csv\Actions\ProductImportAction::prepare()
     */
    public function setUp($serial)
    {

        // load the status of the actual import
        $status = $this->getRegistryProcessor()->getAttribute($serial);

        // load the global data we've prepared initially
        $this->taxClasses = $status[RegistryKeys::GLOBAL_DATA][RegistryKeys::TAX_CLASSES];
        $this->storeWebsites =  $status[RegistryKeys::GLOBAL_DATA][RegistryKeys::STORE_WEBSITES];

        // load the available categories
        $this->categories = $this->getCategoryBunchProcessor()->getCategoriesWithResolvedPath();

        // prepare the callbacks
        parent::setUp($serial);
    }

    /**
     * Clean up the global data after importing the bunch.
     *
     * @param string $serial The serial of the actual import
     *
     * @return void
     */
    public function tearDown($serial)
    {

        // invoke the parent method
        parent::tearDown($serial);

        // update the status
        $this->getRegistryProcessor()->mergeAttributesRecursive(
            $serial,
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
        if (isset($this->categories[$path])) {
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
