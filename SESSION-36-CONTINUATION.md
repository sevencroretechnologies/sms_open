# Session 36: Middleware Implementation Part 1 (Prompts 373-382)

## Overview
This session begins the Middleware Implementation phase for the Smart School Management System. These middleware classes handle access control, session management, localization, and security features.

## Reference Files
- **SESSION-36-CONTINUATION.md** - This file (context and task list)
- **smart-school/codex/DEVIN-AI-MIDDLEWARE.md** - Detailed prompt specifications (AUTHORITATIVE SOURCE)
- **smart-school/codex/DEVIN-AI-CODEX-GUIDE.md** - Master guide for codex files
- **smart-school/GUIDE_FOR_DEVIN.md** - Project-specific guidance

## Prerequisites
1. Merge PR #36 (Session 35) first, or fetch the branch to get the latest form request classes
2. Run `git fetch origin` and check for the latest changes

## Tasks for This Session

### Middleware Classes (Prompts 373-382)
| Prompt # | Description | File |
|----------|-------------|------|
| 373 | Create Role Middleware | `app/Http/Middleware/RoleMiddleware.php` |
| 374 | Create Permission Middleware | `app/Http/Middleware/PermissionMiddleware.php` |
| 375 | Create Module Access Middleware | `app/Http/Middleware/ModuleAccessMiddleware.php` |
| 376 | Create Academic Session Middleware | `app/Http/Middleware/AcademicSessionMiddleware.php` |
| 377 | Create School Context Middleware | `app/Http/Middleware/SchoolContextMiddleware.php` |
| 378 | Create Locale Middleware | `app/Http/Middleware/LocaleMiddleware.php` |
| 379 | Create Timezone Middleware | `app/Http/Middleware/TimezoneMiddleware.php` |
| 380 | Create Force Password Change Middleware | `app/Http/Middleware/ForcePasswordChange.php` |
| 381 | Create Two-Factor Middleware | `app/Http/Middleware/TwoFactorMiddleware.php` |
| 382 | Create API Throttle Middleware | `app/Http/Middleware/ApiThrottleMiddleware.php` |

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

### Registration
All middleware should be registered in:
- `bootstrap/app.php` (Laravel 11) or `app/Http/Kernel.php` (Laravel 10)
- Route middleware aliases for route-level application
- Global middleware for application-wide features

## Verification Steps
1. Run PHP syntax checks on all middleware files: `php -l filename.php`
2. Verify all middleware are in `app/Http/Middleware/` directory
3. Ensure all middleware follow the specifications in DEVIN-AI-MIDDLEWARE.md
4. Test middleware using Laravel Tinker or test routes

## After Completing Tasks
1. Verify all middleware files pass PHP syntax checks
2. Update PROGRESS.md with session completion
3. Create a PR with all changes
4. Wait for CI checks to pass
5. Create SESSION-37-CONTINUATION.md for the next session (Middleware Part 2)
6. Notify user with PR link, summary, and next session prompt

## Dependencies
- Session 35 (Form Request Part 3) must be merged for latest codebase
- Spatie Permission package for role/permission checks
- Laravel's middleware infrastructure

## Next Steps After Session 36
After completing the first 10 middleware prompts (373-382), the next session will continue with:
- Audit Log Middleware (Prompt 383)
- File Access Middleware (Prompt 384)
- Parent-Child Access Middleware (Prompt 385)
- Teacher-Class Access Middleware (Prompt 386)
- Register Middleware and Route Groups (Prompt 387)

---

## Continuation Prompt for Next Session

```
Continue with Session 36 (Middleware Implementation Part 1 Prompts 373-382) for the Smart School Management System

Repository: 01fe23bcs183/sms_open

Reference Files:
- SESSION-36-CONTINUATION.md - This file (context and task list)
- smart-school/codex/DEVIN-AI-MIDDLEWARE.md - Detailed prompt specifications (AUTHORITATIVE SOURCE)
- smart-school/codex/DEVIN-AI-CODEX-GUIDE.md - Master guide for codex files
- smart-school/GUIDE_FOR_DEVIN.md - Project-specific guidance

Tasks for this session:
1. Create Role Middleware (Prompt 373)
2. Create Permission Middleware (Prompt 374)
3. Create Module Access Middleware (Prompt 375)
4. Create Academic Session Middleware (Prompt 376)
5. Create School Context Middleware (Prompt 377)
6. Create Locale Middleware (Prompt 378)
7. Create Timezone Middleware (Prompt 379)
8. Create Force Password Change Middleware (Prompt 380)
9. Create Two-Factor Middleware (Prompt 381)
10. Create API Throttle Middleware (Prompt 382)

Prerequisites:
1. Merge PR #36 (Session 35) first, or fetch the branch to get the latest changes
2. Run `git fetch origin` and check for the latest changes

After completing tasks:
1. Verify all middleware files pass PHP syntax checks
2. Update PROGRESS.md with session completion
3. Create a PR with all changes
4. Wait for CI checks to pass
5. Create SESSION-37-CONTINUATION.md for the next session
6. Notify user with PR link, summary, and next session prompt
```
