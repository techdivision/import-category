<?php

/**
 * TechDivision\Import\Category\Subjects\AbstractCategorySubject
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Category\Subjects;

use TechDivision\Import\Utils\FrontendInputTypes;
use TechDivision\Import\Category\Utils\MemberNames;
use TechDivision\Import\Category\Utils\RegistryKeys;
use TechDivision\Import\Subjects\AbstractEavSubject;
use TechDivision\Import\Subjects\EntitySubjectInterface;
use TechDivision\Import\Utils\StoreViewCodes;

/**
 * The abstract product subject implementation that provides basic category
 * handling business logic.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */
abstract class AbstractCategorySubject extends AbstractEavSubject implements EntitySubjectInterface
{

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
     * The default mappings for the user defined attributes, based on the attributes frontend input type.
     *
     * @var array
     */
    protected $defaultFrontendInputCallbackMappings = array(
        FrontendInputTypes::SELECT      => array('import_category.callback.select'),
        FrontendInputTypes::MULTISELECT => array('import_category.callback.multiselect'),
        FrontendInputTypes::BOOLEAN     => array('import_category.callback.boolean')
    );

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
     * Unifiy the passed path.
     *
     * @param string $path The path to unify
     *
     * @return string The unified path
     */
    protected function unifyPath($path)
    {
        return str_replace('"', null, strtolower($path));
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
        return $this->hasPathEntityIdMapping($path);
    }

    /**
     * Queries whether or not the passed PK and store view code has already been processed.
     *
     * @param string $pk            The PK to check been processed
     * @param string $storeViewCode The store view code to check been processed
     *
     * @return boolean TRUE if the PK and store view code has been processed, else FALSE
     */
    public function storeViewHasBeenProcessed($pk, $storeViewCode)
    {
        return isset($this->pathEntityIdMapping[$pk]) && isset($this->pathStoreViewCodeMapping[$pk]) && in_array($storeViewCode, $this->pathStoreViewCodeMapping[$pk]);
    }

    /**
     * Removes the mapping, e. g. when a category has been deleted.
     *
     * @param string $path The path to delete the mapping for
     *
     * @return void
     */
    public function removePathEntityIdMapping($path)
    {
        unset($this->pathEntityIdMapping[$this->unifyPath($path)]);
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
        $this->pathEntityIdMapping[$this->unifyPath($path)] = $this->getLastEntityId();
    }

    /**
     * Query whether or not a mapping for the passed path is available.
     *
     * @param string $path The path
     *
     * @return bool TRUE if the mapping is available, else FALSE
     */
    public function hasPathEntityIdMapping($path)
    {
        return isset($this->pathEntityIdMapping[$this->unifyPath($path)]);
    }

    /**
     * Find the path from mapping when only entity ID exists
     *
     * @param string $id The entoty_id to find in mapping
     *
     * @return string|bool
     */
    public function findPathfromEntityIdMapping($id)
    {
        return \array_search($id, $this->pathEntityIdMapping);
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
        if (isset($this->pathEntityIdMapping[$unifiedPath = $this->unifyPath($path)])) {
            return $this->pathEntityIdMapping[$unifiedPath];
        }

        // throw an exception if not
        throw new \Exception(
            $this->appendExceptionSuffix(
                sprintf('Can\'t map path %s to any entity ID', $path)
            )
        );
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
        $this->pathStoreViewCodeMapping[$path][] = $storeViewCode;
    }

    /**
     * Intializes the previously loaded global data for exactly one bunch.
     *
     * @param string $serial The serial of the actual import
     *
     * @return void
     */
    public function setUp($serial)
    {

        // load the status of the actual import
        $status = $this->getRegistryProcessor()->getAttribute(RegistryKeys::STATUS);

        // load the global data we've prepared initially
        $this->taxClasses = $status[RegistryKeys::GLOBAL_DATA][RegistryKeys::TAX_CLASSES];
        $this->storeWebsites =  $status[RegistryKeys::GLOBAL_DATA][RegistryKeys::STORE_WEBSITES];

        // load the categories for the admin store view from the global data
        $this->categories = $status[RegistryKeys::GLOBAL_DATA][RegistryKeys::CATEGORIES][StoreViewCodes::ADMIN];

        // load the available category path => entity ID mappings
        foreach ($this->categories as $resolvedPath => $category) {
            $this->pathEntityIdMapping[$this->unifyPath($resolvedPath)] = $category[MemberNames::ENTITY_ID];
        }

        // load the category path => entity ID mappings from the previous subject
        if (isset($status[RegistryKeys::PATH_ENTITY_ID_MAPPING])) {
            $this->pathEntityIdMapping = array_merge($this->pathEntityIdMapping, $status[RegistryKeys::PATH_ENTITY_ID_MAPPING]);
        }

        // prepare the callbacks
        parent::setUp($serial);
    }

    /**
     * Clean up the global data after importing the variants.
     *
     * @param string $serial The serial of the actual import
     *
     * @return void
     */
    public function tearDown($serial)
    {

        // load the registry processor
        $registryProcessor = $this->getRegistryProcessor();

        // load the status of the actual import
        $status = $registryProcessor->getAttribute(RegistryKeys::STATUS);

        // load the categories for the admin store view from the global data
        $categories = $status[RegistryKeys::GLOBAL_DATA][RegistryKeys::CATEGORIES];

        // update the categories of the global data with the new ones
        foreach (array_keys($this->stores) as $storeWebsiteCode) {
            foreach ($this->categories as $path => $category) {
                if ($storeWebsiteCode === StoreViewCodes::ADMIN) {
                    $categories[$storeWebsiteCode][$path] = $category;
                } else {
                    // for store code only just add new category
                    // never update store code specific with admin row information
                    if (!isset($categories[$storeWebsiteCode][$path])) {
                        $categories[$storeWebsiteCode][$path] = $category;
                    }
                }
            }
        }

        // update the status with the actual path => entity ID mappings
        $registryProcessor->mergeAttributesRecursive(
            RegistryKeys::STATUS,
            array(
                RegistryKeys::PATH_ENTITY_ID_MAPPING => $this->pathEntityIdMapping,
                RegistryKeys::GLOBAL_DATA => array(
                    RegistryKeys::CATEGORIES => $categories
                )
            )
        );

        // invoke the parent method
        parent::tearDown($serial);
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
            $this->appendExceptionSuffix(
                sprintf('Found invalid store view code "%s"', $storeViewCode)
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
            $this->appendExceptionSuffix(
                sprintf('Found invalid website code "%s"', $code)
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
        throw new \Exception(sprintf('Can\'t find category with path "%s"', $path));
    }

    /**
     * Add's the passed category with the given path.
     *
     * @param string $path     The path to add the category with
     * @param array  $category The catagory to add
     *
     * @return void
     */
    public function addCategoryByPath($path, array $category)
    {
        $this->categories[$path] = $category;
    }

    /**
     * Query's whether or not the category with the passed path is available or not.
     *
     * @param string $path The path of the category to query
     *
     * @return boolean TRUE if the category is available, else FALSE
     */
    public function hasCategoryByPath($path)
    {
        return isset($this->categories[$path]);
    }
}
