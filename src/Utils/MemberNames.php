<?php

/**
 * TechDivision\Import\Category\Utils\MemberNames
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Category\Utils;

/**
 * Utility class containing the entities member names.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */
class MemberNames extends \TechDivision\Import\Utils\MemberNames
{

    /**
     * Name for the member 'entity_id'.
     *
     * @var string
     */
    const ENTITY_ID = 'entity_id';

    /**
     * Name for the member 'attribute_set_id'.
     *
     * @var string
     */
    const ATTRIBUTE_SET_ID = 'attribute_set_id';

    /**
     * Name for the member 'parent_id'.
     *
     * @var string
     */
    const PARENT_ID = 'parent_id';

    /**
     * Name for the member 'created_at'.
     *
     * @var string
     */
    const CREATED_AT = 'created_at';

    /**
     * Name for the member 'updated_at'.
     *
     * @var string
     */
    const UPDATED_AT = 'updated_at';

    /**
     * Name for the member 'path'.
     *
     * @var string
     */
    const PATH = 'path';

    /**
     * Name for the member 'position'.
     *
     * @var string
     */
    const POSITION = 'position';

    /**
     * Name for the member 'level'.
     *
     * @var string
     */
    const LEVEL = 'level';

    /**
     * Name for the member 'children_count'.
     *
     * @var string
     */
    const CHILDREN_COUNT = 'children_count';

    /**
     * Name for the member 'category_id'.
     *
     * @var string
     */
    const CATEGORY_ID = 'category_id';

    /**
     * Name for the member 'is_anchor'.
     *
     * @var string
     */
    const IS_ANCHOR = 'is_anchor';

    /**
     * Name for the member 'include_in_menu'.
     *
     * @var string
     */
    const INCLUDE_IN_MENU = 'include_in_menu';
}
