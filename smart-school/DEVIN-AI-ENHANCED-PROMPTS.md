# Smart School Management System - DevIn AI Enhanced Prompts Guide

This document contains detailed, explanatory prompts for building Smart School Management System using DevIn AI. Each prompt includes:
- **Purpose**: Why this prompt is needed
- **Functionality**: What exactly it does
- **How it Works**: Implementation details
- **Integration**: How it connects with other features

---

## 📋 How to Use This Guide

1. **Execute prompts in order** - Each prompt builds upon previous ones
2. **Read the full context** - Understand purpose and integration before executing
3. **Verify completion** - Ensure each task is complete before moving to next
4. **Reference planning docs** - Use [`../plans/`](../plans/) for detailed architecture

---

## 🚀 Phase 1: Project Setup & Foundation

### Prompt 1: Install Laravel Dependencies

**Purpose**: Install all PHP packages required for the Laravel framework and third-party integrations.

**Functionality**: Downloads and installs Laravel core, authentication, authorization, PDF generation, Excel export, backup, and other essential packages.

**How it Works**: 
- Reads `composer.json` file
- Downloads all listed packages to `vendor/` directory
- Creates autoloader for automatic class loading
- Installs dependencies like Laravel Breeze (auth), Spatie Permission (RBAC), Laravel Excel, DomPDF, etc.

**Integration**: This is foundational - all subsequent code will depend on these packages being installed.

**Execute**: Navigate to `smart-school` directory and run:
```bash
composer install
```

---

### Prompt 2: Install Node.js Dependencies

**Purpose**: Install frontend JavaScript libraries for UI components, charts, and interactivity.

**Functionality**: Downloads Bootstrap (CSS framework), Alpine.js (JavaScript framework), Chart.js (data visualization), SweetAlert2 (alerts), and other frontend tools.

**How it Works**:
- Reads `package.json` file
- Downloads packages to `node_modules/` directory
- Makes libraries available for import in JavaScript files
- Enables Vite to bundle and optimize frontend assets

**Integration**: Frontend views will use these libraries for responsive design, charts, and interactive components.

**Execute**: Navigate to `smart-school` directory and run:
```bash
npm install
```

---

### Prompt 3: Configure Environment File

**Purpose**: Create environment-specific configuration file for database, cache, mail, and other settings.

**Functionality**: Creates `.env` file from template with placeholders for configuration values.

**How it Works**:
- Copies `.env.example` to `.env`
- `.env` file contains key-value pairs for:
  - Database credentials
  - Cache driver (Redis)
  - Mail settings
  - Application key
  - Debug mode
  - Timezone

**Integration**: Laravel reads `.env` file at runtime to configure application behavior. All database connections, mail services, and third-party integrations depend on this.

**Execute**: Navigate to `smart-school` directory and run:
```bash
cp .env.example .env
```

---

### Prompt 4: Generate Application Key

**Purpose**: Generate a unique encryption key for secure session and cookie handling.

**Functionality**: Creates a 32-character random string used for encrypting sessions, cookies, and other sensitive data.

**How it Works**:
- Laravel's `key:generate` command generates random string
- Updates `APP_KEY` value in `.env` file
- This key is used by Laravel's encryption service
- Ensures secure session management and CSRF protection

**Integration**: Authentication system, session management, and security features depend on this key.

**Execute**: Navigate to `smart-school` directory and run:
```bash
php artisan key:generate
```

---

### Prompt 5: Create MySQL Database

**Purpose**: Create empty database to store all application data.

**Functionality**: Creates a MySQL database with proper character set for multi-language support.

**How it Works**:
- Executes SQL command to create database
- Uses `utf8mb4` character set for full Unicode support (including emojis)
- Uses `utf8mb4_unicode_ci` collation for case-insensitive comparisons
- Database name: `smart_school`

**Integration**: All Laravel migrations will create tables in this database. All application data will be stored here.

