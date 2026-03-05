<?php
/**
 * Uninstall Script
 *
 * @package Custom_Author_Profile
 * @since 2.0.0
 */

// If uninstall not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Load the plugin class for centralized option/meta key lists
require_once plugin_dir_path(__FILE__) . 'includes/class-cap-plugin.php';

// Delete all plugin options
foreach (CAP_Plugin::get_option_keys() as $option) {
    delete_option($option);
}

// Delete all user meta data created by plugin
global $wpdb;

foreach (CAP_Plugin::get_user_meta_keys() as $meta_key) {
    $wpdb->delete($wpdb->usermeta, ['meta_key' => $meta_key]);
}

// Delete all post meta data created by plugin
foreach (CAP_Plugin::get_post_meta_keys() as $meta_key) {
    $wpdb->delete($wpdb->postmeta, ['meta_key' => $meta_key]);
}

// Clear any cached data
wp_cache_flush();
