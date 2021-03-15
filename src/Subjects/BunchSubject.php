<?php

/**
 * TechDivision\Import\Category\Subjects\BunchSubject
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

namespace TechDivision\Import\Category\Subjects;

use TechDivision\Import\Subjects\ExportableTrait;
use TechDivision\Import\Subjects\FileUploadTrait;
use TechDivision\Import\Subjects\ExportableSubjectInterface;
use TechDivision\Import\Subjects\FileUploadSubjectInterface;
use TechDivision\Import\Subjects\UrlKeyAwareSubjectInterface;
use TechDivision\Import\Subjects\CleanUpColumnsSubjectInterface;
use TechDivision\Import\Category\Utils\PageLayoutKeys;
use TechDivision\Import\Category\Utils\DisplayModeKeys;
use TechDivision\Import\Category\Utils\ConfigurationKeys;
use TechDivision\Import\Category\Utils\MemberNames;
use TechDivision\Import\Utils\StoreViewCodes;
use TechDivision\Import\Category\Utils\RegistryKeys;
use TechDivision\Import\Utils\FileUploadConfigurationKeys;


/**
 * The subject implementation that handles the business logic to persist products.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */
class BunchSubject extends AbstractCategorySubject implements ExportableSubjectInterface, FileUploadSubjectInterface, UrlKeyAwareSubjectInterface, CleanUpColumnsSubjectInterface
{

    /**
     * The trait that implements the export functionality.
     *
     * @var \TechDivision\Import\Subjects\ExportableTrait
     */
    use ExportableTrait;

    /**
     * The trait that provides file upload functionality.
     *
     * @var \TechDivision\Import\Subjects\FileUploadTrait
     */
    use FileUploadTrait;

    /**
     * The array with the available display mode keys.
     *
     * @var array
     */
    protected $availableDisplayModes = array(
        'Products only'             => DisplayModeKeys::DISPLAY_MODE_PRODUCTS_ONLY,
        'Static block only'         => DisplayModeKeys::DISPLAY_MODE_STATIC_BLOCK_ONLY,
        'Static block and products' => DisplayModeKeys::DISPLAY_MODE_BOTH
    );

    /**
     * The array with the available page layout keys.
     *
     * @var array
     */
    protected $availablePageLayouts = array(
        '1 column'                 => PageLayoutKeys::PAGE_LAYOUT_1_COLUMN,
        '2 columns with left bar'  => PageLayoutKeys::PAGE_LAYOUT_2_COLUMNS_LEFT,
        '2 columns with right bar' => PageLayoutKeys::PAGE_LAYOUT_2_COLUMNS_RIGHT,
        '3 columns'                => PageLayoutKeys::PAGE_LAYOUT_3_COLUMNS,
        'Empty'                    => PageLayoutKeys::PAGE_LAYOUT_EMPTY
    );
    /**
     * The default callback mappings for the Magento standard category attributes.
     *
     * @var array
     */
    protected $defaultCallbackMappings = array(
        'display_mode' => array('import_category.callback.display.mode'),
        'page_layout'  => array('import_category.callback.page.layout'),
    );

    /**
     * The available entity types.
     *
     * @var array
     */
    protected $entityTypes = array();

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
        $this->entityTypes = $status[RegistryKeys::GLOBAL_DATA][RegistryKeys::ENTITY_TYPES];

        // initialize the flag whether to copy images or not
        if ($this->getConfiguration()->hasParam(FileUploadConfigurationKeys::COPY_IMAGES)) {
            $this->setCopyImages($this->getConfiguration()->getParam(FileUploadConfigurationKeys::COPY_IMAGES));
        }

        // initialize the flag whether to override images or not
        if ($this->getConfiguration()->hasParam(FileUploadConfigurationKeys::OVERRIDE_IMAGES)) {
            $this->setOverrideImages($this->getConfiguration()->getParam(FileUploadConfigurationKeys::OVERRIDE_IMAGES));
        }

        // initialize media directory => can be absolute or relative
        if ($this->getConfiguration()->hasParam(FileUploadConfigurationKeys::MEDIA_DIRECTORY)) {
            try {
                $this->setMediaDir($this->resolvePath($this->getConfiguration()->getParam(FileUploadConfigurationKeys::MEDIA_DIRECTORY)));
            } catch (\InvalidArgumentException $iae) {
                // only if we wanna copy images we need directories
                if ($this->hasCopyImages()) {
                    $this->getSystemLogger()->debug($iae->getMessage());
                }
            }
        }

        // initialize images directory => can be absolute or relative
        if ($this->getConfiguration()->hasParam(FileUploadConfigurationKeys::IMAGES_FILE_DIRECTORY)) {
            try {
                $this->setImagesFileDir($this->resolvePath($this->getConfiguration()->getParam(FileUploadConfigurationKeys::IMAGES_FILE_DIRECTORY)));
            } catch (\InvalidArgumentException $iae) {
                // only if we wanna copy images we need directories
                if ($this->hasCopyImages()) {
                    $this->getSystemLogger()->debug($iae->getMessage());
                }
            }
        }

