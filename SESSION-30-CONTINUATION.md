# Session 30 Continuation Guide - Backend-Frontend Integration (Prompts 302-307)

## Overview
This session completes the Backend-Frontend Integration phase (Phase 7), implementing notification endpoints, dashboard metrics, report exports, locale switching, CSRF support, and real-time events.

---

## IMPORTANT: Where to Find Prompt Specifications

### Primary Reference Files (in order of priority):

| File | Prompt Range | Description |
|------|--------------|-------------|
| `smart-school/codex/PROMPT-CONTINUATION.md` | **302-307** | **USE THIS FOR SESSION 30** - Contains detailed specs for all Backend-Frontend Integration prompts |
| `smart-school/codex/DEVIN-AI-CODEX-GUIDE.md` | N/A | Master guide explaining execution order of all codex files |
| `smart-school/DEVIN-AI-COMPLETE-PROMPTS.md` | 1-291 | Original prompts (Sessions 1-28) - NOT used for Session 30 |

### Additional Reference Files:
| File | Purpose |
|------|---------|
| `smart-school/codex/DEVIN-AI-API-ENDPOINTS-DOCS.md` | API endpoint specifications and response formats |
| `smart-school/codex/DEVIN-AI-EXPORTS.md` | Export functionality (PDF, Excel, CSV) specs |
| `smart-school/codex/DEVIN-AI-REALTIME-NOTIFICATIONS.md` | Real-time notification and broadcasting specs |
| `smart-school/GUIDE_FOR_DEVIN.md` | Project-specific guidance and conventions |
| `PROGRESS.md` | Track what has been completed |

### What to Expect from Each Prompt in PROMPT-CONTINUATION.md:
Each prompt includes these sections:
- **Purpose**: Why this feature is needed
- **Functionality**: What it does
- **How it Works**: Technical implementation details with code examples
- **Integration**: How it connects to frontend views and other components
- **Execute**: Specific action to take

---

## End-of-Session Process (MUST FOLLOW)

After completing all prompts in a session, follow these steps IN ORDER:

### 1. Verify Routes Work
```bash
php artisan route:list | wc -l  # Check total route count
php artisan route:list 2>&1 | head -5  # Check for errors
```

### 2. Test Key Endpoints
```bash
# Start the server
php artisan serve --host=0.0.0.0 --port=8080

# Test public endpoints (no auth required)
curl -s -H "Accept: application/json" http://localhost:8080/api/v1/public/school-info

# Test authenticated endpoints (requires login or token)
# Use browser testing for authenticated routes
```

### 3. Update PROGRESS.md
Add a new section for the completed session with files created/modified and features added.

### 4. Commit Changes
```bash
git add -A
git status  # Review what will be committed
git commit -m "Session X: [Description] (Prompts XXX-XXX)"
```

### 5. Push and Create/Update PR
```bash
git push origin [branch-name]
```
Then use `git_create_pr` or `git_update_pr_description` tool.

### 6. Wait for CI Checks
Use `git_pr_checks` tool with `wait_until_complete="True"` to wait for CI.

### 7. Create Next Session Continuation File
Create `SESSION-{N+1}-CONTINUATION.md` with summary, tasks, reference files, dependencies, and continuation prompt.

### 8. Notify User
Send message with PR link, summary, any issues found, next session prompt, and recording (if applicable).

---

## Previous Session Summary (Session 29 - COMPLETED)

### What Was Done
Session 29 implemented Prompts 292-301, establishing the foundational routing structure and API endpoints:

**Routes & Controllers Created:**
- **1007 total routes** (944 web + 63 API) organized into role-based groups
- Role-based route groups: `admin`, `teacher`, `student`, `parent`, `accountant`, `librarian`
- Route naming convention: `{role}.{module}.{action}` (e.g., `admin.students.index`)
- Versioned API routes at `/api/v1` for AJAX endpoints

**Key Files Created/Modified:**
- `routes/web.php` - Complete web routes with role-based groups
- `routes/api.php` - Versioned API routes for dropdowns, students, attendance, fees, exams, etc.
- `bootstrap/app.php` - Added API route loading
- `app/Providers/AppServiceProvider.php` - Added API rate limiter configuration
- `app/Providers/ViewServiceProvider.php` - View composers for global layout data
- `app/Http/Controllers/Controller.php` - Base response helpers (successResponse, errorResponse, etc.)

**API Resources Created (16 total):**
- StudentResource, TeacherResource, ClassResource, SectionResource
- SubjectResource, AttendanceResource, FeesTransactionResource, ExamResource
- BookResource, NotificationResource, and more

**Form Requests Created (6 total):**
- BaseFormRequest, StoreStudentRequest, UpdateStudentRequest
- StoreAttendanceRequest, StoreFeesTransactionRequest, StoreExamMarksRequest

**Traits Created:**
- `HasPagination` - Server-side pagination support
- `HasDataTables` - DataTables AJAX processing

**Controllers Created:**
- `UploadController` - File uploads with image optimization
- `DownloadController` - Secure file downloads with authorization
- `Api\DropdownController` - Cascading dropdown endpoints
- `Api\StudentController`, `Api\AttendanceController`, `Api\FeesController`
- ~91 stub controllers across all namespaces (placeholders for future implementation)

