# Migration notes


## Backend mapping

`BACKEND/app/Project/_Src/class-map.json` records every old/new PHP class name.
`registry.json` records modules, their paths, models and ordered web/API route files.
`ApplicationServiceProvider` discovers module migrations.
`morph-map.json` preserves persisted polymorphic aliases.
The root database seeder retains the existing dependency order and points to the moved module seeders.

Existing controllers remain the entry points for business mutations. Action classes now live in their module's `Services` folder. API-specific actions/controllers live inside `Services/Api` and `ApiControllers` to avoid collisions with web actions.

The new `Workspace` endpoints are a compatibility layer for Vue. They read existing management view data and extract safe form descriptors, rather than exposing HTML or duplicating business validation. Review the UI boundary in the README before treating this as full feature parity in native Vue.

## Corrections made in the new copy

- Added explicit imports for model siblings that used to share a namespace.
- Created the user before its profile during initial seeding, removing the need to disable foreign-key enforcement. An existing admin is not overwritten.
- Preserved the prior fix removing output before PHP tags in route files.
- Scoped public API route names to avoid collisions with admin resource names.
- Removed unrelated scheduled tenant/property commands inherited from the source.
- Removed management-layout Vite directives: the compatibility pages use their existing public CSS/JS, while the new frontend has its own Vite build.
- Updated an inherited quotation URL test to assert the existing backend itinerary route. The same test failed in the source because it expected the marketing domain even though the implementation deliberately serves itineraries from the backend.
- Resolved TypeScript issues in copied reusable components without modifying the reference project.

## Verification

- 410 moved classes/traits resolve through Composer.
- 103 module migrations applied successfully to an isolated SQLite database.
- Full seeding completed with converted sample assets; optional `sharp` reconversion emitted warnings.
- 34 workspace list endpoints and 21 populated detail endpoints returned HTTP 200 using seeded data.
- All original HTTP method/path pairs are retained; Laravel route caching succeeds.
- PHPUnit: 15 tests, 78 assertions passed, including session login, rejected credentials, role protection, vehicle validation/create/update/delete, route names and morph aliases.
- TypeScript check and production frontend build passed.
- Browser: login, dashboard, vehicle creation/list, and a 390-pixel mobile viewport passed without JavaScript errors.

The database checks used SQLite. A new MySQL deployment has not been exercised. No payment transaction, external email delivery or production deployment was performed.
