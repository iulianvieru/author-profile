<?php
/**
 * Settings Class
 *
 * @package Custom_Author_Profile
 * @since 2.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * CAP_Settings Class
 */
class CAP_Settings {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_init', [$this, 'register_settings']);
    }
    
    /**
     * Register settings
     */
    public function register_settings() {
        $settings = [
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
            // New Author Page Settings
            'cap_author_page_layout',
            'cap_author_image_size',
            'cap_author_show_image',
            'cap_author_show_bio',
            'cap_author_show_social',
            'cap_author_show_email',
            'cap_author_show_website',
        ];
        
        foreach ($settings as $setting) {
            register_setting('cap_settings_group', $setting);
        }
    }
    
    /**
     * Render settings page
     */
    public function render_settings_page() {
        ?>
        <div class="cap-settings-container">
            <div class="cap-settings-header">
                <h1><?php _e('Author Profile Settings', 'custom-author-profile'); ?></h1>
            </div>

            <form method="post" action="options.php" class="cap-settings-form">
                <?php settings_fields('cap_settings_group'); ?>
                <?php do_settings_sections('cap_settings_group'); ?>

                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Add template for Blog Page', 'custom-author-profile'); ?></th>
                        <td>
                            <label class="cap-switch">
                                <input type="checkbox" name="cap_override_blog_template" value="1" <?php checked(1, get_option('cap_override_blog_template', 0)); ?>>
                                <span class="cap-slider"></span>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Override template for Article Page', 'custom-author-profile'); ?></th>
                        <td>
                            <label class="cap-switch">
                                <input type="checkbox" name="cap_override_single_template" value="1" <?php checked(1, get_option('cap_override_single_template', 0)); ?>>
                                <span class="cap-slider"></span>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Override template for Author Page', 'custom-author-profile'); ?></th>
                        <td>
                            <label class="cap-switch">
                                <input type="checkbox" name="cap_override_author_template" value="1" <?php checked(1, get_option('cap_override_author_template', 0)); ?>>
                                <span class="cap-slider"></span>
                            </label>
                        </td>
                    </tr>
                    
                    <!-- NEW: Author Page Configuration -->
                     <tr>
                        <th scope="row"><label for="cap_author_page_layout"><?php _e('Author Page Layout', 'custom-author-profile'); ?></label></th>
                        <td>
                            <select name="cap_author_page_layout" id="cap_author_page_layout">
                                <option value="top" <?php selected(get_option('cap_author_page_layout', 'top'), 'top'); ?>><?php _e('Top Centered', 'custom-author-profile'); ?></option>
                                <option value="left" <?php selected(get_option('cap_author_page_layout', 'top'), 'left'); ?>><?php _e('Left Sidebar', 'custom-author-profile'); ?></option>
                                <option value="right" <?php selected(get_option('cap_author_page_layout', 'top'), 'right'); ?>><?php _e('Right Sidebar', 'custom-author-profile'); ?></option>
                            </select>
                            <p class="description"><?php _e('Choose the layout for the author archive page.', 'custom-author-profile'); ?></p>
                        </td>
                    </tr>
                    <tr>
                         <th scope="row"><label for="cap_author_image_size"><?php _e('Profile Image Size (px)', 'custom-author-profile'); ?></label></th>
                         <td>
                             <input type="number" name="cap_author_image_size" id="cap_author_image_size" value="<?php echo esc_attr(get_option('cap_author_image_size', 200)); ?>" class="small-text" min="50" max="800" />
                             <p class="description"><?php _e('Width/Height of the author profile image in pixels.', 'custom-author-profile'); ?></p>
                         </td>
                     </tr>
                    <tr>
                        <th scope="row"><?php _e('Show Elements in Profile', 'custom-author-profile'); ?></th>
                        <td>
                            <fieldset>
                                <label>
                                    <input type="checkbox" name="cap_author_show_image" value="1" <?php checked(1, get_option('cap_author_show_image', 1)); ?>>
                                    <?php _e('Show Author Image', 'custom-author-profile'); ?>
                                </label><br>
                                <label>
                                    <input type="checkbox" name="cap_author_show_bio" value="1" <?php checked(1, get_option('cap_author_show_bio', 1)); ?>>
                                    <?php _e('Show Bio', 'custom-author-profile'); ?>
                                </label><br>
                                <label>
                                    <input type="checkbox" name="cap_author_show_social" value="1" <?php checked(1, get_option('cap_author_show_social', 1)); ?>>
                                    <?php _e('Show Social Icons', 'custom-author-profile'); ?>
                                </label><br>
                                <label>
                                    <input type="checkbox" name="cap_author_show_email" value="1" <?php checked(1, get_option('cap_author_show_email', 0)); ?>>
                                    <?php _e('Show Email Address', 'custom-author-profile'); ?>
                                </label><br>
                                <label>
                                    <input type="checkbox" name="cap_author_show_website" value="1" <?php checked(1, get_option('cap_author_show_website', 0)); ?>>
                                    <?php _e('Show Website Link', 'custom-author-profile'); ?>
                                </label>
                            </fieldset>
                        </td>
                    </tr>
                    <!-- END NEW: Author Page Configuration -->

                    <tr>
                        <th scope="row"><?php _e('Add author box at end of article', 'custom-author-profile'); ?></th>
                        <td>
                            <label class="cap-switch">
                                <input type="checkbox" name="cap_enable_author_box" value="1" <?php checked(1, get_option('cap_enable_author_box', 0)); ?>>
                                <span class="cap-slider"></span>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="cap_author_box_position"><?php _e('Author Box Position', 'custom-author-profile'); ?></label></th>
                        <td>
                            <select name="cap_author_box_position" id="cap_author_box_position">
                                <option value="bottom" <?php selected(get_option('cap_author_box_position', 'bottom'), 'bottom'); ?>><?php _e('Bottom of Post', 'custom-author-profile'); ?></option>
                                <option value="top" <?php selected(get_option('cap_author_box_position', 'bottom'), 'top'); ?>><?php _e('Top of Post', 'custom-author-profile'); ?></option>
                                <option value="both" <?php selected(get_option('cap_author_box_position', 'bottom'), 'both'); ?>><?php _e('Both Top and Bottom', 'custom-author-profile'); ?></option>
                                <option value="manual" <?php selected(get_option('cap_author_box_position', 'bottom'), 'manual'); ?>><?php _e('Manual (use shortcode)', 'custom-author-profile'); ?></option>
                            </select>
                            <p class="description"><?php _e('Where to display the author box on single posts.', 'custom-author-profile'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="cap_posts_per_page"><?php _e('Posts Per Page', 'custom-author-profile'); ?></label></th>
                        <td>
                            <input type="number" name="cap_posts_per_page" id="cap_posts_per_page" value="<?php echo esc_attr(get_option('cap_posts_per_page', 12)); ?>" class="small-text" min="1" max="100" />
                            <p class="description"><?php _e('Number of posts to display on author and blog archive pages (default: 12).', 'custom-author-profile'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="cap_excerpt_length"><?php _e('Excerpt Length (words)', 'custom-author-profile'); ?></label></th>
                        <td>
                            <input type="number" name="cap_excerpt_length" id="cap_excerpt_length" value="<?php echo esc_attr(get_option('cap_excerpt_length', 55)); ?>" class="small-text" min="10" max="500" />
                            <p class="description"><?php _e('Number of words in post excerpts (default: 55). Leave as 0 to use WordPress default.', 'custom-author-profile'); ?></p>
                        </td>
                    </tr>
                </table>

                <h2><?php _e('Colors', 'custom-author-profile'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="cap_link_color"><?php _e('Link Color', 'custom-author-profile'); ?></label></th>
                        <td>
                            <input type="text" name="cap_link_color" id="cap_link_color" value="<?php echo esc_attr(get_option('cap_link_color', '')); ?>" class="cap-color-picker" />
                            <p class="description"><?php _e('Color for "Read More" and author links. Leave empty for theme default.', 'custom-author-profile'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="cap_link_hover_color"><?php _e('Link Hover Color', 'custom-author-profile'); ?></label></th>
                        <td>
                            <input type="text" name="cap_link_hover_color" id="cap_link_hover_color" value="<?php echo esc_attr(get_option('cap_link_hover_color', '')); ?>" class="cap-color-picker" />
                            <p class="description"><?php _e('Color for links on hover. Leave empty for theme default.', 'custom-author-profile'); ?></p>
                        </td>
                    </tr>
                </table>

                <h2><?php _e('Text Labels', 'custom-author-profile'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="cap_label_read_more"><?php _e('Read More Button', 'custom-author-profile'); ?></label></th>
                        <td>
                            <input type="text" name="cap_label_read_more" id="cap_label_read_more" value="<?php echo esc_attr(get_option('cap_label_read_more', __('Read more', 'custom-author-profile'))); ?>" class="regular-text" />
                            <p class="description"><?php _e('Text for the "Read More" link on blog/author archives.', 'custom-author-profile'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="cap_label_articles_by"><?php _e('Articles By Author', 'custom-author-profile'); ?></label></th>
                        <td>
                            <input type="text" name="cap_label_articles_by" id="cap_label_articles_by" value="<?php echo esc_attr(get_option('cap_label_articles_by', __('Articles by', 'custom-author-profile'))); ?>" class="regular-text" />
                            <p class="description"><?php _e('Text before author name on author page (e.g., "Articles by").', 'custom-author-profile'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="cap_label_content_by"><?php _e('Content Provided By', 'custom-author-profile'); ?></label></th>
                        <td>
                            <input type="text" name="cap_label_content_by" id="cap_label_content_by" value="<?php echo esc_attr(get_option('cap_label_content_by', __('Content provided by:', 'custom-author-profile'))); ?>" class="regular-text" />
                            <p class="description"><?php _e('Text in author box below articles (e.g., "Content by:").', 'custom-author-profile'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="cap_label_more_articles"><?php _e('More Articles Link', 'custom-author-profile'); ?></label></th>
                        <td>
                            <input type="text" name="cap_label_more_articles" id="cap_label_more_articles" value="<?php echo esc_attr(get_option('cap_label_more_articles', __('See more articles by', 'custom-author-profile'))); ?>" class="regular-text" />
                            <p class="description"><?php _e('Text for link to author page in author box (e.g., "See more articles by").', 'custom-author-profile'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="cap_label_prev"><?php _e('Previous Page', 'custom-author-profile'); ?></label></th>
                        <td>
                            <input type="text" name="cap_label_prev" id="cap_label_prev" value="<?php echo esc_attr(get_option('cap_label_prev', __('« Previous', 'custom-author-profile'))); ?>" class="regular-text" />
                            <p class="description"><?php _e('Text for "Previous" pagination link.', 'custom-author-profile'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="cap_label_next"><?php _e('Next Page', 'custom-author-profile'); ?></label></th>
                        <td>
                            <input type="text" name="cap_label_next" id="cap_label_next" value="<?php echo esc_attr(get_option('cap_label_next', __('Next »', 'custom-author-profile'))); ?>" class="regular-text" />
                            <p class="description"><?php _e('Text for "Next" pagination link.', 'custom-author-profile'); ?></p>
                        </td>
                    </tr>
                </table>

                <?php submit_button(); ?>
            </form>
            
            <hr style="margin: 30px 0;">
            
            <form method="post" action="">
                <?php wp_nonce_field('cap_reset_settings', 'cap_reset_nonce'); ?>
                <h3><?php _e('Reset Settings', 'custom-author-profile'); ?></h3>
                <p><?php _e('Reset all plugin settings to their default values. This cannot be undone.', 'custom-author-profile'); ?></p>
                <input type="submit" name="cap_reset_settings" class="button button-secondary" value="<?php esc_attr_e('Reset to Defaults', 'custom-author-profile'); ?>" onclick="return confirm('<?php esc_attr_e('Are you sure you want to reset all settings to defaults? This cannot be undone.', 'custom-author-profile'); ?>');">
            </form>
        </div>
        <?php
    }
}
