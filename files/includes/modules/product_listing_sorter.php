<?php

declare(strict_types=1);
/** Plugin Product Listing Sorter
 * https://github.com/torvista/Zen_Cart-Product_Listing_Sorter
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @torvista 25/11/2022
 * phpstorm inspection
 * @var string $cPath
 * @var array $cPath_array
 * @var queryFactoryResult $db
 * @var string $debug_pls
 * @var array $pls_sort_options
 */
/**
 * loosely based on product_listing_alpha_sorter module
 *
 * @copyright Copyright 2003-2022 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: DrByte 2020 Jul 10 Modified in v1.5.8-alpha $
 */
if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
}
if (defined('PRODUCT_LISTING_SORTER') && PRODUCT_LISTING_SORTER === 'true') {
    if ($debug_pls) {
        echo '<br><b>modules/product_listing_sorter</b>: ' . __LINE__ . '<br>';
    }
// identify the admin-enabled columns and get their column id
    if (isset($column_list)) {
        $product_listing_sorter_options = [];
        // $product_listing_sorter_options[] = compact('id', 'text');
        //$product_listing_sorter_options = compact('id', 'text');
        foreach ($column_list as $key => $value) {
            /* example $column_list
            The order is depends on the numbers used in the admin for each constant: not a fixed order
            Array(
                [0] => PRODUCT_LIST_IMAGE
                [1] => PRODUCT_LIST_NAME
                [2] => PRODUCT_LIST_PRICE
                [3] => PRODUCT_LIST_MODEL
            )
            */
            // copied from default_filter.php. All are admin constants in Configuration->Product Listing
            // the select options array is built/displayed in the same order of priority as $column_list
            // the id value should match any that is set GET sort
            $sort_col = substr($_GET['sort'], 0, 1);
            $sort_col = (int)$key + 1;
            switch ($value) {
                case 'PRODUCT_LIST_MODEL':
                    $product_listing_sorter_options[] = ['id' => $sort_col . 'a', 'text' => TEXT_MODEL . ' ' . TEXT_ASC];
                    $product_listing_sorter_options[] = ['id' => $sort_col . 'd', 'text' => TEXT_MODEL . ' ' . TEXT_DESC];
                    break;
                case 'PRODUCT_LIST_NAME':
                    $product_listing_sorter_options[] = ['id' => $sort_col . 'a', 'text' => TEXT_NAME . ' ' . TEXT_ASC];
                    $product_listing_sorter_options[] = ['id' => $sort_col . 'd', 'text' => TEXT_NAME . ' ' . TEXT_DESC];
                    break;
                case 'PRODUCT_LIST_MANUFACTURER':
                    $product_listing_sorter_options[] = ['id' => $sort_col . 'a', 'text' => TEXT_MANUFACTURER . ' ' . TEXT_ASC];
                    $product_listing_sorter_options[] = ['id' => $sort_col . 'd', 'text' => TEXT_MANUFACTURER . ' ' . TEXT_DESC];
                    break;
                case 'PRODUCT_LIST_QUANTITY':
                    $product_listing_sorter_options[] = ['id' => $sort_col . 'a', 'text' => TEXT_QUANTITY . ' ' . TEXT_ASC];
                    $product_listing_sorter_options[] = ['id' => $sort_col . 'd', 'text' => TEXT_QUANTITY . ' ' . TEXT_DESC];
                    break;
                //not likely
                /* case 'PRODUCT_LIST_IMAGE':
                     $product_listing_sorter_options[] = ['id' => $sort_col . 'a', 'text' => TEXT_IMAGE . ' ' . TEXT_ASC];
                     $product_listing_sorter_options[] = ['id' => $sort_col . 'd', 'text' => TEXT_IMAGE . ' ' . TEXT_ASC];
                     break;
                */
                case 'PRODUCT_LIST_WEIGHT':
                    $product_listing_sorter_options[] = ['id' => $sort_col . 'a', 'text' => TEXT_WEIGHT . ' ' . TEXT_ASC];
                    $product_listing_sorter_options[] = ['id' => $sort_col . 'd', 'text' => TEXT_WEIGHT . ' ' . TEXT_DESC];
                    break;
                case 'PRODUCT_LIST_PRICE':
                    $product_listing_sorter_options[] = ['id' => $sort_col . 'a', 'text' => TEXT_PRICE . ' ' . TEXT_ASC];
                    $product_listing_sorter_options[] = ['id' => $sort_col . 'd', 'text' => TEXT_PRICE . ' ' . TEXT_DESC];
                    break;
            }
        }
    }

    if (isset($product_listing_sorter_options_custom) && count($product_listing_sorter_options_custom) > 0) {
        foreach ($product_listing_sorter_options_custom as $value) {
            array_unshift($product_listing_sorter_options, $value);
        }
    }
//add the Sort/Reset text to the options
    if (!isset($_GET['pls_sort']) || $_GET['pls_sort'] === '') {
        $id = '';
        $text = TEXT_SORT_BY;
    } else {
        $id = '0';
        $text = TEXT_DROPDOWN_RESET;
    }
    array_unshift($product_listing_sorter_options, ['id' => $id, 'text' => $text]);

    if ($debug_pls) {
        echo __LINE__ . ': $product_listing_sorter_options:<pre>' . print_r($product_listing_sorter_options, true) . '</pre>';
    }

    //if you want a label, uncomment the label (!)
    // echo '<label for="productListingSorter" class="inputLabel" style="float:none">' . TEXT_SORT_BY . '</label>';

    echo zen_draw_pull_down_menu(
        'pls_sort',
        $product_listing_sorter_options,
        $_GET['sort'],
        // function zen_draw_pull_down_menu looks for this value in the options array to make this selected. Note that if pls_sort used, the GET sort value shown in the address bar will be that of the previous page, NOT the current value, as it is loaded with GET pls_sort in the default_filter
        'title="' . TEXT_SORT_BY . '" id="productListingSorter" onchange="this.form.submit()"'
    );
}

