# OMOS → ACC → WordPress Action Boundary

Updated: 2026-09-16

## Purpose

This repository participates only in the **authorized action path** for WordPress operations. It is not the canonical OMOS WordPress read/sync bridge and it must not become a shortcut around OMOS Human Gate, policy, WordPress permissions, or target-system audit requirements.

Canonical OMOS runtime: `https://omos.onegodian.com`  
Canonical WordPress bridge contract/source: `ohi-stack/omos-site` → `plugins/omos-core-tools-v1.3.0/`

## Separation

```text
READ / STATUS / MANIFEST / DISPLAY
WordPress ↔ OMOS Core Tools bridge ↔ OMOS runtime

CONSEQUENTIAL WRITE / EXECUTION
OMOS governed decision
→ Human Gate / policy authorization
→ ACC action router
→ acc-wp-adapter
→ target WordPress site
→ action receipt
→ ACC / OMOS audit + Decision Record outcome
```

Installing or configuring `omos-core-tools` does not authorize this adapter to mutate WordPress.

## Required action envelope

Before a WordPress mutation is accepted, the adapter should be able to bind the request to:

- action ID / idempotency key;
- OMOS run or Decision Record reference where applicable;
- ACC execution/job reference;
- target WordPress site;
- declared operation and resource;
- requested capability/scope;
- authorization/Human Gate reference where required;
- requesting principal/tenant context;
- request timestamp;
- payload hash;
- expected source version when conflict detection is possible.

## Required result envelope

After execution, return and preserve:

- action ID;
- target site;
- operation;
- result status;
- target resource ID/URL where safe;
- target response/version metadata;
- executed timestamp;
- result hash or receipt reference where available;
- sanitized error state;
- retry/idempotency state;
- actor/adapter identity;
- audit/provenance reference.

## Security rules

1. Default deny for write capabilities.
2. WordPress credentials remain server-side.
3. Do not accept target credentials in request payloads.
4. Use least-privilege WordPress/application credentials.
5. Enforce WordPress capability and nonce/application-auth requirements appropriate to the execution path.
6. Verify the OMOS/ACC authorization reference for consequential mutations.
7. Idempotency is required for retriable writes.
8. Reject arbitrary target URLs unless explicitly allowlisted/configured.
9. Do not log secrets or sensitive content unnecessarily.
10. A successful HTTP request is not sufficient evidence of a successful business action; persist the target result/receipt.

## Source-of-record rule

The target WordPress property remains authoritative for the state of the WordPress object after execution. OMOS stores the governed decision and the returned execution outcome/provenance; it does not silently rewrite the target's authoritative state in its own records.

## Production gate

No WordPress action connector should be labeled Production until a staging test proves authorization rejection, successful allowed execution, idempotent retry, conflict/failure behavior, audit receipt capture, and visibility of the outcome in the appropriate OMOS/ACC record.
