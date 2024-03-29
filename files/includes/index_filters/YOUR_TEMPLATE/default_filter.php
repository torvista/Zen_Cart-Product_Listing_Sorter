<?php //plugin Product Listing Sorter: add override of $_GET['sort'] from pls dropdown
//declare(strict_types=1); //plugin Product Listing Sorter: debugging
/**
 * default_filter.php  for index filters
 *
 * index filter for the default product type
 * show the products of a specified manufacturer
 *
 * @copyright Copyright 2003-2020 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @todo Need to add/fine-tune ability to override or insert entry-points on a per-product-type basis
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: DrByte 2019 Jul 16 Modified in v1.5.7 $
 */
if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}
//plugin Product Listing Sorter 1 of 3
$pls_debug = false; //boolean: true for debug output
//custom sorting array - optional - examples
$pls_custom_sort = [];
//$pls_custom_sort[] = ['id' => 9, 'text' => PLS_TEXT_CUSTOM_SORT_ORDER1, 'order' => ' ORDER BY p.products_sort_order'];
//$pls_custom_sort[] = ['id' => 10, 'text' => PLS_TEXT_CUSTOM_SORT_ORDER2, 'order' => ' ORDER BY p.products_weight'];
//////////////////////////////////////////////////
if ($pls_debug) {
    echo '<mark>' . __LINE__ . ': PRODUCT_LISTING_DEFAULT_SORT_ORDER=' . PRODUCT_LISTING_DEFAULT_SORT_ORDER . '<br>';
}
if (isset($_GET['product_listing_sorter']) && $_GET['product_listing_sorter'] !== '') {
    if ($pls_debug) {
        echo '<mark>' . __LINE__ . ': $_GET[\'product_listing_sorter\']=' . $_GET['product_listing_sorter'] . '<br>';
    }
    if ($_GET['product_listing_sorter'] === '0') { //from dropdown "Reset"
        $_GET['product_listing_sorter'] = ''; //sets dropdown to "Choose.."
        // set the default sort order from the Admin constant if not defined by customer
        $_GET['sort'] = PRODUCT_LISTING_DEFAULT_SORT_ORDER !== '' ? PRODUCT_LISTING_DEFAULT_SORT_ORDER : '20a';
    } else {
        $_GET['sort'] = $_GET['product_listing_sorter'];
    }
} elseif ($pls_debug) {
    echo '<mark>' . __LINE__ . ': <mark>$_GET[\'product_listing_sorter\']=NOT SET</mark><br>';
}
if ($pls_debug) {
    echo '<mark>' . __LINE__ . ': <mark>$_GET[\'sort\']=' . ($_GET['sort'] ?? 'NOT SET') . '</mark><br>';
}
//eof plugin Product Listing Sorter 1 of 3
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

// set the default sort order setting from the Admin when not defined by customer
if (!isset($_GET['sort']) and PRODUCT_LISTING_DEFAULT_SORT_ORDER != '') {
  $_GET['sort'] = PRODUCT_LISTING_DEFAULT_SORT_ORDER;
}

if (isset($column_list)) {
  if ((!isset($_GET['sort'])) || (isset($_GET['sort']) && !preg_match('/[1-8][ad]/', $_GET['sort'])) || (substr($_GET['sort'], 0, 1) > sizeof($column_list))) {
    for ($i = 0, $n = sizeof($column_list); $i < $n; $i++) {
      if (isset($column_list[$i]) && $column_list[$i] == 'PRODUCT_LIST_NAME') {
        $_GET['sort'] = $i + 1 . 'a';
        $listing_sql .= " ORDER BY p.products_sort_order, pd.products_name";
        break;
        }
//plugin Product Listing Sorter 2 of 3
        //note the above vanilla clause is used to check for $_GET['sort'] not being set or containing invalid values: in which case it gets set with a valid value.
        if ($pls_debug) {
            echo  __LINE__ . ': $column_list <pre>';
            print_r($column_list);
            echo '</pre>';
        }
        if (isset($pls_custom_sort, $_GET['sort']) && count($pls_custom_sort) > 0) {//custom sorting array is in use
                $sort_col = substr($_GET['sort'], 0, 1);
                $sort_order = substr($_GET['sort'], -1);
                foreach ($pls_custom_sort as $custom_sort) {
                    if ($custom_sort['id'] === (int)$sort_col) {
                        $listing_sql .= $custom_sort['order'] . ($sort_order === 'd' ? ' DESC' : '');
                        break 2;
                    }
                }
//eof plugin Product Listing Sorter 2 of 3
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
//plugin Product Listing Sorter 3 of 3
//if a sort is set, OR an alpha sort is set, OR PLS is set: make session listing_sql available for product info page prev/next
if ( (isset($_GET['sort']) && $_GET['sort'] !== '0') || $alpha_sort !== '' || (isset($_GET['product_listing_sorter']) && $_GET['product_listing_sorter'] !== '0') ) {
		 $_SESSION['listing_sql'] = $listing_sql;
    if ($pls_debug) {
        echo '<mark>' . __LINE__ . ': $_GET[\'product_listing_sorter\']=' . ($_GET['product_listing_sorter'] ?? 'NOT SET') . '<br>';
        echo '<mark>' . __LINE__ . ': $_GET[\'sort\']=' . $_GET['sort'] . '<br>';
        echo '<mark>' . __LINE__ . ': $_SESSION[\'listing_sql\']=' . $_SESSION['listing_sql'] . '<br>';
    }
}
//eof plugin Product Listing Sorter 3 of 3
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
