<?php

/**
 * TechDivision\Import\Category\Observers\CleanUpObserver
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Category\Observers;

use TechDivision\Import\Utils\StoreViewCodes;
use TechDivision\Import\Category\Utils\ColumnKeys;

/**
 * Clean-Up after importing the row.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
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
        $this->addPathStoreViewCodeMapping($path, $this->getSubject()->getStoreViewCode(StoreViewCodes::ADMIN));

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
