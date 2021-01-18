<?php

class WooCommercePayLater
{
    public static function activate()
    {
        flush_rewrite_rules();
    }

    public static function deactivate()
    {
        flush_rewrite_rules();
    }

    public static function register()
    {
        // actions
        add_action('admin_menu', array(self::class, 'add_admin_pages'));
        add_action('plugins_loaded', array(self::class, 'init_payment_gateway_class'));

        //filters
        add_filter('woocommerce_payment_gateways', array(self::class, 'add_payment_gateway_class'));
    }

    #region - admin pages
    public static function add_admin_pages()
    {
        add_menu_page('Pay Later', 'Pay Later', 'manage_options', 'pay_later', array(self::class, 'admin_index'), 'dashicons-text-page', 100);

        add_submenu_page('pay_later', 'All Products', 'All Products', 'manage_options', 'all_products', array(self::class, 'admin_all_products'), 1);

        add_submenu_page('pay_later', 'All Customers', 'All Customers', 'manage_options', 'all_customers', array(self::class, 'admin_all_customers'), 2);
    }

    public static function admin_index()
    {
        require_once WOOCOMMERCE_PAY_LATER_PLUGIN_PATH . 'templates/admin-index.php';
    }

    public static function admin_all_products()
    {
        require_once WOOCOMMERCE_PAY_LATER_PLUGIN_PATH . 'templates/all-products.php';
    }

    public static function admin_all_customers()
    {
        require_once WOOCOMMERCE_PAY_LATER_PLUGIN_PATH . 'templates/all-customers.php';
    }

    #endregion

    public static function add_payment_gateway_class($gateways)
    {
        global $woocommerce;

        $items = $woocommerce->cart->get_cart();
        $items_meta = [];

        // If customer is approved for Pay Later AND all of the items in cart are also approved for Pay Later THEN show pay later as an option during checkout
        $user_id = get_current_user_id();

        $user_meta = strtolower(get_user_meta($user_id, 'pay_later_status', true));
        if ($user_meta == 'approved') {
            foreach ($items as $cart_item) {
                $product_id = $cart_item['product_id'];
                $items_meta[] = strtolower(get_post_meta($product_id, '_pay_later_status', true));
            }

            // If any of the items in cart are not approved for Pay Later, but customer is approved, don't show Pay Later
            if (!in_array('not approved', $items_meta)) {
                $gateways[] = 'WooCommercePayLaterGateway';
            }
        }

        // If customer is not approved then don't show Pay Later regardless if any or all products in cart are approved
        // return our custom payment gateway class with other gatways with the help of filter
        return $gateways;
    }

    public static function init_payment_gateway_class()
    {
        require_once WOOCOMMERCE_PAY_LATER_PLUGIN_PATH . 'inc/class-woocommerce-pay-later-gateway.php';
    }
}

WooCommercePayLater::register();