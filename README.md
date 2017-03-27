# M2IF - Category Import

[![Latest Stable Version](https://img.shields.io/packagist/v/techdivision/import-category.svg?style=flat-square)](https://packagist.org/packages/techdivision/import-category) 
 [![Total Downloads](https://img.shields.io/packagist/dt/techdivision/import-category.svg?style=flat-square)](https://packagist.org/packages/techdivision/import-category)
 [![License](https://img.shields.io/packagist/l/techdivision/import-category.svg?style=flat-square)](https://packagist.org/packages/techdivision/import-category)
 [![Build Status](https://img.shields.io/travis/techdivision/import-category/master.svg?style=flat-square)](http://travis-ci.org/techdivision/import-category)
 [![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/techdivision/import-category/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/techdivision/import-category/?branch=master)
 [![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/techdivision/import-category/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/techdivision/import-category/?branch=master)

This library provides a generic approach to import categories in an existing Magento 2 CE/EE instance.

As Magento 2 doesn't provide any category import functionality by default, this library provides this 
functionality with some restrictions. 

## Restrictions

* As there is **NO** unique identifier for categories, like a SKU for products, the library uses the category
  path instead. This makes it necessary, that the value specified in the column `path` is unique, whereas
  Magento itself would basically allow the same category path. This restriction should not have a real big
  impact on the most projects, as it usually doesn't make sense to have the same category path multiple times.
* Only **ONE** store view code per row is allowed
* Multilanguage functionality by providing the same path in multiple rows and different store view code is not
  yet tested

## CSV File Structure

By default, the category import expects a CSV file with the following defaults

* UTF-8 encoding
* Date format is n/d/y, g:i A
* Values delimiter is a comma (,)
* Multiple value delimiter is a pipe (|)
* Text values are enclosed with double apostrophes (")
* Special chars are secaped with a backslash (\)

> Columns that doesn't contain a value are ignored by default. This means, it is **NOT** possible to delete or override
> an existing value with an empty value. To delete an existing value, the whole category has to be removed by running 
> an import with the `delete` operation. After that, the category with the new values can be imported by running an 
> `add-update` operation.

The CSV file with the categories for the Magento 2 CE/EE consists of the following columns

| Column Name                | Type     | Mandatory | Description                                                                           | Example |
|:---------------------------|:---------|:----------| :-------------------------------------------------------------------------------------|:--------|
| store_view_code            | varchar  | yes       | The specific store view(s) where the category is available. If blank, the category is available at the default store view. | default,german,english |
| attribute_set_code         | varchar  | yes       | Assigns the product to a specific attribute set or product template, according to product type. Once the product is created, the attribute set cannot be changed. | default |
| path                       | varchar  | yes       | The complete category path, including the root category.                              | Default Category/MyCategory |
| name                       | varchar  | yes       | The category name appears the naviagtion, and is the name that customers use to identify the category. | My Category |
| is_active                  | int      | yes       | Enables or disables the category.                                                     | 1       |
| is_anchor                  | int      | yes       | If the category is anchor, the category's products as well as the products of the subcategories will be listed. | 1       |
| include_in_menu            | int      | yes       | Specifies if the category will be included in the menu or not.                        | 1       |
| use_name_in_product_search | int      | yes       | If the category name is used for fulltext search on products                          | 1       |
| display_mode               | varchar  | yes       | One of "Products only", "Static block only" or "Static block and products"            | Products only |
| url_key                    | varchar  | yes       | The category's unique URL key                                                         | my-category |
| description                | text     | no        | The category description, that'll be rendered on the category page                    | Some longer text here |
| image_path                 | varchar  | no        | The absolute or relative path to a category image file                                | images/categories/my-category.png |
| meta_title                 | varchar  | no        | The category's title that'll be rendered in the category page's <title> tag           | My Category Name |
| meta_keywords              | text     | no        | The category's meta keywords that'll be rendered in the category page's <meta name="keywords"> tag | Category Name, Keyword 1, Keyword 2 |
| meta_description           | text     | no        | The category's meta description that'll be rendered in the category page's <meta name="description"> tag | A good Description with SEO relevant content |
| landing_page               | int      | no        | The ID of a CMS block that has to be rendered in the category page                    | 2       |
| position                   | int      | no        | The category's position in the navigation                                             | 10      |
| custom_design              | varchar  | no        | The custom design name used to display the catgory                                    | Magneto Blank |
| custom_design_from         | datetime | no        | The start date for the scheduled design update                                        | 10/24/16, 12:36 PM |
| custom_design_to           | datetime | no        | The end date for the scheduled design update                                          | 10/24/16, 12:36 PM |
| page_layout                | varchar  | no        | A custom page layout used to disploy the category, one of 1 column, 2 columns with left bar, 2 columns with right bar, 3 columns, Empty | 1 column |
| custom_layout_update       | text     | no        | A custom page layout update in XML format                                             | <referenceContainer name="catalog.leftnav" remove="true"/> |
| available_sort_by          | text     | no        | The comma separated product list sortings for the catgory                             | Position,Name |
| default_sort_by            | varchar  | no        | The default product list sorting for the category                                     | Position |
| custom_apply_to_products   | int      | no        | If set to 1, the design will also be applied to the products listed in the category   | 1       |
| custom_use_parent_settings | int      | no        | Overrides the custom design settings with the default one's                           | 1       |
| filter_price_range         | decimal  | no        | The layered navigation price steps                                                    | 100.00  |
| created_at                 | varchar  | no        | The category's creation date                                                          | 10/24/16, 12:36 PM |
| updated_at                 | varchar  | no        | The date when the category has been updated                                           | 10/24/16, 12:36 PM |
| additional_attributes      | text     | no        | A comma separated list with additional attributes (the attributes **MUST** already be available) | custom_attribute_01=a-value,custom_attribute_02=value-01|value-02 |
