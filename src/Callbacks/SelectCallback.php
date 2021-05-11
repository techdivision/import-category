<?php

/**
 * TechDivision\Import\Category\Callbacks\SelectCallback
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

namespace TechDivision\Import\Category\Callbacks;

use TechDivision\Import\Category\Utils\ColumnKeys;
use TechDivision\Import\Callbacks\AbstractSelectCallback;

/**
 * A callback implementation that converts the passed select value.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */
class SelectCallback extends AbstractSelectCallback
{

    /**
     * Return's the category path as unique identifier of the actual row.
     *
     * @return mixed The row's unique identifier
     */
    protected function getUniqueIdentifier()
    {
        return $this->getValue(ColumnKeys::PATH);
    }
}
