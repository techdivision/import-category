<?php

/**
 * TechDivision\Import\Category\Utils\SqlStatementKeys
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
class SqlStatementKeys extends \TechDivision\Import\Utils\SqlStatementKeys
{

    /**
     * The SQL statement to load the category with the passed entity ID.
     *
     * @var string
     */
    const CATEGORY = 'category';

    /**
     * The SQL statement to load the category with the passed path.
     *
     * @var string
     */
    const CATEGORY_BY_PATH = 'category.by.path';

    /**
     * The SQL statement to load the category datetime attributes with the passed entity/store ID.
     *
     * @var string
     */
    const CATEGORY_DATETIMES = 'category_datetimes';

    /**
     * The SQL statement to load the category datetime attributes with the passed entity/store ID.
     *
     * @var string
     */
    const CATEGORY_DECIMALS = 'category_decimals';

    /**
     * The SQL statement to load the category datetime attributes with the passed entity/store ID.
     *
     * @var string
     */
    const CATEGORY_INTS = 'catebory_ints';

    /**
     * The SQL statement to load the category datetime attributes with the passed entity/store ID.
     *
     * @var string
     */
    const CATEGORY_TEXTS = 'category_texts';

    /**
     * The SQL statement to load the category datetime attributes with the passed entity/store ID.
     *
     * @var string
     */
    const CATEGORY_VARCHARS = 'category_varchars';

    /**
     * The SQL statement to create new categories.
     *
     * @var string
     */
    const CREATE_CATEGORY = 'create.category';

    /**
     * The SQL statement to update an existing category.
     *
     * @var string
     */
    const UPDATE_CATEGORY = 'update.category';

    /**
     * The SQL statement to create a new category datetime value.
     *
     * @var string
     */
    const CREATE_CATEGORY_DATETIME = 'create.category_datetime';

    /**
     * The SQL statement to update an existing category datetime value.
     *
     * @var string
     */
    const UPDATE_CATEGORY_DATETIME = 'update.category_datetime';

    /**
     * The SQL statement to create a new category decimal value.
     *
     * @var string
     */
    const CREATE_CATEGORY_DECIMAL = 'create.category_decimal';

    /**
     * The SQL statement to update an existing category decimal value.
     *
     * @var string
     */
    const UPDATE_CATEGORY_DECIMAL = 'update.category_decimal';

    /**
     * The SQL statement to create a new category integer value.
     *
     * @var string
     */
    const CREATE_CATEGORY_INT = 'create.category_int';

    /**
     * The SQL statement to update an existing category integer value.
     *
     * @var string
     */
    const UPDATE_CATEGORY_INT = 'update.category_int';

    /**
     * The SQL statement to create a new category varchar value.
     *
     * @var string
     */
    const CREATE_CATEGORY_VARCHAR = 'create.category_varchar';

    /**
     * The SQL statement to update an existing category varchar value.
     *
     * @var string
     */
    const UPDATE_CATEGORY_VARCHAR = 'update.category_varchar';

    /**
     * The SQL statement to create a new category text value.
     *
     * @var string
     */
    const CREATE_CATEGORY_TEXT = 'create.category_text';

    /**
     * The SQL statement to update an existing category text value.
     *
     * @var string
     */
    const UPDATE_CATEGORY_TEXT = 'update.category_text';

    /**
     * The SQL statement to remove a existing category.
     *
     * @var string
     */
    const DELETE_CATEGORY = 'delete.category';

    /**
     * The SQL statement to count the children of the category with the passed path.
     *
     * @var string
     */
    const CATEGORY_COUNT_CHILDREN = 'category.count.children';
}
