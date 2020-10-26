# Version 21.0.0

## Bugfixes

* Fixed techdivision/import-category#69
* Fixed techdivision/import-category#68
* Fixed issue when root category has to be created

## Features

* Add `"include_in_menu": null` to `etc/configuration/default-values.json`
* Remove stack trace of exception for missing media directories > log a simple debug message instead
* Add #PAC-57: Deleting dedicated attribute values via import by setting a configurable value


# Version 20.0.4

## Bugfixes

* Fixed techdivision/import-category#66

## Features

* None

# Version 20.0.3

## Bugfixes

* Fixed techdivision/import-category#64

## Features

* None

# Version 20.0.2

## Bugfixes

* Fixed techdivision/import-category#65

## Features

* None

# Version 20.0.1

## Bugfixes

* Fixed techdivision/import#178

## Features

* None

# Version 20.0.0

## Bugfixes

* None

## Features

* Add #PAC-47: Add entity merger implementation to allow fine grained entity merging

# Version 19.1.0

## Bugfixes

* None

## Features

* Add #PAC-96

# Version 19.0.1

## Bugfixes

* None

## Features

* Remove unneccessary clean-up-empty-columns array from configuration of replace operation

# Version 19.0.0

## Bugfixes

* None

## Features

* Add #PAC-47

# Version 18.0.2

## Bugfixes

* None

## Features

* Add functionality write a log warning instead throwing an exception if the configured media + images file dirctory are not available

# Version 18.0.1

## Bugfixes

* Fixed techdivision/import-cli-simple#246 by reverting PR #165

## Features

* None

# Version 18.0.0

## Bugfixes

* Replace Zend/Filter with new Laminas/Filter library

## Features

* Add techdivision/import-cli-simple#244

# Version 17.0.4

## Bugfixes

* Fixed invalid configuration for children count plugin

## Features

* None

# Version 17.0.3

## Bugfixes

* None

## Features

* Extract dev autoloading
* Move configuration files from etc to configuration subdirectory

# Version 17.0.2

## Bugfixes

* Fixed issue when creating root categories

## Features

* None

# Version 17.0.1

## Bugfixes

* None

## Features

* Activate URL rewrite clean-up functionality by default

# Version 17.0.0

## Bugfixes

* Fixed techdivision/import-cli-simple#234

## Features

* Remove deprecated classes and methods
* Add techdivision/import#146
* Add techdivision/import#162
* Add techdivision/import-cli-simple#216
* Remove unnecessary identifiers from configuration
* Update categories in global data with created ones
* Switch to latest techdivision/import 15.* version as dependency

# Version 16.0.1

## Bugfixes

* Change artefact prefix for category URL rewrites from url-rewrite to category-url-rewrite to avoid conflicts with product import

## Features

* None

# Version 16.0.0

## Bugfixes

* Fixed techdivision/import#156

## Features

* Switch to latest techdivision/import 14.* version as dependency

# Version 15.0.0

## Bugfixes

* None

## Features

* Switch to latest techdivision/import 13.* version as dependency

# Version 14.0.0

## Bugfixes

* None

## Features

* Switch to latest techdivision/import 12.* version as dependency

# Version 13.0.1

## Bugfixes

* Fixed issue with categories that contains a /

## Features

* None

# Version 13.0.0

## Bugfixes

* None

## Features

* Switch to latest techdivision/import 11.* version as dependency

# Version 12.0.0

## Bugfixes

* None

## Features

* Switch to latest techdivision/import 10.0.* version as dependency

# Version 11.0.0

## Bugfixes

* Fixed multi bunch import issue

## Features

* None

# Version 10.0.0

## Bugfixes

* Fixed techdivision/import#147

## Features

* Switch to latest techdivision/import 9.0.* version as dependency

# Version 9.0.0

## Bugfixes

* None

## Features

* Switch to latest techdivision/import 8.0.* version as dependency

# Version 8.0.1

## Bugfixes

* Update default configuration files with listeners
* Replace invalid type hint in CategoryBunchProcessor

## Features

* None

# Version 8.0.0

## Bugfixes

* None

## Features

* Add composite observers to minimize configuration complexity
* Switch to latest techdivision/import 7.0.* version as dependency
* Make Actions and ActionInterfaces deprecated, replace DI configuration with GenericAction + GenericIdentifierAction

# Version 7.0.1

## Bugfixes

* Add missing CategoryBunchProcessor::$eavEntityTypeRepository member variable declaration

## Features

* None

# Version 7.0.0

## Bugfixes

* Fixed #55
* Fixed #56

## Features

* None

# Version 6.0.0

## Bugfixes

* Fixed #50
* Fixed #51
* Fixed #52

## Features

* None

# Version 5.0.1

## Bugfixes

* Update Service Descriptor 

## Features

* None

# Version 5.0.0

## Bugfixes

* None

## Features

* Switch to latest techdivision/import 6.0.* version as dependency

# Version 4.0.0

## Bugfixes

* None

## Features

* Switch to latest techdivision/import 5.0.* version as dependency

# Version 3.0.0

## Bugfixes

* None

## Features

* Switch to latest techdivision/import ~4.0 version as dependency

# Version 2.0.0

## Bugfixes

* None

## Features

* Switch to techdivision/import version ~3.0

# Version 1.0.2

## Bugfixes

* None

## Features

* Also allow techdivision/import ~2.0 versions as dependency

# Version 1.0.1

## Bugfixes

* Switch to phpdocumentor v2.9.* to avoid Travis-CI build errors

