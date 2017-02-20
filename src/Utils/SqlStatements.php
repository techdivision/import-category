<?php

/**
 * TechDivision\Import\Category\Utils\SqlStatements
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

namespace TechDivision\Import\Category\Utils;

/**
 * Utility class with the SQL statements to use.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */
class SqlStatements extends \TechDivision\Import\Utils\SqlStatements
{

    /**
     * The SQL statement to load the category with the passed path.
     *
     * @var string
     */
    const CATEGORY = 'SELECT * FROM catalog_category_entity WHERE path = :path';

    /**
     * The SQL statement to load the category datetime attribute with the passed entity/attribute/store ID.
     *
     * @var string
     */
    const CATEGORY_DATETIME = 'SELECT *
                                 FROM catalog_category_entity_datetime
                                WHERE entity_id = :entity_id
                                  AND attribute_id = :attribute_id
                                  AND store_id = :store_id';

    /**
     * The SQL statement to load the category decimal attribute with the passed entity/attribute/store ID.
     *
     * @var string
     */
    const CATEGORY_DECIMAL = 'SELECT *
                                FROM catalog_category_entity_decimal
                               WHERE entity_id = :entity_id
                                 AND attribute_id = :attribute_id
                                 AND store_id = :store_id';

    /**
     * The SQL statement to load the category integer attribute with the passed entity/attribute/store ID.
     *
     * @var string
     */
    const CATEGORY_INT = 'SELECT *
                            FROM catalog_category_entity_int
                           WHERE entity_id = :entity_id
                             AND attribute_id = :attribute_id
                             AND store_id = :store_id';

    /**
     * The SQL statement to load the category text attribute with the passed entity/attribute/store ID.
     *
     * @var string
     */
    const CATEGORY_TEXT = 'SELECT *
                             FROM catalog_category_entity_text
                            WHERE entity_id = :entity_id
                              AND attribute_id = :attribute_id
                              AND store_id = :store_id';

    /**
     * The SQL statement to load the category varchar attribute with the passed entity/attribute/store ID.
     *
     * @var string
     */
    const CATEGORY_VARCHAR = 'SELECT *
                                FROM catalog_category_entity_varchar
                               WHERE entity_id = :entity_id
                                 AND attribute_id = :attribute_id
                                 AND store_id = :store_id';

    /**
     * The SQL statement to create new categories.
     *
     * @var string
     */
    const CREATE_CATEGORY = 'INSERT
                               INTO catalog_category_entity (attribute_set_id,
                                                             parent_id,
                                                             created_at,
                                                             updated_at,
                                                             path,
                                                             position,
                                                             level,
                                                             children_count)
                             VALUES (:attribute_set_id,
                                     :parent_id,
                                     :created_at,
                                     :updated_at,
                                     :path,
                                     :position,
                                     :level,
                                     :children_count)';

    /**
     * The SQL statement to update an existing category.
     *
     * @var string
     */
    const UPDATE_CATEGORY = 'UPDATE catalog_category_entity
                                SET attribute_set_id = :attribute_set_id,
                                    parent_id = :parent_id,
                                    created_at = :created_at,
                                    updated_at = :updated_at,
                                    path = :path,
                                    position = :position,
                                    level = :level,
                                    children_count = :children_count
                              WHERE entity_id = :entity_id';

    /**
     * The SQL statement to create a new category datetime value.
     *
     * @var string
     */
    const CREATE_CATEGORY_DATETIME = 'INSERT
                                        INTO catalog_category_entity_datetime (
                                                 entity_id,
                                                 attribute_id,
                                                 store_id,
                                                 value
                                             )
                                     VALUES (:entity_id,
                                             :attribute_id,
                                             :store_id,
                                             :value)';

    /**
     * The SQL statement to update an existing category datetime value.
     *
     * @var string
     */
    const UPDATE_CATEGORY_DATETIME = 'UPDATE catalog_category_entity_datetime
                                         SET entity_id = :entity_id,
                                             attribute_id = :attribute_id,
                                             store_id = :store_id,
                                             value = :value
                                       WHERE value_id = :value_id';

    /**
     * The SQL statement to create a new category decimal value.
     *
     * @var string
     */
    const CREATE_CATEGORY_DECIMAL = 'INSERT
                                       INTO catalog_category_entity_decimal (
                                                entity_id,
                                                attribute_id,
                                                store_id,
                                                value
                                            )
                                    VALUES (:entity_id,
                                            :attribute_id,
                                            :store_id,
                                            :value)';

    /**
     * The SQL statement to update an existing category decimal value.
     *
     * @var string
     */
    const UPDATE_CATEGORY_DECIMAL = 'UPDATE catalog_category_entity_decimal
                                        SET entity_id = :entity_id,
                                            attribute_id = :attribute_id,
                                            store_id = :store_id,
                                            value = :value
                                      WHERE value_id = :value_id';

    /**
     * The SQL statement to create a new category integer value.
     *
     * @var string
     */
    const CREATE_CATEGORY_INT = 'INSERT
                                   INTO catalog_category_entity_int (
                                            entity_id,
                                            attribute_id,
                                            store_id,
                                            value
                                        )
                                 VALUES (:entity_id,
                                         :attribute_id,
                                         :store_id,
                                         :value)';

    /**
     * The SQL statement to update an existing category integer value.
     *
     * @var string
     */
    const UPDATE_CATEGORY_INT = 'UPDATE catalog_category_entity_int
                                    SET entity_id = :entity_id,
                                        attribute_id = :attribute_id,
                                        store_id = :store_id,
                                        value = :value
                                  WHERE value_id = :value_id';

    /**
     * The SQL statement to create a new category varchar value.
     *
     * @var string
     */
    const CREATE_CATEGORY_VARCHAR = 'INSERT
                                       INTO catalog_category_entity_varchar (
                                                entity_id,
                                                attribute_id,
                                                store_id,
                                                value
                                            )
                                     VALUES (:entity_id,
                                             :attribute_id,
                                             :store_id,
                                             :value)';

    /**
     * The SQL statement to update an existing category varchar value.
     *
     * @var string
     */
    const UPDATE_CATEGORY_VARCHAR = 'UPDATE catalog_category_entity_varchar
                                        SET entity_id = :entity_id,
                                            attribute_id = :attribute_id,
                                            store_id = :store_id,
                                            value = :value
                                      WHERE value_id = :value_id';

    /**
     * The SQL statement to create a new category text value.
     *
     * @var string
     */
    const CREATE_CATEGORY_TEXT = 'INSERT
                                    INTO catalog_category_entity_text (
                                             entity_id,
                                             attribute_id,
                                             store_id,
                                             value
                                         )
                                  VALUES (:entity_id,
                                          :attribute_id,
                                          :store_id,
                                          :value)';

    /**
     * The SQL statement to update an existing category text value.
     *
     * @var string
     */
    const UPDATE_CATEGORY_TEXT = 'UPDATE catalog_category_entity_text
                                     SET entity_id = :entity_id,
                                         attribute_id = :attribute_id,
                                         store_id = :store_id,
                                         value = :value
                                   WHERE value_id = :value_id';

    /**
     * The SQL statement to remove a existing category.
     *
     * @var string
     */
    const DELETE_CATEGORY = 'DELETE
                               FROM catalog_category_entity
                              WHERE path = :path';
}
