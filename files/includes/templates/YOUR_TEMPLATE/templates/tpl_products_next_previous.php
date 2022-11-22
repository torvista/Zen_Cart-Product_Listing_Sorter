<?php

declare(strict_types=1);
/** Plugin Product Listing Sorter
 * https://github.com/torvista/Zen_Cart-Product_Listing_Sorter
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @torvista 25/11/2022
 * phpstorm inspection
 * @var string $cPath
 * @var string $next_item
 * @var string $next_item_button
 * @var string $next_item_image
 * @var string $previous
 * @var string $previous_button
 * @var string $previous_image
 * */
/**
 * Page Template
 *
 * @package templateSystem
 * @copyright Copyright 2003-2019 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: Scott C Wilson 2019 Jan 14 Modified in v1.5.6b $
 * @author Linda McGrath osCommerce@WebMakers.com
 * @author Thanks to Nirvana, Yoja and Joachim de Boer
 */
?>
<?php
  if (!isset($display_as_mobile)) $display_as_mobile = ($detect->isMobile() && !$detect->isTablet() || $_SESSION['layoutType'] == 'mobile' or  $detect->isTablet() || $_SESSION['layoutType'] == 'tablet'); 
?>
<div class="navNextPrevWrapper centeredContent">
<?php
// only display when more than 1
  if ($products_found_count > 1) {
?>
<p class="navNextPrevCounter"><?php echo (PREV_NEXT_PRODUCT); ?><?php echo ($position+1 . "/" . $counter); ?></p>
<?php // plugin Product Listing Sorter 1 of 2
//button links modified to include/carry any extra parameters such as pls_sort 
      if (defined('PRODUCT_LISTING_SORTER') && PRODUCT_LISTING_SORTER === 'true') { ?>
          <div class="navNextPrevList"><a href="<?php
          echo zen_href_link(zen_get_info_page($previous), "cPath=$cPath&products_id=$previous" . (zen_get_all_get_params(['cPath', 'products_id', 'number_of_uploads']) !== '' ? '&' . zen_get_all_get_params(['cPath', 'products_id', 'number_of_uploads']) : '')); ?>"><?php
          if ($display_as_mobile) {
              echo '<i class="fa fa-chevron-circle-left" title="' . BUTTON_PREVIOUS_ALT . '"></i></a></div>'; ?><?php
          } else { ?><?php
              echo $previous_image . $previous_button; ?></a></div><?php
          } ?>
          <div class="navNextPrevList"><a href="<?php
          echo zen_href_link(FILENAME_DEFAULT, "cPath=$cPath" . (zen_get_all_get_params(['cPath', 'products_id', 'number_of_uploads']) !== '' ? '&' . zen_get_all_get_params(['cPath', 'products_id', 'number_of_uploads']) : '')); ?>"><?php
          if ($display_as_mobile) {
              echo '<i class="fa fa-list" title="' . BUTTON_VIEW_ALL_ALT . '"></i></a></div>'; ?><?php
          } else { ?><?php
              echo zen_image_button(BUTTON_IMAGE_RETURN_TO_PROD_LIST, BUTTON_RETURN_TO_PROD_LIST_ALT); ?></a></div><?php
          } ?>
          <div class="navNextPrevList"><a href="<?php
          echo zen_href_link(zen_get_info_page($next_item), "cPath=$cPath&products_id=$next_item" . (zen_get_all_get_params(['cPath', 'products_id', 'number_of_uploads']) !== '' ? '&' . zen_get_all_get_params(['cPath', 'products_id', 'number_of_uploads']) : '')); ?>"><?php
          if ($display_as_mobile) {
              echo '<i class="fa fa-chevron-circle-right" title="' . BUTTON_NEXT_ALT . '"></i></a></div>'; ?><?php
          } else { ?><?php
              echo $next_item_button . $next_item_image; ?></a></div><?php
          }
      } else {
// eof plugin Product Listing Sorter 1 of 2 ?>
<div class="navNextPrevList"><a href="<?php echo zen_href_link(zen_get_info_page($previous), "cPath=$cPath&products_id=$previous"); ?>"><?php if ($display_as_mobile) { echo '<i class="fa fa-chevron-circle-left" title="' . BUTTON_PREVIOUS_ALT . '"></i></a></div>';?><?php } else { ?><?php echo $previous_image . $previous_button; ?></a></div><?php } ?>

<div class="navNextPrevList"><a href="<?php echo zen_href_link(FILENAME_DEFAULT, "cPath=$cPath"); ?>"><?php if ($display_as_mobile) { echo '<i class="fa fa-list" title="' . BUTTON_VIEW_ALL_ALT . '"></i></a></div>';?><?php } else { ?><?php echo zen_image_button(BUTTON_IMAGE_RETURN_TO_PROD_LIST, BUTTON_RETURN_TO_PROD_LIST_ALT); ?></a></div><?php } ?>

<div class="navNextPrevList"><a href="<?php echo zen_href_link(zen_get_info_page($next_item), "cPath=$cPath&products_id=$next_item"); ?>"><?php if ($display_as_mobile) { echo '<i class="fa fa-chevron-circle-right" title="' . BUTTON_NEXT_ALT . '"></i></a></div>';?><?php } else { ?><?php echo  $next_item_button . $next_item_image; ?></a></div><?php } ?>

<?php //plugin Product Listing Sorter 2 of 2
  } 
// eof plugin Product Listing Sorter 2 of 2 ?>
<?php
  }
?>
</div>
