<?php //plugin Product Listing Sorter, loosely based on adjacent file product_listing_alpha_sorter
//declare(strict_types=1); //plugin Product Listing Sorter: debugging
/**
 * @var $column_list //phpStorm declaration
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 */

if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
}

// build sorter dropdown
if (PRODUCT_LISTING_SORTER === 'true') {

    if (!isset($_GET['product_listing_sorter']) || $_GET['product_listing_sorter'] === '') {
        $id = '';
        $text = TEXT_PRODUCT_LISTING_SORTER_CHOOSE;
    } else {
        $id = '0';
        $text = TEXT_PRODUCT_LISTING_SORTER_RESET;
    }
    $product_listing_sorter_options[] = compact('id', 'text');

    foreach ($column_list as $key => $value) {// identify the enabled columns and get their column id
        /* example $column_list
        Array([0] => PRODUCT_LIST_IMAGE
            [1] => PRODUCT_LIST_NAME
            [2] => PRODUCT_LIST_PRICE
            [3] => PRODUCT_LIST_MODEL)
        */
        switch ($value) {
            case ('PRODUCT_LIST_MODEL'):
                $sort_col = (int)substr(PRODUCT_LIST_MODEL, 0, 1);
                $product_listing_sorter_options[] = ['id' => $sort_col . 'a', 'text' => TEXT_MODEL . ' ' . TEXT_ASC];
                $product_listing_sorter_options[] = ['id' => $sort_col . 'd', 'text' => TEXT_MODEL . ' ' . TEXT_DESC];
                break;
            case ('PRODUCT_LIST_NAME'):
                $sort_col = (int)substr(PRODUCT_LIST_NAME, 0, 1);
                $product_listing_sorter_options[] = ['id' => $sort_col . 'a', 'text' => TEXT_NAME . ' ' . TEXT_ASC];
                $product_listing_sorter_options[] = ['id' => $sort_col . 'd', 'text' => TEXT_NAME . ' ' . TEXT_DESC];
                break;
            case ('PRODUCT_LIST_MANUFACTURER'):
                $sort_col = (int)substr(PRODUCT_LIST_MANUFACTURER, 0, 1);
                $product_listing_sorter_options[] = ['id' => $sort_col . 'a', 'text' => TEXT_MANUFACTURER . ' ' . TEXT_ASC];
                $product_listing_sorter_options[] = ['id' => $sort_col . 'd', 'text' => TEXT_MANUFACTURER . ' ' . TEXT_DESC];
                break;
            case ('PRODUCT_LIST_QUANTITY'):
                $sort_col = (int)substr(PRODUCT_LIST_QUANTITY, 0, 1);
                $product_listing_sorter_options[] = ['id' => $sort_col . 'a', 'text' => TEXT_QUANTITY . ' ' . TEXT_ASC];
                $product_listing_sorter_options[] = ['id' => $sort_col . 'd', 'text' => TEXT_QUANTITY . ' ' . TEXT_DESC];
                break;
            /* disabled: unlikely to require a sort by image name
            case ('PRODUCT_LIST_IMAGE'):
                $sort_col = (int)substr(PRODUCT_LIST_IMAGE, 0, 1);
                $product_listing_sorter_options[] = ['id' => $sort_col . 'a', 'text' => TEXT_IMAGE . ' ' . TEXT_ASC];
                $product_listing_sorter_options[] = ['id' => $sort_col . 'd', 'text' => TEXT_IMAGE . ' ' . TEXT_ASC];
                break;
            */
            case ('PRODUCT_LIST_WEIGHT'):
                $sort_col = (int)substr(PRODUCT_LIST_WEIGHT, 0, 1);
                $product_listing_sorter_options[] = ['id' => $sort_col . 'a', 'text' => TEXT_WEIGHT . ' ' . TEXT_ASC];
                $product_listing_sorter_options[] = ['id' => $sort_col . 'd', 'text' => TEXT_WEIGHT . ' ' . TEXT_DESC];
                break;

            case ('PRODUCT_LIST_PRICE'):
                $sort_col = (int)substr(PRODUCT_LIST_PRICE, 0, 1);
                $product_listing_sorter_options[] = ['id' => $sort_col . 'a', 'text' => TEXT_PRICE . ' ' . TEXT_ASC];
                $product_listing_sorter_options[] = ['id' => $sort_col . 'd', 'text' => TEXT_PRICE . ' ' . TEXT_DESC];
        }
    } ?>
    <?php //plugin Product Listing Sorter 1 of 1
    if (isset($pls_custom_sort) && is_array($pls_custom_sort)) {
        foreach ($pls_custom_sort as $custom_sort) {
            //$sort_col = (int)$custom_sort[0];
            $product_listing_sorter_options[] = ['id' => $custom_sort['id'] . 'a', 'text' => $custom_sort['text'] . ' ' . TEXT_ASC];
            $product_listing_sorter_options[] = ['id' => $custom_sort['id'] . 'd', 'text' => $custom_sort['text'] . ' ' . TEXT_DESC];
        }
    }
//eof plugin Product Listing Sorter 1 of 1 ?>
    <label for="productListingSorter" class="inputLabel"
           style="float:none"><?php //css class "inputLabel" floats this text and box below the other filter boxes in rc template unless it is set to :none inline like this;
        echo TEXT_INFO_SORT_BY; ?></label>
    <?php echo zen_draw_pull_down_menu('product_listing_sorter', $product_listing_sorter_options, ($_GET['product_listing_sorter'] ?? $_GET['sort']),
        'id="productListingSorter" onchange="this.form.submit()"');
}
