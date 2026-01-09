# Session 36: Middleware & File Upload Implementation (Prompts 373-392)

## Overview
This session implements all 15 Middleware classes plus the first 5 File Upload Foundation prompts for the Smart School Management System. These components handle access control, session management, localization, security features, and file storage configuration.

## Reference Files
- **SESSION-36-CONTINUATION.md** - This file (context and task list)
- **smart-school/codex/DEVIN-AI-MIDDLEWARE.md** - Middleware specifications (AUTHORITATIVE SOURCE)
- **smart-school/codex/DEVIN-AI-FILE-UPLOADS.md** - File upload specifications (AUTHORITATIVE SOURCE)
- **smart-school/codex/DEVIN-AI-CODEX-GUIDE.md** - Master guide for codex files
- **smart-school/GUIDE_FOR_DEVIN.md** - Project-specific guidance

## Prerequisites
1. Merge PR #36 (Session 35) first, or fetch the branch to get the latest form request classes
2. Run `git fetch origin` and check for the latest changes

## Tasks for This Session (20 Prompts)

### Part 1: Core Access Middleware (Prompts 373-379)
| Prompt # | Description | File |
|----------|-------------|------|
| 373 | Create Role Middleware | `app/Http/Middleware/RoleMiddleware.php` |
| 374 | Create Permission Middleware | `app/Http/Middleware/PermissionMiddleware.php` |
| 375 | Create Module Access Middleware | `app/Http/Middleware/ModuleAccessMiddleware.php` |
| 376 | Create Academic Session Middleware | `app/Http/Middleware/AcademicSessionMiddleware.php` |
| 377 | Create School Context Middleware | `app/Http/Middleware/SchoolContextMiddleware.php` |
| 378 | Create Locale Middleware | `app/Http/Middleware/LocaleMiddleware.php` |
| 379 | Create Timezone Middleware | `app/Http/Middleware/TimezoneMiddleware.php` |

### Part 2: Security and Request Middleware (Prompts 380-387)
| Prompt # | Description | File |
|----------|-------------|------|
| 380 | Create Force Password Change Middleware | `app/Http/Middleware/ForcePasswordChange.php` |
| 381 | Create Two-Factor Middleware | `app/Http/Middleware/TwoFactorMiddleware.php` |
| 382 | Create API Throttle Middleware | `app/Http/Middleware/ApiThrottleMiddleware.php` |
| 383 | Create Audit Log Middleware | `app/Http/Middleware/AuditLogMiddleware.php` |
| 384 | Create File Access Middleware | `app/Http/Middleware/FileAccessMiddleware.php` |
| 385 | Create Parent-Child Access Middleware | `app/Http/Middleware/ParentChildAccessMiddleware.php` |
| 386 | Create Teacher-Class Access Middleware | `app/Http/Middleware/TeacherClassAccessMiddleware.php` |
| 387 | Register Middleware and Route Groups | `bootstrap/app.php` or routes files |

### Part 3: File Upload Foundations (Prompts 388-392)
| Prompt # | Description | File |
|----------|-------------|------|
| 388 | Configure Storage Disks for Public and Private Files | `config/filesystems.php` |
| 389 | Create File Upload Service | `app/Services/FileUploadService.php` |
| 390 | Create Upload Controller for AJAX | `app/Http/Controllers/UploadController.php` |
| 391 | Add Upload Validation Rules Map | `config/uploads.php` |
| 392 | Implement Secure Download Endpoint | `app/Http/Controllers/DownloadController.php` |

## Implementation Guidelines

### Middleware Structure
Each middleware should include:
1. `handle()` method - Main middleware logic
2. Proper request/response handling
3. Integration with Spatie Permission for role/permission checks
4. Appropriate redirects or JSON responses for unauthorized access

### Key Middleware Patterns
- **Role/Permission checks**: Use Spatie Permission's `hasRole()` and `can()` methods
- **Session management**: Store context in request attributes or container
- **Locale/Timezone**: Use `app()->setLocale()` and `date_default_timezone_set()`
- **Security middleware**: Check user flags and redirect appropriately
- **API responses**: Return JSON for API routes, redirects for web routes

