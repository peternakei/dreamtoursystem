# Workspace navigation review — 20 September 2026

## Consistent Vue navigation

Generic list and detail pages no longer contain Additional tools, Original record tools, or Original list management links. Management controls submit to Laravel from Vue and refresh the current record. The Dream Travel and Tours sidebar stays in place.

For local development, old registered module list/detail/edit URLs redirect to the corresponding Vue page. This includes UUID records, legacy 13-character role/permission identifiers, and trip planner bookmarks. Short Vue list/detail URLs also resolve internally. JSON requests, write actions, and production routing retain their existing behavior. Password recovery and required password changes still use the existing authentication screens.

## Native tools

- Accommodations and vehicles: details, status, deletion, formatted descriptions and media management. Accommodation destination selections remain populated when editing and can be cleared.
- Destinations: details and coordinates, facts and fact editing, repeatable activity/category assignments, image uploads and media management. Activity assignments tolerate omitted images and preserve each uploaded image's row index.
- Library records: gallery/cover/video uploads, preview, title changes, ordering and removal through the existing library endpoints. This shared component also serves website content and rental offers.
- Trips: itinerary days and activities, repeatable point/destination/category/addon assignments, point editing/removal, addon removal, groups/camps, prices, labelled season/class budget inputs, publishing and banner uploads. Group choices are scoped to the current trip. Itinerary changes prompt before navigation or refresh discards them. Applying a published trip to an inquiry opens the new quotation in the Vue builder.
- Configuration and administration: existing supported edit/delete actions now use populated native forms. Role and permission assignments show existing selections; the source application's additive assignment behavior is retained and labelled.
- Bookings, tourists, invoices and receipts retain the native record/history screens introduced in the earlier review. Inquiry and quotation workflows stay in Vue.

The shared form renderer supports multiple selections, repeatable rows (including indexed file uploads), rich text, numeric steps, nested budget names and Laravel field errors. Existing Laravel validation and persistence endpoints remain responsible for writes. Vehicle update validation now excludes the current vehicle correctly when checking unique names.

## Implementation

WorkspaceToolsService enriches the existing PageService descriptors with resource values, related-record actions, media and itinerary data. No old Blade HTML or scripts are inserted into Vue. Some descriptors are still extracted from server-rendered source forms; these are not a complete replacement of the original backend controllers.

EntityPage, EntityForm, WorkspaceField, RichTextField, TripPlanner and LibraryMedia render these controls within MainLayout. WorkspaceResponse returns a safe workspace path for trip-to-quotation redirects. No database migrations are needed.

## Existing limitations

Controllers with unimplemented actions remain read-only for those actions. In particular, no working staff trip-booking cancellation action or manual invoice/receipt create/update/delete implementation was found in the source application. The workspace does not advertise those unsupported actions. Replacing navigation does not create new financial or cancellation rules.

## Verification

WorkspaceNativeToolsTest covers native library forms; media upload/title/order/removal and invalid uploads; vehicle uniqueness; destination facts and repeatable assignments; populated country/role forms; itinerary persistence and selected inactive destinations; trip points, group camps and budget matrix writes; form parsing; authentication; and local redirects that preserve write validation.

Browser verification uses isolated SQLite fixtures for accommodation and vehicle edits, formatted text, destination facts and repeated activities, media upload/preview/removal, itinerary and repeated point saves, the registered module lists and available details, and mobile layouts. The real local database is inspected through read-only list/detail requests. Customer and financial records are not changed by these checks.

## Rental offer modal and photo previews

Car rental → New rental offer and Edit now open a scrollable dialog, consistent with accommodation creation. Save a new offer once to enable its media section; the dialog remains open for adding photos. Validation errors remain inside the dialog, and uploading media preserves unsaved offer edits.

The shared FilePicker follows the restaurant system’s selected-file preview pattern: drag/drop or choose files, view thumbnails before upload, remove a selection, and open a full-size preview. Object URLs are released when selections change or the component closes. Workspace file fields and the shared media library use this picker. Saved photos use a responsive gallery with uncropped image proportions, role labels, title editing, ordering, removal and a larger preview. Rental lists show the cover image, or the first available photo. Media uploads use a separate button rather than a nested form, so uploading does not submit the enclosing content editor.

Verified with isolated rental create/edit and photo upload records, persisted gallery reloads, nested preview dialogs, generic destination upload forms, mobile viewport bounds, Vue type checking and a production build.

## Rental offer details and service module status

Car rental lists now include View beside Edit. `/rental_offers/{uuid}/details` shows the offer, gallery, driver/fuel policies, quotation unit, duration, publication flags, inclusions/exclusions, mileage, terms, service locations, optional extras, translations and linked eligible vehicles. Edit rental offer opens the existing modal on this page. Direct links and the short `/rental_offers/{uuid}` alias stay in Vue. Missing records show a not-found message. This is a staff view; public rental publication rules remain unchanged.

Business Travel and Air Ticketing currently use Reservations → Service requests, filtered by Business or Flight. They share the service-inquiry API and staff review, assignment, notes, quotation and agreed-booking workflow. Business requests retain company/contact, dates, traveller count, meeting, transport and accommodation requirements. Flight requests retain one-way/return/multi-city legs, dates, passenger counts, cabin and preferences; staff quotes contain itinerary, airline, baggage and fare conditions. Actual airline references and confirmation/issuance states are recorded manually after a booking exists. There is no live airline inventory, GDS connection or automatic ticket issuance. Staff can create these requests in Reservations → Service requests → New service request. Website requests continue to use the service inquiry API.

Website content uses Pages (`business-travel` and `air-ticketing`); the separate website’s API wiring still needs its own end-to-end verification. Service bookings are reserved records based on agreed quotations, not automatic payment confirmation.

Verification: rental list-to-view navigation, full-size images, modal edits updating the view, vehicle links, reload/direct/short URLs, missing records and mobile layout; Business/Flight filters; existing DreamTourServicesTest (13 tests, 120 assertions).


## Staff requests and Pages management

Service requests has a native creation dialog for business, flight, rental, international, local, group and safari requests. Staff capture customer contacts, service-specific requirements, request channel, assignment and private notes. Creation uses the same domain validation as website submissions. It records the staff creator without claiming customer account ownership. Existing customers are matched by both email and phone; conflicting contact records are rejected. Requests stay pending, with quotations, supply checks and customer agreement handled by the existing workflow. No booking, payment or ticket issuance occurs on creation.

New staff endpoints are GET /workspace/service-inquiries/options and POST /workspace/service-inquiries, protected by the existing manage_service_inquiries access check. Existing database columns store channel, creator and assignment; no migration is needed.

Pages → New page and Edit page now open modals. Saving keeps the editor open for photo uploads. Each list row has View, linking to /pages/{uuid}/details. The read-only view displays publication state, description, ordered sections, section media, featured-content links, SEO details, translations and a media gallery with full-size previews. Editing from View updates the displayed content after saving. Missing records have a not-found state.
