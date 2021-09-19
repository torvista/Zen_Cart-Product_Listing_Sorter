<?php
/** Plugin Product Listing Sorter
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 */
define('TEXT_PRODUCT_LISTING_SORTER_CHOOSE', 'Choose...'); // first item in drop-down when no sorting options selected
define('TEXT_PRODUCT_LISTING_SORTER_RESET', TEXT_PRODUCTS_LISTING_ALPHA_SORTER_NAMES_RESET);   // first item in drop-down when a sorting option is already in use
define('TEXT_IMAGE', 'Image');
define('TEXT_MODEL', 'Model');
define('TEXT_NAME', 'Name');
define('TEXT_PRICE', 'Price');
define('TEXT_QUANTITY', 'Quantity');
define('TEXT_WEIGHT', 'Image');
define('TEXT_DESC', 'desc');
define('TEXT_ASC', 'asc');
//custom sort examples
define('PLS_TEXT_CUSTOM_SORT_ORDER1', 'Sort Order');
define('PLS_TEXT_CUSTOM_SORT_ORDER2', 'Weight');
//constants in tienda
//Name (asc):pd.products_name;
//Name (desc):pd.products_name DESC;
define('PRODUCT_LISTING_SORTER_LIST', '
Price (↑):p.products_price_sorter;
Price (↓):p.products_price_sorter DESC;
Manufacturer:m.manufacturers_name;
Newest:p.products_date_added DESC;
Best Selling:p.products_ordered DESC;
Ref.(↑):p.products_model;
Ref.(↓):p.products_model DESC
');

define('TEXT_PRODUCT_LISTING_SORTER', 'Sort by:');//label for drop-down
define('TEXT_PRODUCT_LISTING_SORTER_NAMES', 'Choose..');//first item in drop-down when no sorting options selected
define('TEXT_PRODUCT_LISTING_SORTER_NAMES_RESET', '-Reset-');//first item in drop-down when a sorting option is already in use

define('PRODUCT_LISTING_SORTER_LIST_BIKES', '
Bike Specific:p.products_sort_order DESC;
Newest:p.products_date_added DESC;
Best Selling:p.products_ordered DESC;
Price (↑):p.products_price_sorter;
Price (↓):p.products_price_sorter DESC;
Manufacturer:m.manufacturers_name;
Ref.(↑):p.products_model;
Name (↑):pd.products_name;
Name (↓):pd.products_name DESC
');

