# M2IF - Category Import

[![Latest Stable Version](https://img.shields.io/packagist/v/techdivision/import-category.svg?style=flat-square)](https://packagist.org/packages/techdivision/import-category) 
 [![Total Downloads](https://img.shields.io/packagist/dt/techdivision/import-category.svg?style=flat-square)](https://packagist.org/packages/techdivision/import-category)
 [![License](https://img.shields.io/packagist/l/techdivision/import-category.svg?style=flat-square)](https://packagist.org/packages/techdivision/import-category)
 [![Build Status](https://img.shields.io/travis/techdivision/import-category/master.svg?style=flat-square)](http://travis-ci.org/techdivision/import-category)
 [![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/techdivision/import-category/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/techdivision/import-category/?branch=master)
 [![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/techdivision/import-category/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/techdivision/import-category/?branch=master)

## Columns in CSV file

| Column Name           | Type     | Description                                                                           | Example |
|:----------------------|:---------|:--------------------------------------------------------------------------------------|:--------|
| store_view_code       | varchar  |                                                                                       |         |
| attribute_set_code    | varchar  |                                                                                       |         |
| name                  | varchar  |                                                                                       |         |
| path                  | varchar  |                                                                                       |         |
| description           | varchar  |                                                                                       |         |
| url_key               | varchar  |                                                                                       |         |
| meta_title            | varchar  |                                                                                       |         |
| meta_keywords         | text     |                                                                                       |         |
| meta_description      | text     |                                                                                       |         |
| image                 | varchar  |                                                                                       |         |
| image_label           | varchar  |                                                                                       |         |
| created_at            | varchar  |                                                                                       |         |
| updated_at            | varchar  |                                                                                       |         |
| additional_attributes | varchar  |                                                                                       |         |

## Additional Attributes

| Attribute Code             | Type     | Description                                                                           | Example |
|:---------------------------|:---------|:--------------------------------------------------------------------------------------|:--------|
| all_children               | text     |                                                                                       |         |
| automatic_sorting          | text     |                                                                                       |         |
| available_sort_by          | text     |                                                                                       |         |
| children                   | text     |                                                                                       |         |
| custom_apply_to_products   | int      |                                                                                       |         |
| custom_design              | varchar  |                                                                                       |         |
| custom_design_from         | datetime |                                                                                       |         |
| custom_design_to           | datetime |                                                                                       |         |
| custom_layout_update       | text     |                                                                                       |         |
| custom_use_parent_settings | int      |                                                                                       |         |
| default_sort_by            | varchar  |                                                                                       |         |
| display_mode               | varchar  |                                                                                       |         |
| filter_price_range         | decimal  |                                                                                       |         |
| image                      | varchar  |                                                                                       |         |
| include_in_menu            | int      |                                                                                       |         |
| is_active                  | int      |                                                                                       |         |
| is_anchor                  | int      |                                                                                       |         |
| is_virtual_category        | int      | Is the category virtual or not?                                                       |         |
| landing_page               | int      |                                                                                       |         |
| name                       | varchar  |                                                                                       |         |
| page_layout                | varchar  |                                                                                       |         |
| path_in_store              | text     |                                                                                       |         |
| url_path                   | varchar  |                                                                                       |         |
| use_name_in_product_search | int      | If the category name is used for fulltext search on products                          |         |
| virtual_category_root      | int      | Root display of the virtual category (usefull to display a facet category on virtual) |         |
| virtual_rule               | text     | Virtual category rule                                                                 |         |