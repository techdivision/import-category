<?php

/**
 * TechDivision\Import\Category\Utils\ConfigurationKeys
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Category\Utils;

/**
 * Utility class containing the configuration keys.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
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
