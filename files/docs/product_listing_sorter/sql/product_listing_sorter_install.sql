#add admin switch into the Admin->Product Lisintg page
INSERT INTO configuration 
(configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES 
('Include Product Listing Sorter Dropdown (mod)', 'PRODUCT_LISTING_SORTER', 'true', 'Do you want to include the Product Listing Sorter dropdown on the Product Listing page?', '8', '51', 'zen_cfg_select_option(array(\'true\', \'false\'), ', now());
