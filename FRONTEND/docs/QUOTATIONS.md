# Quotation workspace

Quotation details and tools use the Vue workspace sidebar. Start from Reservations → Quotations, or Create trip quotation on an approved inquiry.

## Screens

- /quotations/{uuid}/details: customer, inquiry, totals, current version and version history.
- /inquiries/{uuid}/quotations/new: create from a trip or start a tailor-made quotation.
- /quotation-versions/{uuid}/builder: proposal details, cover, itinerary, activities, price lines, terms, display flags and VAT.
- /quotation-versions/{uuid}/preview: document preview, PDF generation/download, guest link, copyable message drafts and agreed trip booking.

The builder uses the existing Laravel itinerary and pricing services. Explicit line totals are retained; changing quantity or unit price recalculates that line in the editor. Final totals and currency conversion are calculated by Laravel when saved. Saving invalidates an older PDF snapshot. Custom historical activities are retained with their day, and custom terms remain editable. Duplicating preserves dates, VAT, cover, display settings and activity snapshots, while creating a new draft with guest sharing disabled.

Service quotations remain managed through their service request; the generic trip builder cannot bypass supply verification or agreed-price rules. Staff access uses the existing active SuperAdmin workspace middleware, session authentication and CSRF checks.

## Rendering and links

The staff JSON endpoints are under /workspace/quotations and /workspace/quotation-versions, accessed through the existing frontend /backend proxy. They do not render the legacy admin layout. The document-only print-preview is loaded in an iframe only after Load document preview is selected. PDF generation uses the existing PDF service and can take longer than opening details. Generating or downloading a newly generated PDF enables its guest link, following the existing workflow. Creating a link is also an explicit action; opening preview controls alone does not publish it. Message controls only copy drafts and never send email or WhatsApp messages.

During local development, old Laravel quotation details, builder, preview and create URLs redirect to these Vue screens. JSON requests, write actions and standalone PDF/document routes remain on Laravel. Production landing behavior is unchanged; configure the frontend host and reverse proxy for deployment separately.

## Verification

WorkspaceQuotationTest covers native read/write, adjusted prices and VAT, custom terms/activity preservation, version duplication, inquiry approval, service restrictions, staff authorization and local redirects. Browser verification uses isolated SQLite fixtures, including PDF generation/download and mobile layouts. No migration or customer-data conversion is required for this UI change.
