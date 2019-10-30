<?php

/**
 * TechDivision\Import\Category\Listeners\SortCategoryListener
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

namespace TechDivision\Import\Category\Listeners;

use League\Event\EventInterface;
use League\Event\AbstractListener;
use TechDivision\Import\Subjects\SubjectInterface;
use TechDivision\Import\Services\RegistryProcessorInterface;
use TechDivision\Import\Category\Observers\CopyCategoryObserver;
use TechDivision\Import\Category\Utils\ColumnKeys;

/**
 * A listener implementation that sorts categories by their path.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */
class SortCategoryListener extends AbstractListener
{

    /**
     * The registry processor instance.
     *
     * @var \TechDivision\Import\Services\RegistryProcessorInterface
     */
    protected $registryProcessor;

    /**
     * Initializes the listener with the registry processor instance.
     *
     * @param \TechDivision\Import\Services\RegistryProcessorInterface $registryProcessor The registry processor instance
     */
    public function __construct(RegistryProcessorInterface $registryProcessor)
    {
        $this->registryProcessor = $registryProcessor;
    }

    /**
     * Handle an event.
     *
     * @param \League\Event\EventInterface                   $event   The event that triggered the event
     * @param \TechDivision\Import\Subjects\SubjectInterface $subject The subject instance
     *
     * @return void
     */
    public function handle(EventInterface $event, SubjectInterface $subject = null)
    {

        // load the artefacts from the subject
        $artefacts = $subject->getArtefacts();

        // query whether or not the artefacts with the given type are available
        if (isset($artefacts[$type = CopyCategoryObserver::ARTEFACT_TYPE])) {
            // load the artefacts for the given type
            $categories = array_shift($artefacts[CopyCategoryObserver::ARTEFACT_TYPE]);

            // sort them by the path + store_view_code
            usort($categories, function ($a, $b) {
                return
                    strcmp($a[ColumnKeys::PATH], $b[ColumnKeys::PATH]) ?:
                    strcmp($a[ColumnKeys::STORE_VIEW_CODE], $b[ColumnKeys::STORE_VIEW_CODE]);
            });

            // replace the artefacts to be exported later
            $subject->setArtefactsByType($type, array($categories));
        }
    }
}
