<?php

/**
 * TechDivision\Import\Category\Observers\CategoryUrlRewriteObserver
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

use TechDivision\Import\Category\Services\CategoryBunchProcessorInterface;
use TechDivision\Import\Observers\ObserverFactoryInterface;
use TechDivision\Import\Observers\StateDetectorInterface;
use TechDivision\Import\Serializer\SerializerFactoryInterface;
use TechDivision\Import\Subjects\SubjectInterface;
use TechDivision\Import\Utils\MemberNames;
use TechDivision\Import\Utils\StoreViewCodes;
use TechDivision\Import\Category\Utils\ColumnKeys;

/**
 * Observer that extracts the URL rewrite data to a specific CSV file.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */
class CategoryUrlRewriteObserver extends AbstractCategoryImportObserver implements ObserverFactoryInterface
{
    /**
     * The artefact type.
     *
     * @var string
     */
    const ARTEFACT_TYPE = 'category-url-rewrite';

    /**
     * The image artefacts that has to be exported.
     *
     * @var array
     */
    protected $artefacts = array();

    /**
     * The serializer used to serializer/unserialize the categories from the path column.
     *
     * @var \TechDivision\Import\Serializer\SerializerInterface
     */
    protected $serializer;

    /**
     * The serializer factory instance.
     *
     * @var \TechDivision\Import\Serializer\SerializerFactoryInterface
     */
    protected $serializerFactory;

    /** @var CategoryBunchProcessorInterface  */
    protected CategoryBunchProcessorInterface $categoryBunchProcessor;

    /**
     * @param CategoryBunchProcessorInterface $categoryBunchProcessor category bunch processor instance
     * @param SerializerFactoryInterface      $serializerFactory      serializer factory instance
     * @param StateDetectorInterface|null     $stateDetector          state detector instance
     */
    public function __construct(
        CategoryBunchProcessorInterface $categoryBunchProcessor,
        SerializerFactoryInterface $serializerFactory,
        StateDetectorInterface $stateDetector = null
    ) {
        $this->categoryBunchProcessor = $categoryBunchProcessor;
        $this->serializerFactory = $serializerFactory;
        parent::__construct($stateDetector);
    }

    /**
     * Will be invoked by the observer visitor when a factory has been defined to create the observer instance.
     *
     * @param \TechDivision\Import\Subjects\SubjectInterface $subject The subject instance
     *
     * @return \TechDivision\Import\Observers\ObserverInterface The observer instance
     */
    public function createObserver(SubjectInterface $subject)
    {

        // initialize the serializer instance
        $this->serializer = $this->serializerFactory->createSerializer($subject->getConfiguration()->getImportAdapter());

        // return the initialized instance
        return $this;
    }

