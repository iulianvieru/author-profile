<?php
/**
 * Main Plugin Class
 *
 * @package Custom_Author_Profile
 * @since 2.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Main CAP_Plugin Class
 */
class CAP_Plugin {
    
    /**
     * The single instance of the class
     *
     * @var CAP_Plugin
     */
    protected static $_instance = null;
    
    /**
     * Admin instance
     *
     * @var CAP_Admin
     */
    public $admin = null;
    
    /**
     * Frontend instance
     *
     * @var CAP_Frontend
     */
    public $frontend = null;
    
    /**
     * Main Plugin Instance
     *
     * Ensures only one instance of plugin is loaded or can be loaded.
     *
     * @static
     * @return CAP_Plugin - Main instance
     */
    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * Constructor — private to enforce singleton via instance()
     */
    private function __construct() {
        $this->define_constants();
        $this->includes();
        $this->init_hooks();
    }
    
    /**
     * Define plugin constants
     */
    private function define_constants() {
        $plugin_data = get_file_data(CAP_PLUGIN_FILE, ['Version' => 'Version']);
        $this->define('CAP_VERSION', $plugin_data['Version']);
        $this->define('CAP_PLUGIN_FILE', CAP_PLUGIN_FILE);
        $this->define('CAP_PLUGIN_BASENAME', plugin_basename(CAP_PLUGIN_FILE));
        $this->define('CAP_PLUGIN_DIR', plugin_dir_path(CAP_PLUGIN_FILE));
        $this->define('CAP_PLUGIN_URL', plugin_dir_url(CAP_PLUGIN_FILE));
    }
    
    /**
     * Define constant if not already set
     *
     * @param string $name
     * @param string|bool $value
     */
    private function define($name, $value) {
        if (!defined($name)) {
            define($name, $value);
        }
    }
    
    /**
     * Include required core files
     */
    private function includes() {
        // Core classes
        require_once CAP_PLUGIN_DIR . 'includes/class-cap-admin.php';
        require_once CAP_PLUGIN_DIR . 'includes/class-cap-frontend.php';
        require_once CAP_PLUGIN_DIR . 'includes/class-cap-settings.php';
    }
    
    /**
     * Hook into actions and filters
     */
    private function init_hooks() {
        add_action('init', [$this, 'init'], 0);
        add_action('plugins_loaded', [$this, 'load_plugin_textdomain']);
    }
    
    /**
     * Init plugin when WordPress Initialises
     */
    public function init() {
        // Initialize admin
        if (is_admin()) {
            $this->admin = new CAP_Admin();
        }
        
        // Initialize frontend
        $this->frontend = new CAP_Frontend();
        
        // Action hook for third party integrations
        do_action('cap_init');
    }
    
    /**
     * Load plugin text domain for translations
     */
    public function load_plugin_textdomain() {
        load_plugin_textdomain(
            'custom-author-profile',
            false,
            dirname(CAP_PLUGIN_BASENAME) . '/languages'
        );
    }
    
    /**
     * Get the plugin url
     *
     * @return string
     */
    public function plugin_url() {
        return untrailingslashit(plugins_url('/', CAP_PLUGIN_FILE));
    }
    
    /**
     * Get the plugin path
     *
     * @return string
     */
    public function plugin_path() {
        return untrailingslashit(plugin_dir_path(CAP_PLUGIN_FILE));
    }

    /**
     * Plugin activation — set default option values
     */
    public static function activate() {
        $defaults = self::get_default_options();
        foreach ($defaults as $key => $value) {
            if (get_option($key) === false) {
                add_option($key, $value);
            }
        }
    }

    /**
     * Get default option values
     *
     * @return array
     */
    public static function get_default_options() {
        return [
            'cap_override_blog_template'   => 0,
            'cap_override_single_template' => 0,
            'cap_override_author_template' => 0,
            'cap_enable_author_box'        => 0,
            'cap_author_box_position'      => 'bottom',
            'cap_posts_per_page'           => 12,
            'cap_excerpt_length'           => 55,
            'cap_label_read_more'          => 'Read more',
            'cap_label_articles_by'        => 'Articles by',
            'cap_label_content_by'         => 'Content provided by:',
            'cap_label_more_articles'      => 'See more articles by',
            'cap_label_prev'               => '&laquo; Previous',
            'cap_label_next'               => 'Next &raquo;',
            'cap_label_no_posts'           => 'No posts found.',
            'cap_social_icon_color'        => '',
            'cap_link_color'               => '',
            'cap_link_hover_color'         => '',
            'cap_author_page_layout'       => 'top',
            'cap_author_image_size'        => 200,
            'cap_author_show_image'        => 1,
            'cap_author_show_bio'          => 1,
            'cap_author_show_social'       => 1,
            'cap_author_show_email'        => 0,
            'cap_author_show_website'      => 0,
        ];
    }

    /**
     * Get all plugin option keys
     *
     * @return array
     */
    public static function get_option_keys() {
        return array_keys(self::get_default_options());
    }

    /**
     * Get supported social networks
     *
     * @return array Network key => label
     */
    public static function get_social_networks() {
        return [
            'facebook'  => __('Facebook URL', 'custom-author-profile'),
            'instagram' => __('Instagram URL', 'custom-author-profile'),
            'linkedin'  => __('LinkedIn URL', 'custom-author-profile'),
            'twitter'   => __('Twitter/X URL', 'custom-author-profile'),
            'youtube'   => __('YouTube URL', 'custom-author-profile'),
            'tiktok'    => __('TikTok URL', 'custom-author-profile'),
        ];
    }

    /**
     * Get user meta keys managed by this plugin
     *
     * @return array
     */
    public static function get_user_meta_keys() {
        $keys = ['author_profile_image_id', 'author_custom_bio', 'custom_avatar'];
        foreach (array_keys(self::get_social_networks()) as $network) {
            $keys[] = 'author_' . $network;
        }
        return $keys;
    }

    /**
     * Get post meta keys managed by this plugin
     *
     * @return array
     */
    public static function get_post_meta_keys() {
        return [
            '_cap_blog_header_image_desktop',
            '_cap_blog_header_image_mobile',
        ];
    }
}
