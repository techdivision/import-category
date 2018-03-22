<?php

/**
 * TechDivision\Import\Product\Services\CategoryBunchProcessorInterface
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

use TechDivision\Import\Services\EavAwareProcessorInterface;

/**
 * Interface for a category bunch processor.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */
interface CategoryBunchProcessorInterface extends CategoryProcessorInterface, EavAwareProcessorInterface
{

    /**
     * Return's the repository to access EAV attribute option values.
     *
     * @return \TechDivision\Import\Repositories\EavAttributeOptionValueRepositoryInterface The repository instance
     */
    public function getEavAttributeOptionValueRepository();

    /**
     * Return's the repository to access EAV attributes.
     *
     * @return \TechDivision\Import\Repositories\EavAttributeRepositoryInterface The repository instance
     */
    public function getEavAttributeRepository();

    /**
     * Return's the action with the category CRUD methods.
     *
     * @return \TechDivision\Import\Category\Actions\CategoryActionInterface The action instance
     */
    public function getCategoryAction();

    /**
     * Return's the action with the category varchar attribute CRUD methods.
     *
     * @return \TechDivision\Import\Category\Actions\CategoryVarcharActionInterface The action instance
     */
    public function getCategoryVarcharAction();

    /**
     * Return's the action with the category text attribute CRUD methods.
     *
     * @return \TechDivision\Import\Category\Actions\CategoryTextActionInterface The action instance
     */
    public function getCategoryTextAction();

    /**
     * Return's the action with the category int attribute CRUD methods.
     *
     * @return \TechDivision\Import\Category\Actions\CategoryIntActionInterface The action instance
     */
    public function getCategoryIntAction();

    /**
     * Return's the action with the category decimal attribute CRUD methods.
     *
     * @return \TechDivision\Import\Category\Actions\CategoryDecimalActionInterface The action instance
     */
    public function getCategoryDecimalAction();

    /**
     * Return's the action with the category datetime attribute CRUD methods.
     *
     * @return \TechDivision\Import\Category\Actions\CategoryDatetimeActionInterface The action instance
     */
    public function getCategoryDatetimeAction();

    /**
     * Return's the action with the URL rewrite CRUD methods.
     *
     * @return \TechDivision\Import\Actions\UrlRewriteActionInterface The action instance
     */
    public function getUrlRewriteAction();

    /**
     * Return's the category assembler.
     *
     * @return \TechDivision\Import\Assembler\CategoryAssemblerInterface The category assembler instance
     */
    public function getCategoryAssembler();

    /**
     * Return's the repository to load the categories with.
     *
     * @return \TechDivision\Import\Category\Repositories\CategoryRepositoryInterface The repository instance
     */
    public function getCategoryRepository();

    /**
     * Return's the repository to load the category datetime attribute with.
     *
     * @return \TechDivision\Import\Category\Repositories\CategoryDatetimeRepositoryInterface The repository instance
     */
    public function getCategoryDatetimeRepository();

    /**
     * Return's the repository to load the category decimal attribute with.
     *
     * @return \TechDivision\Import\Category\Repositories\CategoryDecimalRepositoryInterface The repository instance
     */
    public function getCategoryDecimalRepository();

    /**
     * Return's the repository to load the category integer attribute with.
     *
     * @return \TechDivision\Import\Category\Repositories\CategoryIntRepositoryInterface The repository instance
     */
    public function getCategoryIntRepository();

    /**
     * Return's the repository to load the category text attribute with.
     *
     * @return \TechDivision\Import\Category\Repositories\CategoryTextRepositoryInterface The repository instance
     */
    public function getCategoryTextRepository();

    /**
     * Return's the repository to load the category varchar attribute with.
     *
     * @return \TechDivision\Import\Category\Repositories\CategoryVarcharRepositoryInterface The repository instance
     */
    public function getCategoryVarcharRepository();

    /**
     * Return's the repository to load the URL rewrites with.
     *
     * @return \TechDivision\Import\Repositories\UrlRewriteRepositoryInterface The repository instance
     */
    public function getUrlRewriteRepository();

    /**
     * Return's the assembler to load the category attributes with.
     *
     * @return \TechDivision\Import\Category\Assemblers\CategoryAttributeAssemblerInterface The assembler instance
     */
    public function getCategoryAttributeAssembler();

    /**
     * Return's an array with the available EAV attributes for the passed is user defined flag.
     *
     * @param integer $isUserDefined The flag itself
     *
     * @return array The array with the EAV attributes matching the passed flag
     */
    public function getEavAttributesByIsUserDefined($isUserDefined = 1);

    /**
     * Returns an array with the available categories and their
     * resolved path as keys.
     *
     * @return array The array with the categories
     */
    public function getCategoriesWithResolvedPath();

    /**
     * Return's an array with all available categories.
     *
     * @return array The available categories
     */
    public function getCategories();

    /**
     * Return's an array with the root categories with the store code as key.
     *
     * @return array The root categories
     */
    public function getRootCategories();

    /**
     * Returns the category varchar values for the categories with
     * the passed with the passed entity IDs.
     *
     * @param array $entityIds The array with the category IDs
     *
     * @return mixed The category varchar values
     */
    public function getCategoryVarcharsByEntityIds(array $entityIds);

    /**
     * Return's the URL rewrites for the passed URL entity type and ID.
     *
     * @param string  $entityType The entity type to load the URL rewrites for
     * @param integer $entityId   The entity ID to laod the rewrites for
     *
     * @return array The URL rewrites
     */
    public function getUrlRewritesByEntityTypeAndEntityId($entityType, $entityId);

