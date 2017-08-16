<?php

/**
 * TechDivision\Import\Category\Observers\CleanUpObserver
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
 * Clean-Up after importing the row.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */
class CleanUpObserver extends AbstractCategoryImportObserver
{

    /**
     * Process the observer's business logic.
     *
     * @return array The processed row
     */
    protected function process()
    {

        // add the path => entity ID/store view code mapping
        $this->addPathEntityIdMapping($path = $this->getValue(ColumnKeys::PATH));
        $this->addPathStoreViewCodeMapping($path, $this->getSubject()->getStoreViewCode());

        // temporary persist the path
        $this->setLastPath($path);
    }

    /**
     * Set's the path of the last imported category.
     *
     * @param string $lastPath The path
     *
     * @return void
     */
    protected function setLastPath($lastPath)
    {
        $this->getSubject()->setLastPath($lastPath);
    }
}
