# Dream Tour backend extension

Read DREAM_TOUR_GAP_MAP.md first. Existing SerenBlue routes, trip pricing, identifiers, customer authentication and original request payloads remain in place. These additions use the existing Vehicles, Inquiries, Tourist, Quotations, Bookings, Pages, Library and Currency modules.

## Implemented staff screens

- Safari planning → Car rental: quotation-only offers, specifications on existing vehicles, publication, featured selection, driver/fuel rules, optional request-only extras, locations, terms and existing library uploads/ordering.
- Reservations → Service requests: rental/business/international/local/group/safari/flight inquiries, assignment, existing approval/comments, private handling notes, customer-visible updates, manual supply verification, service quotations and agreed bookings. The same records also appear in the existing Inquiries list with a contextual service-details link.
- Website content → Pages: completed editing for the existing Page model, ordered sections, media, SEO, publication and French/Swahili content. Travel content links to descriptions/translations on existing trips and destinations.
- Existing Quotations, Bookings, logs, roles and media continue to be used. New module permissions are manage_rental_offers, manage_service_inquiries and manage_page_content. Active SystemUser + SuperAdmin or the respective permission is required for added staff endpoints. The existing Vue workspace login remains SuperAdmin-only.

## Database and installation

Three additive migrations extend existing vehicles, pages, trips, destinations and quotation_versions, and add only rental_offers and inquiry_service_details. The latter is a one-to-one child of an existing inquiry, not a second enquiry system. No customer/account, generic package, blog, fleet allocation, payment, revision or media tables are added. Existing data is retained; old fields are not renamed.

From BACKEND, after configuring the existing main/log database connections:

    php artisan migrate
    php artisan db:seed --class='App\Project\Modules\System\Inquiries\Seeders\DreamServicePermissionsSeeder'
    php artisan db:seed --class='App\Project\Modules\System\Pages\Seeders\DreamContentSeeder'

Only these two additive seeders are needed for an existing populated database. They reuse the current SuperAdmin. They never reset accounts or business records. Six starter page names (home, business-travel, tours, car-rental, air-ticketing, about) are added only when absent and stay unpublished. Existing authored pages are never overwritten. Choose real images, contact details and final copy in Pages before publishing. No invented testimonials, statistics, rates or flight inventory are seeded.

Build FRONTEND with npm run typecheck and npm run build. Development uses the existing same-origin /backend proxy. Public marketing clients should fetch the new content endpoints with their chosen locale; no new refresh queue or cache workflow is needed. This repository is the backoffice, not the separate marketing frontend: selectors/forms/history there must consume the endpoints below. No new public-site route or carousel is imposed.

## Routes and authentication

API paths below are relative to /api. All UUIDs are existing application UUIDs. The collection adds a “Dream Tour — additive services” folder without changing the original requests. Import DreamTour.postman_environment.json and set local IDs/credentials. No live secrets are included. Existing web login + cookie + CSRF is used for /workspace staff requests; existing Sanctum Bearer authentication is used for customer history. Obtain the staff CSRF token from GET /workspace/session, then POST /workspace/login; Postman retains cookies and captures the rotated token.

| Route | Method | Access / result |
| --- | --- | --- |
| rental-vehicles | GET | Public active, rental-published vehicles; optional vehicle_type, locale, limit, offset |
| rental-offers | GET | Public active/published offers; optional purpose, vehicle_type, featured, locale, limit, offset |
| rental-offers/{uuid} | GET | Published offer detail |
| content-pages/{name} | GET | Published existing page content; optional locale |
| travel-content/{trips or destinations}/{uuid} | GET | Localized supplement for existing published/approved trips or active destinations |
| service-preferences | GET | en/fr/sw, configured currencies, existing default currency ID, rental timezone and quotation/manual-availability mode |
| save-service-inquiry | POST | Guest or existing verified Tourist account; contact + service-specific details; 30 submissions/minute throttle |
| get-service-inquiries | GET | Sanctum owner history with limit/offset |
| get-service-inquiry/{uuid} | GET | Sanctum owner detail + sent/accepted quotes; no private operational notes |
| service-inquiries/{uuid}/request-cancellation | POST | Owner requests staff review; reason required; no automatic refund/status change |

The staff routes are relative to the application root (no /api):

