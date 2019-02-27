<?php

/**
 * TechDivision\Import\Category\Observers\CopyCategoryObserver
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

/**
 * Observer that copy's the actual line into a new CSV file for further processing.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */
class CopyCategoryObserver extends AbstractCategoryImportObserver
{

    /**
     * The artefact type.
     *
     * @var string
     */
    const ARTEFACT_TYPE = 'category-create';

    /**
     * Process the observer's business logic.
     *
     * @return array The processed row
     */
    protected function process()
    {

        // initialize the array for the artefacts
        $artefacts = array();

        // initialize the artefacts
        $artefact = array();
        foreach (array_keys($this->getSubject()->getHeaders()) as $columnName) {
            $artefact[$columnName] = $this->getValue($columnName);
        }

        // add the artefacts to the subject
        array_push($artefacts, $artefact);
        $this->addArtefacts($artefacts);
    }

    /**
     * Add the passed category artefacts to the category with the
     * last entity ID.
     *
     * @param array $artefacts The category artefacts
     *
     * @return void
     * @uses \TechDivision\Import\Category\BunchSubject::getLastEntityId()
     */
    protected function addArtefacts(array $artefacts)
    {
        $this->getSubject()->addArtefacts(CopyCategoryObserver::ARTEFACT_TYPE, $artefacts, false);
    }
}
