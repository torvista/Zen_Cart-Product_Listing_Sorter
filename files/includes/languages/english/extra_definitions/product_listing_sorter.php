<?php

declare(strict_types=1);
/** Plugin Product Listing Sorter
 * https://github.com/torvista/Zen_Cart-Product_Listing_Sorter
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @torvista 25/11/2022
 */

define('TEXT_SORT_BY', 'Sort by:');
define('TEXT_DROPDOWN_CHOOSE', 'Choose...'); // first item in drop-down when no sorting options selected
define('TEXT_DROPDOWN_RESET', '-- Reset --');   // first item in drop-down when a sorting option is already in use
define('TEXT_IMAGE', 'Image');
define('TEXT_MODEL', 'Model');
define('TEXT_NAME', 'Name');
define('TEXT_PRICE', 'Price');
define('TEXT_QUANTITY', 'Quantity');
define('TEXT_WEIGHT', 'Image');
define('TEXT_DESC', 'desc');
define('TEXT_ASC', 'asc');
//replace xx with the category/parent-category id
define('PRODUCT_LISTING_SORTER_CUSTOM_CATEGORY_xx', '
Category-Specific:p.products_sort_order DESC;
Generic:p.products_sort_order
');