    /**
     * Return's the URL rewrites for the passed URL entity type and ID.
     *
     * @param string  $entityType The entity type to load the URL rewrites for
     * @param integer $entityId   The entity ID to load the URL rewrites for
     * @param integer $storeId    The store ID to load the URL rewrites for
     *
     * @return array The URL rewrites
     */
    public function getUrlRewritesByEntityTypeAndEntityIdAndStoreId($entityType, $entityId, $storeId);

    /**
     * Intializes the existing attributes for the entity with the passed primary key.
     *
     * @param string  $pk      The primary key of the entity to load the attributes for
     * @param integer $storeId The ID of the store view to load the attributes for
     *
     * @return array The entity attributes
     */
    public function getCategoryAttributesByPrimaryKeyAndStoreId($pk, $storeId);

    /**
     * Load's and return's the EAV attribute option value with the passed code, store ID and value.
     *
     * @param string  $attributeCode The code of the EAV attribute option to load
     * @param integer $storeId       The store ID of the attribute option to load
     * @param string  $value         The value of the attribute option to load
     *
     * @return array The EAV attribute option value
     */
    public function loadEavAttributeOptionValueByAttributeCodeAndStoreIdAndValue($attributeCode, $storeId, $value);

    /**
     * Return's the children count of the category with the passed path.
     *
     * @param string $path The path of the category to count the children for
     *
     * @return integer The children count of the category with the passed path
     */
    public function loadCategoryChildrenChildrenCount($path);

    /**
     * Return's the category with the passed ID.
     *
     * @param string $id The ID of the category to return
     *
     * @return array The category
     */
    public function loadCategory($id);

    /**
     * Load's and return's the datetime attribute with the passed entity/attribute/store ID.
     *
     * @param integer $entityId    The entity ID of the attribute
     * @param integer $attributeId The attribute ID of the attribute
     * @param integer $storeId     The store ID of the attribute
     *
     * @return array|null The datetime attribute
     */
    public function loadCategoryDatetimeAttribute($entityId, $attributeId, $storeId);

    /**
     * Load's and return's the decimal attribute with the passed entity/attribute/store ID.
     *
     * @param integer $entityId    The entity ID of the attribute
     * @param integer $attributeId The attribute ID of the attribute
     * @param integer $storeId     The store ID of the attribute
     *
     * @return array|null The decimal attribute
     */
    public function loadCategoryDecimalAttribute($entityId, $attributeId, $storeId);

    /**
     * Load's and return's the integer attribute with the passed entity/attribute/store ID.
     *
     * @param integer $entityId    The entity ID of the attribute
     * @param integer $attributeId The attribute ID of the attribute
     * @param integer $storeId     The store ID of the attribute
     *
     * @return array|null The integer attribute
     */
    public function loadCategoryIntAttribute($entityId, $attributeId, $storeId);

    /**
     * Load's and return's the text attribute with the passed entity/attribute/store ID.
     *
     * @param integer $entityId    The entity ID of the attribute
     * @param integer $attributeId The attribute ID of the attribute
     * @param integer $storeId     The store ID of the attribute
     *
     * @return array|null The text attribute
     */
    public function loadCategoryTextAttribute($entityId, $attributeId, $storeId);

    /**
     * Load's and return's the varchar attribute with the passed entity/attribute/store ID.
     *
     * @param integer $entityId    The entity ID of the attribute
     * @param integer $attributeId The attribute ID of the attribute
     * @param integer $storeId     The store ID of the attribute
     *
     * @return array|null The varchar attribute
     */
    public function loadCategoryVarcharAttribute($entityId, $attributeId, $storeId);

    /**
     * Persist's the passed category data and return's the ID.
     *
     * @param array       $category The category data to persist
     * @param string|null $name     The name of the prepared statement that has to be executed
     *
     * @return string The ID of the persisted entity
     */
    public function persistCategory($category, $name = null);

    /**
     * Persist's the passed category varchar attribute.
     *
     * @param array       $attribute The attribute to persist
     * @param string|null $name      The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function persistCategoryVarcharAttribute($attribute, $name = null);

    /**
     * Persist's the passed category integer attribute.
     *
     * @param array       $attribute The attribute to persist
     * @param string|null $name      The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function persistCategoryIntAttribute($attribute, $name = null);

    /**
     * Persist's the passed category decimal attribute.
     *
     * @param array       $attribute The attribute to persist
     * @param string|null $name      The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function persistCategoryDecimalAttribute($attribute, $name = null);

    /**
     * Persist's the passed product datetime attribute.
     *
     * @param array       $attribute The attribute to persist
     * @param string|null $name      The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function persistCategoryDatetimeAttribute($attribute, $name = null);

    /**
     * Persist's the passed category text attribute.
     *
     * @param array       $attribute The attribute to persist
     * @param string|null $name      The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function persistCategoryTextAttribute($attribute, $name = null);

    /**
     * Persist's the URL rewrite with the passed data.
     *
     * @param array       $row  The URL rewrite to persist
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return string The ID of the persisted entity
     */
    public function persistUrlRewrite($row, $name = null);

    /**
     * Delete's the URL rewrite with the passed attributes.
     *
     * @param array       $row  The attributes of the entity to delete
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function deleteUrlRewrite($row, $name = null);

    /**
     * Delete's the entity with the passed attributes.
     *
     * @param array       $row  The attributes of the entity to delete
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function deleteCategory($row, $name = null);
}