### File Upload Patterns
- **Storage disks**: Separate public and private storage
- **FileUploadService**: Centralized upload handling with validation
- **Secure downloads**: Policy-based access control for private files

### Registration
All middleware should be registered in:
- `bootstrap/app.php` (Laravel 11) or `app/Http/Kernel.php` (Laravel 10)
- Route middleware aliases for route-level application
- Global middleware for application-wide features

## Verification Steps
1. Run PHP syntax checks on all files: `php -l filename.php`
2. Verify all middleware are in `app/Http/Middleware/` directory
3. Verify all services are in `app/Services/` directory
4. Test middleware and services using Laravel Tinker
5. Record testing video as proof

## After Completing Tasks
1. Verify all files pass PHP syntax checks
2. Test all components using Laravel Tinker
3. Record testing video
4. Update PROGRESS.md with session completion
5. Create a PR with all changes
6. Wait for CI checks to pass
7. Create SESSION-37-CONTINUATION.md for the next session
8. Notify user with PR link, summary, and next session prompt

## Dependencies
- Session 35 (Form Request Part 3) must be merged for latest codebase
- Spatie Permission package for role/permission checks
- Laravel's middleware and storage infrastructure

## Next Steps After Session 36
After completing these 20 prompts (373-392), the next session will continue with:
- Module-specific file uploads (Student Photo, Documents, Homework, Study Materials, etc.)

---

## Continuation Prompt for Next Session

```
Continue with Session 36 (Middleware & File Upload Implementation Prompts 373-392) for the Smart School Management System

Repository: 01fe23bcs183/sms_open

Reference Files:
- SESSION-36-CONTINUATION.md - This file (context and task list)
- smart-school/codex/DEVIN-AI-MIDDLEWARE.md - Middleware specifications (AUTHORITATIVE SOURCE)
- smart-school/codex/DEVIN-AI-FILE-UPLOADS.md - File upload specifications (AUTHORITATIVE SOURCE)
- smart-school/codex/DEVIN-AI-CODEX-GUIDE.md - Master guide for codex files
- smart-school/GUIDE_FOR_DEVIN.md - Project-specific guidance

Tasks for this session (20 prompts):

Part 1: Core Access Middleware (Prompts 373-379)
1. Create Role Middleware (Prompt 373)
2. Create Permission Middleware (Prompt 374)
3. Create Module Access Middleware (Prompt 375)
4. Create Academic Session Middleware (Prompt 376)
5. Create School Context Middleware (Prompt 377)
6. Create Locale Middleware (Prompt 378)
7. Create Timezone Middleware (Prompt 379)

Part 2: Security and Request Middleware (Prompts 380-387)
8. Create Force Password Change Middleware (Prompt 380)
9. Create Two-Factor Middleware (Prompt 381)
10. Create API Throttle Middleware (Prompt 382)
11. Create Audit Log Middleware (Prompt 383)
12. Create File Access Middleware (Prompt 384)
13. Create Parent-Child Access Middleware (Prompt 385)
14. Create Teacher-Class Access Middleware (Prompt 386)
15. Register Middleware and Route Groups (Prompt 387)

Part 3: File Upload Foundations (Prompts 388-392)
16. Configure Storage Disks for Public and Private Files (Prompt 388)
17. Create File Upload Service (Prompt 389)
18. Create Upload Controller for AJAX (Prompt 390)
19. Add Upload Validation Rules Map (Prompt 391)
20. Implement Secure Download Endpoint (Prompt 392)

Prerequisites:
1. Merge PR #36 (Session 35) first, or fetch the branch to get the latest changes
2. Run `git fetch origin` and check for the latest changes

After completing tasks:
1. Verify all files pass PHP syntax checks
2. Test all components using Laravel Tinker
3. Record testing video as proof
4. Update PROGRESS.md with session completion
5. Create a PR with all changes
6. Wait for CI checks to pass
7. Create SESSION-37-CONTINUATION.md for the next session
8. Notify user with PR link, summary, and next session prompt
```