    /**
     * Return's the category bunch processor instance.
     *
     * @return CategoryBunchProcessorInterface The category URL rewrite processor instance
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

        // initialize the array for the artefacts and the store view codes
        $this->artefacts = array();

        // load the category path from the row
        $path = $this->getValue(ColumnKeys::PATH);

        // prepare the store view code
        $this->getSubject()->prepareStoreViewCode();

        // try to load the store view code
        $storeViewCodeValue = $this->getSubject()->getStoreViewCode(StoreViewCodes::ADMIN);
        $storeViewCodes = [$storeViewCodeValue];

        // query whether we've a store view code
        if ($storeViewCodeValue === StoreViewCodes::ADMIN) {
            // if not, load the available store view codes for the root category of the given path
            $storeViewCodes = $this->getRootCategoryStoreViewCodes($path);
        }

        // iterate over the available store view codes
        foreach ($storeViewCodes as $storeViewCode) {
            // do not export URL rewrites for the admin store
            if ($storeViewCode === StoreViewCodes::ADMIN) {
                continue;
            }

            $storeId = $this->getSubject()->getRowStoreId($storeViewCode);
            $category = $this->getCategoryByPkAndStoreId($this->mapPath($path), $storeId);
            if ($storeViewCodeValue ===  StoreViewCodes::ADMIN && $category) {
                // load the url_key attribute
                $urlKey = $this->loadExistingUrlKey($category, $storeViewCode);
                // if url_key attribute found and same store as searched
                if ($urlKey && $urlKey[MemberNames::STORE_ID] == $storeId) {
                    // skip for artefact as default entry
                    continue;
                }
            }

            // iterate over the store view codes and query if artefacts are already available
            if ($this->hasArtefactsByTypeAndEntityId(CategoryUrlRewriteObserver::ARTEFACT_TYPE, $lastEntityId = $this->getSubject()->getLastEntityId())) {
                // if yes, load the artefacts
                $this->artefacts = $this->getArtefactsByTypeAndEntityId(CategoryUrlRewriteObserver::ARTEFACT_TYPE, $lastEntityId);

                // initialize the flag that shows whether an artefact has already been available
                $foundArtefactToUpdate = false;
                // override the existing data with the store view specific one
                for ($i = 0; $i < sizeof($this->artefacts); $i++) {
                    // query whether a URL path has been specfied and the store view codes are equal
                    if ($this->hasValue(ColumnKeys::URL_KEY) && $this->artefacts[$i][ColumnKeys::STORE_VIEW_CODE] === $storeViewCode) {
                        $foundArtefactToUpdate = true;
                        // update the URL path
                        $this->artefacts[$i][ColumnKeys::URL_PATH] = $this->getValue(ColumnKeys::URL_PATH);

                        // also update filename and line number
                        $this->artefacts[$i][ColumnKeys::ORIGINAL_DATA][ColumnKeys::ORIGINAL_FILENAME] = $this->getSubject()->getFilename();
                        $this->artefacts[$i][ColumnKeys::ORIGINAL_DATA][ColumnKeys::ORIGINAL_LINE_NUMBER] = $this->getSubject()->getLineNumber();
                    }
                }

                if (!$foundArtefactToUpdate) {
                    // if no artefacts are available, append new data
                    $this->createArtefact($path, $storeViewCode);
                }
            } else {
                // if no artefacts are available, append new data
                $this->createArtefact($path, $storeViewCode);
            }
        }

        // append the artefacts that has to be exported to the subject
        $this->addArtefacts($this->artefacts);
    }

    /**
     * Creates a new artefact, pre-initialized with the values from the admin row.
     *
     * @param string $path          The path for the new url_key
     * @param string $storeViewCode The Storeview code
     *
     * @return void
     */
    protected function createArtefact(string $path, string $storeViewCode) : void
    {
        // create the new artefact and return it
        $artefact = $this->newArtefact(
            array(
                ColumnKeys::PATH               => $path,
                ColumnKeys::STORE_VIEW_CODE    => $storeViewCode,
                ColumnKeys::URL_PATH           => $this->getValue(ColumnKeys::URL_PATH)
            ),
            array(
                ColumnKeys::PATH               => ColumnKeys::PATH,
                ColumnKeys::STORE_VIEW_CODE    => ColumnKeys::STORE_VIEW_CODE,
                ColumnKeys::URL_PATH           => ColumnKeys::URL_PATH
            )
        );

        // append the artefact to the artefacts
        $this->artefacts[] = $artefact;
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
     * Tries to load the URL key for the passed category and store view code and return's it.
     *
     * @param array  $category      The category to return the URL key for
     * @param string $storeViewCode The store view code of the URL key
     *
     * @return array|null The array with the URL key attribute data
     */
    protected function loadExistingUrlKey(array $category, string $storeViewCode)
    {

        // initialize last entity as primary key
        $pk = $this->getPrimaryKeyId($category);

        // initialize the entity type ID
        $entityType = $this->getSubject()->getEntityType();
        $entityTypeId = (integer) $entityType[MemberNames::ENTITY_TYPE_ID];

        // initialize store ID from store code
        $storeId = $this->getSubject()->getRowStoreId($storeViewCode);

        // take a look if url_key already exist
        return $this->getCategoryBunchProcessor()->loadVarcharAttributeByAttributeCodeAndEntityTypeIdAndStoreIdAndPrimaryKey(
            ColumnKeys::URL_KEY,
            $entityTypeId,
            $storeId,
            $pk
        );
    }

    /**
     * @param array $category From loadCategory
     * @return mixed
     */
    protected function getPrimaryKeyId(array $category)
    {
        return $category[$this->getCategoryBunchProcessor()->getPrimaryKeyMemberName()];
    }

    /**
     * Returns the store view codes relevant to the category represented by the current row.
     *
     * @param string $path The path to return the root category's store view codes for
     *
     * @return array The store view codes for the given root category
     * @throws \Exception Is thrown, if the root category of the passed path is NOT available
     */
    protected function getRootCategoryStoreViewCodes($path)
    {
        return $this->getSubject()->getRootCategoryStoreViewCodes($path);
    }

    /**
     * Queries whether or not artefacts for the passed type and entity ID are available.
     *
     * @param string $type     The artefact type, e. g. configurable
     * @param string $entityId The entity ID to return the artefacts for
     *
     * @return boolean TRUE if artefacts are available, else FALSE
     */
    protected function hasArtefactsByTypeAndEntityId($type, $entityId)
    {
        return $this->getSubject()->hasArtefactsByTypeAndEntityId($type, $entityId);
    }

    /**
     * Return the artefacts for the passed type and entity ID.
     *
     * @param string $type     The artefact type, e. g. configurable
     * @param string $entityId The entity ID to return the artefacts for
     *
     * @return array The array with the artefacts
     * @throws \Exception Is thrown, if no artefacts are available
     */
    protected function getArtefactsByTypeAndEntityId($type, $entityId)
    {
        return $this->getSubject()->getArtefactsByTypeAndEntityId($type, $entityId);
    }

    /**
     * Create's and return's a new empty artefact entity.
     *
     * @param array $columns             The array with the column data
     * @param array $originalColumnNames The array with a mapping from the old to the new column names
     *
     * @return array The new artefact entity
     */
    protected function newArtefact(array $columns, array $originalColumnNames)
    {
        return $this->getSubject()->newArtefact($columns, $originalColumnNames);
    }

    /**
     * Add the passed product type artefacts to the product with the
     * last entity ID.
     *
     * @param array $artefacts The product type artefacts
     *
     * @return void
     * @uses \TechDivision\Import\Product\Media\Subjects\MediaSubject::getLastEntityId()
     */
    protected function addArtefacts(array $artefacts)
    {
        $this->getSubject()->addArtefacts(CategoryUrlRewriteObserver::ARTEFACT_TYPE, $artefacts);
    }
}
