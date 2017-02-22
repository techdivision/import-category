<?php

/**
 * TechDivision\Import\Category\Utils\ColumnKeys
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
 * Utility class containing the CSV column names.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */
class ColumnKeys extends \TechDivision\Import\Utils\ColumnKeys
{

    /**
     * Name for the column 'store_view_code'.
     *
     * @var string
     */
    const STORE_VIEW_CODE = 'store_view_code';

    /**
     * Name for the column 'attribute_set_code'.
     *
     * @var string
     */
    const ATTRIBUTE_SET_CODE = 'attribute_set_code';

    /**
     * Name for the column 'path'.
     *
     * @var string
     */
    const PATH = 'path';

    /**
     * Name for the column 'description'.
     *
     * @var string
     */
    const DESCRIPTION = 'description';

    /**
     * Name for the column 'url_key'.
     *
     * @var string
     */
    const URL_KEY = 'url_key';

    /**
     * Name for the column 'url_path'.
     *
     * @var string
     */
    const URL_PATH = 'url_path';

    /**
     * Name for the column 'meta_title'.
     *
     * @var string
     */
    const META_TITLE = 'meta_title';

    /**
     * Name for the column 'meta_keywords'.
     *
     * @var string
     */
    const META_KEYWORDS = 'meta_keywords';

    /**
     * Name for the column 'meta_description'.
     *
     * @var string
     */
    const META_DESCRIPTION = 'meta_description';

    /**
     * Name for the column 'base_image'.
     *
     * @var string
     */
    const BASE_IMAGE = 'base_image';

    /**
     * Name for the column 'base_image_label'.
     *
     * @var string
     */
    const BASE_IMAGE_LABEL = 'base_image_label';

    /**
     * Name for the column 'created_at'.
     *
     * @var string
     */
    const CREATED_AT = 'created_at';

    /**
     * Name for the column 'updated_at'.
     *
     * @var string
     */
    const UPDATED_AT = 'updated_at';

    /**
     * Name for the column 'position'.
     *
     * @var string
     */
    const POSITION = 'position';

    /**
     * Name for the column 'entity_id'.
     *
     * @var string
     */
    const ENTITY_ID = 'entity_id';

    /**
     * Name for the column 'category_path'.
     *
     * @var string
     */
    const CATEGORY_PATH = 'category_path';

    /**
     * Name for the column 'name'.
     *
     * @var string
     */
    const NAME = 'name';
}
