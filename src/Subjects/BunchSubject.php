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
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Category\Subjects;

use TechDivision\Import\Subjects\ExportableTrait;
use TechDivision\Import\Subjects\FileUploadTrait;
use TechDivision\Import\Subjects\ExportableSubjectInterface;
use TechDivision\Import\Subjects\FileUploadSubjectInterface;
use TechDivision\Import\Category\Utils\PageLayoutKeys;
use TechDivision\Import\Category\Utils\DisplayModeKeys;
use TechDivision\Import\Category\Utils\ConfigurationKeys;
use TechDivision\Import\Category\Utils\MemberNames;

/**
 * The subject implementation that handles the business logic to persist products.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */
class BunchSubject extends AbstractCategorySubject implements ExportableSubjectInterface, FileUploadSubjectInterface
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
     * Intializes the previously loaded global data for exactly one bunch.
     *
     * @param string $serial The serial of the actual import
     *
     * @return void
     */
    public function setUp($serial)
    {

        // initialize the flag whether to copy images or not
        if ($this->getConfiguration()->hasParam(ConfigurationKeys::COPY_IMAGES)) {
            $this->setCopyImages($this->getConfiguration()->getParam(ConfigurationKeys::COPY_IMAGES));
        }

        // initialize media directory => can be absolute or relative
        if ($this->getConfiguration()->hasParam(ConfigurationKeys::MEDIA_DIRECTORY)) {
            $this->setMediaDir(
                $this->resolvePath(
                    $this->getConfiguration()->getParam(ConfigurationKeys::MEDIA_DIRECTORY)
                )
            );
        }

        // initialize images directory => can be absolute or relative
        if ($this->getConfiguration()->hasParam(ConfigurationKeys::IMAGES_FILE_DIRECTORY)) {
            $this->setImagesFileDir(
                $this->resolvePath(
                    $this->getConfiguration()->getParam(ConfigurationKeys::IMAGES_FILE_DIRECTORY)
                )
            );
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
        throw new \Exception(sprintf('Found invalid display mode %s', $displayMode));
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
        throw new \Exception(printf('Found invalid page layout %s', $pageLayout));
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
            $storeCodes = array();

            // try to assemble the store view codes by iterating over the available root categories
            foreach ($this->rootCategories as $storeCode => $category) {
                // query whether or not the entity ID of the root category matches
                if ($category[MemberNames::ENTITY_ID] == $rootCategory[MemberNames::ENTITY_ID]) {
                    $storeCodes[] = $storeCode;
                }
            }

            // return the array with the store view codes
            return $storeCodes;
        }

        // throw an exception, if the root category is NOT available
        throw new \Exception(printf('Can\'t load root category "%s" for path "%s"', $rootCategoryPath, $path));
    }
}
