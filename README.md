# Pacemaker - Category Import

[![Latest Stable Version](https://img.shields.io/packagist/v/techdivision/import-category.svg?style=flat-square)](https://packagist.org/packages/techdivision/import-category) 
 [![Total Downloads](https://img.shields.io/packagist/dt/techdivision/import-category.svg?style=flat-square)](https://packagist.org/packages/techdivision/import-category)
 [![License](https://img.shields.io/packagist/l/techdivision/import-category.svg?style=flat-square)](https://packagist.org/packages/techdivision/import-category)
 [![Build Status](https://img.shields.io/travis/techdivision/import-category/master.svg?style=flat-square)](http://travis-ci.org/techdivision/import-category)
 [![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/techdivision/import-category/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/techdivision/import-category/?branch=master)
 [![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/techdivision/import-category/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/techdivision/import-category/?branch=master)

Please visit the Pacemaker [website](https://pacemaker.techdivision.com) or our [documentation](https://docs.met.tdintern.de/pacemaker/1.3/) for additional information

## Multistore URL Rewrite Import
* Importer supports multistore url_key, just note that if the file contains only default store row for a new category, only one entry for defualt store is stored in the **catalog_product_entity_varchar** table and a key is stored in the **url_rewrite** table for all stores.
* When updating this category with a new key for this category, it will be changed for all stores.
* To avoid this problem, the default line can be omitted and only the specific store imported.

### Special case
In some cases the default row must be included in the product file, in which case, if there are no entries in the varchar for the stores already, the url_rewrite for all stores is updated based on what is in the default column.

### Possible solution
* To solve the problem only the default store and the specific store should be imported and so the category will only be imported for the store and the specific store.
* import should always include all stores in a new category, so each store has an entry in the **catalog_product_entity_varchar** table for the **url_key** attribute.
* If the category is only to be updated in a store, the default row must be supplied with the specific store, then the url_rewrtie and url_key are updated in the Varchar table.