# Inquiry screens and website integration

## Backoffice navigation

Open the Vue workspace (normally http://127.0.0.1:5174), then Reservations → Inquiries.
View opens /inquiries/{uuid}/details in Vue. Service inquiries open /inquiries/{uuid}/services.
The shorter /inquiries/{uuid} path also redirects to details on the Vue origin.
Port 8001 hosts Laravel; in local development its /inquiries/{uuid} browser route redirects to the Vue detail page. JSON and write requests retain their backend behavior.

The Vue inquiry screen displays customer/contact data, travel requirements, approval/comments and linked quotations. Change approval uses the original validated action and refreshes the screen without leaving Vue. Create trip quotation now opens the native Vue creation form and builder. Quotation details, version history, preview, sharing and PDF controls stay within the Vue workspace. See QUOTATIONS.md for the workflow. Generic lists now stay in Vue. Additional tools links to the original layout have been removed. Supported specialist controls now open inside Vue; see WORKSPACE_REVIEW.md for coverage and existing unsupported actions.

## Lists

Column definitions live in src/components/workspace/listPresentation.ts. Inquiries show reference, customer/email/phone, service, requested trip, dates, guests, approval, assigned staff, quotation count and creation date. Other modules use explicit business fields instead of guessing five fields from the first record. Related values are projected to readable strings before search and sort. Null values show a dash; no raw relationship JSON or internal IDs are selected as columns. Wide tables scroll horizontally, with Actions pinned on the right. The original date filtering/pagination options still work.

## Customer website readiness

The backend API can supply published trips, destinations, rental offers, localized page content and supported currencies, and accept the documented service inquiries. Backend verification alone does not prove that the separate website uses those endpoints.

To connect the customer website:

1. Configure its API client to use the actual Laravel /api base URL. During local development this is normally http://127.0.0.1:8001/api. A deployed website needs a reachable HTTPS backend URL; 127.0.0.1 refers to each visitor's own computer.
2. Load content through the existing APIs plus /content-pages/{name}, /rental-offers, /rental-vehicles and /service-preferences. Publish reviewed pages/offers first; unpublished items are intentionally absent.
3. Submit the documented payloads to /save-service-inquiry for rentals/business/local/international/group/safari/flights. Display Laravel field errors and show the returned inquiry reference on success.
4. Use the existing Sanctum customer Bearer token for /get-service-inquiries and /get-service-inquiry/{uuid}. Guest submissions remain staff-managed; submitting an email does not establish ownership or unlock customer history.
5. Staff handle supply checks, quotations and agreed bookings in the backoffice. A saved inquiry is not a confirmed booking or issued ticket. Rental quotations require manual supply verification before booking; actual airline reference/issuance is recorded separately.
6. Test the real website's content load → form submission → staff processing → authorized history → cancellation-request flow. Also test expired quotes, invalid selections/dates and another customer's denied access.

See BACKEND/docs/DREAM_TOUR_API.md and the Postman collection for fields, currencies, auth and captured responses. The website source/URL must be supplied to verify or implement its integration. No website wiring or public deployment is implied by these backoffice changes.
