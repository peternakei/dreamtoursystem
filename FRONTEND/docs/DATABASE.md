# Local MySQL databases

The application uses `dreamtours_main` for business records, users, sessions, cache and queues. Spatie activity records use `dreamtours_logs.activity_log` through the Laravel connection named `logs`. Laravel error logs still use the file channel selected by `LOG_CHANNEL`; `LOGS_DB_*` does not redirect those files.

Configure `DB_*` and `LOGS_DB_*` in `BACKEND/.env`. Setting `LOGS_DB_DATABASE` enables the separate activity connection; `ACTIVITY_LOGGER_DB_CONNECTION` can explicitly override it. Both databases must exist before running `php artisan migrate`. Run migrations normally on the primary connection: the log module's migrations route their own schema operations to the logs connection, while migration history stays in the main database. Do not run all application migrations with `--database=logs`.

Tests force the activity connection to the same isolated in-memory SQLite connection as other test records. They do not use the configured MySQL databases.

This installation was transferred from SQLite with IDs, passwords and existing records preserved. Transient session and cache rows are not copied; they rebuild in MySQL. You may need to sign in again. The original SQLite file remains available, along with a pre-transfer snapshot under `BACKEND/storage/app/private/database-backups/`. A transfer report in that directory records row counts. Do not seed again or use `migrate:fresh` to switch databases.
