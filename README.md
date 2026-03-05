# Custom Author & Blog Templates — v2.1.0

## Overview
WordPress plugin that adds custom author profile fields and custom templates for author pages, blog archives, and single posts.

## Features
- Custom author profile fields (image, rich-text bio, 6 social networks)
- Custom templates for author archives, blog pages, and single posts
- Configurable author box with flexible positioning (top, bottom, both, or shortcode)
- `[cap_author_box]` shortcode for manual placement
- Author page layout options (top centered, left sidebar, right sidebar)
- Toggle visibility of profile image, bio, social icons, email, and website
- Customizable colors and text labels (all labels configurable from admin)
- Posts per page and excerpt length controls (scoped to plugin templates)
- SVG social icons with XSS sanitization
- Translation-ready with full i18n support
- Activation hook sets sensible defaults (templates off by default)
- Clean uninstall removes all plugin data

## File Structure

```
simple-author-profile/
├── simple-author-profile.php       # Main plugin file (loader + activation hook)
├── uninstall.php                   # Cleanup on deletion
├── includes/                       # Core classes
│   ├── class-cap-plugin.php       # Main plugin class (singleton, centralized config)
│   ├── class-cap-admin.php        # Admin functionality
│   ├── class-cap-frontend.php     # Frontend functionality
│   └── class-cap-settings.php     # Settings page + sanitization
├── templates/                      # Template files (overridable via theme)
│   ├── author.php                 # Author archive template
│   ├── single-post.php            # Single post template
│   ├── template-blog.php          # Blog archive template
│   └── images/                    # Social media SVG icons
├── css/                           # Stylesheets
│   ├── admin-style.css
│   ├── author-style.css
│   ├── blog-style.css
│   ├── article-style.css
│   └── cap-author-box.css
├── js/                            # JavaScript files
│   ├── admin.js                   # Color picker init
│   ├── author-profile.js          # Profile image uploader
│   └── meta-box-upload.js         # Blog header image meta box
├── images/                        # Plugin assets (admin icon)
└── languages/                     # Translation files
    └── custom-author-profile.pot
```

## Installation

1. Upload the plugin folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure settings under **Author Profile** in the WordPress admin
4. Edit user profiles to add author images, bios, and social links

## Requirements

- WordPress 5.0+
- PHP 7.4+

## Template Overrides
Themes can override any plugin template by placing a copy in:
```
your-theme/simple-author-profile/author.php
your-theme/simple-author-profile/single-post.php
your-theme/simple-author-profile/template-blog.php
```

## Shortcode
Use `[cap_author_box]` inside post content to manually place the author box when the position setting is set to "Manual (use shortcode)".

## Architecture

### OOP Structure
- **CAP_Plugin**: Private-constructor singleton. Centralizes all configuration — option keys, defaults, social networks, and meta keys are defined once and consumed everywhere.
- **CAP_Admin**: Admin menu, profile fields, meta boxes, media uploaders, reset handler.
- **CAP_Frontend**: Template overrides, author box builder, shortcode, SVG sanitization, avatar filter, scoped excerpt control.
- **CAP_Settings**: Settings registration with per-option `sanitize_callback`, and the settings page renderer.

### Centralized Configuration
All option keys, social networks, user meta keys, and post meta keys are defined as static methods on `CAP_Plugin`:
- `CAP_Plugin::get_default_options()` — option keys + defaults
- `CAP_Plugin::get_option_keys()` — just the keys
- `CAP_Plugin::get_social_networks()` — network key → label map
- `CAP_Plugin::get_user_meta_keys()` — all user meta managed by the plugin
- `CAP_Plugin::get_post_meta_keys()` — all post meta managed by the plugin

Adding a new social network or option requires changing **one method** — settings registration, admin fields, frontend rendering, reset, and uninstall all follow automatically.

### Security
- Nonce verification on all form submissions
- Capability checks (`edit_user`, `edit_post`, `manage_options`)
- `sanitize_callback` on every registered setting (checkboxes, integers, selects, colors, text)
- Input sanitization: `intval`, `esc_url_raw`, `wp_kses_post`, `sanitize_text_field`, `sanitize_hex_color`, `absint`
- Output escaping: `esc_html`, `esc_attr`, `esc_url` throughout
- SVG sanitization: strips `<script>` tags, `on*` event handlers, `javascript:` URLs, and hardcoded dimensions
- Avatar filter recursion guard prevents infinite loops
- `wp_safe_redirect()` for admin redirects

### Internationalization
- Text domain: `custom-author-profile`
- All PHP strings wrapped in `__()` / `_e()`
- All JS strings passed via `wp_localize_script()`
- POT file included for translations
- All user-facing labels configurable from the admin settings page

### Version Management
The version is defined **once** in the plugin file header. `CAP_VERSION` is derived at runtime via `get_file_data()` — no duplicate version strings to maintain.

## Developer Notes

### Constants
| Constant | Description |
|---|---|
| `CAP_VERSION` | Plugin version (read from file header) |
| `CAP_PLUGIN_FILE` | Main plugin file path |
| `CAP_PLUGIN_BASENAME` | Plugin basename for WP internals |
| `CAP_PLUGIN_DIR` | Plugin directory path |
| `CAP_PLUGIN_URL` | Plugin URL |

### Filters
| Filter | Purpose |
|---|---|
| `template_include` | Override WordPress templates |
| `theme_page_templates` | Register custom page templates |
| `the_content` | Add author box to single posts |
| `get_avatar` | Custom user avatars from profile image |
| `excerpt_length` | Custom excerpt length (scoped to plugin templates) |

### Actions
| Action | Purpose |
|---|---|
| `cap_init` | Fires after plugin initialization (for third-party integrations) |
| `init` | Initialize admin/frontend components |
| `plugins_loaded` | Load text domain translations |

## Changelog

### Version 2.1.0
- Single source of truth for version (derived from plugin header via `get_file_data`)
- Centralized social networks, option keys, user/post meta keys in `CAP_Plugin`
- Activation hook with sensible defaults (all template overrides off by default)
- `[cap_author_box]` shortcode for manual author box placement
- `sanitize_callback` for every registered setting (checkbox, int, select, color, text)
- SVG sanitization: strip `<script>`, `on*` handlers, `javascript:` URLs, and dimensions
- Recursion guard on custom avatar filter
- Configurable "No Posts Message" label in admin
- All JS strings localized via `wp_localize_script`
- Meta box inline script extracted to `js/meta-box-upload.js`
- Excerpt length filter scoped to plugin templates only
- Custom colors via `wp_add_inline_style` instead of raw `echo`
- Fixed duplicate `get_footer()` in author template
- Fixed hardcoded Romanian fallback strings → English with `__()`
- Fixed SVG icons rendering at native 800×800 size
- Fixed `custom_avatar` not cleared on profile image removal
- `wp_safe_redirect()` replaces `wp_redirect()`
- Private constructor enforces singleton pattern
- Private `includes()` method
- Removed unused `CAP_TEXT_DOMAIN` constant
- Removed development comments from settings template

### Version 2.0.0
- Complete refactoring to OOP architecture
- Added internationalization (i18n) support
- Separated admin and frontend code
- Added uninstall cleanup
- Added 4 new social networks (LinkedIn, Twitter, YouTube, TikTok)
- Added author box positioning options
- Added color customization
- Added posts per page control
- Added excerpt length control
- Added reset settings functionality
- Improved security with better nonce and capability checks

### Version 1.10
- Initial release with basic functionality

## License
GPL v2 or later

## Author
Clienti pe Viata — [clientipeviata.ro](https://clientipeviata.ro)
