<?php

/**
 * TechDivision\Import\Category\Repositories\CategoryRepository
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
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Category\Repositories;

use TechDivision\Import\Category\Utils\MemberNames;
use TechDivision\Import\Category\Utils\SqlStatementKeys;

/**
 * Repository implementation to load category data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */
class CategoryRepository extends \TechDivision\Import\Repositories\CategoryRepository
{

    /**
     * The prepared statement to load the existing categories.
     *
     * @var \PDOStatement
     */
    protected $categoryStmt;

    /**
     * The prepared statement to load an existing category by it's path
     *
     * @var \PDOStatement
     */
    protected $categoryByPathStmt;

    /**
     * The prepared statement to load the children count of an existing category.
     *
     * @var \PDOStatement
     */
    protected $categoryCountChildrenStmt;

    /**
     * Initializes the repository's prepared statements.
     *
     * @return void
     */
    public function init()
    {

        // initialize the parend class
        parent::init();

        // initialize the prepared statements
        $this->categoryStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::CATEGORY));
        $this->categoryByPathStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::CATEGORY_BY_PATH));
        $this->categoryCountChildrenStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::CATEGORY_COUNT_CHILDREN));
    }

    /**
     * Return's the category with the passed ID.
     *
     * @param string $id The ID of the category to return
     *
     * @return array The category
     */
    public function load($id)
    {
        // load and return the category with the passed ID
        $this->categoryStmt->execute(array(MemberNames::ENTITY_ID => $id));
        return $this->categoryStmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Return's the category with the passed path.
     *
     * @param string $path The path of the category to return
     *
     * @return array The category
     */
    public function findOneByPath($path)
    {
        // load and return the category with the passed path
        $this->categoryByPathStmt->execute(array(MemberNames::PATH => $path));
        return $this->categoryByPathStmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Return's the children count of the category with the passed path.
     *
     * @param string $path The path of the category to count the children for
     *
     * @return integer The children count of the category with the passed path
     */
    public function countChildren($path)
    {
        // load and return the category with the passed path
        $this->categoryCountChildrenStmt->execute(array(MemberNames::PATH => $path));
        return $this->categoryCountChildrenStmt->fetchColumn();
    }
}
