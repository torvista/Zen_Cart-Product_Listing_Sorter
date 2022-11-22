<?php

declare(strict_types=1);
/** Plugin Product Listing Sorter
 * https://github.com/torvista/Zen_Cart-Product_Listing_Sorter
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @torvista 25/11/2022
 * phpstorm inspection
 * @var string $cPath
 * @var array $cPath_array
 * @var string $current_category_id
 * @var queryFactoryResult $db
 */
/**
 * default_filter.php  for index filters
 *
 * index filter for the default product type
 * show the products of a specified manufacturer
 *
 * @copyright Copyright 2003-2022 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @todo Need to add/fine-tune ability to override or insert entry-points on a per-product-type basis
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: DrByte 2020 Jul 10 Modified in v1.5.8-alpha $
 */
if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}
if (isset($_GET['sort']) && strlen($_GET['sort']) > 3) {
  $_GET['sort'] = substr($_GET['sort'], 0, 3);
}
if (isset($_GET['alpha_filter_id']) && (int)$_GET['alpha_filter_id'] > 0) {
  $alpha_sort = " AND pd.products_name LIKE '" . chr((int)$_GET['alpha_filter_id']) . "%' ";
} else {
  $alpha_sort = '';
}
if (!isset($select_column_list)) {
  $select_column_list = '';
}
if (!isset($do_filter_list)) {
  $do_filter_list = false;
}
$and = '';
// show the products of a specified manufacturer
if (isset($_GET['manufacturers_id']) && $_GET['manufacturers_id'] != '') {
  // We show them all
  $and = " AND m.manufacturers_id = " . (int)$_GET['manufacturers_id'] . " ";
  if (isset($_GET['filter_id']) && zen_not_null($_GET['filter_id'])) {
// We are asked to show only a specific category
    $and .= " AND p2c.categories_id = " . (int)$_GET['filter_id'] . " ";
  } else {
    $and .= ' AND p2c.categories_id = p.master_categories_id ';
  }
} else {
  // show the products in a given category
  // We show them all
  $and = " AND p2c.categories_id = " . (int)$current_category_id . " ";
  if (isset($_GET['filter_id']) && zen_not_null($_GET['filter_id'])) {
    // We are asked to show only specific category
    $and .= " AND m.manufacturers_id = " . (int)$_GET['filter_id'] . " ";
  }
}
$listing_sql = "SELECT " . $select_column_list . " p.products_id, p.products_type, p.master_categories_id, p.manufacturers_id, p.products_price, p.products_tax_class_id, pd.products_description,
                       IF(s.status = 1, s.specials_new_products_price, NULL) AS specials_new_products_price,
                       IF(s.status = 1, s.specials_new_products_price, p.products_price) AS final_price,
                       p.products_sort_order, p.product_is_call, p.product_is_always_free_shipping, p.products_qty_box_status
                FROM " . TABLE_PRODUCTS . " p
                LEFT JOIN " . TABLE_SPECIALS . " s ON s.products_id = p.products_id
                LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON pd.products_id = p.products_id
                  AND pd.language_id = " . (int)$_SESSION['languages_id'] . "
                LEFT JOIN " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c ON p2c.products_id = p.products_id
                LEFT JOIN " . TABLE_MANUFACTURERS . " m ON m.manufacturers_id = p.manufacturers_id
                WHERE p.products_status = 1
                " . $and . "
                " . $alpha_sort;
// plugin Product Listing Sorter 1 of 4
$debug_pls = false;   // boolean: true/false, show debugging information
if (defined('PRODUCT_LISTING_SORTER') && PRODUCT_LISTING_SORTER === 'true') {
//$pls_listing_sql only used for custom sort option
    unset($pls_listing_sql);
    if (isset($_GET['pls_sort']) && $_GET['pls_sort'] === '0') {
        unset($_GET['pls_sort']);
        //restore default
        unset($_GET['sort']);
    } elseif (isset($_GET['pls_sort'])) {
        //equating the GET sort means any column headers will display +/- correctly too. But it means the sort value displayed in the address bar will be the previous value, not the current value = pls_sort
        $_GET['sort'] = $_GET['pls_sort'];
    }
}
// eof plugin Product Listing Sorter 1 of 4
// set the default sort order setting from the Admin when not defined by customer
if (!isset($_GET['sort']) and PRODUCT_LISTING_DEFAULT_SORT_ORDER != '') {
  $_GET['sort'] = PRODUCT_LISTING_DEFAULT_SORT_ORDER;
}
// plugin Product Listing Sorter 2 of 4
if ($debug_pls) {
    $fileinfo = '<i>no_file_info</i>';
    $backtrace = debug_backtrace();
    if (!empty($backtrace[0]) && is_array($backtrace[0])) {
        $fileinfo = $backtrace[0]['file'] . ":" . $backtrace[0]['line'];
    }
    echo '<b>default_filter</b>:    ' . __LINE__ . '<br>' .
        'called from ' . $fileinfo . '<br>' .
        '$_SESSION[\'sort\']=' . ($_SESSION['sort'] ?? 'not set') . '<br>' .
        'PRODUCT_LISTING_DEFAULT_SORT_ORDER =' . PRODUCT_LISTING_DEFAULT_SORT_ORDER . '<br>' .
        'alpha sort ' . (PRODUCT_LIST_ALPHA_SORTER === 'true' ? 'ACTIVE, $alpha_sort clause="' . $alpha_sort : 'INACTIVE') . '<br>' .
        '$_GET[\'sort\'] =' . ($_GET['sort'] ?? 'not set') . '<br>' .
        '$column_list =' . (isset($column_list) ? (is_array($column_list) ? '<pre>' . print_r($column_list, true) . '</pre>' : $column_list) : 'not set') . '<br>' .
        '$_GET[\'pls_sort\'] =' . ($_GET['pls_sort'] ?? 'not set') . '<br>' .
        '$cPath_array:<pre>' . print_r($cPath_array, true) . '</pre>' .
        '$listing_sql="' . $listing_sql . '"<br>';
}