| Route | Methods | Purpose |
| --- | --- | --- |
| workspace/rental-offers | GET, POST | Staff list/catalog choices; create offer |
| workspace/rental-offers/{uuid} | PUT | Update or unpublish an offer |
| workspace/vehicles/{uuid}/rental | PUT | Specifications/publication on an existing vehicle |
| workspace/service-inquiries | GET | Existing inquiry queue filtered by service_type; limit/offset |
| workspace/service-inquiries/{uuid} | GET, PUT | Details, assignment, approval, operational handling |
| workspace/service-inquiries/{uuid}/quotations | POST | New quotation using existing builder, currency, terms and price lines |
| workspace/service-inquiries/{uuid}/book | POST | Record an explicitly agreed, supply-verified service booking |
| workspace/pages-content | GET, POST | Existing CMS records; create page |
| workspace/pages-content/{uuid} | PUT | Edit existing page |
| workspace/travel-content | GET | Existing trip/destination choices |
| workspace/travel-content/{type}/{uuid} | PUT | Add service descriptions and translations |
| library/{rental-offers or pages}/{uuid}/media | POST | Existing multipart upload: role, title, files[] |

Existing library media edit/delete/reorder routes also work for these entity types with added permission checks. Image/video constraints continue to come from config/library.php; no new 5 MB rule is imposed. Ticket/customer documents are not supported by these public image uploads; do not upload them there.

## Service request payload

Common required fields: service_type, firstName, lastName, email, phone, country (existing country ID), message (existing 1,000-character inquiry limit), details. Supported service_type values: rental, business, international, local, group, safari, flight. locale is optional en/fr/sw (default en); currency_id is optional and independent of language. Optional budget, destinations (existing IDs), serviceClass and trip_uuid are reused for travel enquiries where appropriate. trip_uuid is prohibited for rentals/flights; no fake trip/group/season is required. Authenticated requests are linked to the verified user's Tourist profile, never assigned to an account found from submitted email/phone.

Guests may submit but cannot use contact details to unlock history. An existing guest Tourist is reused only when BOTH submitted email and phone match. A conflicting contact returns 422 and asks the sender to sign in/contact staff. Guest service inquiries have no verified customer_user_id and remain staff-managed; no automatic email-based claim/account creation is added. Their new records are excluded from the legacy unauthenticated inquiry lookup. Original legacy inquiries and their response shape remain unchanged. Customer clients should combine existing history with get-service-inquiries; agreed service bookings are also ordinary Booking records.

Rental details:

- Required: purpose (corporate/private/wedding/event), vehicle_type (sedan/suv/minibus/van/coaster/luxury/pickup/bus), start_at, end_at OR duration_days, quantity > 0, driver (with_driver/self_drive), pickup, dropoff.
- Optional: published offer_uuid, published vehicle_uuid, passengers > 0, extras array of selected offer extra codes, fuel choice where selectable, timezone matching the configured rental timezone.
- Dates use YYYY-MM-DDTHH:mm in APP_TIMEZONE (current deployment Africa/Dar_es_Salaam), with a future start and a later end. Duration is ceiling(elapsed seconds / 86,400), so any partial 24-hour day rounds up. Duration-only requests derive end_at on the server. Supplying both dates and days must agree. This is request duration information, not an approved fixed-rate commercial billing policy.
- Selected purpose/type/vehicle/driver/fuel/extras must match the offer. Extras mentioned only in notes are never silently included. Catalog and request snapshots clearly return quotation_required=true and estimated_total=null. No zero-price checkout is permitted; cart/likes/ratings keep their verified trip/destination types.
- Rental purpose and price_unit are separate. price_unit may describe day/hour/transfer/package for the quotation, but all offers remain price-on-request. Driver/fuel inclusion and mileage/extra-distance terms are explicit. Seasonal rates or fixed checkout are not enabled until commercial rate rules are supplied; staff can quote season-specific amounts through existing quotes.

Business/international/local/group/safari:

- Require startDate/endDate (YYYY-MM-DD; future start; end on/after start), guests > 0 and details.purpose.
- Business requires company and contact_person; accepts industry, independent geography (local/international), meeting_details, transport, accommodation, selected_services and other applicable fields.
- International requires country_city and accepts sector (agriculture/mining/investment/trade/tourism), places_of_interest, accommodation and purpose.
- Local requires destination_region and accepts transport/accommodation/purpose.
- Group/safari accept customized, route, accommodation and duration_days. Existing destination IDs/serviceClass represent parks and service class. Travel duration, if supplied, must match inclusive calendar dates; this does not change existing trip pricing.

Flights:

