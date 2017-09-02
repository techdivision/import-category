<?php

/**
 * TechDivision\Import\Category\Observers\FileUploadObserver
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

namespace TechDivision\Import\Category\Observers;

use TechDivision\Import\Category\Utils\ColumnKeys;

/**
 * Observer that uploads the category image file specified in a CSV file to a
 * configurable directory.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */
class FileUploadObserver extends AbstractCategoryImportObserver
{

    /**
     * Process the observer's business logic.
     *
     * @return array The processed row
     */
    protected function process()
    {

        // query whether or not we've to upload the image files
        if ($this->getSubject()->hasCopyImages()) {
            // initialize the image path
            if ($imagePath = $this->getValue(ColumnKeys::IMAGE_PATH)) {
                // upload the file and set the new image path
                $imagePath = $this->getSubject()->uploadFile($imagePath);

                // log a message that the image has been copied
                $this->getSubject()->getSystemLogger()->debug(
                    sprintf('Successfully copied image %s => %s', $imagePath, $imagePath)
                );

                // write the new image path to the target column
                $this->setValue(ColumnKeys::IMAGE_PATH, $imagePath);
            }
        }
    }
}
