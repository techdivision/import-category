<?php

/**
 * TechDivision\Import\Category\Utils\CoreConfigDataKeys
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

namespace TechDivision\Import\Category\Utils;

/**
 * Utility class containing the keys Magento uses to persist values in the "core_config_data table".
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */
class CoreConfigDataKeys extends \TechDivision\Import\Utils\CoreConfigDataKeys
{

    /**
     * Name for the column 'catalog/seo/category_url_suffix'.
     *
     * @var string
     */
    const CATALOG_SEO_CATEGORY_URL_SUFFIX = 'catalog/seo/category_url_suffix';

    /**
     * Name for the column 'catalog/seo/save_rewrites_history'.
     *
     * @var string
     */
    const CATALOG_SEO_SAVE_REWRITES_HISTORY = 'catalog/seo/save_rewrites_history';
}
