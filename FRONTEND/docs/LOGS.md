# Administration logs

Open **Administration → Logs** in the Vue sidebar. Only active SystemUser accounts with the SuperAdmin role can access the list and detail APIs. Activity, Request and Error tabs support server-side search, inclusive start/end dates, user ID filtering, pagination (25/50/100), details and related entries by request ID. Dates use APP_TIMEZONE, displayed above the table.

## Storage and setup

This follows the three audit tables in tzrestaurantsystem, adapted to Laravel's existing module registry and Vue workspace. In BACKEND/.env, configure LOGS_DB_* and set ACTIVITY_LOGGER_DB_CONNECTION=logs. With the current local configuration the destination is dreamtours_logs. Then run from BACKEND:

```bash
php artisan config:clear
php artisan migrate
```

Normal migrate discovers the new migration inside Core/Logs/Migrations. Migration bookkeeping stays in the main database; the migration explicitly creates the tables on the configured activity-log connection. Do not run migrate:fresh or seed again when preserving transferred data. Both databases must exist and the configured user must be able to create tables in the log database. The application falls back to the main connection when no separate activity connection is configured.

| Table | Recorded information |
| --- | --- |
| activity_logs | Eloquent creates, updates, deletes and restores for application module models and Spatie permission models; changed values before/after; authentication events; mutation request outcomes |
| request_logs | HTTP method, route template, actor, IP, status, duration and input field names |
| error_logs | Laravel-reported exception class, source location, code and stack frames without arguments |

All three share request_id, user_id, timestamps and a UUID. Main database users are resolved separately. Existing activity_log (singular) remains available for legacy Spatie calls; it is not renamed or erased. The new UI displays the three new tables, not historical entries from that legacy table.

## Coverage and behavior

- Model changes are written after the owning database transaction commits; rolled-back changes are discarded. Console/job model writes are also recorded, with a null request ID when no HTTP request exists.
- Authentication records login, failed authentication and logout. Requests link changes/errors through a server-generated request ID, also returned as X-Request-ID.
- Direct SQL/query-builder bulk writes and pivot synchronizations do not emit Eloquent events. Their HTTP mutation outcomes are visible, but field-by-field changes are not available. Console raw SQL requires explicit instrumentation if audit history is needed.
- Request outcomes describe HTTP status, not a guarantee that a redirect represents a successful business operation. GET requests are recorded in Request logs, including log-page access. OPTIONS and /up are excluded.
- Passwords, tokens, credentials, hidden model attributes, configuration values and content/body fields are redacted from model snapshots. Request body values, headers, cookies, query strings and route parameter values are not stored. Raw exception messages and argument values are omitted because they may include SQL bindings or secrets; the UI shows the exception class and source frames instead. Normal Laravel file logging remains available for development diagnosis.
- Writes are synchronous; no queue worker is required. A log database outage does not fail a user's business action. A minimal “Audit storage unavailable” notice goes to PHP's error log; missed entries are not replayed automatically. Main/log writes are not a distributed transaction.
- Logging starts when this feature is installed. Past user actions cannot be reconstructed from the transferred business records. No automatic history purge is configured.

## Verification

WorkspaceLogsTest uses isolated in-memory SQLite databases for main and logs. It checks committed vs rolled-back changes, redaction, login/logout and request correlation, separate storage, exception capture, administrator authorization, full-history pagination/date filters, and graceful log-storage failure. Existing application tests, Vue typechecking and the production build should also pass.
