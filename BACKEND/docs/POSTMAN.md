# Dream Tour Postman collection

Import these two files into Postman, then select **Dream Tour - Local** as the environment:

- [DreamTour.postman_collection.json](../DreamTour.postman_collection.json)
- [DreamTour.postman_environment.json](../DreamTour.postman_environment.json)

The collection contains 84 requests covering all 52 registered `/api` method/path combinations, plus Dream Tour staff workflows. Requests include sample bodies, authentication, response checks and ID capture scripts.

## Connection

Start the backend using the project setup instructions. Defaults connect directly to Laravel:

| Variable | Default |
| --- | --- |
| `backend_url` | `http://127.0.0.1:8001` |
| `base_url` | `http://127.0.0.1:8001/api` |

For another installation, update both URLs without trailing slashes. Through the supplied Vite proxy, use `http://127.0.0.1:5174/backend` and `http://127.0.0.1:5174/backend/api`, respectively.

## Website and customer requests

1. Run **Splash → Get Splash** to capture country, currency, gender and other available lookup IDs.
2. Set `user_email`, `user_password`, `user_phone` and contact details to your test customer's values. Use **Authentication → Register** for a new customer, or **Login** for an existing customer. Login saves `token` for protected requests. Use a Tourist customer account here; staff login is separate.
3. Run **List Trips**, **Get Trip Details**, **List Destinations** and **List Categories** as needed. Available IDs are captured. Trip details use `trip_uuid`, and capture an available trip group and trip type. Review IDs and booking dates against your chosen trip before submitting.
4. Under **Dream Tour services**, fetch service preferences, published rental offers, pages or localized trip/destination content. `locale` supports `en`, `fr` and `sw`; `page_name` defaults to `home`. Unpublished pages and offers return 404.
5. Choose a rental, business, international, local, group, safari or flight inquiry example. Signed-in examples use the customer token. **Guest rental inquiry** uses separate `guest_email`/`guest_phone` and saves `guest_service_inquiry_uuid`; guest submissions do not grant customer-history access.
6. Use owned history/detail with the same customer who submitted the inquiry. `service_inquiry_uuid` is captured from customer and staff inquiry responses. The selected rental offer must match the sample purpose, vehicle type, driver policy and extras; edit the example for your offer.

Passwords, tokens and record IDs start empty. Catalog and creation responses populate many IDs; supply any remaining IDs from your installation. Blank date variables receive future defaults at request time. Explicit dates override these defaults; rentals use the backend's configured timezone. Booking dates must also fit the selected trip.

API requests disable the cookie jar and use explicit Bearer or No Auth settings so a staff session does not change the customer identity. **Invalid rental quantity** expects HTTP 422 and **History without authentication** expects HTTP 401. Other requests check for a successful HTTP status.

## Public quotations

Set `public_quote_token` to the token from an enabled, unexpired quotation link. This is different from the customer login token. The folder provides detail, acceptance and change-request examples. Acceptance can optionally include a numeric `booking_id` from an existing booking.

## Staff workflows

Set `staff_username` and `staff_password`, then run **Get session and CSRF token** followed by **Existing workspace login**. These requests retain session cookies and capture the rotated CSRF token. Workspace login requires an active SuperAdmin staff account in the current implementation.

Staff folders include rental offers, vehicle specifications, service inquiries, quotations, agreed bookings and CMS content. Follow the [service workflow contract](DREAM_TOUR_API.md) for supply verification, customer agreement and flight issuance prerequisites. Quotation amounts and airline references in examples must be replaced with applicable values. Set `actual_issued_at` only when recording actual issuance.

For **Existing media upload**, select a local image in the `files[]` form-data field. Postman supplies the multipart boundary. `new_page_name` defaults to `postman-demo-page`; choose another name when creating another page. Update requests require the intended `page_uuid` and `page_name`.

Run the requests relevant to your workflow individually. The full collection is a reference catalog, not an ordered scenario: it includes password changes, deletion, cancellation, booking, publication and intentionally failing examples.

## Verification

The collection was checked against Laravel's registered routes. JSON bodies, variable references, URL query structures and JavaScript syntax were checked locally, with simulated responses for authentication and ID capture. These checks do not submit requests against the application or validate your credentials and data.
