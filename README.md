# Custom Author & Blog Templates - Version 2.0.0

## Overview
WordPress plugin that adds custom author profile fields and custom templates for author pages, blog archives, and single posts.

## Features
- Custom author profile fields (image, bio, 6 social networks)
- Custom templates for author archives, blog pages, and single posts
- Configurable author box with flexible positioning
- Customizable colors and text labels
- Posts per page and excerpt length controls
- Translation-ready with i18n support

## File Structure

```
simple-author-profile/
├── simple-author-profile.php       # Main plugin file (loader)
├── uninstall.php                   # Cleanup on deletion
├── includes/                       # Core classes
│   ├── class-cap-plugin.php       # Main plugin class
│   ├── class-cap-admin.php        # Admin functionality
│   ├── class-cap-frontend.php     # Frontend functionality
│   └── class-cap-settings.php     # Settings page
├── templates/                      # Template files
│   ├── author.php                 # Author archive template
│   ├── single-post.php            # Single post template
│   ├── template-blog.php          # Blog archive template
│   └── images/                    # Social media icons
├── css/                           # Stylesheets
│   ├── admin-style.css
│   ├── author-style.css
│   ├── blog-style.css
│   └── article-style.css
├── js/                            # JavaScript files
│   ├── admin.js
│   └── author-profile.js
├── images/                        # Plugin assets
└── languages/                     # Translation files
    └── custom-author-profile.pot
```

## Installation

1. Upload the plugin folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure settings under 'Author Profile' in WordPress admin

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher

## Architecture

### OOP Structure
The plugin uses a modern object-oriented architecture with:

- **CAP_Plugin**: Main singleton class that initializes all components
- **CAP_Admin**: Handles all admin functionality (settings, meta boxes, profile fields)
- **CAP_Frontend**: Manages frontend display (templates, author box, styles)
- **CAP_Settings**: Dedicated settings registration and rendering

### Hooks System
The plugin provides action hooks for third-party integration:
- `cap_init`: Fires after plugin initialization

### Security Features
- Nonce verification on all forms
- Capability checks for user actions
- Input sanitization and output escaping
- SQL injection protection

### Internationalization
- Text domain: `custom-author-profile`
- All strings wrapped in translation functions
- POT file included for translations

### Cleanup
- `uninstall.php` removes all plugin data on deletion
- Cleans options, user meta, and post meta

## Developer Notes

### Constants
- `CAP_VERSION`: Plugin version
- `CAP_PLUGIN_FILE`: Main plugin file path
- `CAP_PLUGIN_DIR`: Plugin directory path
- `CAP_PLUGIN_URL`: Plugin URL
- `CAP_TEXT_DOMAIN`: Translation text domain

### Filters
- `template_include`: Override WordPress templates
- `theme_page_templates`: Register custom page templates
- `the_content`: Add author box to content
- `get_avatar`: Custom user avatars
- `excerpt_length`: Custom excerpt length

### Actions
- `init`: Initialize plugin components
- `plugins_loaded`: Load translations
- `admin_menu`: Add admin menu pages
- `admin_enqueue_scripts`: Load admin assets
- `wp_enqueue_scripts`: Load frontend assets

## Changelog

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
Clienti pe Viata
https://clientipeviata.ro
