<?php

/**
 * TechDivision\Import\Category\Observers\CategoryObserver
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

use TechDivision\Import\Utils\EntityStatus;
use TechDivision\Import\Utils\StoreViewCodes;
use TechDivision\Import\Utils\BackendTypeKeys;
use TechDivision\Import\Subjects\SubjectInterface;
use TechDivision\Import\Category\Utils\ColumnKeys;
use TechDivision\Import\Category\Utils\MemberNames;
use TechDivision\Import\Category\Utils\EntityTypeCodes;
use TechDivision\Import\Category\Services\CategoryBunchProcessorInterface;
use TechDivision\Import\Serializer\SerializerFactoryInterface;
use TechDivision\Import\Observers\StateDetectorInterface;
use TechDivision\Import\Observers\AttributeLoaderInterface;
use TechDivision\Import\Observers\ObserverFactoryInterface;
use TechDivision\Import\Observers\DynamicAttributeObserverInterface;
use TechDivision\Import\Observers\EntityMergers\EntityMergerInterface;

/**
 * Observer that create's the category itself.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */
class CategoryObserver extends AbstractCategoryImportObserver implements DynamicAttributeObserverInterface, ObserverFactoryInterface
{

    /**
     * The artefact type.
     *
     * @var string
     */
    const ARTEFACT_TYPE = 'category-path';

    /**
     * The array with the parent category IDs.
     *
     * @var array
     */
    protected $categoryIds = array();

    /**
     * The actual category path.
     *
     * @var string
     */
    protected $categoryPath = null;

    /**
     * Initialize the dedicated column.
     *
     * @var array
     */
    protected $columns = array(MemberNames::POSITION => array(ColumnKeys::POSITION, BackendTypeKeys::BACKEND_TYPE_INT, 0));

    /**
     * The processor to read/write the necessary category data.
     *
     * @var \TechDivision\Import\Category\Services\CategoryBunchProcessorInterface
     */
    protected $categoryBunchProcessor;

    /**
     * The attribute loader instance.
     *
     * @var \TechDivision\Import\Observers\AttributeLoaderInterface
     */
    protected $attributeLoader;

    /**
     * The entity merger instance.
     *
     * @var \TechDivision\Import\Observers\EntityMergers\EntityMergerInterface
     */
    protected $entityMerger;

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