**Issues Fixed During Testing:**
1. API routes were not loading (added `api:` to bootstrap/app.php)
2. Rate limiter "api" was not defined (added to AppServiceProvider)
3. Missing API controllers (created 8 additional stub controllers)

### PR Status
- **PR #30**: https://github.com/01fe23bcs183/sms_open/pull/30
- **Status**: Ready for review/merge
- **Branch**: `devin/1767893278-session-29-backend-integration`

## Session 30 Scope
**Phase 7: Backend-Frontend Integration - Part 2 (Prompts 302-307)**

This session implements the final 6 prompts of the Backend-Frontend Integration phase.

## Tasks for Session 30

### Prompt 302: Add Notification Fetch and Mark-Read Endpoints
**File**: `app/Http/Controllers/Api/NotificationController.php`

**Purpose**: Connect notification UI to backend data.

**Endpoints to implement:**
- `GET /api/v1/notifications?unread=1&per_page=10` - List notifications with filters
- `PATCH /api/v1/notifications/{id}/read` - Mark single notification as read
- `POST /api/v1/notifications/read-all` - Mark all notifications as read

**Response shape:**
```json
{
  "status": "success",
  "data": [...notifications],
  "meta": {
    "unread_count": 5,
    "current_page": 1,
    "per_page": 10,
    "total": 25
  }
}
```

**Implementation notes:**
- Use `auth()->user()->notifications()` and `unreadNotifications()`
- Header bell icon and dropdowns depend on this
- Works with real-time updates when broadcasting is enabled

### Prompt 303: Provide Dashboard Metrics and Chart Data Endpoints
**File**: `app/Http/Controllers/Api/DashboardMetricsController.php`

**Purpose**: Feed dashboard cards and Chart.js graphs.

**Endpoints to implement:**
- `GET /api/v1/dashboard/metrics` - Aggregated metrics (total students, teachers, fees collected, etc.)
- `GET /api/v1/dashboard/charts/fees` - Fee collection time-series data
- `GET /api/v1/dashboard/charts/attendance` - Attendance trends data

**Response shape for charts:**
```json
{
  "status": "success",
  "data": {
    "labels": ["Jan", "Feb", "Mar", ...],
    "datasets": [
      {
        "label": "Fees Collected",
        "data": [10000, 15000, 12000, ...],
        "backgroundColor": "#4f46e5"
      }
    ]
  }
}
```

**Implementation notes:**
- Use `DashboardService` for aggregations (create if needed)
- Cache metrics with `Cache::remember('dashboard_metrics', 300, ...)` (5-minute TTL)
- Return datasets formatted for Chart.js

### Prompt 304: Add Report Export Endpoints with Filters
**File**: `app/Http/Controllers/ReportExportController.php`

**Purpose**: Let frontend export filtered data to PDF/Excel.

**Endpoints to implement:**
- `POST /api/v1/reports/export` - Generate report with filters
- Accept parameters: `module`, `format` (pdf, xlsx, csv), `filters[]`

**Implementation notes:**
- Validate module and format
- Use `ReportService` and `ExportService` to build data (create if needed)
- For large exports, dispatch a queue job and return a download URL
- Set response headers for file downloads
- Supported modules: students, attendance, fees, exams

### Prompt 305: Implement Locale Switcher and JS Translations
**Files**: 
- `app/Http/Controllers/LocaleController.php`
- `app/Http/Middleware/SetLocale.php`

**Purpose**: Synchronize backend language settings with the UI.

**Endpoints to implement:**
- `POST /locale` - Store selected locale in session and user profile
- `GET /api/v1/translations?group=nav` - Return JSON translation keys

**Implementation notes:**
- Cache translation payloads per locale
- Ensure LocaleMiddleware sets `app()->setLocale()` per request
- Frontend language switcher uses these endpoints
- Alpine.js and JS components can access translations

### Prompt 306: Wire CSRF and Session Support for AJAX
**Files**:
- `resources/views/layouts/app.blade.php` (add meta tag)
- `resources/js/bootstrap.js` or equivalent

**Purpose**: Ensure frontend requests are authenticated and secure.

**Implementation:**
- Add CSRF token meta tag in base Blade layout: `<meta name="csrf-token" content="{{ csrf_token() }}">`
- Configure Axios or fetch to send `X-CSRF-TOKEN` header
- Use `credentials: 'same-origin'` for session cookies
- For form deletes, use method spoofing with `_method=DELETE`
- Confirm API routes using Sanctum receive `X-XSRF-TOKEN`

### Prompt 307: Enable Real-Time Events for UI Updates
**Files**:
- `config/broadcasting.php`
- `routes/channels.php`
- Event classes in `app/Events/`

**Purpose**: Push notifications and live updates to the frontend.

**Implementation:**
- Configure broadcasting in `.env` (Pusher or Redis)
- Create events implementing `ShouldBroadcast`:
  - `NewNotificationEvent`
  - `AttendanceMarkedEvent`
  - `FeesPaidEvent`
