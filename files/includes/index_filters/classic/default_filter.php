<?php //MOD Product Listing Sorter, show disabled products for specific logged in customers
$debug_pls = false;//true/false, show debugging information
$debug_pls_msg  = '';
/**
 * default_filter.php  for index filters
 *
 * index filter for the default product type
 * show the products of a specified manufacturer
 *
 * @package productTypes
 * @copyright Copyright 2003-2019 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @todo Need to add/fine-tune ability to override or insert entry-points on a per-product-type basis
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: Scott C Wilson 2019 Jun 23 Modified in v1.5.6c $
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
//torvista Product Listing Sorter
if ($debug_pls) $debug_pls_msg  .= 'default_filter ' . __LINE__ . ': $_GET[\'sort\'] =' . $_GET['sort'] . '<br>';
if ($debug_pls && PRODUCT_LIST_ALPHA_SORTER === 'true') $debug_pls_msg  .= 'default_filter ' . __LINE__ . ': $alpha_sort=' . $alpha_sort . '<br>';
    $sorter_list_search = explode(';', '0:reset_placeholder;' . PRODUCT_LISTING_SORTER_LIST);//this constant is the drop-down-list text options
 if (!empty($_GET['product_listing_sorter_id']) && array_key_exists($_GET['product_listing_sorter_id'], $sorter_list_search)){
     $product_listing_sorter_id = (int)$_GET['product_listing_sorter_id'];
     if ($debug_pls) $debug_pls_msg  .= 'default_filter ' . __LINE__ . ': $product_listing_sorter_id=' . $product_listing_sorter_id . '<br>';
    for ($j = 0, $n = count($sorter_list_search); $j < $n; $j++) {
        if ($product_listing_sorter_id === $j) {//equate the id with the drop-down list to decide the sorting clause
            $elements_sorter_list = explode(':', $sorter_list_search[$j]);
            $pattern_multi = str_replace(',', '', $elements_sorter_list[1]);//@TODO torvista eats commas
            $product_listing_sorter = " ORDER BY " . $pattern_multi;
            $debug_pls_msg  .= 'default_filter ' . __LINE__ . ': $product_listing_sorter=' . $product_listing_sorter . '<br>';
            break;
        }
    }
} else {//default setting/no selection made/first page load
    $product_listing_sorter = '';
    if ($debug_pls)  $debug_pls_msg  .= 'default_filter ' . __LINE__ . ': no selection, $product_listing_sorter set to \'\'<br>';
}
//eof PLS
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
//torvista show disabled products when admin customer logged in
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
                WHERE (p.products_status = 1" . mv_show_disabled_products() . ")  
                " . $and . "
                " . $alpha_sort . $product_listing_sorter;
//torvista added . $product_listing_sorter; $show_disabled_products above
// set the default sort order setting from the Admin when not defined by customer
if (!isset($_GET['sort']) and PRODUCT_LISTING_DEFAULT_SORT_ORDER != '') {
  $_GET['sort'] = PRODUCT_LISTING_DEFAULT_SORT_ORDER;
}

//torvista ORIGINAL if (isset($column_list)) {
if (isset($column_list) && (!isset($_GET['product_listing_sorter_id']) || $_GET['product_listing_sorter_id']==0)) {//are some columns defined? //MOD Product Listing Sorter
  if ((!isset($_GET['sort'])) || (isset($_GET['sort']) && !preg_match('/[1-8][ad]/', $_GET['sort'])) || (substr($_GET['sort'], 0, 1) > sizeof($column_list))) {
    for ($i = 0, $n = sizeof($column_list); $i < $n; $i++) {
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
//MOD PLS
if (//torvista isset for php notice indefined index
(isset($_GET['sort']) && $_GET['sort'] !=0) || $alpha_sort != 0 || $product_listing_sorter != 0) {//MOD Product Listing Sorter: to make available for product info page prev/next
		 $_SESSION['listing_sql'] = $listing_sql;
}
//eof PLS
  $do_filter_list = false;
  $filterlist = $db->Execute($filterlist_sql);
  if ($filterlist->RecordCount() > 1) {
    $do_filter_list = true;
    if (isset($_GET['manufacturers_id'])) {
      $getoption_set = true;
      $get_option_variable = 'manufacturers_id';
      $options = array(array(
          'id' => '0',//torvista added 0
          'text' => TEXT_ALL_CATEGORIES
      ));
    } else {
      $options = array(array(
          'id' => '0',//torvista added 0
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
