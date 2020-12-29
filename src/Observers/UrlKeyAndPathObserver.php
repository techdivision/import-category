<?php

/**
 * TechDivision\Import\Category\Observers\UrlKeyAndPathObserver
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

use Zend\Filter\FilterInterface;
use TechDivision\Import\Utils\StoreViewCodes;
use TechDivision\Import\Utils\UrlKeyUtilInterface;
use TechDivision\Import\Utils\Filter\UrlKeyFilterTrait;
use TechDivision\Import\Category\Utils\ColumnKeys;
use TechDivision\Import\Category\Utils\MemberNames;
use TechDivision\Import\Category\Utils\ConfigurationKeys;
use TechDivision\Import\Subjects\UrlKeyAwareSubjectInterface;
use TechDivision\Import\Category\Services\CategoryBunchProcessorInterface;

/**
 * Observer that extracts the URL key/path from the category path
 * and adds them as two new columns with the their values.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */
class UrlKeyAndPathObserver extends AbstractCategoryImportObserver
{

    /**
     * The trait that provides string => URL key conversion functionality.
     *
     * @var \TechDivision\Import\Utils\Filter\UrlKeyFilterTrait
     */
    use UrlKeyFilterTrait;

    /**
     * The URL key utility instance.
     *
     * @var \TechDivision\Import\Utils\UrlKeyUtilInterface
     */
    protected $urlKeyUtil;

    /**
     * The category bunch processor instance.
     *
     * @var \TechDivision\Import\Category\Services\CategoryBunchProcessorInterface
     */
    protected $categoryBunchProcessor;

    /**
     * Initialize the observer with the passed product bunch processor instance.
     *
     * @param \TechDivision\Import\Category\Services\CategoryBunchProcessorInterface $categoryBunchProcessor  The category bunch processor instance
     * @param \Zend\Filter\FilterInterface                                           $convertLiteralUrlFilter The URL filter instance
     * @param \TechDivision\Import\Utils\UrlKeyUtilInterface                         $urlKeyUtil              The URL key utility instance
     */
    public function __construct(
        CategoryBunchProcessorInterface $categoryBunchProcessor,
        FilterInterface $convertLiteralUrlFilter,
        UrlKeyUtilInterface $urlKeyUtil
    ) {

        // set the processor and the URL filter instance
        $this->categoryBunchProcessor = $categoryBunchProcessor;
        $this->convertLiteralUrlFilter = $convertLiteralUrlFilter;
        $this->urlKeyUtil = $urlKeyUtil;
    }

