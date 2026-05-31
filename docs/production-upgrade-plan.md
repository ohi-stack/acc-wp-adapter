# ARE Plugin Production Upgrade Plan

## Current Status

- Deal Intake: started / scaffolded
- Pipeline CRM: most developed module
- Offer Generator: bootstrap + docs started
- Funding Tracker: bootstrap started
- MAO Engine: not started
- Buyer Portal: not started
- Automation Engine: not started
- PDF Engine: not started
- Document Library: not started
- Command Center: not started

## Release Requirement

Each plugin must include:

1. Main WordPress plugin bootstrap file
2. Valid plugin header
3. Activation hook
4. Automatic page generation
5. Shortcode registration
6. Admin menu
7. Capability checks
8. Nonce checks
9. Sanitized inputs
10. Escaped outputs
11. README
12. Documentation page
13. Branding assets
14. Versioned changelog
15. Uninstall cleanup policy

## Priority Upgrade Order

1. Deal Intake to 1.0.0 release candidate
2. Pipeline CRM to working Kanban MVP
3. MAO Engine scaffold
4. Offer Generator scaffold completion
5. Funding Tracker scaffold completion
6. Buyer Portal scaffold
7. Automation Engine scaffold
8. PDF Engine scaffold
9. Document Library scaffold
10. Command Center scaffold

## Automatic Page Generation Standard

Every plugin activation must create required pages with WPBakery-safe shortcode content.
