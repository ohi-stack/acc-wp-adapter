# PDF Engine Integration

## Purpose

The PDF engine converts rendered Offer Generator HTML into stored PDF files for deal records, lender review, seller delivery, and archive retention.

## Responsibilities

- Accept rendered HTML from the document generator.
- Apply institutional document styling.
- Generate PDF output.
- Store the file under the configured uploads directory.
- Return file path, URL, checksum, and version metadata.

## Suggested Storage Path

```text
wp-content/uploads/algq/offers/{deal_id}/{document_type}/v{version}.pdf
```

## Interface

```php
Algq_Offer_PDF_Engine::render_pdf($deal_id, $document_type, $html, $options = []);
```

## Status Codes

| Code | Meaning |
| --- | --- |
| draft | Document generated but not finalized. |
| rendered | PDF created successfully. |
| failed | PDF rendering failed. |
| archived | PDF is locked and retained. |

## Production Notes

Use DOMPDF, mPDF, or a hosted PDF rendering service depending on hosting constraints. All live transaction documents should retain version history.