        // prepare the callbacks
        parent::setUp($serial);
    }

    /**
     * Return's the default callback mappings.
     *
     * @return array The default callback mappings
     */
    public function getDefaultCallbackMappings()
    {
        return $this->defaultCallbackMappings;
    }

    /**
     * Return's the display mode for the passed display mode string.
     *
     * @param string $displayMode The display mode string to return the key for
     *
     * @return integer The requested display mode
     * @throws \Exception Is thrown, if the requested display mode is not available
     */
    public function getDisplayModeByValue($displayMode)
    {

        // query whether or not, the requested display mode is available
        if (isset($this->availableDisplayModes[$displayMode])) {
            return $this->availableDisplayModes[$displayMode];
        }

        // throw an exception, if not
        throw new \Exception(
            $this->appendExceptionSuffix(
                sprintf('Found invalid display mode %s', $displayMode)
            )
        );
    }

    /**
     * Return's the page layout for the passed page layout string.
     *
     * @param string $pageLayout The page layout string to return the key for
     *
     * @return integer The requested page layout
     * @throws \Exception Is thrown, if the requested page layout is not available
     */
    public function getPageLayoutByValue($pageLayout)
    {

        // query whether or not, the requested display mode is available
        if (isset($this->availablePageLayouts[$pageLayout])) {
            return $this->availablePageLayouts[$pageLayout];
        }

        // throw an exception, if not
        throw new \Exception(
            $this->appendExceptionSuffix(
                sprintf('Found invalid page layout %s', $pageLayout)
            )
        );
    }

    /**
     * Return's the available store view codes of the available stores.
     *
     * @return array The array with the available store view codes
     */
    public function getStoreViewCodes()
    {
        return array_keys($this->stores);
    }

    /**
     * Returns the store view codes relevant to the category represented by the current row.
     *
     * @param string $path The path to return the root category's store view codes for
     *
     * @return array The store view codes for the given root category
     * @throws \Exception Is thrown, if the root category of the passed path is NOT available
     */
    public function getRootCategoryStoreViewCodes($path)
    {

        // explode the path of the root category
        list ($rootCategoryPath, ) = explode('/', $path);

        // query whether or not a root category with the given path exists
        if ($rootCategory = $this->getCategoryByPath($rootCategoryPath)) {
            // initialize the array with the store view codes
            $storeViewCodes = array();

            // try to assemble the store view codes by iterating over the available root categories
            foreach ($this->rootCategories as $storeViewCode => $category) {
                // query whether or not the entity ID of the root category matches
                if ((integer) $category[$this->getPrimaryKeyMemberName()] === (integer) $rootCategory[$this->getPrimaryKeyMemberName()]) {
                    $storeViewCodes[] = $storeViewCode;
                }
            }

            // return the array with the store view codes
            return $storeViewCodes;
        }

        // throw an exception, if the root category is NOT available
        throw new \Exception(
            $this->appendExceptionSuffix(
                sprintf('Can\'t load root category "%s" for path "%s"', $rootCategoryPath, $path)
            )
        );
    }

    /**
     * Return's the PK column name to create the product => attribute relation.
     *
     * @return string The PK column name
     */
    protected function getPrimaryKeyMemberName()
    {
        return MemberNames::ENTITY_ID;
    }

    /**
     * Return's the entity type for the configured entity type code.
     *
     * @return array The requested entity type
     * @throws \Exception Is thrown, if the requested entity type is not available
     */
    public function getEntityType()
    {

        // query whether or not the entity type with the passed code is available
        if (isset($this->entityTypes[$entityTypeCode = $this->getEntityTypeCode()])) {
            return $this->entityTypes[$entityTypeCode];
        }

        // throw a new exception
        throw new \Exception(
            $this->appendExceptionSuffix(
                sprintf('Requested entity type "%s" is not available', $entityTypeCode)
            )
        );
    }

    /**
     * Return's TRUE, if the passed URL key varchar value IS related with the actual PK.
     *
     * @param array $categoryVarcharAttribute The varchar value to check
     *
     * @return boolean TRUE if the URL key is related, else FALSE
     */
    public function isUrlKeyOf(array $categoryVarcharAttribute)
    {
        return ((integer) $categoryVarcharAttribute[MemberNames::ENTITY_ID] === (integer) $this->getLastEntityId()) &&
               ((integer) $categoryVarcharAttribute[MemberNames::STORE_ID] === (integer) $this->getRowStoreId(StoreViewCodes::ADMIN));
    }

    /**
     * Merge the columns from the configuration with all image type columns to define which
     * columns should be cleaned-up.
     *
     * @return array The columns that has to be cleaned-up
     */
    public function getCleanUpColumns()
    {

        // initialize the array for the columns that has to be cleaned-up
        $cleanUpColumns = array();

        // query whether or not an array has been specified in the configuration
        if ($this->getConfiguration()->hasParam(ConfigurationKeys::CLEAN_UP_EMPTY_COLUMNS)) {
            $cleanUpColumns = $this->getConfiguration()->getParam(ConfigurationKeys::CLEAN_UP_EMPTY_COLUMNS);
        }

        // return the array with the column names
        return $cleanUpColumns;
    }
}
