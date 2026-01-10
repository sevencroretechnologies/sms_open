# Devin Session Guide: Smart School Management System

## Project Overview

This is a Laravel-based School Management System with the following structure:
- **Framework**: Laravel 10+ with PHP 8.2
- **Frontend**: Blade templates with Bootstrap 5
- **Database**: SQLite (development) / MySQL (production)
- **Authentication**: Laravel Breeze with Spatie Permission package

## Repository Information

- **Repository**: https://github.com/sevencroretechnologies/sms_open
- **Main Directory**: `smart-school-app/`
- **Current Branch**: `devin/1767956798-fix-missing-views` (PR #4)

## Current State

### Completed Work
1. **53 View Files Created** across 10 directories:
   - `resources/views/admin/students/` (8 files)
   - `resources/views/admin/teachers/` (4 files)
   - `resources/views/admin/classes/` (6 files)
   - `resources/views/admin/sections/` (4 files)
   - `resources/views/admin/subjects/` (4 files)
   - `resources/views/admin/timetable/` (4 files)
   - `resources/views/admin/roles/` (4 files)
   - `resources/views/admin/parents/` (4 files)
   - `resources/views/admin/academic-sessions/` (4 files)
   - `resources/views/admin/communication/` (11 files)

2. **Navigation Fixed**: All sidebar links now point to correct routes

3. **Routes Added**: Roles and Users resource routes in `routes/web.php`

### What Needs to Be Done

#### 1. Database Models & Relationships

**Models to update/create** (in `app/Models/`):

```
Student.php - needs relationships:
  - belongsTo: User, StudentCategory, SchoolClass, Section, AcademicSession
  - hasMany: Attendance, FeeTransaction, ExamMark
  - belongsToMany: Parents (through student_parent pivot)

Teacher.php - needs relationships:
  - belongsTo: User, Department
  - hasMany: ClassSubject, Attendance (as marker)
  - belongsToMany: Subjects, Classes

SchoolClass.php (or ClassModel.php) - needs relationships:
  - hasMany: Section, Student, ClassSubject
  - belongsToMany: Subjects, Teachers

Section.php - needs relationships:
  - belongsTo: SchoolClass
  - hasMany: Student, ClassTimetable

Subject.php - needs relationships:
  - belongsToMany: Classes, Teachers
  - hasMany: ClassSubject, ExamSchedule

Role.php - already exists via Spatie, may need:
  - Custom attributes or scopes

ParentModel.php - needs relationships:
  - belongsTo: User
  - belongsToMany: Students
```

**Migration files to check/update** (in `database/migrations/`):
- Ensure all foreign keys are properly defined
- Add missing pivot tables if needed

#### 2. Controller CRUD Implementation

**Controllers to update** (in `app/Http/Controllers/Admin/`):

Each controller needs full CRUD implementation:

```php
// Example pattern for StudentController.php
public function index(Request $request)
{
    $query = Student::with(['class', 'section', 'category']);
    
    // Apply filters
    if ($request->class_id) {
        $query->where('class_id', $request->class_id);
    }
    if ($request->section_id) {
        $query->where('section_id', $request->section_id);
    }
    if ($request->search) {
        $query->where(function($q) use ($request) {
            $q->where('first_name', 'like', "%{$request->search}%")
              ->orWhere('last_name', 'like', "%{$request->search}%")
              ->orWhere('admission_no', 'like', "%{$request->search}%");
        });
    }
    
    $students = $query->paginate(20);
    $classes = SchoolClass::all();
    $sections = Section::all();
    
    return view('admin.students.index', compact('students', 'classes', 'sections'));
}

public function store(Request $request)
{
    $validated = $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|email|unique:students',
        'class_id' => 'required|exists:classes,id',
        'section_id' => 'required|exists:sections,id',
        // ... more validation rules
    ]);
    
    $student = Student::create($validated);
    
    return redirect()->route('admin.students.index')
        ->with('success', 'Student created successfully.');
}
```

**Controllers needing CRUD**:
- StudentController
- TeacherController
- ClassController
- SectionController
- SubjectController
- TimetableController
- RoleController
- ParentController
- AcademicSessionController

#### 3. Form Request Validation

Create Form Request classes in `app/Http/Requests/Admin/`:

```
StoreStudentRequest.php
UpdateStudentRequest.php
StoreTeacherRequest.php
UpdateTeacherRequest.php
StoreClassRequest.php
... etc
```

#### 4. Authentication Middleware

**Current middleware** (in `routes/web.php`):
```php
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'verified', 'role:admin'])
    ->group(function () {
        // routes here
    });
```

**Additional middleware to add**:
- Permission-based access control using Spatie
- Rate limiting for sensitive operations
- Activity logging

Example:
```php
Route::resource('students', StudentController::class)
    ->middleware('permission:students.view|students.create|students.edit|students.delete');
```

#### 5. Style Refinements

**Files to update**:
- `resources/css/app.css` - Custom styles
- `resources/views/layouts/app.blade.php` - Main layout
- `resources/views/layouts/navigation.blade.php` - Sidebar

**Style improvements needed**:
- Consistent card shadows and borders
- Better form input styling
- Improved table responsiveness
- Loading states for buttons
- Toast notifications for success/error messages
- Modal confirmations for delete actions

## File Structure Reference

```
smart-school-app/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Admin/           # Admin controllers
│   │   ├── Middleware/          # Custom middleware
│   │   └── Requests/            # Form requests (to create)
│   └── Models/                  # Eloquent models
├── database/
│   ├── migrations/              # Database migrations
│   └── seeders/                 # Database seeders
├── resources/
│   ├── views/
│   │   ├── admin/               # Admin views
│   │   └── layouts/             # Layout templates
│   └── css/                     # Stylesheets
├── routes/
│   └── web.php                  # Web routes
└── config/                      # Configuration files
```

## Testing Commands

```bash
# Start development server
php artisan serve

# Or use Docker
docker compose up

# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Check routes
php artisan route:list --path=admin
```

## Demo Credentials

- **Email**: admin@smartschool.com
- **Password**: password123

## Important Notes

1. **Don't modify test files** unless explicitly asked
2. **Follow existing code conventions** - check neighboring files for patterns
3. **Use existing Blade components** where available
4. **Always validate form inputs** server-side
5. **Use transactions** for multi-table operations
6. **Add flash messages** for user feedback
7. **Test locally** before pushing changes

## PR Workflow

1. Create branch: `git checkout -b devin/$(date +%s)-feature-name`
2. Make changes
3. Run `php artisan route:list` to verify routes
4. Test in browser
5. Commit with descriptive message
6. Push and create PR
7. Wait for CI (if configured)
