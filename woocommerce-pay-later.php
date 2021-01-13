<?php
/**
 * @package WooCommercePayLater
 */
/**
 * Plugin Name: WooCommerce Pay Later
 * Plugin URI: http://www.example.com
 * Description: This is a WooCommerce plugin to provide a payment method for "Pay Later". This method lets the customer pay for the order after certain amount of time specified by admin.
 * Version: 1.0.0
 * Author: Waqar Irfan
 * Author URI: http://www.example.com
 * License: GPLv3 or later
 * Text Domain: woocommercepaylater
 */

defined('ABSPATH') || exit;

if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) return;

define('WOOCOMMERCE_PAY_LATER_PLUGIN_PATH', plugin_dir_path(__FILE__));

require_once WOOCOMMERCE_PAY_LATER_PLUGIN_PATH . 'inc/class-woocommerce-pay-later.php';

// activation
register_activation_hook(__FILE__, array('WooCommercePayLater', 'activate'));

// deactivation
register_deactivation_hook(__FILE__, array('WooCommercePayLater', 'deactivate'));
