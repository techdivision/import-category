<?php

/**
 * TechDivision\Import\Category\Observers\ClearCategoryObserver
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

use TechDivision\Import\Category\Utils\ColumnKeys;
use TechDivision\Import\Category\Utils\MemberNames;
use TechDivision\Import\Category\Utils\SqlStatementKeys;
use TechDivision\Import\Category\Services\CategoryBunchProcessorInterface;

/**
 * Observer that removes the category with the path found in the CSV file.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */
class ClearCategoryObserver extends AbstractCategoryImportObserver
{

    /**
     * The processor to read/write the necessary category data.
     *
     * @var \TechDivision\Import\Category\Services\CategoryBunchProcessorInterface
     */
    protected $categoryBunchProcessor;

    /**
     * Initialize the subject instance.
     *
     * @param \TechDivision\Import\Category\Services\CategoryBunchProcessorInterface $categoryBunchProcessor The category bunch processor instance
     */
    public function __construct(CategoryBunchProcessorInterface $categoryBunchProcessor)
    {
        $this->categoryBunchProcessor = $categoryBunchProcessor;
    }

    /**
     * Return's the category bunch processor instance.
     *
     * @return \TechDivision\Import\Category\Services\CategoryBunchProcessorInterface The category bunch processor instance
     */
    protected function getCategoryBunchProcessor()
    {
        return $this->categoryBunchProcessor;
    }

    /**
     * Process the observer's business logic.
     *
     * @return array The processed row
     */
    protected function process()
    {

        // query whether or not, we've found a new path => means we've found a new category
        if ($this->hasBeenProcessed($path = $this->getValue(ColumnKeys::PATH))) {
            return;
        }

        // load the category by it's path
        $category = $this->loadCategory($this->mapPath($path));

        // FIRST delete the data related with the category with the passed category path
        $this->deleteUrlRewrite(array(ColumnKeys::PATH => $category[MemberNames::PATH]), SqlStatementKeys::DELETE_URL_REWRITE_BY_PATH);
        $this->deleteUrlRewrite(array(MemberNames::CATEGORY_ID => $category[$this->getPkMemberName()]), SqlStatementKeys::DELETE_URL_REWRITE_BY_CATEGORY_ID);

        // delete the category with the passed path
        $this->deleteCategory(array(ColumnKeys::PATH => $category[MemberNames::PATH]));

        // remove the path => entity ID mapping from the subject
        $this->removePathEntityIdMapping($path);

        // store the ID of the last deleted category
        $this->setLastEntityId($category[$this->getPkMemberName()]);
    }

    /**
     * Return the primary key member name.
     *
     * @return string The primary key member name
     */
    protected function getPkMemberName()
    {
        return MemberNames::ENTITY_ID;
    }

    /**
     * Queries whether or not the path has already been processed.
     *
     * ATTENTION: This INVERTS the parent method, as the category
     *            has been processed as it is still available.
     *
     * @param string $path The path to check
     *
     * @return boolean TRUE if the path has been processed, else FALSE
     */
    protected function hasBeenProcessed($path)
    {
        return !parent::hasBeenProcessed($path);
    }

    /**
     * Set's the ID of the product that has been created recently.
     *
     * @param string $lastEntityId The entity ID
     *
     * @return void
     */
    protected function setLastEntityId($lastEntityId)
    {
        $this->getSubject()->setLastEntityId($lastEntityId);
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
        $this->getSubject()->addArtefacts(ClearCategoryObserver::ARTEFACT_TYPE, $artefacts, false);
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
        return $this->getCategoryBunchProcessor()->loadCategory($id);
    }

    /**
     * Return's the category with the passed path.
     *
     * @param string $path The path of the category to return
     *
     * @return array The category
     * @deprecated Since 7.0.0
     */
    protected function getCategoryByPath($path)
    {
        return $this->getSubject()->getCategoryByPath($path);
    }

    /**
     * Delete's the URL rewrite(s) with the passed attributes.
     *
     * @param array       $row  The attributes of the entity to delete
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    protected function deleteUrlRewrite($row, $name = null)
    {
        $this->getCategoryBunchProcessor()->deleteUrlRewrite($row, $name);
    }

    /**
     * Delete's the entity with the passed attributes.
     *
     * @param array       $row  The attributes of the entity to delete
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    protected function deleteCategory($row, $name = null)
    {
        $this->getCategoryBunchProcessor()->deleteCategory($row, $name);
    }
}
