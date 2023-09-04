<?php

/**
 * TechDivision\Import\Category\Observers\UrlKeyAndPathObserver
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Category\Observers;

use Laminas\Filter\FilterInterface;
use TechDivision\Import\Utils\ConfigurationKeys;
use TechDivision\Import\Utils\RegistryKeys;
use TechDivision\Import\Utils\StoreViewCodes;
use TechDivision\Import\Utils\UrlKeyUtilInterface;
use TechDivision\Import\Utils\Filter\UrlKeyFilterTrait;
use TechDivision\Import\Subjects\UrlKeyAwareSubjectInterface;
use TechDivision\Import\Category\Utils\ColumnKeys;
use TechDivision\Import\Category\Utils\MemberNames;
use TechDivision\Import\Category\Services\CategoryBunchProcessorInterface;
use TechDivision\Import\Utils\Generators\GeneratorInterface;

/**
 * Observer that extracts the URL key/path from the category path
 * and adds them as two new columns with the their values.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
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
     * The reverse sequence generator instance.
     *
     * @var \TechDivision\Import\Utils\Generators\GeneratorInterface
     */
    protected $reverseSequenceGenerator;

    /**
     * @var bool The flag to check if the URL key has been read from database
     */
    protected $originUrlKey = false;

    /**
     * Initialize the observer with the passed product bunch processor instance.
     *
     * @param \TechDivision\Import\Category\Services\CategoryBunchProcessorInterface $categoryBunchProcessor   The category bunch processor instance
     * @param \Laminas\Filter\FilterInterface                                        $convertLiteralUrlFilter  The URL filter instance
     * @param \TechDivision\Import\Utils\UrlKeyUtilInterface                         $urlKeyUtil               The URL key utility instance
     * @param \TechDivision\Import\Utils\Generators\GeneratorInterface               $reverseSequenceGenerator The reverse sequence generator instance
     */
    public function __construct(
        CategoryBunchProcessorInterface $categoryBunchProcessor,
        FilterInterface $convertLiteralUrlFilter,
        UrlKeyUtilInterface $urlKeyUtil,
        GeneratorInterface $reverseSequenceGenerator
    ) {

        // set the processor and the URL filter instance
        $this->categoryBunchProcessor = $categoryBunchProcessor;
        $this->convertLiteralUrlFilter = $convertLiteralUrlFilter;
        $this->urlKeyUtil = $urlKeyUtil;
        $this->reverseSequenceGenerator = $reverseSequenceGenerator;
    }

    /**
     * Process the observer's business logic.
     *
     * @return void
     */
    protected function process()
    {

        // initialize the entity and the category
        $entity = null;
        $category = array();

        // prepare the store view code
        $this->prepareStoreViewCode();

        // set the entity ID for the category with the passed path
        try {
            $entity = $this->getCategoryByPath($path = $this->getValue(ColumnKeys::PATH));
            $this->setIds($category = $entity);
        } catch (\Exception $e) {
            $this->setIds(array());
            $category[MemberNames::ENTITY_ID] = $this->getReverseSequenceGenerator()->generate();
        }

        // load the URL key from the actual row.
        $urlKey = $this->getUrlKeyFromRow($entity);

        // stop processing, if no URL key is available
        if ($urlKey === null || $urlKey === '') {
            // throw an exception, that the URL key can not be
            // initialized, we're in the default store view
            if ($this->getStoreViewCode(StoreViewCodes::ADMIN) === StoreViewCodes::ADMIN) {
                throw new \Exception(sprintf('Can\'t initialize the URL key for category "%s" because columns "url_key" or "name" have a value set for default store view', $path));
            }
            // stop processing, because we're in a store
            // view row and a URL key is not mandatory
            return;
        }

        // load ID of the actual store view
        $storeId = $this->getRowStoreId(StoreViewCodes::ADMIN);

        // explode the path into the category names
        if ($categories = $this->explode($this->getValue(ColumnKeys::PATH), '/')) {
            // initialize the array for the category paths
            $categoryPaths = array();
            // iterate over the parent category names and try
            // to load the categories to build the URL path
            for ($i = sizeof($categories) - 1; $i > 1; $i--) {
                try {
                    // prepare the expected category name
                    $categoryPath = implode('/', array_slice($categories, 0, $i));
                    // load the existing category and prepend the URL key the array with the category URL keys
                    $existingCategory = $this->getCategoryByPkAndStoreId($this->mapPath($categoryPath), $storeId);
                    // query whether a URL key is available or not
                    if (isset($existingCategory[MemberNames::URL_KEY])) {
                        array_unshift($categoryPaths, $existingCategory[MemberNames::URL_KEY]);
                    } else {
                        $this->getSystemLogger()->debug(sprintf('Can\'t find URL key for category "%s"', $categoryPath));
                    }
                } catch (\Exception $e) {
                    $this->getSystemLogger()->debug(sprintf('Can\'t load parent category "%s"', $categoryPath));
                }
            }
        }

        // generate the unique URL key
        $uniqueUrlKey = $this->makeUnique(
            $this->getSubject(),
            $category,
            $urlKey,
            sizeof($categoryPaths) > 0 ? array(implode('/', $categoryPaths)) : array()
        );

        if ($urlKey !== $uniqueUrlKey && !$this->getSubject()->isStrictMode()) {
            $message = sprintf(
                'Generate new unique URL key "%s" for store "%s" and category with PATH "%s"',
                $uniqueUrlKey,
                $this->getStoreViewCode(StoreViewCodes::ADMIN),
                $path
            );
            $this->getSubject()->getSystemLogger()->warning($message);
            $this->mergeStatus(
                array(
                    RegistryKeys::NO_STRICT_VALIDATIONS => array(
                        basename($this->getFilename()) => array(
                            $this->getLineNumber() => array(
                                ColumnKeys::URL_KEY => $message
                            )
                        )
                    )
                )
            );
        }

        // update the URL key with the unique value
        $this->setValue(ColumnKeys::URL_KEY, $uniqueUrlKey);

        // finally, append the URL key as last element to the path
        array_push($categoryPaths, $uniqueUrlKey);

        // create the virtual column for the URL path
        if ($this->hasHeader(ColumnKeys::URL_PATH) === false) {
            $this->addHeader(ColumnKeys::URL_PATH);
        }

        // set the URL path
        $this->setValue(ColumnKeys::URL_PATH, implode('/', $categoryPaths));
    }

    /**
     * @param array|null $entity category entity
     * @return mixed|string
     * @throws \Exception
     */
    protected function getUrlKeyFromRow($entity)
    {
        $this->originUrlKey = false;
        $urlKey = null;
        // query whether the URL key column has a value
        if ($this->hasValue(ColumnKeys::URL_KEY)) {
            $urlKey = $this->getValue(ColumnKeys::URL_KEY);
        } else {
            // query whether the existing category `url_key` should be re-created from the category name
            if (is_array($entity) && !$this->getSubject()->getConfiguration()->getParam(ConfigurationKeys::UPDATE_URL_KEY_FROM_NAME, true)) {
                // if the category already exists and NO re-creation from the category name has to
                // be done, load the original `url_key`from the category and use that to proceed
                $urlKey = $this->loadUrlKey($this->getSubject(), $this->getPrimaryKey());
                $this->originUrlKey = !empty($urlKey);
            }

            // try to load the value from column `name` if URL key is still
            // empty, because we need it to process the the rewrites later on
            if ($urlKey === null || ($urlKey === '' && $this->hasValue(ColumnKeys::NAME))) {
                $urlKey = $this->convertNameToUrlKey($this->getValue(ColumnKeys::NAME));
            }
        }

        return $urlKey;
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
     * Returns the reverse sequence generator instance.
     *
     * @return \TechDivision\Import\Utils\Generators\GeneratorInterface The reverse sequence generator
     */
    protected function getReverseSequenceGenerator() : GeneratorInterface
    {
        return $this->reverseSequenceGenerator;
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
        $this->setLastEntityId(isset($category[MemberNames::ENTITY_ID]) ? $category[MemberNames::ENTITY_ID] : null);
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
        return $this->getSubject()->getLastEntityId();
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
     * @param \TechDivision\Import\Subjects\UrlKeyAwareSubjectInterface $subject  The subject to make the URL key unique for
     * @param array                                                     $entity   The entity to make the URL key unique for
     * @param string                                                    $urlKey   The URL key to make unique
     * @param array                                                     $urlPaths The URL paths to make unique
     *
     * @return string The unique URL key
     */
    protected function makeUnique(UrlKeyAwareSubjectInterface $subject, array $entity, string $urlKey, array $urlPaths = array())
    {
        return $this->getUrlKeyUtil()->makeUnique($subject, $entity, $urlKey, $urlPaths);
    }
}
