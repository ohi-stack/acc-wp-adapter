# Branding, CSS, JS, Admin Dashboard, Settings, and Widgets Standard

## Brand Authority

All Algonquian Real Estate plugins must share one institutional visual system:

- Primary blue: #0B3D91
- Institutional gold: #D4AF37
- White: #FFFFFF
- Dark text: #111827
- Muted gray: #6B7280
- Border gray: #E5E7EB

## Required Asset Directories

Each plugin must include:

assets/css/admin.css
assets/css/frontend.css
assets/js/admin.js
assets/js/frontend.js
assets/images/logo.png
assets/images/banner.png
assets/images/plugin-card.png

## Required Admin Dashboard

Each plugin must include an admin dashboard page with:

- Branded header
- Plugin title
- Version number
- KPI cards
- Recent activity panel
- Quick actions
- Documentation links
- Settings shortcut

## Required Settings Page

Each plugin must include a settings page with:

- Nonce-protected form
- Capability check
- Sanitized settings save
- Escaped settings output
- Brand/logo settings when applicable
- Notification email settings when applicable
- Integration toggles when applicable

## Required Widgets

Each plugin must expose dashboard widgets where useful.

Examples:

- Deal Intake: New Leads, Duplicate Leads, High Priority Leads
- Pipeline CRM: Deals by Stage, Active Deals, Offers Sent
- MAO Engine: Average MAO, Approved Deals, Risk Flags
- Offer Generator: Offers Generated, Pending Approvals, Recent PDFs
- Funding Tracker: Commitments, Funding Gaps, Capital Available
- Buyer Portal: Registered Buyers, NDA Accepted, Deal Views
- Automation Engine: Active Rules, Failed Runs, Recent Actions
- PDF Engine: Documents Generated, Signature Pending, Signed Files
- Document Library: Documents, Templates, Downloads
- Command Center: Platform KPIs, Pipeline Value, System Health

## Required CSS Standard

Admin and frontend styles must be responsive, branded, and isolated with plugin-specific class prefixes.

## Required JS Standard

Scripts must avoid global pollution and use localized WordPress data for REST URLs and nonces.

## Release Rule

A plugin is not production-ready until it has branding assets, admin dashboard, settings, widgets, CSS, JS, documentation, changelog, and uninstall cleanup.
