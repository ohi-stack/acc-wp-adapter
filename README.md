# ACC WordPress Adapter

ACC WordPress Adapter — governed execution bridge between ACC, OMOS, WordPress REST APIs, WooCommerce systems, and plugin endpoints.

## Algonquian Real Estate Platform

Release Status: 1.0.0 Release Candidate.

The `plugin/algonquian-real-estate.php` file is the main WordPress bootstrap for the Algonquian Real Estate Platform. It provides the release-ready foundation for seller intake, MAO underwriting, buyer registration, and the internal admin dashboard.

### Registered Shortcodes

```text
[algq_seller_intake]
[algq_mao_calculator]
[algq_buyer_registration]
[algq_admin_dashboard]
```

### Release Readiness

- Main plugin bootstrap exists under `/plugin`.
- Valid WordPress plugin header is present.
- Required shortcodes are registered.
- Admin menu registers under `Algonquian RE`.
- Activation hook creates core tables and platform options.
- Admin forms and handlers include nonces and capability checks.
- Inputs are sanitized.
- Outputs are escaped.
- Shortcode callbacks return buffered HTML and do not directly print output.

## Repository Scope

This repository exists to provide a controlled integration layer between:

- OneGodian.org
- OneGodian.com
- OMOS runtime systems
- ACC orchestration infrastructure
- WordPress/WooCommerce environments
- AlgonquianRealEstate.com platform operations

while preserving separation between:

- public explanation,
- commercial operations,
- and runtime execution.

## Primary Responsibilities

### Runtime Bridging

- Route approved requests to OMOS runtime endpoints
- Handle API key enforcement
- Normalize WordPress-originated requests
- Enforce timeout and retry safety

### WooCommerce Integration

- Membership entitlement checks
- Product access validation
- Subscription-aware dashboard access
- Digital product unlocks

### Algonquian Real Estate Platform Components

- Seller intake shortcode
- MAO calculator shortcode
- Buyer registration shortcode
- Admin dashboard shortcode
- Offer Generator module scaffold
- PDF engine integration
- REST API integration

### Governance & Safety

- Audit alignment
- Request logging
- Idempotent actions
- Environment separation
- Operational disclaimers

## Recommended Production Architecture

### AlgonquianRealEstate.com

Real estate acquisition, underwriting, document automation, buyer portal, funding tracker, and command center platform.

### OneGodian.org

Public educational and interpretive layer.

### OneGodian.com

Commerce and membership operations layer.

### OMOS Runtime

Protocol, orchestration, and runtime processing layer.

### ACC

Execution and command infrastructure layer.

## Operational Guidance

Avoid positioning plugin functionality as:

- governmental authority,
- compulsory systems,
- sovereign override structures,
- financial guarantees,
- or legal advice.

Use disciplined language focused on:

- voluntary participation,
- commercial software systems,
- real estate operations,
- workflow automation,
- documentation discipline,
- and human-reviewed transaction support.

## Production Rule

If a feature is not:

- operational,
- documented,
- repeatable,
- and testable,

it is not considered production-ready.