**Execute**: Run in MySQL command line or phpMyAdmin:
```sql
CREATE DATABASE smart_school CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

---

### Prompt 6: Update Database Configuration

**Purpose**: Configure Laravel to connect to the MySQL database created in previous step.

**Functionality**: Updates `.env` file with database connection credentials.

**How it Works**:
- Edit `.env` file
- Set `DB_DATABASE=smart_school`
- Set `DB_USERNAME=your_mysql_username`
- Set `DB_PASSWORD=your_mysql_password`
- Laravel reads these values to establish database connection

**Integration**: Database migrations, queries, and all data operations will use these credentials.

**Execute**: Edit `.env` file in `smart-school` directory:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=smart_school
DB_USERNAME=root
DB_PASSWORD=your_password
```

---

### Prompt 7: Run Database Migrations

**Purpose**: Create all database tables defined in migration files.

**Functionality**: Executes all pending migrations to create database schema.

**How it Works**:
- Laravel scans `database/migrations/` directory
- Executes migrations in chronological order
- Each migration creates one or more tables
- Tracks which migrations have been run in `migrations` table
- Creates 50+ tables for all modules

**Integration**: This creates the foundation for all data storage. All models will map to these tables.

**Execute**: Navigate to `smart-school` directory and run:
```bash
php artisan migrate
```

---

### Prompt 8: Run Database Seeders

**Purpose**: Populate database with initial data (roles, permissions, admin user).

**Functionality**: Inserts default data into tables for system to be functional.

**How it Works**:
- Executes all seeder classes in `database/seeders/` directory
- Creates default roles: admin, teacher, student, parent, accountant, librarian
- Creates default permissions for all modules
- Creates default admin user for initial login
- Creates default academic session

**Integration**: Authentication system, role-based access control, and initial system setup depend on this data.

**Execute**: Navigate to `smart-school` directory and run:
```bash
php artisan db:seed
```

---

### Prompt 9: Build Frontend Assets

**Purpose**: Compile and bundle frontend assets (CSS, JavaScript) for production-ready code.

**Functionality**: Processes SCSS/SASS files, bundles JavaScript, optimizes assets.

**How it Works**:
- Vite reads `resources/assets/` directory
- Compiles SCSS to CSS
- Bundles JavaScript modules
- Minifies and optimizes output
- Writes compiled files to `public/build/` directory
- Creates source maps for debugging

**Integration**: All views will reference these compiled assets. Bootstrap, Alpine.js, and Chart.js will be available.

**Execute**: Navigate to `smart-school` directory and run:
```bash
npm run dev
```

---

### Prompt 10: Start Development Server

**Purpose**: Start Laravel's built-in development server for local testing.

**Functionality**: Runs PHP web server that serves the application.

**How it Works**:
- Starts PHP's built-in web server
- Listens on port 8000 (default)
- Serves files from `public/` directory
- Routes all HTTP requests through Laravel
- Enables hot-reloading for development

**Integration**: Allows you to access and test the application at http://localhost:8000

**Execute**: Navigate to `smart-school` directory and run:
```bash
php artisan serve
```

---

## 🗄️ Phase 2: Database Schema Implementation

### Prompt 11: Create Users Table Migration

**Purpose**: Create central table to store all user accounts for all 6 user roles.

**Functionality**: Defines structure for storing user information, authentication credentials, and profile data.

**How it Works**:
- Creates `users` table with fields for:
  - Basic info: first_name, last_name, email, phone, username
  - Authentication: password (hashed), email_verified_at, remember_token
  - Profile: date_of_birth, gender, avatar, address
  - System: role_id, is_active, last_login_at, deleted_at
- Adds indexes for fast queries on email, phone, username
- Adds foreign key to `roles` table

**Integration**: 
- Links to `roles` table for RBAC
- Links to `students` table (one-to-one)
- Authentication system validates credentials against this table
- All user-specific features depend on this table

