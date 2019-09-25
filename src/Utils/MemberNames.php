<?php

/**
 * TechDivision\Import\Category\Utils\MemberNames
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
 * Utility class containing the entities member names.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
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
}
