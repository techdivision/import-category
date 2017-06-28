<?php

/**
 * TechDivision\Import\Category\Plugins\ChildrenCountPlugin
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

namespace TechDivision\Import\Category\Plugins;

use TechDivision\Import\ApplicationInterface;
use TechDivision\Import\Utils\EntityStatus;
use TechDivision\Import\Plugins\AbstractPlugin;
use TechDivision\Import\Category\Utils\MemberNames;
use TechDivision\Import\Category\Services\CategoryProcessorInterface;

/**
 * Plugin that updates the categories children count attribute after a successfull category import.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */
class ChildrenCountPlugin extends AbstractPlugin
{

    /**
     * The category processor instance.
     *
     * @var \TechDivision\Import\Category\Services\CategoryProcessorInterface
     */
    protected $processor;

    /**
     * Initializes the plugin with the application instance.
     *
     * @param \TechDivision\Import\ApplicationInterface                         $application The application instance
     * @param \TechDivision\Import\Category\Services\CategoryProcessorInterface $processor   The category processor instance
     */
    public function __construct(ApplicationInterface $application, CategoryProcessorInterface $processor)
    {

        // call the parent constructor
        parent::__construct($application);

        // set the passed processor instance
        $this->processor = $processor;
    }

    /**
     * Process the plugin functionality.
     *
     * @return void
     * @throws \Exception Is thrown, if the plugin can not be processed
     */
    public function process()
    {

        // load all available categories
        $categories = $this->getImportProcessor()->getCategories();

        // update the categories children count
        foreach ($categories as $category) {
            // load the category itself
            $this->category = $this->loadCategory($pk = $this->getPrimaryKey($category));

            // update the category's children count
            $this->persistCategory($this->initializeCategory($this->prepareAttributes()));

            // write a log message
            $this->getSystemLogger()->debug(
                sprintf('Successfully updated category with primary key %d', $pk)
            );
        }
    }

    /**
     * Prepare the attributes of the entity that has to be persisted.
     *
     * @return array The prepared attributes
     */
    protected function prepareAttributes()
    {

        // initialize the values that has to be updated
        $updatedAt = date('Y-m-d H:i:s');
        $childrenCount = $this->loadCategoryChildrenChildrenCount($this->category[MemberNames::PATH] . '/%');

        // prepare and return the array with the updated values
        return array(
            MemberNames::UPDATED_AT     => $updatedAt,
            MemberNames::CHILDREN_COUNT => $childrenCount
        );
    }

    /**
     * Initialize the category with the passed attributes and returns an instance.
     *
     * @param array $attr The category attributes
     *
     * @return array The initialized category
     */
    protected function initializeCategory(array $attr)
    {
        return $this->mergeEntity(
            $this->category,
            $attr
        );
    }

    /**
     * Return's the primary key of the passed category.
     *
     * @param array $category The category to return the primary key for
     *
     * @return integer The primary key of the category
     */
    protected function getPrimaryKey(array $category)
    {
        return $category[MemberNames::ENTITY_ID];
    }

    /**
     * Return's the configured processor instance.
     *
     * @return object The processor instance
     * @throws \Exception Is thrown, if no processor factory has been configured
     */
    protected function getProcessor()
    {
        return $this->processor;
    }

    /**
     * Return's the category with the passed ID.
     *
     * @param string $id The ID of the category to return
     *
     * @return array The category
     */
    protected function loadCategory($id)
    {
        return $this->getProcessor()->loadCategory($id);
    }

    /**
     * Return's the children count of the category with the passed path.
     *
     * @param string $path The path of the category to count the children for
     *
     * @return integer The children count of the category with the passed path
     */
    protected function loadCategoryChildrenChildrenCount($path)
    {
        return $this->getProcessor()->loadCategoryChildrenChildrenCount($path);
    }

    /**
     * Persist's the passed category data and return's the ID.
     *
     * @param array $category The category data to persist
     *
     * @return string The ID of the persisted entity
     */
    protected function persistCategory($category)
    {
        return $this->getProcessor()->persistCategory($category);
    }

    /**
     * Merge's and return's the entity with the passed attributes and set's the
     * status to 'update'.
     *
     * @param array $entity The entity to merge the attributes into
     * @param array $attr   The attributes to be merged
     *
     * @return array The merged entity
     */
    protected function mergeEntity(array $entity, array $attr)
    {
        return array_merge($entity, $attr, array(EntityStatus::MEMBER_NAME => EntityStatus::STATUS_UPDATE));
    }
}