    /**
     * Process the observer's business logic.
     *
     * @return void
     */
    protected function process()
    {

        // initialize the URL key and array for the categories
        $urlKey = null;
        $category = array();

        // set the entity ID for the category with the passed path
        try {
            $this->setIds($category = $this->getCategoryByPath($path = $this->getValue(ColumnKeys::PATH)));
        } catch (\Exception $e) {
            $this->setIds(array());
        }

        // query whether or not the URL key column has a value
        if ($this->hasValue(ColumnKeys::URL_KEY)) {
            $urlKey = $this->makeUnique($this->getSubject(), $this->getValue(ColumnKeys::URL_KEY));
        } else {
            // query whether or not the existing category `url_key` should be re-created from the category name
            if ($category && !$this->getSubject()->getConfiguration()->getParam(ConfigurationKeys::UPDATE_URL_KEY_FROM_NAME, true)) {
                // if the category already exists and NO re-creation from the category name has to
                // be done, load the original `url_key`from the category and use that to proceed
                $urlKey = $this->loadUrlKey($this->getSubject(), $this->getPrimaryKey());
            }

            // query whether or not the an URL key for the exisitng category
            // has been available, if not but the column `name` contains a
            // value try to create an URL key from the category's name
            if ($urlKey === '' || $urlKey === null && $this->hasValue(ColumnKeys::NAME)) {
                $urlKey = $this->makeUnique($this->getSubject(), $this->convertNameToUrlKey($this->getValue(ColumnKeys::NAME)));
            }

            // if no URL key has benn available nor the column `name`
            // contains a value, we've to throw an exception
            if ($urlKey === '' || $urlKey === null) {
                // throw an exception, that the URL key can not be initialized and we're in default store view
                if ($this->getSubject()->getStoreViewCode(StoreViewCodes::ADMIN) === StoreViewCodes::ADMIN) {
                    throw new \Exception(sprintf('Can\'t initialize the URL key for category "%s" because columns "url_key" or "name" have a value set for default store view', $path));
                }
            }

            // otherwise set the URL key as value for column`url_key`,
            // because we need one to process the rewrites later
            $this->setValue(ColumnKeys::URL_KEY, $urlKey);
        }

        // explode the path into the category names
        if ($categories = $this->explode($this->getValue(ColumnKeys::PATH), '/')) {
            // initialize the category with the actual category's URL key
            $categoryPaths = array($urlKey);
            // prepare the store view code
            $this->prepareStoreViewCode();
            // load ID of the actual store view
            $storeId = $this->getRowStoreId(StoreViewCodes::ADMIN);

            // iterate over the category names and try to load the category therefore
            for ($i = sizeof($categories) - 1; $i > 1; $i--) {
                try {
                    // prepare the expected category name
                    $categoryPath = implode('/', array_slice($categories, 0, $i));
                    // load the existing category and prepend the URL key the array with the category URL keys
                    $existingCategory = $this->getCategoryByPkAndStoreId($this->mapPath($categoryPath), $storeId);
                    // query whether or not an URL key is available or not
                    if (isset($existingCategory[MemberNames::URL_KEY])) {
                        array_unshift($categoryPaths, $existingCategory[MemberNames::URL_KEY]);
                    } else {
                        $this->getSystemLogger()->debug(sprintf('Can\'t find URL key for category %s', $categoryPath));
                    }
                } catch (\Exception $e) {
                    $this->getSystemLogger()->debug(sprintf('Can\'t load parent category %s', $categoryPath));
                }
            }

            // create the header for the URL path
            if (!$this->hasHeader(ColumnKeys::URL_PATH)) {
                $this->addHeader(ColumnKeys::URL_PATH);
            }

            // set the URL path
            $this->setValue(ColumnKeys::URL_PATH, implode('/', $categoryPaths));
        }
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
     * Returns the category bunch processor instance.
     *
     * @return \TechDivision\Import\Category\Services\CategoryBunchProcessorInterface The category bunch processor instance
     */
    protected function getCategoryBunchProcessor()
    {
        return $this->categoryBunchProcessor;
    }

    /**
     * Returns the URL key utility instance.
     *
     * @return \TechDivision\Import\Utils\UrlKeyUtilInterface The URL key utility instance
     */
    protected function getUrlKeyUtil()
    {
        return $this->urlKeyUtil;
    }

    /**
     * Return's the category with the passed path.
     *
     * @param string $path The path of the category to return
     *
     * @return array The category
     * @throws \Exception Is thrown, if the requested category is not available
     */
    protected function getCategoryByPath($path)
    {
        return $this->getSubject()->getCategoryByPath($path);
    }

    /**
     * Returns the category with the passed primary key and the attribute values for the passed store ID.
     *
     * @param string  $pk      The primary key of the category to return
     * @param integer $storeId The store ID of the category values
     *
     * @return array|null The category data
     */
    protected function getCategoryByPkAndStoreId($pk, $storeId)
    {
        return $this->getCategoryBunchProcessor()->getCategoryByPkAndStoreId($pk, $storeId);
    }

    /**
     * Temporarily persist's the IDs of the passed category.
     *
     * @param array $category The category to temporarily persist the IDs for
     *
     * @return void
     */
    protected function setIds(array $category)
    {
        $this->setLastEntityId(isset($category[$this->getPkMemberName()]) ? $category[$this->getPkMemberName()] : null);
    }

    /**
     * Set's the ID of the category that has been created recently.
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
     * Return's the PK to of the product.
     *
     * @return integer The PK to create the relation with
     */
    protected function getPrimaryKey()
    {
        $this->getSubject()->getLastEntityId();
    }

    /**
     * Load's and return's the url_key with the passed primary ID.
     *
     * @param \TechDivision\Import\Subjects\UrlKeyAwareSubjectInterface $subject      The subject to load the URL key
     * @param int                                                       $primaryKeyId The ID from category
     *
     * @return string|null url_key or null
     */
    protected function loadUrlKey(UrlKeyAwareSubjectInterface $subject, $primaryKeyId)
    {
        return $this->getUrlKeyUtil()->loadUrlKey($subject, $primaryKeyId);
    }

    /**
     * Make's the passed URL key unique by adding the next number to the end.
     *
     * @param \TechDivision\Import\Subjects\UrlKeyAwareSubjectInterface $subject The subject to make the URL key unique for
     * @param string                                                    $urlKey  The URL key to make unique
     *
     * @return string The unique URL key
     */
    protected function makeUnique(UrlKeyAwareSubjectInterface $subject, $urlKey)
    {
        return $this->getUrlKeyUtil()->makeUnique($subject, $urlKey);
    }
}
