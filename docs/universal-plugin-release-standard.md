# Universal Plugin Release Standard

Every Algonquian Real Estate WordPress plugin must include the following production elements before release.

## Required Build Elements

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

## Required Security Rules

- All admin actions must verify a nonce.
- All form submissions must sanitize input before storage.
- All output must be escaped before rendering.
- Admin screens must enforce capability checks.
- Public shortcodes must avoid direct database writes unless protected by nonce and validation.

## Required Activation Rules

On activation, every plugin must:

- Create required pages if they do not already exist.
- Insert WPBakery-safe shortcode content.
- Store generated page IDs in plugin options.
- Register roles or capabilities when needed.
- Flush rewrite rules when custom post types or routes are registered.

## Required Page Format

[vc_column_text]
[plugin_shortcode]
[/vc_column_text]

## Required Documentation

Each plugin must ship with:

- README.md
- CHANGELOG.md
- docs/INSTALL.md
- docs/SHORTCODES.md
- docs/ADMIN.md
- docs/SECURITY.md
- docs/UNINSTALL.md

## Required Branding

Each plugin must include:

- Logo
- Banner
- Admin header image or style
- Plugin catalog card image
- Product-style description
