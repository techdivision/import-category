<?php

/**
 * TechDivision\Import\Category\Actions\CategoryAction
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
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Category\Actions;

use TechDivision\Import\Utils\EntityStatus;
use TechDivision\Import\Actions\AbstractAction;

/**
 * An action implementation that provides CRUD functionality for categories.
 *
 * @author     Tim Wagner <t.wagner@techdivision.com>
 * @copyright  2019 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/techdivision/import-category
 * @link       http://www.techdivision.com
 * @deprecated Since version 8.0.0 use \TechDivision\Import\Actions\GenericIdentifierAction instead
 */
class CategoryAction extends AbstractAction implements CategoryActionInterface
{

    /**
     * Helper method that create/update the passed entity, depending on
     * the entity's status.
     *
     * @param array $row The entity data to create/update
     *
     * @return string The last inserted ID
     */
    public function persist(array $row)
    {

        // load the method name
        $methodName = $row[EntityStatus::MEMBER_NAME];

        // invoke the method
        return $this->$methodName($row);
    }

    /**
     * Creates's the entity with the passed attributes.
     *
     * @param array       $row  The attributes of the entity to create
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return string The last inserted ID
     */
    public function create($row, $name = null)
    {
        return $this->getCreateProcessor()->execute($row, $name);
    }

    /**
     * Update's the entity with the passed attributes.
     *
     * @param array       $row  The attributes of the entity to update
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return string The ID of the updated product
     */
    public function update($row, $name = null)
    {
        return $this->getUpdateProcessor()->execute($row, $name);
    }
}
