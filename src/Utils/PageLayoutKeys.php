<?php

/**
 * TechDivision\Import\Category\Utils\PageLayoutKeys
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
 * Utility class containing the available page layout keys.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */
class PageLayoutKeys
{

    /**
     * Key for '1 column'.
     *
     * @var integer
     */
    const PAGE_LAYOUT_1_COLUMN = '1column';

    /**
     * Key for '2 columns with left bar'.
     *
     * @var integer
     */
    const PAGE_LAYOUT_2_COLUMNS_LEFT = '2columns-left';

    /**
     * Key for '2 columns with right bar'.
     *
     * @var integer
     */
    const PAGE_LAYOUT_2_COLUMNS_RIGHT = '2columns-right';

    /**
     * Key for '3 columns'.
     *
     * @var integer
     */
    const PAGE_LAYOUT_3_COLUMNS = '3columns';

    /**
     * Key for 'Empty'.
     *
     * @var integer
     */
    const PAGE_LAYOUT_EMPTY = 'empty';

    /**
     * This is a utility class, so protect it against direct
     * instantiation.
     */
    private function __construct()
    {
    }

    /**
     * This is a utility class, so protect it against cloning.
     *
     * @return void
     */
    private function __clone()
    {
    }
}
