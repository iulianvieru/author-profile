<?php
/**
 * Frontend Class
 *
 * @package Custom_Author_Profile
 * @since 2.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * CAP_Frontend Class
 */
class CAP_Frontend {

    /**
     * Guard flag to prevent recursive avatar filtering
     *
     * @var bool
     */
    private $is_filtering_avatar = false;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->init_hooks();
    }
    
    /**
     * Hook into actions and filters
     */
    private function init_hooks() {
        // Template overrides
        add_filter('template_include', [$this, 'override_templates']);
        add_filter('theme_page_templates', [$this, 'register_blog_template']);
        
        // Enqueue styles
        add_action('wp_enqueue_scripts', [$this, 'enqueue_styles']);
        
        // Author box
        add_filter('the_content', [$this, 'add_author_box']);
        
        // Shortcode
        add_shortcode('cap_author_box', [$this, 'render_author_box_shortcode']);
        
        // Custom avatar
        add_filter('get_avatar', [$this, 'custom_user_avatar'], 10, 5);
        
        // Excerpt length
        add_filter('excerpt_length', [$this, 'custom_excerpt_length'], 999);
        
        // Posts per page
        add_action('pre_get_posts', [$this, 'modify_author_posts_per_page']);
    }
    
    /**
     * Override templates
     */
    public function override_templates($template) {
        if (is_author() && get_option('cap_override_author_template', 0)) {
            $custom_template = locate_template(['simple-author-profile/author.php']);
            if (!$custom_template) {
                $custom_template = CAP_PLUGIN_DIR . 'templates/author.php';
            }
            
            if (file_exists($custom_template)) {
                return $custom_template;
            }
        }
        
        if (is_page_template('template-blog.php') && get_option('cap_override_blog_template', 0)) {
            $blog_template = locate_template(['simple-author-profile/template-blog.php']);
            if (!$blog_template) {
                $blog_template = CAP_PLUGIN_DIR . 'templates/template-blog.php';
            }

            if (file_exists($blog_template)) {
                return $blog_template;
            }
        }
        
        if (is_single() && get_option('cap_override_single_template', 0)) {
            $single_template = locate_template(['simple-author-profile/single-post.php']);
            if (!$single_template) {
                $single_template = CAP_PLUGIN_DIR . 'templates/single-post.php';
            }
            
            if (file_exists($single_template)) {
                return $single_template;
            }
        }
        
        return $template;
    }
    
    /**
     * Register blog template
     */
    public function register_blog_template($templates) {
        $templates['template-blog.php'] = __('Custom Blog Archive', 'custom-author-profile');
        return $templates;
    }
    
    /**
     * Enqueue styles
     */
    public function enqueue_styles() {
        if (is_author()) {
            wp_enqueue_style('cap-author-styles', CAP_PLUGIN_URL . 'css/author-style.css', [], CAP_VERSION);
        }
        
        if (is_page_template('template-blog.php')) {
            wp_enqueue_style('cap-blog-styles', CAP_PLUGIN_URL . 'css/blog-style.css', [], CAP_VERSION);
        }
        
        if (is_single()) {
            // Load Full Article Template Styles only if enabled
            if (get_option('cap_override_single_template', 0)) {
                wp_enqueue_style('cap-single-post-styles', CAP_PLUGIN_URL . 'css/article-style.css', [], CAP_VERSION);
            }

            // Load Author Box Styles if enabled
            if (get_option('cap_enable_author_box', 0)) {
                wp_enqueue_style('cap-author-box-styles', CAP_PLUGIN_URL . 'css/cap-author-box.css', [], CAP_VERSION);
            }
        }
        
        // Add custom colors CSS if set
        $this->enqueue_custom_colors();
    }
    
    /**
     * Enqueue custom colors
     */
    private function enqueue_custom_colors() {
        $social_color = get_option('cap_social_icon_color', '');
        $link_color = get_option('cap_link_color', '');
        $link_hover = get_option('cap_link_hover_color', '');
        
        if ($social_color || $link_color || $link_hover) {
            $custom_css = '';
            if ($social_color) {
                $custom_css .= '.social-icon { filter: brightness(0) saturate(100%) !important; opacity: 0.8; } .social-icon:hover { opacity: 1; }';
            }
            if ($link_color) {
                $custom_css .= '.read-more, .cap-author-link { color: ' . esc_attr($link_color) . ' !important; }';
            }
            if ($link_hover) {
                $custom_css .= '.read-more:hover, .cap-author-link:hover { color: ' . esc_attr($link_hover) . ' !important; }';
            }
            wp_register_style('cap-custom-colors', false);
            wp_enqueue_style('cap-custom-colors');
            wp_add_inline_style('cap-custom-colors', $custom_css);
        }
    }
    
    /**
     * Add author box to content
     */
    public function add_author_box($content) {
        if (!is_single() || get_post_type() != 'post') {
            return $content;
        }
        
        $position = get_option('cap_author_box_position', 'bottom');
        
        if ($position === 'manual' || !get_option('cap_enable_author_box', 0)) {
            return $content;
        }
        
        $author_box = $this->build_author_box();
        
        if ($position === 'top') {
            return $author_box . $content;
        } elseif ($position === 'both') {
            return $author_box . $content . $author_box;
        } else {
            return $content . $author_box;
        }
    }

    /**
     * Build author box HTML
     *
     * @return string
     */
    public function build_author_box() {
        $author_id = get_the_author_meta('ID');
        $image_id = get_user_meta($author_id, 'author_profile_image_id', true);
        $profile_image = $image_id ? wp_get_attachment_image_url($image_id, 'medium') : '';
        
        $output = '<div class="cap-author-box">';
        
        // Profile Image
        $output .= '<div class="cap-author-image-container">';
        if ($profile_image) {
            $output .= '<img class="cap-author-box-image" src="' . esc_url($profile_image) . '" alt="' . esc_attr(get_the_author()) . '">';
        } else {
            $output .= get_avatar($author_id, 175, '', esc_attr(get_the_author()));
        }
        $output .= '</div>';
        
        // Content
        $output .= '<div class="cap-author-box-content">';
        $output .= '<h3 class="cap-author-box-title">' . esc_html(get_option('cap_label_content_by', __('Content provided by:', 'custom-author-profile'))) . ' ' . esc_html(get_the_author()) . '</h3>';
        
        // Social icons (centralized)
        $output .= '<div class="cap-author-social">';
        foreach (array_keys(CAP_Plugin::get_social_networks()) as $network) {
            $url = get_user_meta($author_id, 'author_' . $network, true);
            if ($url) {
                $icon = $this->get_svg_icon($network);
                if (!$icon) {
                    $icon = '<img src="' . esc_url(CAP_PLUGIN_URL . 'templates/images/' . $network . '.svg') . '" alt="' . esc_attr(ucfirst($network)) . '" class="social-icon">';
                }
                $output .= '<a href="' . esc_url($url) . '" target="_blank" rel="noopener noreferrer">' . $icon . '</a>';
            }
        }
        $output .= '</div>';
        
        // More articles link
        $output .= '<a href="' . esc_url(get_author_posts_url($author_id)) . '" class="cap-author-link">';
        $output .= esc_html(get_option('cap_label_more_articles', __('See more articles by', 'custom-author-profile'))) . ' ' . esc_html(get_the_author()) . ' &raquo;';
        $output .= '</a>';
        
        $output .= '</div>'; // .cap-author-box-content
        $output .= '</div>'; // .cap-author-box
        
        return $output;
    }

    /**
     * Render author box shortcode
     *
     * Usage: [cap_author_box]
     *
     * @param array $atts Shortcode attributes
     * @return string
     */
    public function render_author_box_shortcode($atts) {
        if (!is_single() || get_post_type() !== 'post') {
            return '';
        }
        
        // Ensure styles are loaded
        if (!wp_style_is('cap-author-box-styles', 'enqueued')) {
            wp_enqueue_style('cap-author-box-styles', CAP_PLUGIN_URL . 'css/cap-author-box.css', [], CAP_VERSION);
        }
        
        return $this->build_author_box();
    }

    /**
     * Get SVG icon content (sanitized)
     * 
     * @param string $network
     * @return string|false
     */
    public function get_svg_icon($network) {
        $file_path = CAP_PLUGIN_DIR . 'templates/images/' . sanitize_file_name($network) . '.svg';
        if (file_exists($file_path)) {
            return $this->sanitize_svg(file_get_contents($file_path));
        }
        return false;
    }

    /**
     * Sanitize SVG content to prevent XSS
     *
     * @param string $svg
     * @return string
     */
    private function sanitize_svg($svg) {
        // Remove script tags
        $svg = preg_replace('/<script\b[^>]*>.*?<\/script>/is', '', $svg);
        // Remove event handlers (on*)
        $svg = preg_replace('/\s+on\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]*)/i', '', $svg);
        // Remove javascript: URLs
        $svg = preg_replace('/(href|xlink:href)\s*=\s*["\']?\s*javascript:[^"\'>\s]*/i', '', $svg);
        // Strip width/height attributes from <svg> root so CSS controls sizing
        $svg = preg_replace('/(<svg\b[^>]*)\s+(width|height)\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]*)/i', '$1', $svg);
        $svg = preg_replace('/(<svg\b[^>]*)\s+(width|height)\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]*)/i', '$1', $svg);
        return $svg;
    }
    
    /**
     * Custom user avatar
     */
    public function custom_user_avatar($avatar, $id_or_email, $size, $default, $alt) {
        if ($this->is_filtering_avatar) {
            return $avatar;
        }
        $this->is_filtering_avatar = true;

        $user_id = false;
        
        if (is_numeric($id_or_email)) {
            $user_id = $id_or_email;
        } elseif (is_object($id_or_email) && !empty($id_or_email->user_id)) {
            $user_id = $id_or_email->user_id;
        } elseif (is_string($id_or_email)) {
            $user = get_user_by('email', $id_or_email);
            if ($user) {
                $user_id = $user->ID;
            }
        }
        
        if ($user_id) {
            $custom_avatar = get_user_meta($user_id, 'custom_avatar', true);
            if ($custom_avatar) {
                $size = intval($size);
                $this->is_filtering_avatar = false;
                return "<img src='" . esc_url($custom_avatar) . "' width='{$size}' height='{$size}' alt='" . esc_attr($alt) . "' class='avatar avatar-{$size} photo' />";
            }
        }
        
        $this->is_filtering_avatar = false;
        return $avatar;
    }
    
    /**
     * Custom excerpt length — only applied on plugin templates
     */
    public function custom_excerpt_length($length) {
        $custom_length = get_option('cap_excerpt_length', 0);
        if ($custom_length <= 0) {
            return $length;
        }
        
        // Only apply on our plugin's templates
        if (is_author() && get_option('cap_override_author_template', 0)) {
            return $custom_length;
        }
        if (is_page_template('template-blog.php') && get_option('cap_override_blog_template', 0)) {
            return $custom_length;
        }
        
        return $length;
    }
    
    /**
     * Modify author posts per page
     */
    public function modify_author_posts_per_page($query) {
        if (!is_admin() && $query->is_main_query() && is_author()) {
            $posts_per_page = get_option('cap_posts_per_page', 0);
            if ($posts_per_page > 0) {
                $query->set('posts_per_page', $posts_per_page);
            }
        }
    }
}