## Features

* None

# Version 1.0.0

## Bugfixes

* None

## Features

* Move PHPUnit test from tests to tests/unit folder for integration test compatibility reasons

# Version 1.0.0-beta26

## Bugfixes

* None

## Features

* Add missing interfaces for actions and repositories
* Replace class type hints for CategoryBunchProcessor with interfaces

# Version 1.0.0-beta25

## Bugfixes

* None

## Features

* Configure DI to passe event emitter to subjects constructor

# Version 1.0.0-beta24

## Bugfixes

* None

## Features

* Refactored DI + switch to new SqlStatementRepositories instead of SqlStatements

# Version 1.0.0-beta23

## Bugfixes

* None

## Features

* Remove unnecessary AbstractCategorySubject::tearDown() method

# Version 1.0.0-beta22

## Bugfixes

* Fixed invalid URL rewrite creation in a multi-store environment

## Features

* None

# Version 1.0.0-beta21

## Bugfixes

* None

## Features

* Refactor file upload functionality

# Version 1.0.0-beta20

## Bugfixes

* None

## Features

* Refactor attribute import functionality

# Version 1.0.0-beta19

## Bugfixes

* None

## Features

* Remove system-name from default configuration and set archive-artefacts to TRUE

# Version 1.0.0-beta18

## Bugfixes

* None

## Features

* Create class DependencyInjectionKeys in symfony folder (for integration testing purposes)

# Version 1.0.0-beta17

## Bugfixes

* Fixed invalid URL rewrite creation

## Features

* None

# Version 1.0.0-beta16

## Bugfixes

* Fixed issue when invoking AbstractCategorySubject::storeViewHasBeenProcessed($pk, $storeViewCode) method always returns false

## Features

* None

# Version 1.0.0-beta15

## Bugfixes

* None

## Features

* Refactoring for better URL rewrite + attribute handling

# Version 1.0.0-beta14

## Bugfixes

* None

## Features

* Refactoring filesystem handling

# Version 1.0.0-beta13

## Bugfixes

* Fixed invalid path generation when updating more than two times

## Features

* None

# Version 1.0.0-beta12

## Bugfixes

* None

## Features

* Add custom system logger to default configuration

# Version 1.0.0-beta11

## Bugfixes

* None

## Features

* Replace array with system loggers with a collection

# Version 1.0.0-beta10

## Bugfixes

* None

## Features

* Use EntitySubjectInterface for entity related subjects

# Version 1.0.0-beta9

## Bugfixes

* None

## Features

* Refactor to optimize DI integration

# Version 1.0.0-beta8

## Bugfixes

* None

## Features

* Switch to new plugin + subject factory implementations
 
# Version 1.0.0-beta7

## Bugfixes

* None

## Features

* Update Symfony DI ID for application

# Version 1.0.0-beta6

## Bugfixes

* None

## Features

* Use Robo for Travis-CI build process 
* Refactoring for new ConnectionInterface + SqlStatementsInterface

# Version 1.0.0-beta5

## Bugfixes

* None

## Features

* Remove archive directory from default configuration file

# Version 1.0.0-beta4

## Bugfixes

* None

## Features

* Refactoring Symfony DI integration

# Version 1.0.0-beta3

## Bugfixes

* Add missing loadEavAttributeOptionValueByAttributeCodeAndStoreIdAndValue() method to AbstractCategorySubject + CategoryBunchProcessor

## Features

* None

# Version 1.0.0-beta2

## Bugfixes

* None

## Features

* Update default configuration file

# Version 1.0.0-beta1

## Bugfixes

* None

## Features

* Integrate Symfony DI functionality

# Version 1.0.0-alpha14

## Bugfixes

* Fixed invalid constructor of CategoryBunchProcessor
* Remove FilesytemTrait use statement from MediaSubject to avoid PHP 5.6 PHPUnit error

## Features

* None

# Version 1.0.0-alpha13

## Bugfixes

* None

## Features

* Make select, multiselect + boolean callbacks abstract
* Refactoring for DI integration

# Version 1.0.0-alpha12

## Bugfixes

* None

## Features

* Switch to latest callback interface and optimise error messages

# Version 1.0.0-alpha11

## Bugfixes

* None

## Features

* Extend method getSystemLogger() with parameter name to load a specific logger

# Version 1.0.0-alpha10

## Bugfixes

* None

## Features

* Add functionality for URL rewrite add-update operation

# Version 1.0.0-alpha9

## Bugfixes

* None

## Features

* Fixed PSR-2 errors

# Version 1.0.0-alpha8

## Bugfixes

* None

## Features

* Refactoring to create Magento 2 conform category URL rewrites

# Version 1.0.0-alpha7

## Bugfixes

* None

## Features

* Add dummy UrlRewriteUpdateObserver class to satisfy dependencies in configuration

# Version 1.0.0-alpha6

## Bugfixes

* None

## Features

* Make ChildrenCountPlugin more generic

# Version 1.0.0-alpha5

## Bugfixes

* None

## Features

* Add basic catgory update functionality

# Version 1.0.0-alpha4

## Bugfixes

* None

## Features

* Refactor URL rewrite handling

# Version 1.0.0-alpha3

## Bugfixes

* None

## Features

* Add basic URL rewrite handling

# Version 1.0.0-alpha2

## Bugfixes

* None

## Features

* Implement category delete + replace functionality

# Version 1.0.0-alpha1

## Bugfixes

* None

## Features

* Initial Release
