<?php
/**
 * Admin Class
 *
 * @package Custom_Author_Profile
 * @since 2.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * CAP_Admin Class
 */
class CAP_Admin {
    
    /**
     * Settings instance
     *
     * @var CAP_Settings
     */
    public $settings = null;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->settings = new CAP_Settings();
        $this->init_hooks();
    }
    
    /**
     * Hook into actions and filters
     */
    private function init_hooks() {
        // Admin menu
        add_action('admin_menu', [$this, 'add_admin_menu']);
        
        // Enqueue admin scripts
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_media_uploader']);
        
        // Profile fields
        add_action('show_user_profile', [$this, 'add_profile_fields']);
        add_action('edit_user_profile', [$this, 'add_profile_fields']);
        add_action('personal_options_update', [$this, 'save_profile_fields']);
        add_action('edit_user_profile_update', [$this, 'save_profile_fields']);
        
        // Meta boxes
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
        add_action('save_post', [$this, 'save_meta_boxes']);
        
        // Reset settings handler
        add_action('admin_init', [$this, 'handle_reset_settings']);
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            __('Author Profile Settings', 'custom-author-profile'),
            __('Author Profile', 'custom-author-profile'),
            'manage_options',
            'cap-settings',
            [$this->settings, 'render_settings_page'],
            plugins_url('images/icon.webp', CAP_PLUGIN_FILE),
            25
        );
        
        add_submenu_page(
            'cap-settings',
            __('Settings', 'custom-author-profile'),
            __('Settings', 'custom-author-profile'),
            'manage_options',
            'cap-settings',
            [$this->settings, 'render_settings_page']
        );
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts($hook) {
        if ($hook === 'toplevel_page_cap-settings' || $hook === 'author-profile_page_cap-settings') {
            wp_enqueue_style('cap-admin-style', CAP_PLUGIN_URL . 'css/admin-style.css', [], CAP_VERSION);
            wp_enqueue_style('wp-color-picker');
            wp_enqueue_script('cap-admin-js', CAP_PLUGIN_URL . 'js/admin.js', ['wp-color-picker'], CAP_VERSION, true);
        }
    }
    
    /**
     * Enqueue media uploader
     */
    public function enqueue_media_uploader($hook) {
        if (in_array($hook, ['profile.php', 'user-edit.php'])) {
            wp_enqueue_media();
            wp_enqueue_script('cap-author-profile-js', CAP_PLUGIN_URL . 'js/author-profile.js', ['jquery'], CAP_VERSION, true);
            wp_localize_script('cap-author-profile-js', 'capProfile', [
                'chooseTitle'  => __('Choose Profile Image', 'custom-author-profile'),
                'selectButton' => __('Select Image', 'custom-author-profile'),
                'changeLabel'  => __('Change Image', 'custom-author-profile'),
                'selectLabel'  => __('Select Image', 'custom-author-profile'),
            ]);
        }

        // Meta box upload scripts on page edit screens
        if (in_array($hook, ['post.php', 'post-new.php'])) {
            $screen = get_current_screen();
            if ($screen && $screen->post_type === 'page') {
                wp_enqueue_media();
                wp_enqueue_script('cap-meta-box-js', CAP_PLUGIN_URL . 'js/meta-box-upload.js', ['jquery'], CAP_VERSION, true);
                wp_localize_script('cap-meta-box-js', 'capMetaBox', [
                    'selectTitle'  => __('Select image', 'custom-author-profile'),
                    'selectButton' => __('Use this image', 'custom-author-profile'),
                ]);
            }
        }
    }
    
    /**
     * Add profile fields
     */
    public function add_profile_fields($user) {
        $image_id = get_user_meta($user->ID, 'author_profile_image_id', true);
        ?>
        <h3><?php _e('Author Page Information', 'custom-author-profile'); ?></h3>
        <table class="form-table">
            <tr>
                <th><label><?php _e('Profile Image', 'custom-author-profile'); ?></label></th>
                <td>
                    <img id="author-profile-image-preview" src="<?php echo $image_id ? esc_url(wp_get_attachment_url($image_id)) : ''; ?>" style="max-width:150px;height:auto;margin-bottom:10px;display:<?php echo $image_id ? 'block':'none'; ?>;">
                    <input type="hidden" name="author_profile_image_id" id="author_profile_image_id" value="<?php echo esc_attr($image_id); ?>" />
                    <br>
                    <button type="button" class="button" id="author-profile-image-upload"><?php echo $image_id ? __('Change', 'custom-author-profile') : __('Select', 'custom-author-profile'); ?> <?php _e('Image', 'custom-author-profile'); ?></button>
                    <button type="button" id="author-profile-image-remove" class="button" style="display:<?php echo $image_id ? 'inline-block':'none'; ?>;"><?php _e('Remove Image', 'custom-author-profile'); ?></button>
                    <p class="description"><?php _e('Select profile image from Media Library.', 'custom-author-profile'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="author_custom_bio"><?php _e('Bio', 'custom-author-profile'); ?></label></th>
                <td>
                    <?php
                    $bio = get_user_meta($user->ID, 'author_custom_bio', true);
                    wp_editor($bio, 'author_bio_editor', [
                        'textarea_name' => 'author_custom_bio',
                        'media_buttons' => false,
                        'textarea_rows' => 8,
                        'tinymce' => true,
                    ]);
                    ?>
                </td>
            </tr>
            <?php foreach (CAP_Plugin::get_social_networks() as $network => $label): ?>
            <tr>
                <th><label for="author_<?php echo esc_attr($network); ?>"><?php echo esc_html($label); ?></label></th>
                <td><input type="url" name="author_<?php echo esc_attr($network); ?>" id="author_<?php echo esc_attr($network); ?>" value="<?php echo esc_url(get_user_meta($user->ID, 'author_' . $network, true)); ?>" class="regular-text" /></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php
    }
    
    /**
     * Save profile fields
     */
    public function save_profile_fields($user_id) {
        if (!current_user_can('edit_user', $user_id)) {
            return;
        }
        
        if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'update-user_' . $user_id)) {
            return;
        }
        
        $fields = [
            'author_profile_image_id' => 'intval',
            'author_custom_bio' => 'wp_kses_post',
        ];
        foreach (array_keys(CAP_Plugin::get_social_networks()) as $network) {
            $fields['author_' . $network] = 'esc_url_raw';
        }
        
        foreach ($fields as $field => $sanitize) {
            if (isset($_POST[$field])) {
                update_user_meta($user_id, $field, $sanitize($_POST[$field]));
            }
        }
        
        // Update or clear custom avatar
        if (!empty($_POST['author_profile_image_id'])) {
            $image_url = wp_get_attachment_url(intval($_POST['author_profile_image_id']));
            if ($image_url) {
                update_user_meta($user_id, 'custom_avatar', esc_url($image_url));
            }
        } else {
            delete_user_meta($user_id, 'custom_avatar');
        }
    }
    
    /**
     * Add meta boxes
     */
    public function add_meta_boxes() {
        add_meta_box(
            'cap_blog_header_image',
            __('Custom Header Background', 'custom-author-profile'),
            [$this, 'render_blog_header_meta_box'],
            'page',
            'side',
            'default'
        );
    }
    
    /**
     * Render blog header meta box
     */
    public function render_blog_header_meta_box($post) {
        $desktop_id = get_post_meta($post->ID, '_cap_blog_header_image_desktop', true);
        $mobile_id  = get_post_meta($post->ID, '_cap_blog_header_image_mobile', true);
        
        $desktop_url = $desktop_id ? wp_get_attachment_url($desktop_id) : '';
        $mobile_url  = $mobile_id  ? wp_get_attachment_url($mobile_id)  : '';
        
        wp_nonce_field('cap_blog_header_image_nonce', 'cap_blog_header_image_nonce_field');
        ?>
        <p><strong><?php _e('Desktop Background', 'custom-author-profile'); ?></strong></p>
        <div id="cap-blog-header-desktop-preview" style="margin-bottom:10px;">
            <?php if ($desktop_url): ?>
                <img src="<?php echo esc_url($desktop_url); ?>" style="max-width:100%; border-radius:6px;" />
            <?php endif; ?>
        </div>
        <input type="hidden" id="cap_blog_header_image_desktop" name="cap_blog_header_image_desktop" value="<?php echo esc_attr($desktop_id); ?>" />
        <button type="button" class="button" id="cap-upload-desktop"><?php _e('Upload Desktop', 'custom-author-profile'); ?></button>
        <button type="button" class="button" id="cap-remove-desktop" style="<?php echo $desktop_url ? '' : 'display:none;'; ?>"><?php _e('Remove', 'custom-author-profile'); ?></button>
        
        <hr>
        
        <p><strong><?php _e('Mobile Background', 'custom-author-profile'); ?></strong></p>
        <div id="cap-blog-header-mobile-preview" style="margin-bottom:10px;">
            <?php if ($mobile_url): ?>
                <img src="<?php echo esc_url($mobile_url); ?>" style="max-width:100%; border-radius:6px;" />
            <?php endif; ?>
        </div>
        <input type="hidden" id="cap_blog_header_image_mobile" name="cap_blog_header_image_mobile" value="<?php echo esc_attr($mobile_id); ?>" />
        <button type="button" class="button" id="cap-upload-mobile"><?php _e('Upload Mobile', 'custom-author-profile'); ?></button>
        <button type="button" class="button" id="cap-remove-mobile" style="<?php echo $mobile_url ? '' : 'display:none;'; ?>"><?php _e('Remove', 'custom-author-profile'); ?></button>
        <?php
    }
    
    /**
     * Save meta boxes
     */
    public function save_meta_boxes($post_id) {
        if (!isset($_POST['cap_blog_header_image_nonce_field']) ||
            !wp_verify_nonce($_POST['cap_blog_header_image_nonce_field'], 'cap_blog_header_image_nonce')) {
            return;
        }
        
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        if (isset($_POST['cap_blog_header_image_desktop'])) {
            update_post_meta($post_id, '_cap_blog_header_image_desktop', intval($_POST['cap_blog_header_image_desktop']));
        }
        
        if (isset($_POST['cap_blog_header_image_mobile'])) {
            update_post_meta($post_id, '_cap_blog_header_image_mobile', intval($_POST['cap_blog_header_image_mobile']));
        }
    }
    
    /**
     * Handle reset settings
     */
    public function handle_reset_settings() {
        if (isset($_POST['cap_reset_settings']) && current_user_can('manage_options')) {
            if (!isset($_POST['cap_reset_nonce']) || !wp_verify_nonce($_POST['cap_reset_nonce'], 'cap_reset_settings')) {
                return;
            }
            
            // Delete all options
            foreach (CAP_Plugin::get_option_keys() as $option) {
                delete_option($option);
            }
            
            add_action('admin_notices', function() {
                echo '<div class="notice notice-success is-dismissible"><p>' . __('Settings have been reset to defaults.', 'custom-author-profile') . '</p></div>';
            });
            
            wp_safe_redirect(admin_url('admin.php?page=cap-settings'));
            exit;
        }
    }
}
