<?php

/**
 * TechDivision\Import\Category\Observers\UrlRewriteUpdateObserver
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

use TechDivision\Import\Category\Utils\MemberNames;
use TechDivision\Import\Category\Utils\CoreConfigDataKeys;
use TechDivision\Import\Category\Utils\ColumnKeys;
use TechDivision\Import\Category\Utils\ConfigurationKeys;

/**
 * Observer that creates/updates the category's URL rewrites.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */
class UrlRewriteUpdateObserver extends UrlRewriteObserver
{

    /**
     * Array with the existing URL rewrites of the actual category.
     *
     * @var array
     */
    protected $existingUrlRewrites = array();

    /**
     * Return's the URL rewrite for the passed request path.
     *
     * @param string $requestPath The request path to return the URL rewrite for
     *
     * @return array|null The URL rewrite
     */
    protected function getExistingUrlRewrite($requestPath)
    {
        if (isset($this->existingUrlRewrites[$requestPath])) {
            return $this->existingUrlRewrites[$requestPath];
        }
    }

    /**
     * Remove's the passed URL rewrite from the existing one's.
     *
     * @param array $urlRewrite The URL rewrite to remove
     *
     * @return void
     */
    protected function removeExistingUrlRewrite(array $urlRewrite)
    {

        // load store ID and request path
        $requestPath = $urlRewrite[MemberNames::REQUEST_PATH];

        // query whether or not the URL rewrite exists and remove it, if available
        if (isset($this->existingUrlRewrites[$requestPath])) {
            unset($this->existingUrlRewrites[$requestPath]);
        }
    }

    /**
     * Process the observer's business logic.
     *
     * @return void
     */
    protected function process()
    {

        // process the new URL rewrites first
        parent::process();

        // create redirect URL rewrites for the existing URL rewrites
        foreach ($this->existingUrlRewrites as $existingUrlRewrite) {
            // query whether or not 301 redirects have to be created, so don't
            // create redirects if the the rewrite history has been deactivated
            if ($this->getSubject()->getCoreConfigData(CoreConfigDataKeys::CATALOG_SEO_SAVE_REWRITES_HISTORY, true)) {

                // load target path
                $targetPath = $this->prepareRequestPath();

                // skip update of URL rewrite, if nothing to change
                if ($targetPath === $existingUrlRewrite[MemberNames::TARGET_PATH] &&
                    301 === (int)$existingUrlRewrite[MemberNames::REDIRECT_TYPE]) {
                    // stop processing the URL rewrite
                    continue;
                }

                // override data with the 301 configuration
                $attr = array(
                    MemberNames::REDIRECT_TYPE    => 301,
                    MemberNames::TARGET_PATH      => $targetPath,
                    MemberNames::IS_AUTOGENERATED => 0
                );

                // merge and return the prepared URL rewrite
                $existingUrlRewrite = $this->mergeEntity($existingUrlRewrite, $attr);

                // create the URL rewrite
                $this->persistUrlRewrite($existingUrlRewrite);
            } else {
                // query whether or not the URL rewrite has to be removed
                if ($this->getSubject()->getConfiguration()->hasParam(ConfigurationKeys::CLEAN_UP_URL_REWRITES) &&
                    $this->getSubject()->getConfiguration()->getParam(ConfigurationKeys::CLEAN_UP_URL_REWRITES)
                ) {
                    // delete the existing URL rewrite
                    $this->deleteUrlRewrite($existingUrlRewrite);

                    // log a message, that old URL rewrites have been cleaned-up
                    $this->getSubject()
                         ->getSystemLogger()
                         ->warning(
                             sprintf(
                                 'Cleaned-up %d URL rewrite "%s" for category with path "%s"',
                                 $existingUrlRewrite[MemberNames::REQUEST_PATH],
                                 $this->getValue(ColumnKeys::PATH)
                             )
                         );
                }
            }
        }
    }

    /**
     * Prepare's the URL rewrites that has to be created/updated.
     *
     * @return void
     */
    protected function prepareUrlRewrites()
    {

        // call the parent method
        parent::prepareUrlRewrites();

        // (re-)initialize the array for the existing URL rewrites
        $this->existingUrlRewrites = array();

        // load primary key and entity type
        $pk = $this->getPrimaryKey();
        $entityType = UrlRewriteObserver::ENTITY_TYPE;

        // load the store ID to use
        $storeId = $this->getSubject()->getRowStoreId();

        // load the existing URL rewrites of the actual entity
        $existingUrlRewrites = $this->getUrlRewritesByEntityTypeAndEntityIdAndStoreId($entityType, $pk, $storeId);

        // prepare the existing URL rewrites to improve searching them by store ID/request path
        foreach ($existingUrlRewrites as $existingUrlRewrite) {
            // load the request path from the existing URL rewrite
            $requestPath = $existingUrlRewrite[MemberNames::REQUEST_PATH];

            // append the URL rewrite with its store ID/request path
            $this->existingUrlRewrites[$requestPath] = $existingUrlRewrite;
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

        // load store ID and request path
        $categoryId = $attr[MemberNames::ENTITY_ID];
        $requestPath = $attr[MemberNames::REQUEST_PATH];

        // iterate over the available URL rewrites to find the one that matches the category ID
        foreach ($this->existingUrlRewrites as $urlRewrite) {
            // compare the category IDs AND the request path
            if ($categoryId === $urlRewrite[MemberNames::ENTITY_ID] &&
                $requestPath === $urlRewrite[MemberNames::REQUEST_PATH]
            ) {
                // if a URL rewrite has been found, do NOT create a redirect
                $this->removeExistingUrlRewrite($urlRewrite);

                // if the found URL rewrite has been autogenerated, then update it
                return $this->mergeEntity($urlRewrite, $attr);
            }
        }

        // simple return the attributes
        return $attr;
    }

    /**
     * Return's the URL rewrites for the passed URL entity type and ID.
     *
     * @param string  $entityType The entity type to load the URL rewrites for
     * @param integer $entityId   The entity ID to load the URL rewrites for
     * @param integer $storeId    The store ID to load the URL rewrites for
     *
     * @return array The URL rewrites
     */
    protected function getUrlRewritesByEntityTypeAndEntityIdAndStoreId($entityType, $entityId, $storeId)
    {
        return $this->getCategoryBunchProcessor()->getUrlRewritesByEntityTypeAndEntityIdAndStoreId($entityType, $entityId, $storeId);
    }

    /**
     * Delete's the URL rewrite with the passed attributes.
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
}
