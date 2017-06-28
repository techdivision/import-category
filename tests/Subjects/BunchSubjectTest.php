<?php

/**
 * TechDivision\Import\Category\Subjects\BunchSubjectTest
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

namespace TechDivision\Import\Category\Subjects;

/**
 * Test class for the product action implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */
class BunchSubjectTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The subject we want to test.
     *
     * @var \TechDivision\Import\Category\Subjects\BunchSubject
     */
    protected $subject;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     * @see \PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {

        // create a mock registry processor
        $mockRegistryProcessor = $this->getMockBuilder('TechDivision\Import\Services\RegistryProcessorInterface')
                                      ->setMethods(get_class_methods('TechDivision\Import\Services\RegistryProcessorInterface'))
                                      ->getMock();

        // create a mock category processor
        $mockCategoryProcessor = $this->getMockBuilder('TechDivision\Import\Category\Services\CategoryBunchProcessorInterface')
                                      ->setMethods(get_class_methods('TechDivision\Import\Category\Services\CategoryBunchProcessorInterface'))
                                      ->getMock();

        // create a generator
        $mockGenerator = $this->getMockBuilder('TechDivision\Import\Utils\Generators\GeneratorInterface')
                              ->setMethods(get_class_methods('TechDivision\Import\Utils\Generators\GeneratorInterface'))
                              ->getMock();

        // create the subject to be tested
        $this->subject = new BunchSubject(
            $mockRegistryProcessor,
            $mockGenerator,
            array(),
            $mockCategoryProcessor
        );
    }

    /**
     * Test's the persistCategory() method successfull.
     *
     * @return void
     */
    public function testPersistCategorySuccessufull()
    {

        // create a mock category processor
        $mockProcessor = $this->getMockBuilder('TechDivision\Import\Category\Services\CategoryBunchProcessorInterface')
                              ->setMethods(get_class_methods('TechDivision\Import\Category\Services\CategoryBunchProcessorInterface'))
                              ->getMock();
        $mockProcessor->expects($this->once())
                      ->method('persistCategory')
                      ->with($category = array('path' => '2/3/4'))
                      ->willReturn(null);

        // create a mock subject configuration
        $mockSubjectConfiguration = $this->getMockBuilder('TechDivision\Import\Configuration\SubjectConfigurationInterface')
                                           ->setMethods(get_class_methods('TechDivision\Import\Configuration\SubjectConfigurationInterface'))
                                           ->getMock();

        // inject the processor and configuration
        $this->subject->setCategoryProcessor($mockProcessor);
        $this->subject->setConfiguration($mockSubjectConfiguration);

        // make sure that the category will be persisted
        $this->assertNull($this->subject->persistCategory($category));
    }
}
