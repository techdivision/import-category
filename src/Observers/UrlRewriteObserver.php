<?php

/**
 * TechDivision\Import\Category\Observers\UrlRewriteObserver
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
use TechDivision\Import\Category\Utils\CoreConfigDataKeys;
use TechDivision\Import\Category\Services\CategoryUrlRewriteProcessorInterface;

/**
 * Observer that creates/updates the category's URL rewrites.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */
class UrlRewriteObserver extends AbstractCategoryImportObserver
{

    /**
     * The entity type to load the URL rewrites for.
     *
     * @var string
     */
    const ENTITY_TYPE = 'category';

    /**
     * The processor to read/write the necessary category data.
     *
     * @var \TechDivision\Import\Category\Services\CategoryUrlRewriteProcessorInterface
     */
    protected $categoryUrlRewriteProcessor;

    /**
     * Initialize the subject instance.
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
     * @return void
     */
    protected function process()
    {

        // load the path of the category we want to create the URL rewrites for
        $path = $this->getValue(ColumnKeys::PATH);

        // try to load the entity ID for the product with the passed SKU
        if ($category = $this->loadCategory($this->mapPath($path))) {
            $this->setLastEntityId($category[MemberNames::ENTITY_ID]);
        } else {
            // prepare a log message
            $message = sprintf('Category with path "%s" can\'t be loaded to create URL rewrites', $path);
            // query whether or not we're in debug mode
            if ($this->getSubject()->isDebugMode()) {
                $this->getSubject()->getSystemLogger()->warning($message);
                return;
            } else {
                throw new \Exception($message);
            }
        }

        // initialize the store view code
        $this->getSubject()->prepareStoreViewCode();

        // prepare the URL rewrites
        $this->prepareUrlRewrites();

        // initialize and persist the URL rewrite
        if ($urlRewrite = $this->initializeUrlRewrite($this->prepareAttributes())) {
            $this->persistUrlRewrite($urlRewrite);
        }
    }

    /**
     * Initialize the URL rewrite with the passed attributes and returns an instance.
     *
     * @param array $attr The URL rewrite attributes
     *
     * @return array The initialized URL rewrite
     */
    protected function initializeUrlRewrite(array $attr)
    {
        return $attr;
    }

    /**
     * Prepare's the URL rewrites that has to be created/updated.
     *
     * @return void
     */
    protected function prepareUrlRewrites()
    {
        // nothing to prepare here
    }

    /**
     * Prepare the attributes of the entity that has to be persisted.
     *
     * @return array The prepared attributes
     */
    protected function prepareAttributes()
    {

        // load the store ID to use
        $storeId = $this->getRowStoreId();

        // initialize the values
        $entityId = $this->getSubject()->getLastEntityId();
        $requestPath = $this->prepareRequestPath();
        $targetPath = $this->prepareTargetPath();

        // return the prepared URL rewrite
        return $this->initializeEntity(
            array(
                MemberNames::ENTITY_TYPE      => UrlRewriteObserver::ENTITY_TYPE,
                MemberNames::ENTITY_ID        => $entityId,
                MemberNames::REQUEST_PATH     => $requestPath,
                MemberNames::TARGET_PATH      => $targetPath,
                MemberNames::REDIRECT_TYPE    => 0,
                MemberNames::STORE_ID         => $storeId,
                MemberNames::DESCRIPTION      => null,
                MemberNames::IS_AUTOGENERATED => 1,
                MemberNames::METADATA         => null
            )
        );
    }

    /**
     * Prepare's the target path for a URL rewrite.
     *
     * @return string The target path
     */
    protected function prepareTargetPath()
    {
        return sprintf('catalog/category/view/id/%d', $this->getPrimaryKey());
    }

    /**
     * Prepare's the request path for a URL rewrite or the target path for a 301 redirect.
     *
     * @return string The request path
     */
    protected function prepareRequestPath()
    {

        // load the category URL suffix to use
        $urlSuffix = $this->getSubject()->getCoreConfigData(CoreConfigDataKeys::CATALOG_SEO_CATEGORY_URL_SUFFIX, '.html');

        // prepare and return the category URL
        return sprintf('%s%s', $this->getValue(ColumnKeys::URL_PATH), $urlSuffix);
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
     * Return's the store ID of the actual row, or of the default store
     * if no store view code is set in the CSV file.
     *
     * @param string|null $default The default store view code to use, if no store view code is set in the CSV file
     *
     * @return integer The ID of the actual store
     * @throws \Exception Is thrown, if the store with the actual code is not available
     */
    protected function getRowStoreId($default = null)
    {
        return $this->getSubject()->getRowStoreId($default);
    }

    /**
     * Persist's the URL rewrite with the passed data.
     *
     * @param array $row The URL rewrite to persist
     *
     * @return string The ID of the persisted entity
     */
    protected function persistUrlRewrite($row)
    {
        return $this->getCategoryUrlRewriteProcessor()->persistUrlRewrite($row);
    }

    /**
     * Return's the PK to create the product => attribute relation.
     *
     * @return integer The PK to create the relation with
     */
    protected function getPrimaryKey()
    {
        return $this->getSubject()->getLastEntityId();
    }
}