- Define private channels in `routes/channels.php` with auth callbacks
- Use Laravel Echo on the frontend to listen for events
- Queue broadcasts for scalability

## Dependencies & Prerequisites

### From Session 29 (must be merged first):
- Base Controller with response helpers
- API route structure at `/api/v1`
- Rate limiter configuration
- View composers for global data
- API resources for JSON formatting

### Database Tables Used:
- `notifications` (Laravel's built-in notification system)
- `users` (for notification relationships)
- `students`, `teachers`, `classes`, `sections` (for metrics)
- `fees_transactions` (for fee charts)
- `attendances` (for attendance charts)
- `settings` (for locale preferences)

### Packages That May Be Needed:
- `maatwebsite/excel` - Already installed for exports
- `barryvdh/laravel-dompdf` - Already installed for PDF exports
- `pusher/pusher-php-server` - For real-time broadcasting (optional)

## Reference Documents
- `smart-school/codex/PROMPT-CONTINUATION.md` - Full prompt specifications (Prompts 292-307)
- `smart-school/codex/DEVIN-AI-API-ENDPOINTS-DOCS.md` - API endpoint documentation
- `smart-school/codex/DEVIN-AI-EXPORTS.md` - Export specifications
- `smart-school/codex/DEVIN-AI-REALTIME-NOTIFICATIONS.md` - Real-time notification specs

## Verification Steps
1. Run `php artisan route:list | grep -E "notification|dashboard|report|locale|translation"` to verify new routes
2. Test notification endpoints with authenticated user
3. Test dashboard metrics return valid Chart.js format
4. Test locale switching persists across requests
5. Verify CSRF token is included in AJAX requests
6. Test broadcasting configuration (if Pusher/Redis configured)

## Session 30 Continuation Prompt
```
Continue with Session 30 (Backend-Frontend Integration Prompts 302-307) for the Smart School Management System

Repository: 01fe23bcs183/sms_open

Reference the SESSION-30-CONTINUATION.md file for context and the smart-school/codex/PROMPT-CONTINUATION.md file for detailed prompt specifications.

Tasks for this session:
- Add Notification Fetch and Mark-Read Endpoints (Prompt 302)
- Provide Dashboard Metrics and Chart Data Endpoints (Prompt 303)
- Add Report Export Endpoints with Filters (Prompt 304)
- Implement Locale Switcher and JS Translations (Prompt 305)
- Wire CSRF and Session Support for AJAX (Prompt 306)
- Enable Real-Time Events for UI Updates (Prompt 307)

Prerequisites:
1. Merge PR #30 (Session 29) first, or fetch the branch to get the base controller helpers and API route structure
2. Run `git fetch origin` and check for the latest changes

After completing tasks:
1. Verify all new routes work correctly with `php artisan route:list`
2. Test API endpoints with curl or browser
3. Update PROGRESS.md with session completion
4. Create a PR with all changes
5. Wait for CI checks to pass
```

## Project Status After Session 30
- **Completed Prompts**: 307/497 (61.8%)
- **Phase 7 (Backend-Frontend Integration)**: COMPLETE (16 prompts)
- **Next Phase**: Phase 8 - Middleware Implementation (Prompts 308-322)

## Codex Execution Order (Full Reference)

| Phase | Prompts | Codex File | Sessions |
|-------|---------|------------|----------|
| 7 | 292-307 | `PROMPT-CONTINUATION.md` | 29-30 (CURRENT) |
| 8 | 308-322 | `DEVIN-AI-MIDDLEWARE.md` | 31-32 |
| 9 | 323-337 | `DEVIN-AI-SERVICE-LAYER.md` | 33-34 |
| 10 | 338-372 | `DEVIN-AI-FORM-REQUESTS.md` | 35-38 |
| 11 | 373-387 | `DEVIN-AI-FILE-UPLOADS.md` | 39-40 |
| 12 | 388-407 | `DEVIN-AI-PAYMENT-GATEWAY.md` | 41-43 |
| 13 | 408-422 | `DEVIN-AI-EXPORTS.md` | 44-45 |
| 14 | 423-437 | `DEVIN-AI-API-ENDPOINTS-DOCS.md` | 46-47 |
| 15 | 438-452 | `DEVIN-AI-QUEUES-JOBS.md` | 48-49 |
| 16 | 453-467 | `DEVIN-AI-EVENTS-LISTENERS.md` | 50-51 |
| 17 | 468-482 | `DEVIN-AI-REALTIME-NOTIFICATIONS.md` | 52-53 |
| 18 | 483-497 | `DEVIN-AI-I18N-RTL.md` | 54-55 |

## Notes
- Session 30 completes the Backend-Frontend Integration phase
- After Session 30, the next phase is Middleware Implementation (see `smart-school/codex/DEVIN-AI-MIDDLEWARE.md`)
- Real-time events (Prompt 307) may require additional configuration in `.env` for Pusher/Redis
- The stub controllers from Session 29 will be replaced with actual implementations in later phases
- Always check for open PRs from previous sessions before starting a new session
