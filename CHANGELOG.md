# Gerobaks Backend Changelog

_All notable changes to the Gerobaks Laravel backend that power the mobile application are captured here._

## [2025-09-26] Observability & Mobile Integration Enhancements

### Added

-   **Sentry error & performance monitoring** with the official Laravel SDK (DSN pre-wired via `.env`). Exceptions and slow transactions now stream to our Sentry project so the mobile team can correlate backend issues with client-side crashes.
-   **Stacked logging pipeline** that forwards log events to both local files and Sentry, memudahkan tim mobile melacak event ID dari backend ketika terjadi crash di aplikasi.
-   **Backend-focused changelog** to keep product, mobile, and backend squads aligned on shipped API capabilities.

### Changed

-   **AES-256 encryption cast** wraps addresses, payment references, and chat payloads before they leave the database layer, hardening privacy for mobile users.
-   **Interactive API explorer** in `resources/views/docs/index.blade.php` now includes animated environment switching, health checks, and auth helpers tailored for the Flutter QA flows.

### Fixed

-   **Payment webhook routing** now resolves the correct controller, unblocking the mobile checkout confirmation path.

> For historical releases prior to this entry, consult the root `CHANGELOG_PROJECT.md` while we migrate notes into this file.
