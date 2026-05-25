# ACC WordPress Adapter

ACC WordPress Adapter — governed execution bridge between ACC, OMOS, WordPress REST APIs, WooCommerce systems, and plugin endpoints.

This repository exists to provide a controlled integration layer between:

- OneGodian.org
- OneGodian.com
- OMOS runtime systems
- ACC orchestration infrastructure
- WordPress/WooCommerce environments

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

### Public Components

- Belief Mapper Lite shortcode
- Dashboard embed blocks
- Identity pathway modules
- Consent and disclosure notices

### Governance & Safety

- Audit alignment
- Request logging
- Idempotent actions
- Environment separation
- Operational disclaimers

## Recommended Production Architecture

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
- or financial guarantees.

Use disciplined language focused on:

- voluntary participation,
- identity reflection,
- educational tooling,
- commercial software systems,
- and human-centered interaction.

## Production Rule

If a feature is not:

- operational,
- documented,
- repeatable,
- and testable,

it is not considered production-ready.
