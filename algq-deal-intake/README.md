# Algonquian Deal Intake

Version: 1.0.0  
Author: Onegodian | Algonquian Real Estate

## Purpose

Captures public and internal real estate acquisition leads, normalizes seller/property data, assigns a permanent deal ID, scores the lead, applies acquisition tags, and prepares the record for downstream underwriting, document generation, pipeline management, and buyer disposition.

## Shortcodes

- `[deal_intake_form_public]`
- `[deal_intake_form_internal]`
- `[deal_quick_capture]`

## Core Functions

- Public seller/property submission form
- Internal acquisition intake form
- Quick capture form for mobile or call-based entry
- Auto-generated deal IDs in `ARE-YYYY-0001` format
- Deal custom post type
- Seller and property metadata
- Lead source tracking
- Auto-tagging: Distressed, Landlord, Pre-foreclosure, Duplicate, High Priority
- Lead scoring
- Duplicate detection
- Activity log foundation
- REST API scaffold
- CSV export scaffold
- Admin command center scaffold

## Installation

1. Copy `algq-deal-intake` into `wp-content/plugins/`.
2. Activate the plugin in WordPress Admin.
3. Add one of the shortcodes to a page.
4. Configure acquisition team notifications in the plugin settings.

## WPBakery Rule

When embedding plugin documentation or catalog content inside WPBakery, use:

```text
[vc_column_text]
...content...
[/vc_column_text]
```

Never close WPBakery text blocks with `</vc_column_text>`.
