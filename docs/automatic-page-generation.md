# Automatic Page Generation

Each Algonquian Real Estate plugin must create its required WordPress pages on activation and insert the appropriate shortcode into each page.

## Page Content Standard

Use WPBakery-compatible content blocks:

[vc_column_text]
[plugin_shortcode]
[/vc_column_text]

Do not use HTML-style closing tags for WPBakery shortcodes.

## Required Page Map

| Plugin | Page | Shortcode |
|---|---|---|
| Deal Intake | /sell-your-property | [deal_intake_form_public] |
| Deal Intake | /deals/new | [deal_intake_form_internal] |
| Deal Intake | /deals/quick-capture | [deal_quick_capture] |
| Pipeline CRM | /pipeline/board | [algq_pipeline_board] |
| Pipeline CRM | /pipeline/reports | [algq_pipeline_reports] |
| MAO Engine | /plugin/mao-engine/calculator | [algq_mao_calculator] |
| Offer Generator | /plugin/offer-generator/templates | [algq_offer_generator] |
| Buyer Portal | /buyer-dashboard | [algq_buyer_dashboard] |
| Funding Tracker | /funding-dashboard | [algq_funding_dashboard] |
| Automation Engine | /automation-rules | [algq_automation_rules] |
| PDF & Signature | /documents/signatures | [algq_signature_dashboard] |
| Document Library | /documents | [algq_document_library] |
| Command Center | /dashboard | [algq_command_center] |

## Implementation Rule

Every plugin should include a page installer class or activation method that:

1. Checks whether the page slug already exists.
2. Creates the page if missing.
3. Inserts WPBakery-wrapped shortcode content.
4. Saves created page IDs into plugin options.
5. Does not overwrite user-edited pages.
