<?php

/**
 * TechDivision\Import\Category\Observers\CategoryUrlRewriteObserver
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

/**
 * Observer that extracts the URL rewrite data to a specific CSV file.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */
class CategoryUrlRewriteObserver extends AbstractCategoryImportObserver
{

    /**
     * The artefact type.
     *
     * @var string
     */
    const ARTEFACT_TYPE = 'category-url-rewrite';

    /**
     * The image artefacts that has to be exported.
     *
     * @var array
     */
    protected $artefacts = array();

    /**
     * Process the observer's business logic.
     *
     * @return array The processed row
     * @todo See PAC-307
     */
    protected function process()
    {

        // do nothing if the column `url_key` is empty
        if ($this->hasValue(ColumnKeys::URL_KEY) === false) {
            return;
        }

        // initialize the array for the artefacts and the store view codes
        $this->artefacts = array();
        $storeViewCodes = array();

        // load the category path from the row
        $path = $this->getValue(ColumnKeys::PATH);

        // prepare the store view code
        $this->getSubject()->prepareStoreViewCode();

        // try to load the store view code
        $storeViewCode = $this->getSubject()->getStoreViewCode(StoreViewCodes::ADMIN);

        // query whether or not we've a store view code
        if ($storeViewCode === StoreViewCodes::ADMIN) {
            // if not, load the available store view codes for the root category of the given path
            $storeViewCodes = $this->getRootCategoryStoreViewCodes($this->getValue(ColumnKeys::PATH));
        } else {
            array_push($storeViewCodes, $storeViewCode);
        }

        // iterate over the available store view codes
        foreach ($storeViewCodes as $storeViewCode) {
            // do not export URL rewrites for the admin store
            if ($storeViewCode === StoreViewCodes::ADMIN) {
                continue;
            }

            // iterate over the store view codes and query if artefacts are already available
            if ($this->hasArtefactsByTypeAndEntityId(CategoryUrlRewriteObserver::ARTEFACT_TYPE, $lastEntityId = $this->getSubject()->getLastEntityId())) {
                // if yes, load the artefacs
                $this->artefacts = $this->getArtefactsByTypeAndEntityId(CategoryUrlRewriteObserver::ARTEFACT_TYPE, $lastEntityId);

                // override the existing data with the store view specific one
                for ($i = 0; $i < sizeof($this->artefacts); $i++) {
                    // query whether or not a URL path has be specfied and the store view codes are equal
                    if ($this->hasValue(ColumnKeys::URL_KEY) && $this->artefacts[$i][ColumnKeys::STORE_VIEW_CODE] === $storeViewCode) {
                        // update the URL path
                        $this->artefacts[$i][ColumnKeys::URL_PATH] = $this->getValue(ColumnKeys::URL_PATH);

                        // also update filename and line number
                        $this->artefacts[$i][ColumnKeys::ORIGINAL_DATA][ColumnKeys::ORIGINAL_FILENAME] = $this->getSubject()->getFilename();
                        $this->artefacts[$i][ColumnKeys::ORIGINAL_DATA][ColumnKeys::ORIGINAL_LINE_NUMBER] = $this->getSubject()->getLineNumber();
                    }
                }
            } else {
                // if no arefacts are available, append new data
                $artefact = $this->newArtefact(
                    array(
                        ColumnKeys::PATH               => $path,
                        ColumnKeys::STORE_VIEW_CODE    => $storeViewCode,
                        ColumnKeys::URL_PATH           => $this->getValue(ColumnKeys::URL_PATH)
                    ),
                    array(
                        ColumnKeys::PATH               => ColumnKeys::PATH,
                        ColumnKeys::STORE_VIEW_CODE    => ColumnKeys::STORE_VIEW_CODE,
                        ColumnKeys::URL_PATH           => ColumnKeys::URL_PATH
                    )
                );

                // append the base image to the artefacts
                $this->artefacts[] = $artefact;
            }
        }

        // append the artefacts that has to be exported to the subject
        $this->addArtefacts($this->artefacts);
    }

    /**
     * Returns the store view codes relevant to the category represented by the current row.
     *
     * @param string $path The path to return the root category's store view codes for
     *
     * @return array The store view codes for the given root category
     * @throws \Exception Is thrown, if the root category of the passed path is NOT available
     */
    protected function getRootCategoryStoreViewCodes($path)
    {
        return $this->getSubject()->getRootCategoryStoreViewCodes($path);
    }

    /**
     * Queries whether or not artefacts for the passed type and entity ID are available.
     *
     * @param string $type     The artefact type, e. g. configurable
     * @param string $entityId The entity ID to return the artefacts for
     *
     * @return boolean TRUE if artefacts are available, else FALSE
     */
    protected function hasArtefactsByTypeAndEntityId($type, $entityId)
    {
        return $this->getSubject()->hasArtefactsByTypeAndEntityId($type, $entityId);
    }

    /**
     * Return the artefacts for the passed type and entity ID.
     *
     * @param string $type     The artefact type, e. g. configurable
     * @param string $entityId The entity ID to return the artefacts for
     *
     * @return array The array with the artefacts
     * @throws \Exception Is thrown, if no artefacts are available
     */
    protected function getArtefactsByTypeAndEntityId($type, $entityId)
    {
        return $this->getSubject()->getArtefactsByTypeAndEntityId($type, $entityId);
    }

    /**
     * Create's and return's a new empty artefact entity.
     *
     * @param array $columns             The array with the column data
     * @param array $originalColumnNames The array with a mapping from the old to the new column names
     *
     * @return array The new artefact entity
     */
    protected function newArtefact(array $columns, array $originalColumnNames)
    {
        return $this->getSubject()->newArtefact($columns, $originalColumnNames);
    }

    /**
     * Add the passed product type artefacts to the product with the
     * last entity ID.
     *
     * @param array $artefacts The product type artefacts
     *
     * @return void
     * @uses \TechDivision\Import\Product\Media\Subjects\MediaSubject::getLastEntityId()
     */
    protected function addArtefacts(array $artefacts)
    {
        $this->getSubject()->addArtefacts(CategoryUrlRewriteObserver::ARTEFACT_TYPE, $artefacts);
    }
}
