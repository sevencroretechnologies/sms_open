# Prompt for Next Devin Session

## Task: Implement Full CRUD Functionality for Smart School Management System

**Repository**: https://github.com/sevencroretechnologies/sms_open
**Directory**: smart-school-app/

**Reference File**: Read `DEVIN-SESSION-GUIDE.md` in the repository root for detailed technical guidance, file structure, and code patterns.

---

## Overview

The Smart School Management System has view files and routes created, but the controllers return empty data and forms don't save to the database. Your task is to implement full CRUD functionality by:

1. Adding real database models with relationships
2. Connecting forms to save data to the database
3. Adding proper authentication middleware
4. Making style refinements

---

## Task 1: Database Models & Relationships

Update/create these models in `app/Models/`:

**Student.php**:
- Add fillable fields: first_name, last_name, email, phone, admission_no, date_of_birth, gender, address, class_id, section_id, category_id, session_id, parent_id, photo, status
- Add relationships: belongsTo (User, StudentCategory, SchoolClass, Section, AcademicSession), hasMany (Attendance, FeeTransaction)

**Teacher.php**:
- Add fillable fields: employee_id, first_name, last_name, email, phone, gender, date_of_birth, address, qualification, experience, department, joining_date, photo, status
- Add relationships: belongsTo (User), belongsToMany (Subjects, Classes)

**SchoolClass.php** (rename from ClassModel if needed):
- Add fillable fields: name, numeric_name, description, status
- Add relationships: hasMany (Section, Student, ClassSubject)

**Section.php**:
- Add fillable fields: name, class_id, capacity, status
- Add relationships: belongsTo (SchoolClass), hasMany (Student)

**Subject.php**:
- Add fillable fields: name, code, type, description, status
- Add relationships: belongsToMany (Classes, Teachers)

**ParentModel.php**:
- Add fillable fields: father_name, mother_name, email, phone, occupation, address, user_id
- Add relationships: belongsTo (User), belongsToMany (Students)

**AcademicSession.php**:
- Add fillable fields: name, start_date, end_date, is_current, status

**StudentCategory.php**:
- Add fillable fields: name, description, status

---

## Task 2: Controller CRUD Implementation

Update these controllers in `app/Http/Controllers/Admin/`:

For each controller, implement these methods with real database operations:

```php
// Pattern to follow:
public function index(Request $request)
{
    // Query with relationships and filters
    // Return paginated results
}

public function create()
{
    // Load related data for dropdowns
    // Return create view
}

public function store(Request $request)
{
    // Validate input
    // Create record
    // Redirect with success message
}

public function show($id)
{
    // Load record with relationships
    // Return show view
}

public function edit($id)
{
    // Load record and related data
    // Return edit view
}

public function update(Request $request, $id)
{
    // Validate input
    // Update record
    // Redirect with success message
}

public function destroy($id)
{
    // Delete record (soft delete if applicable)
    // Redirect with success message
}
```

**Controllers to update**:
1. StudentController - full CRUD with filters (class, section, category, search)
2. TeacherController - full CRUD with filters (department, status, search)
3. ClassController - full CRUD
4. SectionController - full CRUD with class filter
5. SubjectController - full CRUD
6. StudentCategoryController - full CRUD
7. AcademicSessionController - full CRUD with set-current functionality
8. ParentController - full CRUD
9. RoleController - full CRUD with permission management
10. TimetableController - full CRUD with class/section filters

---

## Task 3: Form Validation

Create Form Request classes in `app/Http/Requests/Admin/`:

Example for StoreStudentRequest.php:
```php
<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email',
            'phone' => 'nullable|string|max:20',
            'admission_no' => 'required|string|unique:students,admission_no',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'required|exists:sections,id',
            'category_id' => 'nullable|exists:student_categories,id',
            'address' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
        ];
    }
}
```

Create similar request classes for: Teacher, Class, Section, Subject, Parent, AcademicSession, Role

---

## Task 4: Authentication Middleware

Update `routes/web.php` to add permission-based middleware:

```php
// Add permission checks to routes
Route::resource('students', StudentController::class)
    ->middleware('permission:students.view|students.create|students.edit|students.delete');

Route::resource('teachers', TeacherController::class)
    ->middleware('permission:teachers.view|teachers.create|teachers.edit|teachers.delete');

// ... similar for other resources
```

Update `database/seeders/PermissionSeeder.php` to ensure all permissions exist.

---

## Task 5: Style Refinements

1. **Add toast notifications** for success/error messages in `resources/views/layouts/app.blade.php`:
```blade
@if(session('success'))
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div class="toast show" role="alert">
            <div class="toast-body bg-success text-white">
                {{ session('success') }}
            </div>
        </div>
    </div>
@endif
```

2. **Add delete confirmation modals** to index views

3. **Improve form styling** - consistent input groups, better spacing

4. **Add loading states** to submit buttons

5. **Make tables responsive** with horizontal scroll on mobile

---

## Testing Checklist

After implementation, test:
- [ ] Can create a new student with all fields
- [ ] Can edit an existing student
- [ ] Can delete a student (with confirmation)
- [ ] Student list shows real data with pagination
- [ ] Filters work correctly (class, section, search)
- [ ] Form validation shows errors properly
- [ ] Success messages appear after actions
- [ ] Repeat for Teachers, Classes, Sections, Subjects

---

## Commands to Run

```bash
# Start server
php artisan serve

# Run migrations
php artisan migrate

# Seed database with sample data
php artisan db:seed

# Clear caches after changes
php artisan cache:clear && php artisan config:clear && php artisan view:clear

# Check routes
php artisan route:list --path=admin
```

---

## Important Notes

- Always create a new branch before making changes
- Test locally before pushing
- Create a PR when done
- Reference `DEVIN-SESSION-GUIDE.md` for detailed code patterns and file structure
