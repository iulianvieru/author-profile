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
     * Plugin version
     *
     * @var string
     */
    public $version = '2.0.0';
    
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
     * Constructor
     */
    public function __construct() {
        $this->define_constants();
        $this->includes();
        $this->init_hooks();
    }
    
    /**
     * Define plugin constants
     */
    private function define_constants() {
        $this->define('CAP_VERSION', $this->version);
        $this->define('CAP_PLUGIN_FILE', CAP_PLUGIN_FILE);
        $this->define('CAP_PLUGIN_BASENAME', plugin_basename(CAP_PLUGIN_FILE));
        $this->define('CAP_PLUGIN_DIR', plugin_dir_path(CAP_PLUGIN_FILE));
        $this->define('CAP_PLUGIN_URL', plugin_dir_url(CAP_PLUGIN_FILE));
        $this->define('CAP_TEXT_DOMAIN', 'custom-author-profile');
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
    public function includes() {
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
}
