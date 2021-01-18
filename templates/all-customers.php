<div class="wrap">
    <h1 class="wp-heading-inline">All WooCommerce Customers</h1>
    <hr class="wp-header-end">
    <form method='post' name='form_woocommerce_pay_later_all_customers'
          action='<?php echo $_SERVER['PHP_SELF'] . "?page=all_customers'" ?>'>
        <?php

        class CustomersList extends WP_List_Table
        {
            public function get_columns()
            {
                $columns = [
                    'cb' => ' < input type = "checkbox" />',
                    'name' => __('Name', 'sp'),
                    'email' => __('Email', 'sp'),
                    'role' => __('Role', 'sp'),
                    'pay_later_status' => __('Pay Later', 'sp'),
                    'action' => __('Action', 'sp'),
                ];

                return $columns;
            }

            public function column_default($item, $column_name)
            {
                switch ($column_name) {
                    case 'name':
                    case 'email':
                    case 'role':
                        return ucfirst($item[$column_name]);

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
                return sprintf('<input type = "checkbox" name = "bulk-pay-later-for-every-customer[]" value = "%s" />', $item['ID']);
            }

            public function get_bulk_actions()
            {
                $actions = [
                    'bulk-approve' => 'Approve Pay Later For Every Customer',
                    'bulk-deny' => 'Deny Pay Later For Every Every Customer'
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
                        $this->approve_all_users_for_pay_later();
                        break;

                    case 'bulk-deny':
                        $this->deny_all_users_for_pay_later();
                        break;

                    default:
                        // do nothing or something else
                        return;
                        break;
                }

            }

            public function approve_all_users_for_pay_later()
            {
                if (isset($_POST['bulk-pay-later-for-every-customer'])) {
                    $all_user_ids = $_POST['bulk-pay-later-for-every-customer'];
                    foreach ($all_user_ids as $user_id) {
                        update_user_meta($user_id, 'pay_later_status', 'Approved', '');
                    }
                }
            }

            public function deny_all_users_for_pay_later()
            {
                if (isset($_POST['bulk-pay-later-for-every-customer'])) {
                    $all_user_ids = $_POST['bulk-pay-later-for-every-customer'];
                    foreach ($all_user_ids as $user_id) {
                        update_user_meta($user_id, 'pay_later_status', 'Not Approved', '');
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


            public function get_customers($orderby = '', $order = '', $search_term = '')
            {
                global $wpdb;
                $all_users = [];

                $sql = "SELECT * FROM {$wpdb->prefix}users";

                if (!empty($search_term)) {
                    $sql .= " WHERE display_name LIKE '%" . esc_attr($search_term) . "%'";
                } else {
                    if ($orderby == 'name' && $order == 'asc') {
                        $sql .= " ORDER BY display_name ASC";
                    } elseif ($orderby == 'name' && $order == 'desc') {
                        $sql .= " ORDER BY display_name DESC";
                    }
                }

                $users = $wpdb->get_results($sql);

                foreach ($users as $user) {
                    $pay_later_status = get_user_meta($user->ID, 'pay_later_status', true);
                    $wp_capabilities = get_user_meta($user->ID, 'wp_capabilities', true);
                    $user_role = array_keys($wp_capabilities);

                    $all_users[] = [
                        'ID' => $user->ID,
                        'name' => $user->display_name,
                        'email' => $user->user_email,
                        'role' => $user_role[0],
                        'pay_later_status' => $pay_later_status
                    ];
                }

                return $all_users;
            }

            public function prepare_items()
            {
                /** Process bulk action */
                $this->process_bulk_action();

                $orderby = isset($_GET['orderby']) ? trim($_GET['orderby']) : "";
                $order = isset($_GET['order']) ? trim($_GET['order']) : "";
                $search_term = isset($_POST['s']) ? trim($_POST['s']) : "";

                $this->items = $this->get_customers($orderby, $order, $search_term);

                $columns = $this->get_columns();


                $hidden = $this->get_hidden_columns();
                $sortable = $this->get_sortable_columns();

                $this->_column_headers = array($columns, $hidden, $sortable);
            }
        }


        function woocommerce_pay_later_all_customers_table_layout()
        {
            if (isset($_POST['edit-row'])) {
                $pay_later_status = get_user_meta($_POST['edit-row'], 'pay_later_status', true);
                if (strtolower($pay_later_status) == 'not approved') {
                    update_user_meta($_POST['edit-row'], 'pay_later_status', 'Approved', '');
                } else {
                    update_user_meta($_POST['edit-row'], 'pay_later_status', 'Not Approved', '');
                }
            }

            $customersList = new CustomersList();
            $customersList->prepare_items();
            $customersList->search_box("Search Customer(s)", "search_customer_id");
            $customersList->display();
        }

        woocommerce_pay_later_all_customers_table_layout();


        ?>
    </form>
</div>
