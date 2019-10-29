<?php

/**
 * TechDivision\Import\Category\Subjects\SortableBunchSubject
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

namespace TechDivision\Import\Category\Subjects;

/**
 * The subject implementation that supports artefact replacement.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */
class SortableBunchSubject extends BunchSubject
{

    /**
     * Set's/Replace's the artefacts for the given type whith the ones from the passed array.
     *
     * @param string $type      The artefact type to replace
     * @param array  $artefacts The artefacts to replace with
     *
     * @return void
     */
    public function setArtefactsByType($type, array $artefacts)
    {
        $this->artefacts[$type] = $artefacts;
    }
}
