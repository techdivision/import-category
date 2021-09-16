<?php

/**
 * TechDivision\Import\Category\Utils\DisplayModeKeys
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
 * Utility class containing the available display mode keys.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */
class DisplayModeKeys
{

    /**
     * Key for 'Products only'.
     *
     * @var integer
     */
    const DISPLAY_MODE_PRODUCTS_ONLY = 'PRODUCTS';

    /**
     * Key for 'Static block only'.
     *
     * @var integer
     */
    const DISPLAY_MODE_STATIC_BLOCK_ONLY = 'PAGE';

    /**
     * Key for 'Static block and products'.
     *
     * @var integer
     */
    const DISPLAY_MODE_BOTH = 'PRODUCTS_AND_PAGE';

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
