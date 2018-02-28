<?php

/**
 * TechDivision\Import\Category\Assemblers\CategoryAttributeAssembler
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
 * @copyright 2018 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Category\Assemblers;

use TechDivision\Import\Category\Utils\MemberNames;
use TechDivision\Import\Category\Repositories\CategoryIntRepositoryInterface;
use TechDivision\Import\Category\Repositories\CategoryTextRepositoryInterface;
use TechDivision\Import\Category\Repositories\CategoryVarcharRepositoryInterface;
use TechDivision\Import\Category\Repositories\CategoryDecimalRepositoryInterface;
use TechDivision\Import\Category\Repositories\CategoryDatetimeRepositoryInterface;

/**
 * Assembler implementation that provides functionality to assemble category attribute data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2018 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */
class CategoryAttributeAssembler implements CategoryAttributeAssemblerInterface
{

    /**
     * The category datetime repository instance.
     *
     * @var \TechDivision\Import\Category\Repositories\CategoryDatetimeRepositoryInterface
     */
    protected $categoryDatetimeRepository;

    /**
     * The category decimal repository instance.
     *
     * @var \TechDivision\Import\Category\Repositories\CategoryDecimalRepositoryInterface
     */
    protected $categoryDecimalRepository;

    /**
     * The category integer repository instance.
     *
     * @var \TechDivision\Import\Category\Repositories\CategoryIntRepositoryInterface
     */
    protected $categoryIntRepository;

    /**
     * The category text repository instance.
     *
     * @var \TechDivision\Import\Category\Repositories\CategoryTextRepositoryInterface
     */
    protected $categoryTextRepository;

    /**
     * The category varchar repository instance.
     *
     * @var \TechDivision\Import\Category\Repositories\CategoryVarcharRepositoryInterface
     */
    protected $categoryVarcharRepository;

    /**
     * Initializes the assembler with the necessary repositories.
     *
     * @param \TechDivision\Import\Category\Repositories\CategoryDatetimeRepositoryInterface $categoryDatetimeRepository The category datetime repository instance
     * @param \TechDivision\Import\Category\Repositories\CategoryDecimalRepositoryInterface  $categoryDecimalRepository  The category decimal repository instance
     * @param \TechDivision\Import\Category\Repositories\CategoryIntRepositoryInterface      $categoryIntRepository      The category integer repository instance
     * @param \TechDivision\Import\Category\Repositories\CategoryTextRepositoryInterface     $categoryTextRepository     The category text repository instance
     * @param \TechDivision\Import\Category\Repositories\CategoryVarcharRepositoryInterface  $categoryVarcharRepository  The category varchar repository instance
     */
    public function __construct(
        CategoryDatetimeRepositoryInterface $categoryDatetimeRepository,
        CategoryDecimalRepositoryInterface $categoryDecimalRepository,
        CategoryIntRepositoryInterface $categoryIntRepository,
        CategoryTextRepositoryInterface $categoryTextRepository,
        CategoryVarcharRepositoryInterface $categoryVarcharRepository
    ) {
        $this->categoryDatetimeRepository = $categoryDatetimeRepository;
        $this->categoryDecimalRepository = $categoryDecimalRepository;
        $this->categoryIntRepository = $categoryIntRepository;
        $this->categoryTextRepository = $categoryTextRepository;
        $this->categoryVarcharRepository = $categoryVarcharRepository;
    }

    /**
     * Intializes the existing attributes for the entity with the passed primary key.
     *
     * @param string  $pk      The primary key of the entity to load the attributes for
     * @param integer $storeId The ID of the store view to load the attributes for
     *
     * @return array The entity attributes
     */
    public function getCategoryAttributesByPrimaryKeyAndStoreId($pk, $storeId)
    {

        // initialize the array for the attributes
        $attributes = array();

        // load the datetime attributes
        foreach ($this->categoryDatetimeRepository->findAllByPrimaryKeyAndStoreId($pk, $storeId) as $attribute) {
            $attributes[$attribute[MemberNames::ATTRIBUTE_ID]] = $attribute;
        }

        // load the decimal attributes
        foreach ($this->categoryDecimalRepository->findAllByPrimaryKeyAndStoreId($pk, $storeId) as $attribute) {
            $attributes[$attribute[MemberNames::ATTRIBUTE_ID]] = $attribute;
        }

        // load the integer attributes
        foreach ($this->categoryIntRepository->findAllByPrimaryKeyAndStoreId($pk, $storeId) as $attribute) {
            $attributes[$attribute[MemberNames::ATTRIBUTE_ID]] = $attribute;
        }

        // load the text attributes
        foreach ($this->categoryTextRepository->findAllByPrimaryKeyAndStoreId($pk, $storeId) as $attribute) {
            $attributes[$attribute[MemberNames::ATTRIBUTE_ID]] = $attribute;
        }

        // load the varchar attributes
        foreach ($this->categoryVarcharRepository->findAllByPrimaryKeyAndStoreId($pk, $storeId) as $attribute) {
            $attributes[$attribute[MemberNames::ATTRIBUTE_ID]] = $attribute;
        }

        // return the array with the attributes
        return $attributes;
    }
}
