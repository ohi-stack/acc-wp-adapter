# Universal Production Standard for Every Algonquian Real Estate Plugin

Every plugin in the Algonquian Real Estate WordPress suite must meet this checklist before being packaged as a release ZIP.

## Required Components

1. Plugin Bootstrap
2. Activation Hook
3. Automatic Page Generation
4. Shortcodes
5. Admin Menu
6. Capabilities
7. Nonces
8. Input Sanitization
9. Output Escaping
10. README
11. Documentation
12. Branding Assets
13. Changelog
14. Uninstall Cleanup

## Required Repository Layout

```text
plugin-slug/
├── README.md
├── CHANGELOG.md
├── LICENSE
├── docs/
│   ├── install-guide.md
│   ├── shortcodes.md
│   ├── admin-settings.md
│   ├── hooks.md
│   └── database-schema.md
├── plugin/
│   ├── plugin-slug.php
│   ├── uninstall.php
│   ├── includes/
│   │   ├── class-activator.php
│   │   ├── class-page-generator.php
│   │   ├── class-shortcodes.php
│   │   ├── class-admin.php
│   │   ├── class-capabilities.php
│   │   ├── class-security.php
│   │   └── class-database.php
│   ├── admin/
│   │   ├── dashboard.php
│   │   ├── settings.php
│   │   └── help.php
│   ├── assets/
│   │   ├── css/
│   │   ├── js/
│   │   └── images/
│   └── sql/
│       └── schema.sql
└── .github/
    └── workflows/
        └── build-plugin.yml
```

## Bootstrap Requirements

The main plugin file must include a valid WordPress plugin header:

```php
/**
 * Plugin Name: Plugin Display Name
 * Description: Clear description of plugin purpose.
 * Version: 1.0.0
 * Author: Onegodian | Algonquian Real Estate
 * Text Domain: plugin-slug
 * Requires at least: 6.4
 * Requires PHP: 7.4
 */
```

The bootstrap must:

- Exit if `ABSPATH` is not defined.
- Define plugin constants.
- Load required classes.
- Register activation and deactivation hooks.
- Register shortcodes.
- Register admin menus.
- Avoid fatal errors when dependencies are missing.

## Activation Hook Requirements

Activation must:

- Create required tables with `dbDelta()`.
- Create required options.
- Register default settings.
- Generate plugin pages.
- Store plugin version.
- Flush rewrite rules only when needed.

## Automatic Page Generation Requirements

Every plugin must create its own pages during activation.

Each generated page must:

- Use a stable slug.
- Avoid duplicate creation.
- Store generated page IDs in an option.
- Use WPBakery-compatible content.

Correct WPBakery format:

```text
[vc_column_text]
[plugin_shortcode]
[/vc_column_text]
```

Never use:

```text
</vc_column_text>
```

## Shortcode Requirements

Shortcodes must:

- Be registered on `init`.
- Return buffered HTML using `ob_start()` and `ob_get_clean()`.
- Never directly echo output from the callback.
- Sanitize attributes with `shortcode_atts()` and WordPress sanitizers.
- Escape all rendered values.

## Admin Menu Requirements

Admin screens must:

- Use `add_menu_page()` or `add_submenu_page()`.
- Require appropriate capabilities.
- Check `current_user_can()` before rendering.
- Avoid fatal errors if optional integrations are inactive.

## Capabilities

Use the least required capability for each feature:

| Feature | Capability |
| --- | --- |
| Settings | manage_options |
| Deal editing | edit_posts or custom capability |
| Document generation | edit_posts or custom capability |
| Reports | read_private_posts or custom capability |
| System logs | manage_options |

## Nonces

Every admin form and state-changing action must include:

```php
wp_nonce_field('action_name', 'nonce_name');
check_admin_referer('action_name', 'nonce_name');
```

AJAX and REST state changes must use nonce or permission callbacks.

## Input Sanitization

Use:

- `sanitize_text_field()`
- `sanitize_textarea_field()`
- `sanitize_email()`
- `sanitize_key()`
- `esc_url_raw()`
- `absint()`
- `floatval()`
- `wp_kses_post()` for controlled HTML

## Output Escaping

Use:

- `esc_html()`
- `esc_attr()`
- `esc_url()`
- `wp_kses_post()`

No unescaped user-provided data may be printed.

## README Requirements

Each README must include:

- Plugin name
- Version
- Release status
- Purpose
- Features
- Shortcodes
- Admin pages
- Installation steps
- Generated pages
- Dependencies
- Security notes
- Changelog link

Release status must be explicit:

```text
Release Status: 1.0.0 Release Candidate
```

## Documentation Requirements

Each plugin must include docs for:

- Installation
- Getting started
- Shortcodes
- Admin settings
- Hooks/actions/filters
- Database schema
- Permissions
- Troubleshooting

## Branding Assets

Each plugin must include:

- Plugin logo or placeholder
- Product/package image or placeholder
- Admin icon or dashicon assignment
- Consistent black/gold/white Algonquian Real Estate styling

## Changelog

Each plugin must include `CHANGELOG.md` using semantic versioning:

- `1.0.0` initial production release
- `1.0.1` patch release
- `1.1.0` feature release
- `2.0.0` major architecture release

## Uninstall Cleanup

Every plugin must include `uninstall.php`.

The uninstall routine must:

- Exit unless `WP_UNINSTALL_PLUGIN` is defined.
- Remove temporary options and transients.
- Clear scheduled events.
- Avoid deleting user content, documents, leads, deals, or transaction records unless a separate explicit setting permits it.

## Build Workflow

Every plugin must include a GitHub Actions workflow that:

- Runs PHP syntax checks.
- Builds an installable ZIP.
- Uploads ZIP as an artifact.

## Final Release Gate

A plugin is not production-ready until it passes all items in this standard.