if (defined('PRODUCT_LISTING_SORTER') && PRODUCT_LISTING_SORTER === 'true') {
    //this file is parsed twice:https://github.com/zencart/zencart/issues/5396
    //so need to (re)initialise it
    $product_listing_sorter_options_custom = [];

    //ADD a possible custom sort options definition per category/sub-categories
    //$cPath_array is not always a full path, so make one
    $cPath_full = array_reverse(explode('_', zen_get_generated_category_path_ids($current_category_id)));
    if ($debug_pls) {
        echo __LINE__ . ': $cPath_full:<pre>' . print_r($cPath_full, true) . '</pre>';
    }
    //check if a custom sort options list is defined for the current category or any of it's parents
    foreach ($cPath_full as $key => $pls_category) {
        if ($debug_pls) {
            echo __LINE__ . ': parsing $cPath_full[' . $key . '][' . $pls_category . ']<br>';
        }
        if (defined('PRODUCT_LISTING_SORTER_CUSTOM_CATEGORY_' . $pls_category)) {
            // make array from the define
            $pls_custom_define = explode(';', constant('PRODUCT_LISTING_SORTER_CUSTOM_CATEGORY_' . $pls_category));
            if ($debug_pls) {
                echo __LINE__ . ': parsed PRODUCT_LISTING_SORTER_CUSTOM_CATEGORY_' . $pls_category . '<br>' .
                    '$pls_custom_define:<pre>' . print_r($pls_custom_define, true) . '</pre>';
            }
            break;
        }
    }

    if (isset($pls_custom_define) && count($pls_custom_define) > 0) {
        if (isset($column_list)) {
            //the core sort ids are dynamic and assigned in sequential order based on the admin switches
            $get_sort_last = count($column_list) - 1;
        } else {
            $get_sort_last = '';
        }
        foreach ($pls_custom_define as $pls_custom_option) {
            $pls_custom_array = explode(':', $pls_custom_option);
            //use the next sort id
            $product_listing_sorter_options_custom[] = ['id' => $get_sort_last, 'text' => $pls_custom_array[0], 'order_by' => $pls_custom_array[1]];

            if (isset($_GET['pls_sort']) && ($get_sort_last === (int)$_GET['pls_sort'])) {
                if ($debug_pls) {
                    echo __LINE__ . ': custom pls_sort found:' . $get_sort_last . '<br>';
                }
                $pls_listing_sql = $listing_sql . ' ORDER BY ' . $pls_custom_array[1];
            }

            $get_sort_last++;
        }
        if ($debug_pls) {
            echo __LINE__ . ': $product_listing_sorter_options_custom:<pre>' . print_r($product_listing_sorter_options_custom, true) . '</pre>';
        }
    }
}
// eof plugin Product Listing Sorter 2 of 4
// plugin Product Listing Sorter 3 of 4
// add exception if a custom sort option has been used (which may be greater than 8)
//if (isset($column_list)) {
if (isset($column_list) && !isset($pls_listing_sql)) {
// eof plugin Product Listing Sorter 3 of 4
    if (!isset($_GET['sort'])
      || !preg_match('/[1-8][ad]/', $_GET['sort'])
      || (substr($_GET['sort'], 0, 1) > sizeof($column_list))) {for ($i = 0, $n = sizeof($column_list); $i < $n; $i++) {
      if (isset($column_list[$i]) && $column_list[$i] == 'PRODUCT_LIST_NAME') {
        $_GET['sort'] = $i + 1 . 'a';
        $listing_sql .= " ORDER BY p.products_sort_order, pd.products_name";
        break;
      } else {
        // sort by products_sort_order when PRODUCT_LISTING_DEFAULT_SORT_ORDER is left blank
        // for reverse, descending order use:
        // $listing_sql .= " order by p.products_sort_order desc, pd.products_name";
        $listing_sql .= " ORDER BY p.products_sort_order, pd.products_name";
        break;
      }
    }
    // if set to nothing use products_sort_order and PRODUCTS_LIST_NAME is off
    if (PRODUCT_LISTING_DEFAULT_SORT_ORDER == '') {
      $_GET['sort'] = '20a';
    }
  } else {
    $sort_col = substr($_GET['sort'], 0, 1);
    $sort_order = substr($_GET['sort'], -1);
    switch ($column_list[$sort_col - 1]) {
      case 'PRODUCT_LIST_MODEL':
        $listing_sql .= " ORDER BY p.products_model " . ($sort_order == 'd' ? 'DESC' : '') . ", pd.products_name";
        break;
      case 'PRODUCT_LIST_NAME':
        $listing_sql .= " ORDER BY pd.products_name " . ($sort_order == 'd' ? 'DESC' : '');
        break;
      case 'PRODUCT_LIST_MANUFACTURER':
        $listing_sql .= " ORDER BY m.manufacturers_name " . ($sort_order == 'd' ? 'DESC' : '') . ", pd.products_name";
        break;
      case 'PRODUCT_LIST_QUANTITY':
        $listing_sql .= " ORDER BY p.products_quantity " . ($sort_order == 'd' ? 'DESC' : '') . ", pd.products_name";
        break;
      case 'PRODUCT_LIST_IMAGE':
        $listing_sql .= " ORDER BY pd.products_name";
        break;
      case 'PRODUCT_LIST_WEIGHT':
        $listing_sql .= " ORDER BY p.products_weight " . ($sort_order == 'd' ? 'DESC' : '') . ", pd.products_name";
        break;
      case 'PRODUCT_LIST_PRICE':
        $listing_sql .= " ORDER BY p.products_price_sorter " . ($sort_order == 'd' ? 'DESC' : '') . ", pd.products_name";
        break;
    }
  }
}
// plugin Product Listing Sorter 4 of 4
if (defined('PRODUCT_LISTING_SORTER') && PRODUCT_LISTING_SORTER === 'true' && isset($pls_listing_sql)) {
    $listing_sql = $pls_listing_sql;
    if ($debug_pls) {
        echo __LINE__ . '<br>' . '$listing_sql="' . $listing_sql . '"<br>';
    }
}
// eof plugin Product Listing Sorter 4 of 4
// optional Product List Filter
if (PRODUCT_LIST_FILTER > 0) {
  if (isset($_GET['manufacturers_id']) && $_GET['manufacturers_id'] != '') {
    $filterlist_sql = "SELECT c.categories_id AS id, cd.categories_name AS name
                       FROM " . TABLE_PRODUCTS . " p
                       LEFT JOIN " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c ON p2c.products_id = p.products_id
                       LEFT JOIN " . TABLE_CATEGORIES . " c ON c.categories_id = p2c.categories_id
                       LEFT JOIN " . TABLE_CATEGORIES_DESCRIPTION . " cd ON cd.categories_id = p2c.categories_id
                         AND cd.language_id = " . (int)$_SESSION['languages_id'] . "
                       WHERE p.products_status = 1
                       AND p.manufacturers_id = " . (int)$_GET['manufacturers_id'] . "
                       GROUP BY c.categories_id, cd.categories_name
                       ORDER BY cd.categories_name";
  } else {
    $filterlist_sql = "SELECT m.manufacturers_id AS id, m.manufacturers_name AS name
                       FROM " . TABLE_PRODUCTS . " p
                       LEFT JOIN " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c ON p2c.products_id = p.products_id
                       JOIN " . TABLE_MANUFACTURERS . " m ON m.manufacturers_id = p.manufacturers_id
                       WHERE p.products_status = 1
                       AND p2c.categories_id = " . (int)$current_category_id . "
                       GROUP BY m.manufacturers_id, m.manufacturers_name
                       ORDER BY m.manufacturers_name";
  }
  $do_filter_list = false;
  $filterlist = $db->Execute($filterlist_sql);
  if ($filterlist->RecordCount() > 1) {
    $do_filter_list = true;
    if (isset($_GET['manufacturers_id'])) {
      $getoption_set = true;
      $get_option_variable = 'manufacturers_id';
      $options = array(array(
          'id' => '',
          'text' => TEXT_ALL_CATEGORIES
      ));
    } else {
      $options = array(array(
          'id' => '',
          'text' => TEXT_ALL_MANUFACTURERS
      ));
    }
    foreach ($filterlist as $item) {
      $options[] = array(
        'id' => $item['id'],
        'text' => $item['name']
      );
    }
  }
}
