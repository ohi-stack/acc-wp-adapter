# Algonquian Offer Generator Integrations

## Purpose

This document defines the integration surface for the Offer Generator module across the Algonquian Real Estate WordPress platform.

## Integrated Systems

| System | Direction | Purpose |
| --- | --- | --- |
| Deal Intake | inbound | Receives seller/property data and creates source deal records. |
| Pipeline CRM | inbound/outbound | Reads deal status and updates stage after document generation. |
| MAO Engine | inbound | Pulls underwriting outputs used in offer pricing. |
| PDF Engine | outbound | Sends rendered HTML for PDF conversion and archival. |
| Signature Engine | outbound/inbound | Sends execution-ready documents and receives signature status. |
| Buyer Portal | outbound | Publishes approved deal packages to qualified buyers. |
| Funding Tracker | outbound | Provides offer terms and source/use data for lender review. |
| Command Center | outbound | Reports generated offers, document status, and pipeline metrics. |
| WooCommerce/Memberships | inbound | Controls premium template, buyer, and investor access where enabled. |

## Canonical Flow

```text
Deal Intake -> Pipeline CRM -> MAO Engine -> Offer Generator -> PDF Engine -> Signature Engine -> Document Library -> Command Center
```

## Production Principle

The Offer Generator should not be the system of record for all transaction data. It should consume normalized deal records, render documents, store document metadata, and publish audit events.
