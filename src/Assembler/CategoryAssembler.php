<?php

/**
 * TechDivision\Import\Category\Assembler\CategoryAssembler
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

namespace TechDivision\Import\Category\Assembler;

use TechDivision\Import\Category\Utils\MemberNames;
use TechDivision\Import\Category\Repositories\CategoryRepositoryInterface;
use TechDivision\Import\Category\Repositories\CategoryVarcharRepositoryInterface;
use TechDivision\Import\Utils\CategoryPathUtilInterface;

/**
 * Assembler implementation that provides functionality to assemble category data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */
class CategoryAssembler extends \TechDivision\Import\Assembler\CategoryAssembler implements CategoryAssemblerInterface
{

    /**
     * The category attribute assembler instance.
     *
     * @var \TechDivision\Import\Category\Assembler\CategoryAttributeAssemblerInterface
     */
    protected $categoryAttributeAssembler;

    /**
     * Initialize the assembler with the passed instances.
     *
     * @param \TechDivision\Import\Category\Repositories\CategoryRepositoryInterface        $categoryRepository         The repository to access categories
     * @param \TechDivision\Import\Category\Repositories\CategoryVarcharRepositoryInterface $categoryVarcharRepository  The repository instance
     * @param \TechDivision\Import\Category\Assembler\CategoryAttributeAssemblerInterface   $categoryAttributeAssembler The category attribute assembler instance
     * @param \TechDivision\Import\Utils\CategoryPathUtilInterface                          $categoryPathUtil           The utility to handle category paths
     */
    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        CategoryVarcharRepositoryInterface $categoryVarcharRepository,
        CategoryAttributeAssemblerInterface $categoryAttributeAssembler,
        CategoryPathUtilInterface $categoryPathUtil
    ) {

        // pass the repositories to the parent instance
        parent::__construct($categoryRepository, $categoryVarcharRepository, $categoryPathUtil);

        // set the category attribute assembler instance
        $this->categoryAttributeAssembler = $categoryAttributeAssembler;
    }

    /**
     * Returns the category with the passed primary key and the attribute values for the passed store ID.
     *
     * @param string  $pk      The primary key of the category to return
     * @param integer $storeId The store ID of the category values
     *
     * @return array|null The category data
     */
    public function getCategoryByPkAndStoreId($pk, $storeId)
    {

        // load the category with the passed path
        if ($category = $this->categoryRepository->load($pk)) {
            // load the category's attribute values for the passed PK and store ID
            $attributes = $this->categoryAttributeAssembler->getCategoryAttributesByPrimaryKeyAndStoreIdExtendedWithAttributeCode($pk, $storeId);

            // assemble the category withe the attributes
            foreach ($attributes as $attribute) {
                $category[$attribute[MemberNames::ATTRIBUTE_CODE]] = $attribute[MemberNames::VALUE];
            }

            // return the category assembled with the values for the given store ID
            return $category;
        }
    }
}
