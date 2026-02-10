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

// Delete all plugin options
$options = [
    'cap_override_blog_template',
    'cap_override_single_template',
    'cap_override_author_template',
    'cap_enable_author_box',
    'cap_author_box_position',
    'cap_posts_per_page',
    'cap_excerpt_length',
    'cap_label_read_more',
    'cap_label_articles_by',
    'cap_label_content_by',
    'cap_label_more_articles',
    'cap_label_prev',
    'cap_label_next',
    'cap_social_icon_color',
    'cap_link_color',
    'cap_link_hover_color',
    'cap_author_page_layout',
    'cap_author_image_size',
    'cap_author_show_bio',
    'cap_author_show_social',
    'cap_author_show_email',
    'cap_author_show_website',
];

foreach ($options as $option) {
    delete_option($option);
}

// Delete all user meta data created by plugin
global $wpdb;

$user_meta_keys = [
    'author_profile_image_id',
    'author_custom_bio',
    'author_facebook',
    'author_instagram',
    'author_linkedin',
    'author_twitter',
    'author_youtube',
    'author_tiktok',
    'custom_avatar',
];

foreach ($user_meta_keys as $meta_key) {
    $wpdb->delete($wpdb->usermeta, ['meta_key' => $meta_key]);
}

// Delete all post meta data created by plugin
$post_meta_keys = [
    '_cap_blog_header_image_desktop',
    '_cap_blog_header_image_mobile',
];

foreach ($post_meta_keys as $meta_key) {
    $wpdb->delete($wpdb->postmeta, ['meta_key' => $meta_key]);
}

// Clear any cached data
wp_cache_flush();
