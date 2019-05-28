<?php

/**
 * TechDivision\Import\Category\Observers\ClearImageObserver
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

namespace TechDivision\Import\Category\Observers;

use TechDivision\Import\Utils\StoreViewCodes;
use TechDivision\Import\Category\Utils\ColumnKeys;
use TechDivision\Import\Category\Utils\MemberNames;

/**
 * Observer that extracts the category's image from a CSV file to be added to image specific CSV file.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */
class ClearImageObserver extends AbstractCategoryImportObserver
{

    /**
     * Process the observer's business logic.
     *
     * @return array The processed row
     */
    protected function process()
    {

        // initialize the store view code
        $this->getSubject()->prepareStoreViewCode();

        // load the PK
        $pk = $this->getValue($this->getPrimaryKeyColumnName());

        // load the store view - if no store view has been set, we assume the admin
        // store view, which will contain the default (fallback) attribute values
        $storeViewCode = $this->getSubject()->getStoreViewCode(StoreViewCodes::ADMIN);

        // query whether or not the row has already been processed
        if ($this->storeViewHasBeenProcessed($pk, $storeViewCode)) {
            // log a message
            $this->getSystemLogger()->warning(
                $this->getSubject()->appendExceptionSuffix(
                    sprintf(
                        'Attributes for %s "%s" + store view code "%s" has already been processed',
                        $this->getPrimaryKeyColumnName(),
                        $pk,
                        $storeViewCode
                    )
                )
            );

            // return immediately
            return;
        }

        // load the store ID, use the admin store if NO store view code has been set
        $storeId = $this->getSubject()->getRowStoreId(StoreViewCodes::ADMIN);

        // load the image attribute
        $attribute = $this->getEavAttributeByAttributeCode('image');

        // remove the image if one exists
        if ($value = $this->loadVarcharAttribute($pk, $attribute[MemberNames::ATTRIBUTE_ID], $storeId)) {
            $this->deleteVarcharAttribute(array(MemberNames::VALUE_ID => $value[MemberNames::VALUE_ID]));
        }
    }

    /**
     * Delete's the varchar attribute with the passed value ID.
     *
     * @param array       $row  The attributes of the entity to delete
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    protected function deleteVarcharAttribute(array $row, $name = null)
    {
        return $this->getCategoryBunchProcessor()->deleteCategoryVarcharAttribute($row, $name);
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
    protected function loadVarcharAttribute($entityId, $attributeId, $storeId)
    {
        return $this->getCategoryBunchProcessor()->loadCategoryVarcharAttribute($entityId, $attributeId, $storeId);
    }

    /**
     * Return's the EAV attribute with the passed attribute code.
     *
     * @param string $attributeCode The attribute code
     *
     * @return array The array with the EAV attribute
     * @throws \Exception Is thrown if the attribute with the passed code is not available
     */
    protected function getEavAttributeByAttributeCode($attributeCode)
    {
        return $this->getSubject()->getEavAttributeByAttributeCode($attributeCode);
    }

    /**
     * Return's the PK to create the product => attribute relation.
     *
     * @return integer The PK to create the relation with
     */
    protected function getPrimaryKey()
    {
        return $this->getSubject()->getLastEntityId();
    }

    /**
     * Return's the PK column name to create the product => attribute relation.
     *
     * @return string The PK column name
     */
    protected function getPrimaryKeyMemberName()
    {
        return MemberNames::ENTITY_ID;
    }

    /**
     * Return's the column name that contains the primary key.
     *
     * @return string the column name that contains the primary key
     */
    protected function getPrimaryKeyColumnName()
    {
        return ColumnKeys::PATH;
    }
}
