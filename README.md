# ACC WordPress Adapter

ACC WordPress Adapter — governed execution bridge between ACC, OMOS, WordPress REST APIs, WooCommerce systems, and plugin endpoints.

## Canonical Boundary

This repository is the **authenticated operational-write layer** for WordPress actions initiated through ACC or other approved control-plane workflows.

It is distinct from the shared public/status synchronization layer:

- `ohi-stack/onegodian-platform-plugin` v0.3.0+ — shared WordPress infrastructure and read-through OMOS health/manifest/provider/persistence status.
- `ohi-stack/omos-site` — canonical OMOS runtime and OMOS-specific WordPress assets.
- `ohi-stack/acc-wp-adapter` — governed authenticated WordPress/WooCommerce writes and operational execution.

The canonical OMOS runtime is:

`https://omos.onegodian.com`

`https://omos.onegodian.org`, if deployed, is a WordPress presentation client and not a second execution authority.

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
- QuantumOHI.com where authorized operational actions are needed
- OMOS runtime systems
- ACC orchestration infrastructure
- WordPress/WooCommerce environments
- AlgonquianRealEstate.com platform operations

while preserving separation between:

- public explanation and status synchronization,
- commercial operations,
- and authenticated runtime/action execution.

## Primary Responsibilities

### Governed Runtime Bridging

- Route approved requests to OMOS or approved WordPress runtime endpoints
- Handle API-key or delegated credential enforcement
- Normalize WordPress-originated or ACC-originated requests
- Enforce timeout, retry, idempotency, and replay safety
- Preserve request/response provenance for consequential actions

### WordPress Operational Actions

Examples of actions that belong in this adapter layer rather than the public OMOS status bridge include:

- create/update approved WordPress content;
- perform authorized plugin operations;
- update controlled site configuration;
- process approved WooCommerce operations;
- invoke authenticated application endpoints;
- execute controlled synchronization writes;
- report action evidence back to ACC/OMOS.

Every consequential write must remain subject to the applicable capability, policy, approval, and audit boundary.

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
- Human approval for consequential changes where required
- No provider/model secrets in browser-visible or public WordPress state

## OMOS / WordPress Synchronization Model

```text
                 OMOS CENTRAL RUNTIME
              https://omos.onegodian.com
                         │
              public runtime status reads
                         │
          OneGodian Platform Plugin v0.3.0+
                         │
      OneGodian.org / OneGodian.com / QuantumOHI.com

For consequential writes:

OMOS / Human Gate
        ↓
       ACC
        ↓
ACC WordPress Adapter
        ↓
WordPress / WooCommerce authorized action
        ↓
Execution evidence / audit result
```

The shared plugin handles common read-through status. This adapter handles governed writes. They should not duplicate each other.

## Recommended Production Architecture

### OneGodian.org

Public educational and interpretive layer, with shared OMOS runtime-status sync.

### OneGodian.com

Commerce and membership operations layer, with shared OMOS status plus WooCommerce actions only through approved authenticated paths.

### QuantumOHI.com

Technology/O-H-I presentation and services layer, consuming shared OMOS status and using authenticated actions only where explicitly configured.

### OMOS.OneGodian.org

Optional WordPress presentation client of the canonical `.com` OMOS runtime.

### OMOS Runtime

Governed intelligence, provider orchestration, Alignment, Council processing, persistence, Human Gate, and Decision Records.

### ACC

Execution and command infrastructure layer. ACC authorizes and coordinates external actions; it does not replace OMOS governed intelligence or WordPress domain responsibilities.

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

Repository synchronization does not prove the target WordPress site has deployed the corresponding revision. Deployment proof remains site-specific.