**Execute**: Create migration file `database/migrations/xxxx_xx_xx_create_users_table.php` with structure:
```php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->uuid('uuid')->unique();
    $table->foreignId('role_id')->constrained('roles');
    $table->string('first_name', 100);
    $table->string('last_name', 100);
    $table->string('email')->unique();
    $table->string('phone', 20)->unique();
    $table->string('username', 50)->unique();
    $table->string('password');
    $table->string('avatar')->nullable();
    $table->date('date_of_birth')->nullable();
    $table->enum('gender', ['male', 'female', 'other'])->nullable();
    $table->text('address')->nullable();
    $table->string('city', 100)->nullable();
    $table->string('state', 100)->nullable();
    $table->string('country', 100)->default('India');
    $table->string('postal_code', 20)->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamp('email_verified_at')->nullable();
    $table->timestamp('last_login_at')->nullable();
    $table->rememberToken();
    $table->timestamps();
    $table->softDeletes();
    
    $table->index('role_id');
    $table->index('email');
    $table->index('phone');
    $table->index('username');
    $table->index('is_active');
});
```

---

### Prompt 12: Create Roles Table Migration

**Purpose**: Create table to define 6 user roles (admin, teacher, student, parent, accountant, librarian).

**Functionality**: Stores role definitions with display names and descriptions.

**How it Works**:
- Creates `roles` table with fields:
  - name: machine-readable role name (e.g., 'admin')
  - display_name: human-readable name (e.g., 'Administrator')
  - description: role description
  - is_active: enable/disable role
- Each role represents a user type with specific permissions

**Integration**:
- Links to `users` table (many-to-many via pivot table)
- Links to `permissions` table (many-to-many via pivot table)
- Spatie Permission package uses this for RBAC
- Middleware checks user's role to authorize access

