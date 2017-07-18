<?php

/**
 * TechDivision\Import\Category\Observers\AbstractCategoryImportObserver
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

use TechDivision\Import\Observers\AbstractObserver;
use TechDivision\Import\Subjects\SubjectInterface;

/**
 * Abstract category observer that handles the process to import category bunches.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */
abstract class AbstractCategoryImportObserver extends AbstractObserver implements CategoryImportObserverInterface
{

    /**
     * Will be invoked by the action on the events the listener has been registered for.
     *
     * @param \TechDivision\Import\Subjects\SubjectInterface $subject The subject instance
     *
     * @return array The modified row
     * @see \TechDivision\Import\Observers\ObserverInterface::handle()
     */
    public function handle(SubjectInterface $subject)
    {

        // initialize the row
        $this->setSubject($subject);
        $this->setRow($subject->getRow());

        // process the functionality and return the row
        $this->process();

        // return the processed row
        return $this->getRow();
    }

    /**
     * Process the observer's business logic.
     *
     * @return void
     */
    abstract protected function process();

    /**
     * Queries whether or not the path has already been processed.
     *
     * @param string $path The path to check
     *
     * @return boolean TRUE if the path has been processed, else FALSE
     */
    protected function hasBeenProcessed($path)
    {
        return $this->getSubject()->hasBeenProcessed($path);
    }

    /**
     * Return's TRUE if the passed path is the actual one.
     *
     * @param string $path The path to check
     *
     * @return boolean TRUE if the passed path is the actual one
     */
    protected function isLastPath($path)
    {
        return $this->getSubject()->getLastPath() === $path;
    }

    /**
     * Add the passed path => entity ID mapping.
     *
     * @param string $path The path
     *
     * @return void
     */
    protected function addPathEntityIdMapping($path)
    {
        $this->getSubject()->addPathEntityIdMapping($path);
    }

    /**
     * Add the passed path => store view code mapping.
     *
     * @param string $path          The path
     * @param string $storeViewCode The store view code
     *
     * @return void
     */
    protected function addPathStoreViewCodeMapping($path, $storeViewCode)
    {
        $this->getSubject()->addPathStoreViewCodeMapping($path, $storeViewCode);
    }
}
