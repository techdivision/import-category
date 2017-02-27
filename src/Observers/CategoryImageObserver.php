<?php

/**
 * TechDivision\Import\Category\Observers\CategoryImageObserver
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
 * Observer that extracts the category's image from a CSV file to be added to image specific CSV file.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */
class CategoryImageObserver extends AbstractCategoryImportObserver
{

    /**
     * The artefact type.
     *
     * @var string
     */
    const ARTEFACT_TYPE = 'media';

    /**
     * Process the observer's business logic.
     *
     * @return array The processed row
     */
    protected function process()
    {

        // initialize the array for the category images
        $artefacts = array();

        // load the store view code
        $storeViewCode = $this->getValue(ColumnKeys::STORE_VIEW_CODE);

        // query whether or not, we've a image
        if ($imagePath = $this->getValue(ColumnKeys::IMAGE)) {
            // prepare and append the image to the artefacts
            $artefacts[] = array(
                ColumnKeys::STORE_VIEW_CODE  => $storeViewCode,
                ColumnKeys::IMAGE_PATH       => $imagePath,
                ColumnKeys::IMAGE_PATH_NEW   => $imagePath
            );
        }

        // append the images to the subject
        $this->addArtefacts($artefacts);
    }

    /**
     * Add the passed category image artefacts to the category with the
     * last entity ID.
     *
     * @param array $artefacts The category image artefacts
     *
     * @return void
     */
    protected function addArtefacts(array $artefacts)
    {
        $this->getSubject()->addArtefacts(CategoryImageObserver::ARTEFACT_TYPE, $artefacts);
    }
}
