<?php

/**
 * TechDivision\Import\Category\Callbacks\PageLayoutCallback
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Category\Callbacks;

use TechDivision\Import\Observers\AttributeCodeAndValueAwareObserverInterface;

/**
 * A callback implementation that converts the passed page layout.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */
class PageLayoutCallback extends AbstractCategoryImportCallback
{

    /**
     * Will be invoked by a observer it has been registered for.
     *
     * @param \TechDivision\Import\Observers\AttributeCodeAndValueAwareObserverInterface|null $observer The observer
     *
     * @return mixed The modified value
     */
    public function handle(AttributeCodeAndValueAwareObserverInterface $observer = null)
    {

        // set the observer
        $this->setObserver($observer);

        try {
            // replace the passed attribute value into the page layout
            return $this->getSubject()->getPageLayoutByValue($observer->getAttributeValue());
        } catch (\Exception $e) {
            $this->getSystemLogger()->info($e->getMessage());
        }
    }
}
