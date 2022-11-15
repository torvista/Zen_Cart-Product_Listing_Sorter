<?php
/** Plugin Product Listing Sorter
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 */

  //prevent page error if install sql not yet
  if (!defined('PRODUCT_LISTING_SORTER')) {
#add admin switch into the Admin->Product Listing page
      $sql_install = "INSERT INTO configuration
      (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added)
      VALUES
      ('Include Product Listing Sorter Dropdown (plugin)', 'PRODUCT_LISTING_SORTER', 'false', 'Do you want to show the Product Listing Sorter dropdown on the Product Listing page?', '8', '51', 'zen_cfg_select_option(array(\'true\', \'false\'), ', now());";
      $db->Execute($sql_install);
  }

