<?php
/**
 * Plugin Name: Custom Author & Blog Templates
 * Plugin URI: https://clientipeviata.ro
 * Description: Adds custom author profile fields (image uploader, HTML bio editor, social links), and custom author and blog page templates.
 * Version: 2.1.0
 * Author: Clienti pe Viata
 * Author URI: https://clientipeviata.ro
 * Text Domain: custom-author-profile
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.7
 * Requires PHP: 7.4
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package Custom_Author_Profile
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Define plugin file constant
if (!defined('CAP_PLUGIN_FILE')) {
    define('CAP_PLUGIN_FILE', __FILE__);
}

/**
 * Main instance of Custom Author Profile
 *
 * Returns the main instance of CAP to prevent the need to use globals.
 *
 * @since  2.1.0
 * @return CAP_Plugin
 */
function CAP() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-cap-plugin.php';
    return CAP_Plugin::instance();
}

// Register activation hook
register_activation_hook(__FILE__, function() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-cap-plugin.php';
    CAP_Plugin::activate();
});

// Initialize the plugin
CAP();

