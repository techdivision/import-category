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
use TechDivision\Import\Subjects\ExportableSubjectInterface;
use TechDivision\Import\Category\Utils\PageLayoutKeys;
use TechDivision\Import\Category\Utils\DisplayModeKeys;

/**
 * The subject implementation that handles the business logic to persist products.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */
class BunchSubject extends AbstractCategorySubject implements ExportableSubjectInterface
{

    /**
     * The trait that implements the export functionality.
     *
     * @var \TechDivision\Import\Subjects\ExportableTrait
     */
    use ExportableTrait;

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
                sprintf(
                    'Found invalid display mode %s',
                    $displayMode
                )
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
                sprintf(
                    'Found invalid page layout %s',
                    $pageLayout
                )
            )
        );
    }
}
