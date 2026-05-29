# Algonquian Offer Generator

Version: 1.0.0  
Author: Onegodian  
Namespace: `algq-offer-generator`

## Purpose

Algonquian Offer Generator is a WordPress plugin module for Algonquian Real Estate LLC. It generates acquisition offer documents from structured CRM deal data, including:

- Purchase Agreements
- Letters of Intent
- Seller Financing Offers
- Cash offer summaries

The plugin is designed to connect Deal Intake, Pipeline CRM, MAO underwriting, document generation, audit logging, and deal-to-contract automation.

## Installed Location

Upload the `algq-offer-generator` directory to:

```text
wp-content/plugins/algq-offer-generator/
```

Then activate it in WordPress Admin → Plugins.

## Core Capabilities

- Creates custom SQL tables on activation
- Stores normalized deal, property, seller, financial, offer, document, and audit records
- Provides admin UI pages under **Algonquian Offers**
- Supports merge-field replacement using `{{field_name}}` syntax
- Includes built-in templates for Purchase Agreement, LOI, and Seller Financing Offer
- Provides automation hook for pipeline status transitions
- Records generated documents and audit events

## Shortcodes

```text
[algq_offer_generator]
[algq_offer_generator deal_id="1"]
```

## Automation Hook

The plugin listens for:

```php
do_action('algq_pipeline_status_changed', $deal_id, $old_status, $new_status);
```

When `$new_status` is `offer_sent`, the plugin can automatically generate offer records and log the event.

## Compliance Note

Templates are operational transaction templates and should be reviewed by qualified Connecticut real estate counsel before production execution.
