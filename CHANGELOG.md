# Gerobaks Backend Changelog

_All notable changes to the Gerobaks Laravel backend that power the mobile application are captured here._

## [2025-10-31] 📚 Complete OpenAPI Documentation & Swagger UI

### Added

-   **Complete OpenAPI 3.0.3 Specification** with 60+ endpoints fully documented in `public/openapi.yaml`
-   **Interactive Swagger UI** - Professional API testing interface accessible at `/`, `/docs`, `/api-docs`
-   **Mobile Format Schedule Endpoint** - Indonesian field names support via `POST /api/schedules/mobile`
    -   Fields: `alamat`, `tanggal`, `waktu`, `koordinat`, `jenis_layanan`, `metode_pembayaran`
    -   Role: `end_user` only
    -   Validation: All required fields enforced
-   **Multi-Environment Support** in Swagger UI
    -   Local: http://127.0.0.1:8000
    -   Staging: https://staging-gerobaks.dumeg.com
    -   Production: https://gerobaks.dumeg.com
-   **Comprehensive Documentation Files**:
    -   `SWAGGER_DOCUMENTATION.md` (9KB) - Complete guide with features, setup, troubleshooting
    -   `SWAGGER_UI_TUTORIAL.md` (13KB) - Step-by-step tutorial for testing APIs
    -   `API_QUICK_REFERENCE.md` (8KB) - Quick reference card with all endpoints
    -   `SWAGGER_IMPLEMENTATION_SUMMARY.md` - Implementation summary

### Improved

-   **DocsController** - Updated `openapi()` method to serve from `public_path('openapi.yaml')`
-   **API Documentation Coverage** - All 17 endpoint categories now documented:
    -   Health (2), Authentication (4), User Management (3)
    -   Schedules (7), Tracking (3), Services (3)
    -   Orders (6), Payments (4), Balance (4)
    -   Ratings (2), Notifications (3), Chat (2)
    -   Feedback (2), Subscriptions (7), Dashboard (2)
    -   Reports (4), Settings (3), Admin (9)
-   **Request/Response Examples** - Real-world examples with test credentials
-   **Error Documentation** - Complete error responses (401, 403, 422, 500)
-   **Authentication Flow** - Bearer token examples with role-based access
-   **Dark Mode Support** - Swagger UI with light/dark theme toggle

### Fixed

-   **Schedule Creation 422 Errors** - Mobile endpoint now validates all required Indonesian fields
-   **Schedule Creation 403 Errors** - Clear role-based endpoint documentation (`end_user` vs `mitra`)
-   **OpenAPI Spec Route** - Properly serves YAML from `/openapi.yaml`
-   **Missing Endpoints** - All backend routes now included in documentation

### Technical Details

-   **OpenAPI Version**: 3.0.3
-   **Swagger UI Version**: 5.17.14
-   **Total Endpoints**: 60+ across 17 categories
-   **Documentation Format**: YAML (1500+ lines)
-   **UI Framework**: Tailwind CSS + Flowbite + AOS Animation
-   **Authentication**: Laravel Sanctum Bearer Token
-   **File Size**: openapi.yaml (~45KB), Docs (~30KB combined)

### Mobile App Integration

-   **Endpoint Format Validation**: Mobile app schedule creation now matches backend expectations
-   **Field Name Mapping**: Indonesian field names properly mapped to database columns
-   **Service Types Documented**: All 5 waste types enumerated
    -   `pickup_sampah_organik`, `pickup_sampah_anorganik`
    -   `pickup_sampah_daur_ulang`, `pickup_sampah_b3`, `pickup_sampah_campuran`
-   **Payment Methods**: `cash`, `transfer`, `wallet`
-   **Date/Time Format**: `tanggal` (YYYY-MM-DD), `waktu` (HH:mm)
-   **Coordinate Format**: `koordinat.lat`, `koordinat.lng` (float)

### Access URLs

-   **Swagger UI**: http://127.0.0.1:8000 (local) | https://gerobaks.dumeg.com (production)
-   **OpenAPI Spec**: http://127.0.0.1:8000/openapi.yaml
-   **GitHub Repo**: https://github.com/fk0u/gerobackend

### Benefits

-   ✅ **No Postman Needed** - Test all APIs directly from browser
-   ✅ **Interactive Testing** - Try It Out feature for all endpoints
-   ✅ **Auto Token Management** - Bearer token stored in localStorage
-   ✅ **Multi-Environment** - Switch between Local/Staging/Production
-   ✅ **Always Up-to-Date** - Single source of truth for API contract
-   ✅ **Team Collaboration** - QA, Frontend, Mobile teams can test independently
-   ✅ **Professional Presentation** - Modern UI with dark mode
-   ✅ **Complete Examples** - Copy-paste ready cURL commands

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