- Require trip_type (one_way/round_trip/multi_city), geography (domestic/international), adults >= 1, cabin (economy/premium_economy/business/first), ordered legs.
- Each leg has origin, destination, departure_date (future YYYY-MM-DD); origin/destination differ. Dates must be nondecreasing. One-way has one leg; round-trip has two reversing legs; multi-city has at least two.
- Optional children/infants >= 0, flexibility, airline_preference, group_travel. If common guests is supplied it must equal the passenger total. The inquiry dates and guest count derive from the legs and counts.
- This records assistance requests only. No live fares, reservations, airline/GDS connection, ticket issuance automation or new payments are implemented.

## Quotations, fulfilment and price history

Staff service quotation fields: title, currency_id, valid_until, status (draft/sent), vat_enabled, terms, and at least one price line (description, positive integer quantity, positive unit_price with at most 2 decimal places). Totals are computed by the existing QuotePricingService, not accepted from the browser. Flight quotes additionally require flight_offer.itinerary, airline, cabin, baggage and fare_conditions. No default safari day/meal inclusions are carried into a service quotation. The quote stores its service/request context and validity. Sent records the staff-selected state; no new email/WhatsApp integration is triggered.

Booking requires quotation_uuid and customer_agreed=true. The version must belong to this inquiry, be sent/unexpired and have a positive total. Rentals additionally require an actual staff supply verification. The transaction locks the inquiry detail and quote to avoid duplicate bookings. It copies the stored amount/VAT/currency/terms into agreed_price and the existing Booking, with null trip/group/type references where not applicable. It uses the existing Reserved status, never assumes Confirmed/Paid. Existing reference-number observers remain responsible for numbering. Repeated confirmation returns the same booking. Agreed service quotation content is protected from subsequent builder edits, and future rates do not recalculate the stored booking/agreed amount.

Flight issuance_state may be not_issued, confirmed, issued or cancelled. Confirmed/issued require a recorded booking and actual booking_reference; issued also requires the actual past/current issued_at. No reference/date can be fabricated for an unissued inquiry. Cancelling an already confirmed/issued flight retains its recorded reference and issuance time. Supply verification/agent notes stay private; explicit customer_notes and actual fulfilment state are visible to the owner. Use existing Bookings operations for final confirmation/payment/cancellation. A service customer's cancellation request records a review request against the original terms; even the existing customer cancel-booking action takes this review path for new service bookings only. Legacy trip cancellation is unchanged.

## Content, locale and currency

Page sections support hero, service, gallery, featured, process, values, statistics, consultation and contact entries, ordered by their array position. Fields include heading/text/value, CTA label/http(s) or relative URL, uploaded page-media UUID, and optional existing trip/destination/rental-offer references. Unpublished/inactive featured references are omitted publicly. Hero can remain a static photo; no carousel is required. Contact/social links can be authored as contact sections, while existing SystemConfigurations remain intact.

translations.en/fr/sw support base content fields with per-field English fallback; page sections also translate heading/text/CTA label. Staff own translations in the existing CMS. Blank translations do not overwrite English. Marketing UI dictionaries should use the same locale codes. Public pages/travel supplements/catalog accept locale without changing operational identifiers or old response envelopes. Price/currency selection is independent: return the existing configured currency records and the existing QuotePricingService default (USD when present). Existing exchange-rate semantics/endpoints remain unchanged. New rental prices are null until quotation; agreed quote totals retain their stored currency, never a symbol-only conversion.

## Responses and regression evidence

New successful endpoints use the existing style: status=true, code=200, message and data. List keys are vehicles, rental_offers or inquiries with total/limit/offset (default 20, max page size 100; this is pagination, not a business quantity cap). Validation uses Laravel's real 422 {message, errors}; missing authentication is 401; staff permission failures are 403; unowned/missing records return 404. Existing legacy endpoints retain their original status/envelope/limit/offset behavior.

DREAM_TOUR_RESPONSE_EXAMPLES.json contains real 200 rental list/submission, 422 validation and 401 history responses captured from the isolated preview using artificial .test contacts. UUIDs there are fixture values, not production IDs. The Postman folder includes rental, business, international, local, group, safari, multi-city flight, staff quotation/booking/issuance, CMS/localization and media examples.

Validation: 35 Laravel tests passed (258 assertions), Vue typecheck and production build passed; all 431 original route method/path/name/action definitions remain present among 455 total routes. The original 16 Postman groups are unchanged. An isolated browser workflow passed for rental offer creation, supply verification, service quotation, agreed booking, page editing/public retrieval and mobile layout. Legacy security findings are listed separately in the gap map; this extension does not replace legacy authentication.
