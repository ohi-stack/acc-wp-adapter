# Algonquian Offer Generator Production Readiness

## Required Plugin Standard

The Offer Generator must include the following before release packaging:

- Plugin Bootstrap
- Activation Hook
- Automatic Page Generation
- Shortcodes
- Admin Menu
- Capabilities
- Nonces
- Input Sanitization
- Output Escaping
- README
- Documentation
- Branding Assets
- Changelog
- Uninstall Cleanup

## Current Production Requirements

### Plugin Bootstrap

Main file:

```text
plugin/algq-offer-generator.php
```

Required header fields:

- Plugin Name
- Plugin URI
- Description
- Version
- Author
- Text Domain
- Requires at least
- Requires PHP

### Activation Hook

Activation must:

- Install database tables
- Create required options
- Generate frontend pages
- Store generated page IDs
- Store plugin version

### Automatic Page Generation

The plugin must generate:

```text
/plugin
/plugin/offer-generator
/plugin/offer-generator/start
/plugin/offer-generator/docs
/plugin/offer-generator/templates
```

Each page must contain the correct shortcode in WPBakery syntax:

```text
[vc_column_text]
[algq_offer_generator]
[/vc_column_text]
```

### Shortcodes

Required shortcode:

```text
[algq_offer_generator]
```

### Admin Menu

Required screens:

- Dashboard
- Templates
- Documents
- Settings

### Security

All admin handlers must use:

- `current_user_can()`
- `wp_nonce_field()`
- `check_admin_referer()`
- `sanitize_text_field()`
- `sanitize_key()`
- `absint()`
- `wp_kses_post()`
- `esc_html()`
- `esc_attr()`
- `esc_url()`

### Branding Assets

Required assets:

```text
assets/css/admin.css
assets/js/admin.js
assets/images/.gitkeep
```

Admin styling should use Algonquian blue/gold institutional branding.

### Documentation

Required docs:

- README.md
- CHANGELOG.md
- docs/integrations.md
- docs/pdf-engine.md
- docs/production-readiness.md

### Uninstall Cleanup

The uninstall routine must remove plugin options and temporary data without deleting transaction records or generated documents by default.

## Release Status

Version: 1.0.0 RC
Status: Production hardening in progress
