<?php

/**
 * TechDivision\Import\Category\Utils\ConfigurationKeys
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
 * Utility class containing the configuration keys.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */
class ConfigurationKeys extends \TechDivision\Import\Utils\ConfigurationKeys
{

    /**
     * Name for the column 'media-directory'.
     *
     * @var string
     */
    const MEDIA_DIRECTORY = 'media-directory';

    /**
     * Name for the column 'images-file-directory'.
     *
     * @var string
     */
    const IMAGES_FILE_DIRECTORY = 'images-file-directory';

    /**
     * Name for the column 'copy-images'.
     *
     * @var string
     */
    const COPY_IMAGES = 'copy-images';

    /**
     * Name for the column 'override-images'.
     *
     * @var string
     */
    const OVERRIDE_IMAGES = 'override-images';
}
