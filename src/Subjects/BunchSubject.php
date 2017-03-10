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
        'display_mode' => array('TechDivision\\Import\\Category\\Callbacks\\DisplayModeCallback'),
        'page_layout'  => array('TechDivision\\Import\\Category\\Callbacks\\PageLayoutCallback'),
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
            sprintf(
                'Found invalid display mode %s in file %s on line %d',
                $displayMode,
                $this->getFilename(),
                $this->getLineNumber()
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
            sprintf(
                'Found invalid page layout %s in file %s on line %d',
                $pageLayout,
                $this->getFilename(),
                $this->getLineNumber()
            )
        );
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
        return $this->getCategoryProcessor()->getUrlRewritesByEntityTypeAndEntityId($entityType, $entityId);
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
        return $this->getCategoryProcessor()->loadCategory($id);
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
        return $this->getCategoryProcessor()->loadCategoryDatetimeAttribute($entityId, $attributeId, $storeId);
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
        return $this->getCategoryProcessor()->loadCategoryDecimalAttribute($entityId, $attributeId, $storeId);
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
        return $this->getCategoryProcessor()->loadCategoryIntAttribute($entityId, $attributeId, $storeId);
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
        return $this->getCategoryProcessor()->loadCategoryTextAttribute($entityId, $attributeId, $storeId);
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
        return $this->getCategoryProcessor()->loadCategoryVarcharAttribute($entityId, $attributeId, $storeId);
    }

    /**
     * Persist's the passed category data and return's the ID.
     *
     * @param array $category The category data to persist
     *
     * @return string The ID of the persisted entity
     */
    public function persistCategory($category)
    {
        return $this->getCategoryProcessor()->persistCategory($category);
    }

    /**
     * Persist's the passed category varchar attribute.
     *
     * @param array $attribute The attribute to persist
     *
     * @return void
     */
    public function persistCategoryVarcharAttribute($attribute)
    {
        $this->getCategoryProcessor()->persistCategoryVarcharAttribute($attribute);
    }

    /**
     * Persist's the passed category integer attribute.
     *
     * @param array $attribute The attribute to persist
     *
     * @return void
     */
    public function persistCategoryIntAttribute($attribute)
    {
        $this->getCategoryProcessor()->persistCategoryIntAttribute($attribute);
    }

    /**
     * Persist's the passed category decimal attribute.
     *
     * @param array $attribute The attribute to persist
     *
     * @return void
     */
    public function persistCategoryDecimalAttribute($attribute)
    {
        $this->getCategoryProcessor()->persistCategoryDecimalAttribute($attribute);
    }

    /**
     * Persist's the passed category datetime attribute.
     *
     * @param array $attribute The attribute to persist
     *
     * @return void
     */
    public function persistCategoryDatetimeAttribute($attribute)
    {
        $this->getCategoryProcessor()->persistCategoryDatetimeAttribute($attribute);
    }

    /**
     * Persist's the passed category text attribute.
     *
     * @param array $attribute The attribute to persist
     *
     * @return void
     */
    public function persistCategoryTextAttribute($attribute)
    {
        $this->getCategoryProcessor()->persistCategoryTextAttribute($attribute);
    }

    /**
     * Persist's the URL rewrite with the passed data.
     *
     * @param array $row The URL rewrite to persist
     *
     * @return string The ID of the persisted entity
     */
    public function persistUrlRewrite($row)
    {
        $this->getCategoryProcessor()->persistUrlRewrite($row);
    }

    /**
     * Delete's the URL rewrite(s) with the passed attributes.
     *
     * @param array       $row  The attributes of the entity to delete
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function deleteUrlRewrite($row, $name = null)
    {
        $this->getCategoryProcessor()->deleteUrlRewrite($row, $name);
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
        $this->getCategoryProcessor()->deleteCategory($row, $name);
    }
}