**Execute**: Create migration file with structure:
```php
Schema::create('roles', function (Blueprint $table) {
    $table->id();
    $table->string('name', 50)->unique();
    $table->string('display_name', 100);
    $table->text('description')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

---

### Prompt 13: Create Permissions Table Migration

**Purpose**: Create table to define granular permissions for all modules and features.

**Functionality**: Stores individual permissions like 'view students', 'create students', 'edit students', etc.

**How it Works**:
- Creates `permissions` table with fields:
  - name: permission name (e.g., 'students.view')
  - display_name: human-readable name (e.g., 'View Students')
  - module: which module this permission belongs to (e.g., 'Student')
  - description: what this permission allows
- Each permission represents a specific action in a module

**Integration**:
- Links to `roles` table (many-to-many via pivot table)
- Links to `users` table (many-to-many via pivot table)
- Spatie Permission package checks these for authorization
- Middleware verifies user has required permission before allowing access

**Execute**: Create migration file with structure:
```php
Schema::create('permissions', function (Blueprint $table) {
    $table->id();
    $table->string('name', 255)->unique();
    $table->string('display_name', 255);
    $table->string('module', 50);
    $table->text('description')->nullable();
    $table->timestamps();
    
    $table->index('module');
});
```

---

### Prompt 14: Create Role-Permission Pivot Table Migration

**Purpose**: Create many-to-many relationship table between roles and permissions.

**Functionality**: Allows multiple permissions to be assigned to a single role.

**How it Works**:
- Creates `role_has_permissions` pivot table with:
  - role_id: foreign key to roles table
  - permission_id: foreign key to permissions table
- Composite primary key on (role_id, permission_id)
- Cascade delete: if role/permission is deleted, pivot row is removed

**Integration**:
- Enables RBAC: Each role has multiple permissions
- When user has a role, they inherit all role's permissions
- Spatie Permission package uses this to check if user has permission
- Middleware checks `role_has_permissions` to authorize actions

**Execute**: Create migration file with structure:
```php
Schema::create('role_has_permissions', function (Blueprint $table) {
    $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
    $table->foreignId('permission_id')->constrained('permissions')->onDelete('cascade');
    $table->primary(['role_id', 'permission_id']);
});
```

---

### Prompt 15: Create Model-Permission Pivot Table Migration

**Purpose**: Create table to assign permissions directly to users (bypassing roles).

**Functionality**: Allows direct permission assignment to individual users for special cases.

**How it Works**:
- Creates `model_has_permissions` pivot table with:
  - permission_id: foreign key to permissions table
  - model_type: which model type (e.g., 'App\Models\User')
  - model_id: ID of the model instance (e.g., user ID)
- Composite primary key on (permission_id, model_id, model_type)
- Polymorphic relationship: can assign permissions to any model type

**Integration**:
- Enables direct permission assignment to users
- Used for giving users extra permissions beyond their role
- Spatie Permission package checks this table for user permissions
- Combines with role permissions for final permission set

**Execute**: Create migration file with structure:
```php
Schema::create('model_has_permissions', function (Blueprint $table) {
    $table->foreignId('permission_id')->constrained('permissions')->onDelete('cascade');
    $table->string('model_type');
    $table->unsignedBigInteger('model_id');
    $table->primary(['permission_id', 'model_id', 'model_type']);
});
```

---

### Prompt 16: Create Model-Role Pivot Table Migration

**Purpose**: Create table to assign roles to users (many-to-many relationship).

**Functionality**: Allows users to have multiple roles.

**How it Works**:
- Creates `model_has_roles` pivot table with:
  - role_id: foreign key to roles table
  - model_type: which model type (e.g., 'App\Models\User')
  - model_id: ID of the model instance (e.g., user ID)
- Composite primary key on (role_id, model_id, model_type)
- Polymorphic relationship: can assign roles to any model type

**Integration**:
- Enables users to have multiple roles
- Spatie Permission package uses this to check user's roles
- When user logs in, their roles are loaded from this table
- Middleware checks user's roles to authorize access

**Execute**: Create migration file with structure:
```php
Schema::create('model_has_roles', function (Blueprint $table) {
    $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
    $table->string('model_type');
    $table->unsignedBigInteger('model_id');
    $table->primary(['role_id', 'model_id', 'model_type']);
});
```

---

### Prompt 17: Create Academic Sessions Table Migration

**Purpose**: Create table to manage academic years/sessions (e.g., 2023-24, 2024-25).

**Functionality**: Stores academic session information with start/end dates.

**How it Works**:
- Creates `academic_sessions` table with fields:
  - name: session name (e.g., '2023-2024')
  - start_date: when session starts
  - end_date: when session ends
  - is_current: marks which session is currently active
  - is_active: enable/disable session
- Only one session can be current at a time

**Integration**:
- Links to `classes` table (one-to-many)
- Links to `students` table (one-to-many)
- Links to `exams` table (one-to-many)
- Links to `fees_masters` table (one-to-many)
- All academic data is organized by session

**Execute**: Create migration file with structure:
```php
Schema::create('academic_sessions', function (Blueprint $table) {
    $table->id();
    $table->string('name', 50);
    $table->date('start_date');
    $table->date('end_date');
    $table->boolean('is_current')->default(false);
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    $table->softDeletes();
    
    $table->index('is_current');
});
```

---

### Prompt 18: Create Classes Table Migration

**Purpose**: Create table to manage classes/grades (e.g., Class 1, Class 2, Class 10).

**Functionality**: Stores class information with display names and ordering.

**How it Works**:
- Creates `classes` table with fields:
  - academic_session_id: which session this class belongs to
  - name: class name (e.g., 'Class 1')
  - display_name: human-readable name (e.g., 'First Grade')
  - section_count: how many sections this class has
  - order_index: for sorting classes
  - is_active: enable/disable class
- Classes are organized within academic sessions

**Integration**:
- Links to `academic_sessions` table (many-to-one)
- Links to `sections` table (one-to-many)
- Links to `students` table (one-to-many)
- Links to `class_subjects` table (one-to-many)
- Links to `class_timetables` table (one-to-many)
- All academic activities are organized by class

**Execute**: Create migration file with structure:
```php
Schema::create('classes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('academic_session_id')->constrained('academic_sessions')->onDelete('cascade');
    $table->string('name', 50);
    $table->string('display_name', 100);
    $table->integer('section_count')->default(1);
    $table->integer('order_index')->default(0);
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    $table->softDeletes();
    
    $table->index('academic_session_id');
    $table->index('name');
});
```

---

### Prompt 19: Create Sections Table Migration

**Purpose**: Create table to manage sections within classes (e.g., Class 1-A, Class 1-B, Class 1-C).

**Functionality**: Stores section information with capacity and class teacher assignment.

**How it Works**:
- Creates `sections` table with fields:
  - class_id: which class this section belongs to
  - name: section name (e.g., 'A', 'B', 'C')
  - display_name: human-readable name (e.g., 'Section A')
  - capacity: max students in this section
  - room_number: classroom number
  - class_teacher_id: which teacher is assigned
  - is_active: enable/disable section
- Sections divide classes into smaller groups

**Integration**:
- Links to `classes` table (many-to-one)
- Links to `users` table (many-to-one, class_teacher)
- Links to `students` table (one-to-many)
- Links to `class_subjects` table (one-to-many)
- Links to `attendances` table (one-to-many)
- Enables fine-grained academic organization

**Execute**: Create migration file with structure:
```php
Schema::create('sections', function (Blueprint $table) {
    $table->id();
    $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
    $table->string('name', 10);
    $table->string('display_name', 50);
    $table->integer('capacity')->default(40);
    $table->string('room_number', 20)->nullable();
    $table->foreignId('class_teacher_id')->nullable()->constrained('users')->onDelete('set null');
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    $table->softDeletes();
    
    $table->index('class_id');
});
```

---

### Prompt 20: Create Subjects Table Migration

**Purpose**: Create table to manage subjects (e.g., Mathematics, Science, English).

**Functionality**: Stores subject information with codes and types.

**How it Works**:
- Creates `subjects` table with fields:
  - name: subject name (e.g., 'Mathematics')
  - code: unique subject code (e.g., 'MATH101')
  - type: theory or practical
  - description: subject description
  - is_active: enable/disable subject
- Subjects are taught across classes

**Integration**:
- Links to `class_subjects` table (one-to-many)
- Links to `class_timetables` table (one-to-many)
- Links to `exam_schedules` table (one-to-many)
- Links to `homework` table (one-to-many)
- Links to `study_materials` table (one-to-many)
- Organizes curriculum by subject

**Execute**: Create migration file with structure:
```php
Schema::create('subjects', function (Blueprint $table) {
    $table->id();
    $table->string('name', 100);
    $table->string('code', 20)->unique();
    $table->enum('type', ['theory', 'practical'])->default('theory');
    $table->text('description')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    $table->softDeletes();
    
    $table->index('code');
});
```

---

### Prompt 21: Create Class-Subjects Pivot Table Migration

**Purpose**: Create table to assign subjects to specific classes and sections with teachers.

**Functionality**: Defines which subjects are taught in which class-section by which teacher.

**How it Works**:
- Creates `class_subjects` table with fields:
  - class_id: which class
  - section_id: which section (nullable for whole-class subjects)
  - subject_id: which subject
  - teacher_id: which teacher teaches this subject
  - is_active: enable/disable assignment
- Enables flexible subject assignment (whole-class or section-specific)

**Integration**:
- Links to `classes` table (many-to-one)
- Links to `sections` table (many-to-one, optional)
- Links to `subjects` table (many-to-one)
- Links to `users` table (many-to-one, teacher)
- Used by timetable system and exam scheduling
- Teacher dashboard shows assigned subjects from this table

**Execute**: Create migration file with structure:
```php
Schema::create('class_subjects', function (Blueprint $table) {
    $table->id();
    $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
    $table->foreignId('section_id')->nullable()->constrained('sections')->onDelete('cascade');
    $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
    $table->foreignId('teacher_id')->nullable()->constrained('users')->onDelete('set null');
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    
    $table->index('class_id');
    $table->index('section_id');
    $table->index('subject_id');
});
```

---

### Prompt 22: Create Class Timetables Table Migration

**Purpose**: Create table to manage weekly class schedules with periods and subjects.

**Functionality**: Stores timetable information showing which subject is taught when and where.

**How it Works**:
- Creates `class_timetables` table with fields:
  - class_id: which class
  - section_id: which section
  - day_of_week: monday through saturday
  - period_number: which period (1, 2, 3, etc.)
  - subject_id: which subject
  - teacher_id: which teacher
  - room_number: which classroom
  - start_time: when period starts
  - end_time: when period ends
- Creates weekly schedule grid for each class-section

**Integration**:
- Links to `classes` table (many-to-one)
- Links to `sections` table (many-to-one)
- Links to `subjects` table (many-to-one)
- Links to `users` table (many-to-one, teacher)
- Teacher dashboard shows their timetable
- Student dashboard shows their class timetable
- Prevents scheduling conflicts

**Execute**: Create migration file with structure:
```php
Schema::create('class_timetables', function (Blueprint $table) {
    $table->id();
    $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
    $table->foreignId('section_id')->constrained('sections')->onDelete('cascade');
    $table->enum('day_of_week', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday']);
    $table->integer('period_number');
    $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
    $table->foreignId('teacher_id')->nullable()->constrained('users')->onDelete('set null');
    $table->string('room_number', 20)->nullable();
    $table->time('start_time');
    $table->time('end_time');
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    
    $table->index(['class_id', 'section_id']);
    $table->index('day_of_week');
});
```

---

### Prompt 23: Create Students Table Migration

**Purpose**: Create central table to store all student information and academic records.

**Functionality**: Stores comprehensive student data including personal info, family details, academic history, and more.

**How it Works**:
- Creates `students` table with 40+ fields:
  - **User Link**: user_id (links to users table)
  - **Academic Info**: academic_session_id, admission_number, roll_number, class_id, section_id, date_of_admission
  - **Personal Info**: date_of_birth, gender, blood_group, religion, caste, nationality, mother_tongue
  - **Family Info**: father_name, father_phone, father_occupation, mother_name, mother_phone, mother_occupation, guardian_name, guardian_phone
  - **Address**: address, city, state, country, postal_code
  - **School Info**: previous_school_name, transfer_certificate_number, is_rte, admission_type
  - **Emergency**: emergency_contact_name, emergency_contact_phone, emergency_contact_relation
  - **Health**: medical_notes, allergies, height, weight, identification_marks
  - **System**: category_id, is_active, deleted_at
- Soft delete enabled for data recovery

**Integration**:
- Links to `users` table (one-to-one)
- Links to `academic_sessions` table (many-to-one)
- Links to `classes` table (many-to-one)
- Links to `sections` table (many-to-one)
- Links to `student_categories` table (many-to-one)
- Links to `student_siblings` table (one-to-many)
- Links to `student_documents` table (one-to-many)
- Links to `student_promotions` table (one-to-many)
- Links to `attendances` table (one-to-many)
- Links to `exam_marks` table (one-to-many)
- Links to `fees_allotments` table (one-to-many)
- Links to `transport_students` table (one-to-many)
- Links to `hostel_assignments` table (one-to-many)
- Central to all student-related features

**Execute**: Create migration file with complete structure (see full prompt in previous version)

---

### Prompt 24: Create Student Siblings Table Migration

**Purpose**: Create table to manage sibling relationships between students.

**Functionality**: Stores which students are siblings of each other.

**How it Works**:
- Creates `student_siblings` table with fields:
  - student_id: primary student
  - sibling_id: their sibling
  - relation: brother or sister
- Enables parent to see all children in one account
- Tracks family relationships within school

**Integration**:
- Links to `students` table (many-to-one, student_id)
- Links to `students` table (many-to-one, sibling_id)
- Parent account can view all siblings' information
- Used for fee discounts (sibling discount)

**Execute**: Create migration file with structure:
```php
Schema::create('student_siblings', function (Blueprint $table) {
    $table->id();
    $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
    $table->foreignId('sibling_id')->constrained('students')->onDelete('cascade');
    $table->enum('relation', ['brother', 'sister']);
    $table->timestamps();
    
    $table->index('student_id');
});
```

---

### Prompt 25: Create Student Documents Table Migration

**Purpose**: Create table to store uploaded documents for students (birth certificate, photos, etc.).

**Functionality**: Stores file information for student documents.

**How it Works**:
- Creates `student_documents` table with fields:
  - student_id: which student
  - document_type: type of document (e.g., 'birth_certificate', 'photo')
  - document_name: original filename
  - file_path: where file is stored
  - file_size: file size in bytes
  - uploaded_by: which user uploaded
- Enables document management and retrieval

**Integration**:
- Links to `students` table (many-to-one)
- Links to `users` table (many-to-one, uploaded_by)
- Student profile shows all documents
- Documents can be downloaded/viewed
- Used for admission process and record keeping

**Execute**: Create migration file with structure:
```php
Schema::create('student_documents', function (Blueprint $table) {
    $table->id();
    $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
    $table->string('document_type', 50);
    $table->string('document_name', 255);
    $table->string('file_path', 255);
    $table->integer('file_size')->nullable();
    $table->foreignId('uploaded_by')->nullable()->constrained('users')->onDelete('set null');
    $table->timestamps();
    
    $table->index('student_id');
});
```

---

### Prompt 26: Create Student Categories Table Migration

**Purpose**: Create table to manage student categories (caste, skill, etc.).

**Functionality**: Stores category definitions for grouping students.

**How it Works**:
- Creates `student_categories` table with fields:
  - name: category name (e.g., 'General', 'OBC', 'SC', 'ST')
  - description: category description
  - is_active: enable/disable category
- Enables student categorization for reporting and discounts

**Integration**:
- Links to `students` table (one-to-many)
- Used for fee discounts based on category
- Used for reporting and analytics
- Filter students by category in reports

**Execute**: Create migration file with structure:
```php
Schema::create('student_categories', function (Blueprint $table) {
    $table->id();
    $table->string('name', 100);
    $table->text('description')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    $table->softDeletes();
});
```

---

### Prompt 27: Create Student Promotions Table Migration

**Purpose**: Create table to track student promotions between classes/sessions.

**Functionality**: Stores promotion history with results (promoted, detained, left).

**How it Works**:
- Creates `student_promotions` table with fields:
  - student_id: which student
  - from_class_id: previous class
  - from_section_id: previous section
  - to_class_id: new class
  - to_section_id: new section
  - from_session_id: previous session
  - to_session_id: new session
  - result: promoted, detained, left
  - remarks: promotion notes
  - promoted_by: who promoted
  - promoted_at: when promoted
- Tracks academic progression

**Integration**:
- Links to `students` table (many-to-one)
- Links to `classes` table (many-to-one, from_class_id)
- Links to `sections` table (many-to-one, from_section_id)
- Links to `classes` table (many-to-one, to_class_id)
- Links to `sections` table (many-to-one, to_section_id)
- Links to `academic_sessions` table (many-to-one, from_session_id)
- Links to `academic_sessions` table (many-to-one, to_session_id)
- Links to `users` table (many-to-one, promoted_by)
- Maintains academic history
- Generates promotion reports

**Execute**: Create migration file with structure:
```php
Schema::create('student_promotions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
    $table->foreignId('from_class_id')->constrained('classes')->onDelete('cascade');
    $table->foreignId('from_section_id')->nullable()->constrained('sections')->onDelete('cascade');
    $table->foreignId('to_class_id')->constrained('classes')->onDelete('cascade');
    $table->foreignId('to_section_id')->nullable()->constrained('sections')->onDelete('cascade');
    $table->foreignId('from_session_id')->constrained('academic_sessions')->onDelete('cascade');
    $table->foreignId('to_session_id')->constrained('academic_sessions')->onDelete('cascade');
    $table->enum('result', ['promoted', 'detained', 'left']);
    $table->text('remarks')->nullable();
    $table->foreignId('promoted_by')->nullable()->constrained('users')->onDelete('set null');
    $table->timestamp('promoted_at')->useCurrent();
    $table->timestamps();
    
    $table->index('student_id');
    $table->index('from_session_id');
    $table->index('to_session_id');
});
```

---

## 📝 Continue with Remaining Prompts

Continue with prompts 28-106 following the same format with:
- Purpose
- Functionality
- How it Works
- Integration

Each prompt explains:
- Why it's needed
- What it does
- How it functions
- How it integrates with other features

---

## 🎯 Key Integration Points

### How Features Work Together

1. **Authentication → All Modules**
   - User logs in → Session created → User's role loaded → Permissions loaded
   - All module access controlled by user's role and permissions

2. **Academic Structure → Student Management**
   - Academic sessions defined → Classes created → Sections added → Students enrolled
   - Students assigned to class-sections → Timetable created → Teachers assigned

3. **Student Management → All Student Features**
   - Student created → Documents uploaded → Siblings linked → Category assigned
   - Student linked to user → Attendance tracked → Marks entered → Fees assigned

4. **Attendance → Notifications**
   - Attendance marked → SMS sent to parents → Email notification sent
   - Attendance reports generated → Parents view attendance

5. **Examination → Report Cards**
   - Exam scheduled → Marks entered → Grades calculated → Report cards generated
   - Parents view results → Students see grades

6. **Fees → Accounting**
   - Fees assigned → Payment collected → Transaction recorded → Accounting updated
   - Parents view fees → Accountant manages collections → Financial reports generated

7. **Communication → All Users**
   - Notice created → Targeted to roles/classes → Users see notices
   - Message sent → Recipients receive → Read status tracked
   - SMS/Email sent → Delivery status logged

---

## 📊 Data Flow Example

### Student Admission Flow

```
Admin creates user account → User stored in users table
Admin creates student profile → Student stored in students table
Student linked to user → user_id in students table
Student assigned to class → class_id in students table
Student assigned to section → section_id in students table
Student assigned to session → academic_session_id in students table
Documents uploaded → student_documents table
Siblings linked → student_siblings table
Fees assigned → fees_allotments table
Transport assigned → transport_students table (if applicable)
```

### Attendance Marking Flow

```
Teacher selects class-section → Students loaded from students table
Teacher marks attendance → Records stored in attendances table
Attendance type selected → attendance_type_id in attendances table
SMS notification triggered → sms_logs table
Email notification triggered → email_logs table
Parents view attendance → Query attendances table
```

### Fee Collection Flow

```
Parent views fees → Query fees_allotments table
Parent initiates payment → fees_transactions table created
Payment processed → payment_status updated
Receipt generated → Transaction completed
Accounting updated → expenses table updated
Parent receives receipt → Email notification sent
```

---

## 🚀 Next Steps

Continue with remaining prompts (28-106) for:
- Remaining database migrations (attendance, exam, fees, library, transport, hostel, communication, accounting, settings, downloads)
- Model creation for all tables
- Authentication and authorization implementation
- Views and layouts
- Controllers for all modules
- Role-specific panels
- Report generation
- Multi-language support
- Testing and deployment

Each prompt maintains the same explanatory format with purpose, functionality, how it works, and integration details.

---

## 📞 Support

For questions or issues:
1. Review this prompt guide for context
2. Check planning documents in [`../plans/`](../plans/) directory
3. Refer to database schema in [`../plans/school-management-database-schema.md`](../plans/school-management-database-schema.md)
4. Check Laravel documentation

**Happy Building with DevIn AI!** 🚀