    /**
     * Initialize the subject instance.
     *
     * @param \TechDivision\Import\Category\Services\CategoryBunchProcessorInterface $categoryBunchProcessor The category bunch processor instance
     * @param \TechDivision\Import\Observers\AttributeLoaderInterface                $attributeLoader        The attribute loader instance
     * @param \TechDivision\Import\Observers\EntityMergers\EntityMergerInterface     $entityMerger           The entity merger instance
     * @param \TechDivision\Import\Serializer\SerializerFactoryInterface             $serializerFactory      The serializer factory instance
     * @param \TechDivision\Import\Observers\StateDetectorInterface|null             $stateDetector          The state detector instance to use
     */
    public function __construct(
        CategoryBunchProcessorInterface $categoryBunchProcessor,
        AttributeLoaderInterface $attributeLoader,
        EntityMergerInterface $entityMerger,
        SerializerFactoryInterface $serializerFactory,
        StateDetectorInterface $stateDetector = null
    ) {

        // initialize the bunch processor and the attribute loader instance
        $this->categoryBunchProcessor = $categoryBunchProcessor;
        $this->attributeLoader = $attributeLoader;
        $this->entityMerger = $entityMerger;
        $this->serializerFactory = $serializerFactory;

        // pass the state detector to the parent method
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
     * Returns the category bunch processor instance.
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
     * @return void
     */
    protected function process()
    {

        // prepare the store view code
        $this->prepareStoreViewCode();

        // explode the path into the category names
        if ($categories = $this->getValue(ColumnKeys::PATH, array(), function ($value) {
            return $this->serializer->explode($value);
        })) {
            // initialize the flag to query whether or not the category is a leaf
            $leaf = false;
            // initialize the artefacts and reset the category IDs
            $artefacts = array();
            $this->categoryIds = array(1);

            // iterate over the category names and try to load the category therefore
            for ($i = 0; $i < sizeof($categories); $i++) {
                // query whether or not the category is a leaf
                if ($i === sizeof($categories) - 1) {
                    $leaf = true;
                }

                // prepare the expected category name
                $this->categoryPath = $this->serializer->implode(array_slice($categories, 0, $i + 1));

                // Attention: Prepare the static entity values, whether the category is
                // a leaf or not, because ONLY when the category is a leaf, we want to
                // OVERRIDE the values from the DB with the values from the import file
                $category = $this->initializeCategory($leaf ? $this->prepareDynamicAttributes() : array());

                // persist and update the category with the new/existing entity ID
                try {
                    $category[$this->getPkMemberName()] = $this->persistCategory($category);
                } catch (\Exception $e) {
                    // if something went wrong in persist
                    // show more information to localize error in CSV
                    $message = $this->getSubject()->appendExceptionSuffix($e->getMessage());
                    $this->getSubject()->getSystemLogger()->critical($message, ['categoryPath' => $this->categoryPath]);
                    throw $e;
                }

                // append the ID of the new category to array with the IDs
                array_push($this->categoryIds, $category[MemberNames::ENTITY_ID]);

                // prepare the artefact and put it on the stack
                $artefacts[] = array(
                    $this->getPkMemberName() => $category[$this->getPkMemberName()],
                    MemberNames::PATH        => implode('/', $this->categoryIds)
                );

                // Attention: Only update the virtual category values if this is the
                // last category from the path, because otherwise the values from the
                // database will be overwritten and lead to an unexpected behaviour
                // in case of create categories on-the-fly during the product import
                if ($leaf) {
                    // update the persisted category with the additional attribute values
                    $category[MemberNames::NAME]            = $this->getValue(ColumnKeys::NAME);
                    $category[MemberNames::URL_KEY]         = $this->getValue(ColumnKeys::URL_KEY);
                    $category[MemberNames::URL_PATH]        = $this->getValue(ColumnKeys::URL_PATH);
                    $category[MemberNames::IS_ACTIVE]       = $this->getValue(ColumnKeys::IS_ACTIVE);
                    $category[MemberNames::IS_ANCHOR]       = $this->getValue(ColumnKeys::IS_ANCHOR);
                    $category[MemberNames::INCLUDE_IN_MENU] = $this->getValue(ColumnKeys::INCLUDE_IN_MENU);

                    // temporary persist primary keys
                    $this->updatePrimaryKeys($category);

                    // add the category by the given path as well as the path mapping
                    $this->addCategoryByPath($this->categoryPath, $category);
                    $this->addPathEntityIdMapping($this->categoryPath);
                }
            }

            // add the artefacts
            $this->addArtefacts($artefacts);
        }
    }

    /**
     * Merge's and return's the entity with the passed attributes and set's the
     * passed status.
     *
     * @param array       $entity        The entity to merge the attributes into
     * @param array       $attr          The attributes to be merged
     * @param string|null $changeSetName The change set name to use
     *
     * @return array The merged entity
     * @todo https://github.com/techdivision/import/issues/179
     */
    protected function mergeEntity(array $entity, array $attr, $changeSetName = null)
    {
        return array_merge(
            $entity,
            $this->entityMerger ? $this->entityMerger->merge($this, $entity, $attr) : $attr,
            array(EntityStatus::MEMBER_NAME => $this->detectState($entity, $attr, $changeSetName))
        );
    }

    /**
     * Appends the dynamic to the static attributes for the EAV attribute
     * and returns them.
     *
     * @return array The array with all available attributes
     */
    protected function prepareDynamicAttributes()
    {
        return array_merge($this->prepareAttributes(), $this->attributeLoader ? $this->attributeLoader->load($this, $this->columns) : array());
    }

    /**
     * Prepare the attributes of the entity that has to be persisted.
     *
     * @return array The prepared attributes
     */
    protected function prepareAttributes()
    {

        // prepare the date format for the created at/updated at dates
        $createdAt = $this->getValue(ColumnKeys::CREATED_AT, date('Y-m-d H:i:s'), array($this, 'formatDate'));
        $updatedAt = $this->getValue(ColumnKeys::UPDATED_AT, date('Y-m-d H:i:s'), array($this, 'formatDate'));

        // load the product's attribute set ID
        $attributeSet = $this->getAttributeSetByAttributeSetName($this->getValue(ColumnKeys::ATTRIBUTE_SET_CODE));
        $attributeSetId = $attributeSet[MemberNames::ATTRIBUTE_SET_ID];
        $this->setAttributeSet($attributeSet);

        // initialize parent ID (the parent array entry)
        $parentId = $this->categoryIds[sizeof($this->categoryIds) - 1];

        // initialize the level
        $level = sizeof($this->categoryIds);

        // load the position, if available
        $position = $this->getValue(ColumnKeys::POSITION, 0);

        // return the prepared product
        return $this->initializeEntity(
            $this->loadRawEntity(
                array(
                    MemberNames::CREATED_AT       => $createdAt,
                    MemberNames::UPDATED_AT       => $updatedAt,
                    MemberNames::ATTRIBUTE_SET_ID => $attributeSetId,
                    MemberNames::PATH             => '',
                    MemberNames::PARENT_ID        => $parentId,
                    MemberNames::POSITION         => $position,
                    MemberNames::LEVEL            => $level,
                    MemberNames::CHILDREN_COUNT   => 0
                )
            )
        );
    }

    /**
     * Load's and return's a raw category entity without primary key but the mandatory members only and nulled values.
     *
     * @param array $data An array with data that will be used to initialize the raw entity with
     *
     * @return array The initialized entity
     */
    protected function loadRawEntity(array $data = array())
    {
        return $this->getCategoryBunchProcessor()->loadRawEntity(EntityTypeCodes::CATALOG_CATEGORY_ENTITY, $data);
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

        // load ID of the actual store view
        $storeId = $this->getRowStoreId(StoreViewCodes::ADMIN);

        // try to load the category by the given path and store ID
        $category = $this->hasPathEntityIdMapping($this->categoryPath) ? $this->getCategoryByPkAndStoreId($this->mapPath($this->categoryPath), $storeId) : null;

        // try to load the entity, if the category is available
        if ($category && $entity = $this->loadCategory($this->getPrimaryKey($category))) {
            // remove the fake path from the attributes (because this contains
            // the names instead of the entity IDs), when we update the entity
            unset($attr[MemberNames::PATH]);
            // merge it with the attributes, if we can find it
            return $this->mergeEntity($entity, $attr);
        }

        // otherwise simply return the attributes
        return $attr;
    }

    /**
     * Query whether or not a mapping for the passed path is available.
     *
     * @param string $path The path
     *
     * @return bool TRUE if the mapping is available, else FALSE
     */
    protected function hasPathEntityIdMapping($path)
    {
        return $this->getSubject()->hasPathEntityIdMapping($path);
    }

    /**
     * Return's the primary key of the category.
     *
     * @param array $category The category
     *
     * @return integer The primary key
     */
    protected function getPrimaryKey($category)
    {
        return $category[$this->getPkMemberName()];
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
     * Add's the passed category with the given path.
     *
     * @param string $path     The path to add the category with
     * @param array  $category The catagory to add
     *
     * @return void
     */
    protected function addCategoryByPath($path, array $category)
    {
        $this->getSubject()->addCategoryByPath($path, $category);
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
     * Tmporary persist the entity ID
     *
     * @param array $category The category to update the IDs
     *
     * @return void
     */
    protected function updatePrimaryKeys(array $category)
    {
        $this->setLastEntityId($category[$this->getPkMemberName()]);
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
     * Persist's the passed category data and return's the ID.
     *
     * @param array $category The category data to persist
     *
     * @return string The ID of the persisted entity
     */
    protected function persistCategory($category)
    {
        return $this->getCategoryBunchProcessor()->persistCategory($category);
    }

    /**
     * Set's the attribute set of the product that has to be created.
     *
     * @param array $attributeSet The attribute set
     *
     * @return void
     */
    protected function setAttributeSet(array $attributeSet)
    {
        $this->getSubject()->setAttributeSet($attributeSet);
    }

    /**
     * Return's the attribute set of the product that has to be created.
     *
     * @return array The attribute set
     */
    protected function getAttributeSet()
    {
        $this->getSubject()->getAttributeSet();
    }

    /**
     * Return's the attribute set with the passed attribute set name.
     *
     * @param string $attributeSetName The name of the requested attribute set
     *
     * @return array The attribute set data
     */
    protected function getAttributeSetByAttributeSetName($attributeSetName)
    {
        return $this->getSubject()->getAttributeSetByAttributeSetName($attributeSetName);
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
        $this->getSubject()->addArtefacts(CategoryObserver::ARTEFACT_TYPE, $artefacts);
    }
}
