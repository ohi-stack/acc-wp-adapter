# Algonquian Real Estate Platform

Release Status: 1.0.0 Release Candidate

## Description

Algonquian Real Estate Platform is the core WordPress plugin for AlgonquianRealEstate.com. It provides the base acquisition, underwriting, buyer registration, dashboard, database, and admin framework used by the broader ARE plugin suite.

## Shortcodes

- `[algq_seller_intake]`
- `[algq_mao_calculator]`
- `[algq_buyer_registration]`
- `[algq_admin_dashboard]`

## Installation

1. Upload the plugin folder to `/wp-content/plugins/`.
2. Activate **Algonquian Real Estate Platform** in WordPress Admin.
3. Visit **Algonquian RE** in the admin menu.
4. Configure company settings.
5. Add the shortcodes to WordPress pages.

## Admin Menu

The plugin registers an admin menu at:

```text
/wp-admin/admin.php?page=algq-platform
```

## Security

The plugin uses:

- WordPress nonces
- Capability checks
- Sanitized inputs
- Escaped outputs
- Buffered shortcode rendering

## Database Tables

Created on activation:

- `wp_algq_deals`
- `wp_algq_activity_logs`

## FAQ

### Does this plugin replace the specialized plugins?
No. This is the core platform plugin. Specialized modules such as Pipeline CRM, MAO Engine, Buyer Portal, and PDF Signature Engine may extend it.

### Are shortcodes safe for WPBakery?
Yes. Shortcode callbacks return buffered HTML and should be placed inside valid `[vc_column_text]...[/vc_column_text]` wrappers.

## Screenshots

Screenshots should be added under `/assets/screenshots/` before final marketplace release.

## Reviews

Internal release candidate review status: pending QA.
