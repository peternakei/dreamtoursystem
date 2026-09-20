# Dream Tour additive gap map

Discovery: 20 September 2026. Target: dreamtoursystem, the modular Laravel/Vue copy of SerenBlue. Original repository and tzrestaurantsystem are references only.

| Requirement | Existing source / capability | Gap and smallest addition |
| --- | --- | --- |
| Tours, safaris, destinations, groups | Trips, Categories, Destinations, TripGroup; /api/trips and search_trip, published + Approved gating | Keep pricing/routes/statuses. Reuse categories for geography/purpose; add optional structured offer descriptions and translations around existing records. |
| Business / local / international / custom groups | Inquiry, Tourist, trip types, dates, guests, destinations, service class, budget, notes | Add a one-to-one service detail record linked to existing inquiries; validate service-specific data without fake trip IDs. No separate enquiry/customer engine. |
| Rental catalog | Vehicle already has name, capacity, description, active flag, UUID, soft delete, HasLibraryMedia | Add rental specifications and a separate rental-publication flag; preserve safari vehicle behavior. |
| Rental offers | No rental offer model; trip prices depend on unrelated trip/group/class/season structures | Add rental_offers under Vehicles, linked to catalog vehicles/type; explicit purpose, billing unit, driver/fuel rules, extras, terms, featured flag and media. Quotation-only/manual availability confirmed by owner. No checkout or automatic fleet allocation. |
| Requests/history | save-inquiry + get-inquiries + get-inquiry; existing staff list/detail/approval and assignment columns | New service-specific handler writes Inquiry plus details. Verified Sanctum customer ownership for new personal-data responses. Existing inquiry screen gains service details and operations fields. Legacy requests require no discriminator. |
| Quotes/bookings | QuoteBuilderService::createFromInquiry, quotation versions/price lines/currency/terms/public links; trip_id already nullable on bookings | Reuse quotation builder and stored price context. Require staff supply verification for new rental confirmation. Preserve old booking/cart paths; quotation-only offers never become zero-priced checkout entries. |
| Flights | No flight request/issuance data | Store validated ordered flight legs and passenger/cabin details on inquiry service detail; add private staff handling and explicit issuance state/reference. Existing quotations supply itinerary and price; no airline integrations. |
| CMS | Pages table + Page model and /pages routes exist, but PageController methods are empty; Vue currently shows fallback read-only records | Complete the existing Pages module with structured sections, SEO, publication/order, media and localized content; no new pages/settings/blog/media tables or revision system. |
| Other content | Faqs, Testimonials, Subscriptions, Library, SystemConfigurations already exist | Preserve workflows. CMS blocks reference existing destinations/trips/rental offers and library media. No new mandatory marketing pages or carousel. |
| Language | No backend translation mechanism found in reviewed modules | Optional en/fr/sw localized fields around existing content; English fallback. Do not translate operational identifiers or overwrite source copy. |
| Currency | Currency records TZS/USD/EUR/GBP; exchange-rate endpoint already exists; quotation default selected by QuotePricingService | Expose configured currencies; keep default and conversion behavior. Store preferred currency independently of language. Quote amounts stay in stored currency; no symbol-only conversion or historical repricing. |
| Cart/likes/ratings | Explicit supported types trip/destination, not arbitrary morph types | Leave unchanged for quotation-only new services. Add no vehicle/flight types to validators unless corresponding transaction support is implemented. |
| Auth/access/uploads | Sanctum customer auth, web sessions, SuperAdmin workspace, Spatie permissions, library upload/ordering rules | Reuse mechanisms; protect added staff endpoints and keep private operations out of public resources. Add module permissions through existing permission records. |

## Baseline and verification plan

Run existing Laravel tests, Vue typecheck/build, then add integration coverage for old inquiry payloads and response shapes, legacy trip/cart validation, rental selection/date/quantity/driver rules, guest vs customer ownership, staff access, localization fallback, publication filtering and service quote/issuance rules. Compare existing route definitions before/after. All migrations additive; never fresh-migrate or seed the user's populated database.

## Separate legacy findings (not silently redesigned)

- Inquiries/Routes/api_inquiry.php exposes get-inquiries without authentication. GetAllInquiryFormAction resolves a tourist using supplied email OR phone and returns inquiry metadata. New service records must be excluded from that unverified legacy lookup and retrieved through verified ownership.
- GetInquiryDetailsFormAction treats the quotations collection as a single model when nonempty; its legacy behavior needs a separate fix, not an undisclosed response-envelope change.
- QuoteBookingService creates credentials from an email for a newly created tourist and uses email OR phone matching. New service booking must not invoke that legacy identity-creation path.
- Library routes use auth:web without module permissions. Newly introduced entity types must receive an explicit additional access check without changing existing upload rules.

## Commercial decision

Owner confirmed quotation-only rental offers with manual supply verification. No fixed-rate checkout, automatic availability promise or live ticket issuance. Requested days are derived as ceiling(elapsed hours / 24), including partial days, in Africa/Dar_es_Salaam; this is duration information, not an agreed charge. Any agreed amount comes from staff quotation records.
