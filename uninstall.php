<?php

/**
 * Trigger this on plugin uninstall
 *
 * @package WooCommercePayLater
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
    die();
}

// clear database stored data
//$books = get_post(array('post_type' => 'book', 'numberposts' => -1));
//
//foreach ($books as $book) {
//    wp_delete_post($book->ID, true);
//}

// access the db via sql
global $wpdb;

$wpdb->query("DELETE FROM $wpdb->posts WHERE post_type = 'woocommerce_paylater'");
$wpdb->query("DELETE FROM $wpdb->postmeta WHERE post_id NOT IN (SELECT id FROM wp_posts)");
// another way with prefix
$wpdb->query("DELETE FROM {$wpdb->prefix}term_relationships WHERE post_id NOT IN (SELECT id FROM wp_posts)");
