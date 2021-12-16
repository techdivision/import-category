<?php

/**
 * TechDivision\Import\Category\Observers\ClearUrlRewriteObserver
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Category\Observers;

use TechDivision\Import\Category\Utils\ColumnKeys;
use TechDivision\Import\Category\Utils\MemberNames;
use TechDivision\Import\Category\Utils\SqlStatementKeys;
use TechDivision\Import\Category\Services\CategoryUrlRewriteProcessorInterface;

/**
 * Observer that removes the URL rewrite for the category with the path found in the CSV file.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */
class ClearUrlRewriteObserver extends AbstractCategoryImportObserver
{

    /**
     * The product URL rewrite processor instance.
     *
     * @var \TechDivision\Import\Category\Services\CategoryUrlRewriteProcessorInterface
     */
    protected $categoryUrlRewriteProcessor;

    /**
     * Initialize the observer with the passed category URL rewrite processor instance.
     *
     * @param \TechDivision\Import\Category\Services\CategoryUrlRewriteProcessorInterface $categoryUrlRewriteProcessor The category URL rewrite processor instance
     */
    public function __construct(CategoryUrlRewriteProcessorInterface $categoryUrlRewriteProcessor)
    {
        $this->categoryUrlRewriteProcessor = $categoryUrlRewriteProcessor;
    }

    /**
     * Return's the category URL rewrite processor instance.
     *
     * @return \TechDivision\Import\Category\Services\CategoryUrlRewriteProcessorInterface The category URL rewrite processor instance
     */
    protected function getCategoryUrlRewriteProcessor()
    {
        return $this->categoryUrlRewriteProcessor;
    }

    /**
     * Process the observer's business logic.
     *
     * @return array The processed row
     */
    protected function process()
    {

        // query whether or not, we've found a new SKU => means we've found a new product
        if ($this->isLastPath($path = $this->getValue(ColumnKeys::PATH))) {
            return;
        }

        try {
            // try to load the entity ID for the product with the passed path
            $category = $this->loadCategory($this->mapPath($path));
            if (!$category) {
                // TODO Logmessage ? Maybe a child who was previously deleted
                return;
            }
            // Load all category children
            $categories = $this->loadCategoriesByPath($category[MemberNames::PATH]);

            // First delete category children
            foreach ($categories as $categoryChild) {
                $this->deleteCategory($categoryChild);
            }

            // second delete origin category
            $this->deleteCategory($category);
        } catch (\Exception $e) {
            $this->getSubject()
                 ->getSystemLogger()
                 ->debug(sprintf('Category with path "%s" can\'t be loaded to clear URL rewrites, reason: "%s"', $path, $e->getMessage()));
        }
    }

    /**
     * Delete url rewrites from Category
     *
     * @param array $category $category to delete the url rewrites
     * @return void
     */
    protected function deleteCategory(array $category)
    {
        // delete all URL rewrites for products with passed categorie entity ID
        $this->deleteUrlRewrite(array(MemberNames::CATEGORY_ID => $category[MemberNames::ENTITY_ID]), SqlStatementKeys::DELETE_URL_REWRITE_BY_CATEGORY_ID);
        // delete the URL rewrites of the category with the passed categorie entity ID
        $this->deleteUrlRewrite(
            array(
                MemberNames::ENTITY_ID   => $category[MemberNames::ENTITY_ID],
                MemberNames::ENTITY_TYPE => 'category'
            ),
            SqlStatementKeys::DELETE_URL_REWRITE_BY_ENTITY_ID_AND_ENTITY_TYPE
        );
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
        return $this->getCategoryUrlRewriteProcessor()->loadCategory($id);
    }

    /**
     * Return's the category with the passed ID.
     *
     * @param string $path The ID of the category to return
     *
     * @return array The category
     */
    protected function loadCategoriesByPath($path)
    {
        return $this->getCategoryUrlRewriteProcessor()->loadCategoriesByPath($path);
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
        $this->getCategoryUrlRewriteProcessor()->deleteUrlRewrite($row, $name);
    }
}
