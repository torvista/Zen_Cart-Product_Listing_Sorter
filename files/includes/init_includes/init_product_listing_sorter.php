<?php

declare(strict_types=1);
/** Plugin Product Listing Sorter
 * https://github.com/torvista/Zen_Cart-Product_Listing_Sorter
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @torvista 25/11/2022
 */

  if (!defined('PRODUCT_LISTING_SORTER')) {
#add admin switch into the Admin->Product Listing page
      $sql_install = "INSERT INTO " . TABLE_CONFIGURATION . "
      (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added)
      VALUES
      ('Include Product Listing Sorter Dropdown (plugin)', 'PRODUCT_LISTING_SORTER', 'false', 'Do you want to show the Product Listing Sorter dropdown on the Product Listing page?', '8', '51', 'zen_cfg_select_option(array(\'true\', \'false\'), ', now());";
      $db->Execute($sql_install);
  }

