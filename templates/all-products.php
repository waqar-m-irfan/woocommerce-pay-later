<div class="wrap">
    <h1 class="wp-heading-inline">All WooCommerce Products</h1>
    <hr class="wp-header-end">
    <form method='post' name='form_woocommerce_pay_later_all_products'
          action='<?php echo $_SERVER['PHP_SELF'] . "?page=all_products'" ?>'>
        <?php

        class ProductsList extends WP_List_Table
        {
            public function get_columns()
            {
                $columns = [
                    'cb' => ' < input type = "checkbox" />',
                    'name' => __('Name', 'sp'),
                    'sku' => __('SKU', 'sp'),
                    'pay_later_status' => __('Pay Later', 'sp'),
                    'action' => __('Action', 'sp'),
                ];

                return $columns;
            }

            public function column_default($item, $column_name)
            {
                switch ($column_name) {
                    case 'name':
                    case 'sku':
                        return $item[$column_name];

                    case 'pay_later_status':
                        $pay_later_status = $item['pay_later_status'];
                        return $pay_later_status == '' ? 'Not Approved' : $pay_later_status;

                    case 'action':
                        return sprintf('<button type="submit" name="edit-row" class="button button-primary save" value="' . $item['ID'] . '">Change</button>', null);
                    default:
                        return print_r($item, true); //Show the whole array for troubleshooting purposes
                }
            }

            public function column_cb($item)
            {
                return sprintf('<input type = "checkbox" name = "bulk-pay-later-for-every-product[]" value = "%s" />', $item['ID']);
            }

            public function get_bulk_actions()
            {
                $actions = [
                    'bulk-approve' => 'Approve Pay Later For Every Product',
                    'bulk-deny' => 'Deny Pay Later For Every Product'
                ];

                return $actions;
            }

            public function process_bulk_action()
            {
                // security check!
                if (isset($_POST['_wpnonce']) && !empty($_POST['_wpnonce'])) {

                    $nonce = filter_input(INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING);
                    $action = 'bulk-' . $this->_args['plural'];

                    if (!wp_verify_nonce($nonce, $action))
                        wp_die('Nope! Security check failed!');

                }

                $action = $this->current_action();

                switch ($action) {

                    case 'bulk-approve':
                        $this->approve_all_products_for_pay_later();
                        break;

                    case 'bulk-deny':
                        $this->deny_all_products_for_pay_later();
                        break;

                    default:
                        // do nothing or something else
                        return;
                        break;
                }

            }

            public function approve_all_products_for_pay_later()
            {
                if (isset($_POST['bulk-pay-later-for-every-product'])) {
                    $all_product_ids = $_POST['bulk-pay-later-for-every-product'];
                    foreach ($all_product_ids as $product_id) {
                        update_post_meta($product_id, '_pay_later_status', 'Approved', '');
                    }
                }
            }

            public function deny_all_products_for_pay_later()
            {
                if (isset($_POST['bulk-pay-later-for-every-product'])) {
                    $all_product_ids = $_POST['bulk-pay-later-for-every-product'];
                    foreach ($all_product_ids as $product_id) {
                        update_post_meta($product_id, '_pay_later_status', 'Not Approved', '');
                    }
                }
            }

            public function get_hidden_columns()
            {
                return array();
            }

            public function get_sortable_columns()
            {
                $sortable_columns = array(
                    'name' => array('name', true),
                );

                return $sortable_columns;
            }


            public function get_products($orderby = '', $order = '', $search_term = '')
            {
                global $wpdb;
                $all_products = [];

                $sql = "SELECT * FROM {$wpdb->prefix}posts WHERE post_type = 'product'";

                if (!empty($search_term)) {
                    $sql .= " AND post_title LIKE '%" . $search_term . "%'";
                } else {
                    if ($orderby == 'name' && $order == 'asc') {
                        $sql .= " ORDER BY post_title ASC";

                    } elseif ($orderby == 'name' && $order == 'desc') {
                        $sql .= " ORDER BY post_title DESC";
                    }
                }

                $products = $wpdb->get_results($sql);


                foreach ($products as $product) {
                    $sku = get_post_meta($product->ID, '_sku', true);
                    $pay_later_status = get_post_meta($product->ID, '_pay_later_status', true);

                    $all_products[] = [
                        'ID' => $product->ID,
                        'name' => $product->post_title,
                        'sku' => $sku,
                        'pay_later_status' => $pay_later_status
                    ];
                }
                return $all_products;
            }

            public function prepare_items()
            {
//                /** Process bulk action */
                $this->process_bulk_action();

                $orderby = isset($_GET['orderby']) ? trim($_GET['orderby']) : "";
                $order = isset($_GET['order']) ? trim($_GET['order']) : "";
                $search_term = isset($_POST['s']) ? trim($_POST['s']) : "";

                $this->items = $this->get_products($orderby, $order, $search_term);

                $columns = $this->get_columns();

                $hidden = $this->get_hidden_columns();
                $sortable = $this->get_sortable_columns();

                $this->_column_headers = array($columns, $hidden, $sortable);
            }
        }


        function woocommerce_pay_later_all_customers_table_layout()
        {
            if (isset($_POST['edit-row'])) {
                $pay_later_status = get_post_meta($_POST['edit-row'], '_pay_later_status', true);
                if (strtolower($pay_later_status) == 'not approved') {
                    update_post_meta($_POST['edit-row'], '_pay_later_status', 'Approved', '');
                } else {
                    update_post_meta($_POST['edit-row'], '_pay_later_status', 'Not Approved', '');
                }
            }

            $productsList = new ProductsList();
            $productsList->prepare_items();
            $productsList->search_box("Search Product(s)", "search_product_id");
            $productsList->display();
        }

        woocommerce_pay_later_all_customers_table_layout();


        ?>
    </form>
</div>
