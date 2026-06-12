# Algonquian Real Estate Plugin Package Archive

Commit purpose:

> Add Algonquian Real Estate plugin package archive
>
> - Add 12 released plugin ZIP packages
> - Add platform package manifest
> - Track production and release-candidate plugin versions
> - Preserve deployable artifacts for WordPress installation

## Package Manifest

| # | Package | Version / Status | Purpose |
|---:|---|---|---|
| 1 | `Algonquian-Real-Estate-Platform-v1.0.0.zip` | 1.0.0 | Main platform package. |
| 2 | `algonquian-real-estate-plugin-suite.zip` | Suite archive | Full plugin suite archive. |
| 3 | `algonquian-real-estate-plugin-suite-updated.zip` | Suite archive updated | Updated full plugin suite archive. |
| 4 | `algq-deal-intake-1.0.2-rc.2.zip` | 1.0.2-rc.2 | Seller lead capture and deal intake. |
| 5 | `algq-pipeline-crm-1.0.0-production.zip` | 1.0.0 production | Acquisition lifecycle CRM and Kanban pipeline. |
| 6 | `algq-automation-engine-1.0.0-production.zip` | 1.0.0 production | Trigger-based workflow automation. |
| 7 | `algq-buyer-portal-1.0.0.zip` | 1.0.0 | Buyer portal and deal access controls. |
| 8 | `algq-deal-marketplace-1.0.0.zip` | 1.0.0 | Buyer-facing marketplace and NDA-gated deal distribution. |
| 9 | `algq-digital-store-1.0.0.zip` | 1.0.0 | Digital products storefront. |
| 10 | `algq-document-library-1.0.0.zip` | 1.0.0 | Institutional document library and lender package support. |
| 11 | `algq-pdf-signature-1.0.0.zip` | 1.0.0 | PDF and signature workflow engine. |
| 12 | `algq-woocommerce-bridge-1.0.0-rc3-dashboard-branding.zip` | 1.0.0-rc3 | WooCommerce bridge, dashboard, and branding integration. |

## Source-Control Rule

The preferred production structure is:

```text
source/
├── algq-deal-intake/
├── algq-pipeline-crm/
├── algq-automation-engine/
├── algq-buyer-portal/
├── algq-deal-marketplace/
├── algq-digital-store/
├── algq-document-library/
├── algq-pdf-signature/
├── algq-woocommerce-bridge/
└── algq-offer-generator/

packages/
└── package-manifest.md
```

Binary ZIP files should be attached to GitHub Releases or committed with Git LFS when package archival inside the repository is required.

## Production Package Standard

Each plugin package must include:

- Plugin Bootstrap
- Activation Hook
- Automatic Page Generation
- Shortcodes
- Admin Menu
- Capabilities
- Nonces
- Input Sanitization
- Output Escaping
- README
- Documentation
- Branding Assets
- Changelog
- Uninstall Cleanup

## Deployment Note

Before a plugin ZIP is installed on a production WordPress site, confirm:

1. The plugin activates without fatal errors.
2. Admin pages load.
3. Auto-generated pages contain valid WPBakery syntax.
4. Shortcodes render.
5. Required database tables/options are created.
6. No duplicate pages are created on reactivation.
7. All user-facing output is escaped.
8. Form submissions use nonces and capability checks.
