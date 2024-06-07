<?php

/**
 * Copyright (c) 2024 TechDivision GmbH
 * All rights reserved
 *
 * This product includes proprietary software developed at TechDivision GmbH, Germany
 * For more information see https://www.techdivision.com/
 *
 * To obtain a valid license for using this software please contact us at
 * license@techdivision.com
 */

namespace TechDivision\Import\Category\Callbacks;

use Crossbase\ImportAttribute\Api\MagentoAttributeInterface;
use TechDivision\Import\Callbacks\IndexedArrayValidatorCallback;
use TechDivision\Import\Category\Utils\ColumnKeys;
use TechDivision\Import\Utils\StoreViewCodes;

/**
 * @copyright Copyright © 2024 TechDivision GmbH <info@techdivision.com> - TechDivision GmbH
 * @link      http://www.techdivision.com/
 * @author    MET <met@techdivision.com>
 */
class CategoryPathValidatorCallback extends IndexedArrayValidatorCallback
{

    /**
     * Will be invoked by a observer it has been registered for.
     *
     * @param string|null $attributeCode  The code of the attribute that has to be validated
     * @param string|null $attributeValue The attribute value to be validated
     *
     * @return mixed The modified value
     */
    public function handle($attributeCode = null, $attributeValue = null)
    {
        $subject = $this->getSubject();

        // Prepare the store view code
        $subject->prepareStoreViewCode();

        // Explode the path of the category
        $categoryNames = $this->getSubject()->explode($attributeValue, "/");
        $categoryName = end($categoryNames);

        // Get store view code value
        $storeViewCodeValue = $subject->getStoreViewCode(StoreViewCodes::ADMIN);

        // Check if store view code is admin
        if ($storeViewCodeValue === StoreViewCodes::ADMIN) {
            // Get category name attribute
            $attributeCategoryName = $subject->getValue(ColumnKeys::NAME);

            // Check if category name matches the attribute value (path)
            if ($categoryName === $attributeCategoryName) {
                $message = sprintf(
                    'Value "%s" for column "%s" (matches category name: "%s")',
                    $attributeValue,
                    $attributeCode,
                    $categoryName
                );
                $subject->getSystemLogger()->info($message);
            } else {
                $message = sprintf(
                    'Found invalid value "%s" for column "%s" (must match category name: "%s")',
                    $attributeValue,
                    $attributeCode,
                    $attributeCategoryName
                );

                // Check if strict mode is enabled
                if ($this->hasHandleStrictMode($attributeCode, $message)) {
                    return;
                }

                // Throw an exception for invalid value
                throw new \InvalidArgumentException($message);
            }
        }
    }
}
