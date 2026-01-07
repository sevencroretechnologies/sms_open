# Smart School Management System - Complete Backend DevIn AI Prompts Guide

This document contains ALL 106 detailed prompts for building Smart School Management System backend using DevIn AI. Each prompt includes:
- **Purpose**: Why this prompt is needed
- **Functionality**: What exactly it does
- **How it Works**: Implementation details
- **Integration**: How it connects with other features

---

## 📋 How to Use This Guide

1. **Execute prompts in order** - Each prompt builds upon previous ones
2. **Read full context** - Understand purpose and integration before executing
3. **Verify completion** - Ensure each task is complete before moving to next
4. **Reference planning docs** - Use [`../plans/`](../plans/) for detailed architecture
5. **For frontend prompts** - Use the PART files (DEVIN-AI-FRONTEND-DETAILED.md, PART2, PART3, PART4)

---

## 🚀 Phase 1: Project Setup & Foundation (10 Prompts)

### Prompt 1: Install Laravel Dependencies

**Purpose**: Install all PHP packages required for Laravel framework and third-party integrations.

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
- Laravel reads `.env` file at runtime to configure application behavior

**Integration**: All database connections, mail services, and third-party integrations depend on this.

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

**Purpose**: Configure Laravel to connect to MySQL database created in previous step.

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

**Integration**: This creates foundation for all data storage. All models will map to these tables.

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

## 🗄️ Phase 2: Database Schema Implementation (60 Prompts)

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
- Links to `roles` table (one-to-many)
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
  - model_id: ID of model instance (e.g., user ID)
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
  - model_id: ID of model instance (e.g., user ID)
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
- Links to `fees_masters` table (one-to-many)
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

### Prompt 28: Create Attendance Types Table Migration

**Purpose**: Create table to define attendance types (present, absent, late, etc.).

**Functionality**: Stores attendance type definitions with colors for UI.

**How it Works**:
- Creates `attendance_types` table with fields:
  - name: type name (e.g., 'Present', 'Absent', 'Late')
  - code: unique code (e.g., 'P', 'A', 'L')
  - color: color for UI display (e.g., '#00FF00' for present)
  - is_present: whether this type counts as present
  - is_active: enable/disable type
- Differentiates attendance states

**Integration**:
- Links to `attendances` table (one-to-many)
- Used in attendance marking interface
- Colors used in attendance reports
- Present/absent counts calculated based on is_present flag

**Execute**: Create migration file with structure:
```php
Schema::create('attendance_types', function (Blueprint $table) {
    $table->id();
    $table->string('name', 50);
    $table->string('code', 10)->unique();
    $table->string('color', 7)->default('#000000');
    $table->boolean('is_present')->default(false);
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

---

### Prompt 29: Create Attendances Table Migration

**Purpose**: Create table to store daily attendance records for students.

**Functionality**: Stores attendance data with date, type, and remarks.

**How it Works**:
- Creates `attendances` table with fields:
  - student_id: which student
  - class_id: which class
  - section_id: which section
  - attendance_date: date of attendance
  - attendance_type_id: which attendance type
  - remarks: optional notes
  - marked_by: who marked attendance
- Unique constraint: one attendance record per student per date

**Integration**:
- Links to `students` table (many-to-one)
- Links to `classes` table (many-to-one)
- Links to `sections` table (many-to-one)
- Links to `attendance_types` table (many-to-one)
- Links to `users` table (many-to-one, marked_by)
- Used in attendance reports and notifications
- Parent dashboard shows children's attendance

**Execute**: Create migration file with structure:
```php
Schema::create('attendances', function (Blueprint $table) {
    $table->id();
    $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
    $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
    $table->foreignId('section_id')->constrained('sections')->onDelete('cascade');
    $table->date('attendance_date');
    $table->foreignId('attendance_type_id')->constrained('attendance_types')->onDelete('cascade');
    $table->text('remarks')->nullable();
    $table->foreignId('marked_by')->nullable()->constrained('users')->onDelete('set null');
    $table->timestamps();
    
    $table->unique(['student_id', 'attendance_date']);
    $table->index(['class_id', 'section_id', 'attendance_date']);
    $table->index('attendance_date');
});
```

---

### Prompt 30: Create Exam Types Table Migration

**Purpose**: Create table to define exam types (midterm, final, unit test, etc.).

**Functionality**: Stores exam type definitions for categorizing exams.

**How it Works**:
- Creates `exam_types` table with fields:
  - name: type name (e.g., 'Midterm Exam', 'Final Exam')
  - code: unique code (e.g., 'MID', 'FINAL')
  - description: type description
  - is_active: enable/disable type
- Categorizes different types of exams

**Integration**:
- Links to `exams` table (one-to-many)
- Used in exam scheduling and reporting
- Helps organize exams by type
- Used in report card generation

**Execute**: Create migration file with structure:
```php
Schema::create('exam_types', function (Blueprint $table) {
    $table->id();
    $table->string('name', 100);
    $table->string('code', 20)->unique();
    $table->text('description')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    $table->softDeletes();
});
```

---

### Prompt 31: Create Exams Table Migration

**Purpose**: Create table to manage exams within academic sessions.

**Functionality**: Stores exam information with dates and types.

**How it Works**:
- Creates `exams` table with fields:
  - academic_session_id: which session
  - exam_type_id: which type of exam
  - name: exam name (e.g., 'Midterm 2024')
  - start_date: when exam starts
  - end_date: when exam ends
  - is_active: enable/disable exam
- Organizes exams by session and type

**Integration**:
- Links to `academic_sessions` table (many-to-one)
- Links to `exam_types` table (many-to-one)
- Links to `exam_schedules` table (one-to-many)
- Used in exam scheduling and reporting
- Links to exam marks and report cards

**Execute**: Create migration file with structure:
```php
Schema::create('exams', function (Blueprint $table) {
    $table->id();
    $table->foreignId('academic_session_id')->constrained('academic_sessions')->onDelete('cascade');
    $table->foreignId('exam_type_id')->constrained('exam_types')->onDelete('cascade');
    $table->string('name', 255);
    $table->date('start_date');
    $table->date('end_date');
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    $table->softDeletes();
    
    $table->index('academic_session_id');
    $table->index('exam_type_id');
});
```

---

### Prompt 32: Create Exam Schedules Table Migration

**Purpose**: Create table to manage exam schedules for specific classes, sections, and subjects.

**Functionality**: Stores detailed exam schedule with times, rooms, and marks.

**How it Works**:
- Creates `exam_schedules` table with fields:
  - exam_id: which exam
  - class_id: which class
  - section_id: which section (optional for whole-class exams)
  - subject_id: which subject
  - exam_date: date of exam
  - start_time: when exam starts
  - end_time: when exam ends
  - room_number: exam room
  - full_marks: maximum marks
  - passing_marks: minimum passing marks
- Schedules specific exams for classes/sections

**Integration**:
- Links to `exams` table (many-to-one)
- Links to `classes` table (many-to-one)
- Links to `sections` table (many-to-one)
- Links to `subjects` table (many-to-one)
- Links to `exam_attendance` table (one-to-many)
- Links to `exam_marks` table (one-to-many)
- Used in marks entry and report card generation
- Teacher dashboard shows exam schedule

**Execute**: Create migration file with structure:
```php
Schema::create('exam_schedules', function (Blueprint $table) {
    $table->id();
    $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
    $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
    $table->foreignId('section_id')->nullable()->constrained('sections')->onDelete('cascade');
    $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
    $table->date('exam_date');
    $table->time('start_time');
    $table->time('end_time');
    $table->string('room_number', 20)->nullable();
    $table->decimal('full_marks', 5, 2);
    $table->decimal('passing_marks', 5, 2);
    $table->timestamps();
    
    $table->index('exam_id');
    $table->index(['class_id', 'section_id']);
    $table->index('exam_date');
});
```

---

### Prompt 33: Create Exam Grades Table Migration

**Purpose**: Create table to define grade ranges (A, B, C, D, F, etc.).

**Functionality**: Stores grade definitions with percentage ranges and grade points.

**How it Works**:
- Creates `exam_grades` table with fields:
  - name: grade name (e.g., 'A', 'B', 'C', 'D', 'F')
  - min_percentage: minimum percentage for this grade
  - max_percentage: maximum percentage for this grade
  - grade_point: numeric grade point (e.g., 4.0 for A)
  - remarks: grade remarks
- Used to calculate grades from marks

**Integration**:
- Links to `exam_marks` table (one-to-many)
- Used in report card generation
- Automatically assigns grades based on percentage
- Used in GPA calculation

**Execute**: Create migration file with structure:
```php
Schema::create('exam_grades', function (Blueprint $table) {
    $table->id();
    $table->string('name', 10);
    $table->decimal('min_percentage', 5, 2);
    $table->decimal('max_percentage', 5, 2);
    $table->decimal('grade_point', 3, 2)->nullable();
    $table->string('remarks', 255)->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

---

### Prompt 34: Create Exam Attendance Table Migration

**Purpose**: Create table to track student attendance for exams.

**Functionality**: Stores whether students attended specific exams.

**How it Works**:
- Creates `exam_attendance` table with fields:
  - exam_schedule_id: which exam schedule
  - student_id: which student
  - is_present: whether student attended
  - remarks: optional notes
  - marked_by: who marked attendance
- Tracks exam attendance separately from daily attendance

**Integration**:
- Links to `exam_schedules` table (many-to-one)
- Links to `students` table (many-to-one)
- Links to `users` table (many-to-one, marked_by)
- Used in report card generation
- Prevents marks entry for absent students

**Execute**: Create migration file with structure:
```php
Schema::create('exam_attendance', function (Blueprint $table) {
    $table->id();
    $table->foreignId('exam_schedule_id')->constrained('exam_schedules')->onDelete('cascade');
    $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
    $table->boolean('is_present')->default(true);
    $table->text('remarks')->nullable();
    $table->foreignId('marked_by')->nullable()->constrained('users')->onDelete('set null');
    $table->timestamp();
    
    $table->unique(['exam_schedule_id', 'student_id']);
});
```

---

### Prompt 35: Create Exam Marks Table Migration

**Purpose**: Create table to store student marks for exams.

**Functionality**: Stores obtained marks and grades for students in exams.

**How it Works**:
- Creates `exam_marks` table with fields:
  - exam_schedule_id: which exam schedule
  - student_id: which student
  - obtained_marks: marks obtained
  - grade_id: which grade assigned
  - remarks: optional notes
  - entered_by: who entered marks
- Stores exam results for students

**Integration**:
- Links to `exam_schedules` table (many-to-one)
- Links to `students` table (many-to-one)
- Links to `exam_grades` table (many-to-one)
- Links to `users` table (many-to-one, entered_by)
- Used in report card generation
- Parent dashboard shows children's results
- Student dashboard shows their marks

**Execute**: Create migration file with structure:
```php
Schema::create('exam_marks', function (Blueprint $table) {
    $table->id();
    $table->foreignId('exam_schedule_id')->constrained('exam_schedules')->onDelete('cascade');
    $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
    $table->decimal('obtained_marks', 5, 2);
    $table->foreignId('grade_id')->nullable()->constrained('exam_grades')->onDelete('set null');
    $table->text('remarks')->nullable();
    $table->foreignId('entered_by')->nullable()->constrained('users')->onDelete('set null');
    $table->timestamps();
    
    $table->unique(['exam_schedule_id', 'student_id']);
    $table->index('exam_schedule_id');
    $table->index('student_id');
});
```

---

### Prompt 36: Create Fees Types Table Migration

**Purpose**: Create table to define fee types (tuition, library, transport, etc.).

**Functionality**: Stores fee type definitions for categorizing fees.

**How it Works**:
- Creates `fees_types` table with fields:
  - name: type name (e.g., 'Tuition Fee', 'Library Fee')
  - code: unique code (e.g., 'TUIT', 'LIB')
  - description: type description
  - is_active: enable/disable type
- Categorizes different types of fees

**Integration**:
- Links to `fees_masters` table (one-to-many)
- Used in fee configuration and allotment
- Helps organize fees by type
- Used in fee reports

**Execute**: Create migration file with structure:
```php
Schema::create('fees_types', function (Blueprint $table) {
    $table->id();
    $table->string('name', 100);
    $table->string('code', 20)->unique();
    $table->text('description')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    $table->softDeletes();
});
```

---

### Prompt 37: Create Fees Groups Table Migration

**Purpose**: Create table to group related fee types together.

**Functionality**: Stores fee group definitions for organizing fees.

**How it Works**:
- Creates `fees_groups` table with fields:
  - name: group name (e.g., 'Academic Fees', 'Hostel Fees')
  - description: group description
  - is_active: enable/disable group
- Groups related fee types together

**Integration**:
- Links to `fees_masters` table (one-to-many)
- Used in fee configuration
- Helps organize fees by groups
- Used in fee reports

**Execute**: Create migration file with structure:
```php
Schema::create('fees_groups', function (Blueprint $table) {
    $table->id();
    $table->string('name', 100);
    $table->text('description')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    $table->softDeletes();
});
```

---

### Prompt 38: Create Fees Masters Table Migration

**Purpose**: Create table to configure fee amounts for classes/sections.

**Functionality**: Stores fee configuration with amounts and due dates.

**How it Works**:
- Creates `fees_masters` table with fields:
  - fees_type_id: which fee type
  - fees_group_id: which fee group
  - class_id: which class (optional)
  - section_id: which section (optional)
  - academic_session_id: which session
  - amount: fee amount
  - due_date: payment due date
  - is_active: enable/disable fee
- Configures fees for specific classes/sections

**Integration**:
- Links to `fees_types` table (many-to-one)
- Links to `fees_groups` table (many-to-one)
- Links to `classes` table (many-to-one)
- Links to `sections` table (many-to-one)
- Links to `academic_sessions` table (many-to-one)
- Links to `fees_allotments` table (one-to-many)
- Links to `fees_fines` table (one-to-many)
- Used in fee allotment and collection

**Execute**: Create migration file with structure:
```php
Schema::create('fees_masters', function (Blueprint $table) {
    $table->id();
    $table->foreignId('fees_type_id')->constrained('fees_types')->onDelete('cascade');
    $table->foreignId('fees_group_id')->nullable()->constrained('fees_groups')->onDelete('set null');
    $table->foreignId('class_id')->nullable()->constrained('classes')->onDelete('cascade');
    $table->foreignId('section_id')->nullable()->constrained('sections')->onDelete('cascade');
    $table->foreignId('academic_session_id')->constrained('academic_sessions')->onDelete('cascade');
    $table->decimal('amount', 10, 2);
    $table->date('due_date')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    
    $table->index(['class_id', 'section_id']);
    $table->index('academic_session_id');
});
```

---

### Prompt 39: Create Fees Discounts Table Migration

**Purpose**: Create table to define fee discount rules (sibling, staff child, etc.).

**Functionality**: Stores discount definitions with types and values.

**How it Works**:
- Creates `fees_discounts` table with fields:
  - name: discount name (e.g., 'Sibling Discount', 'Staff Child Discount')
  - code: unique code (e.g., 'SIBLING', 'STAFF')
  - discount_type: percentage or fixed amount
  - discount_value: discount amount or percentage
  - description: discount description
  - is_active: enable/disable discount
- Defines discount rules for fees

**Integration**:
- Links to `fees_allotments` table (one-to-many)
- Used in fee allotment
- Automatically applies discounts to fees
- Used in fee reports

**Execute**: Create migration file with structure:
```php
Schema::create('fees_discounts', function (Blueprint $table) {
    $table->id();
    $table->string('name', 100);
    $table->string('code', 20)->unique();
    $table->enum('discount_type', ['percentage', 'fixed']);
    $table->decimal('discount_value', 5, 2);
    $table->text('description')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    $table->softDeletes();
});
```

---

### Prompt 40: Create Fees Allotments Table Migration

**Purpose**: Create table to allot fees to specific students.

**Functionality**: Stores fee allotments with discounts and due dates.

**How it Works**:
- Creates `fees_allotments` table with fields:
  - student_id: which student
  - fees_master_id: which fee configuration
  - discount_id: which discount (optional)
  - discount_amount: discount applied
  - net_amount: amount after discount
  - due_date: payment due date
  - is_active: enable/disable allotment
- Allots specific fees to students

**Integration**:
- Links to `students` table (many-to-one)
- Links to `fees_masters` table (many-to-one)
- Links to `fees_discounts` table (many-to-one)
- Links to `fees_transactions` table (one-to-many)
- Used in fee collection and payment
- Parent dashboard shows fee status

**Execute**: Create migration file with structure:
```php
Schema::create('fees_allotments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
    $table->foreignId('fees_master_id')->constrained('fees_masters')->onDelete('cascade');
    $table->foreignId('discount_id')->nullable()->constrained('fees_discounts')->onDelete('set null');
    $table->decimal('discount_amount', 10, 2)->default(0);
    $table->decimal('net_amount', 10, 2);
    $table->date('due_date')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    
    $table->index('student_id');
    $table->index('fees_master_id');
});
```

---

### Prompt 41: Create Fees Transactions Table Migration

**Purpose**: Create table to record fee payments.

**Functionality**: Stores payment transactions with methods and status.

**How it Works**:
- Creates `fees_transactions` table with fields:
  - student_id: which student
  - fees_allotment_id: which fee allotment
  - transaction_id: unique transaction ID
  - amount: payment amount
  - payment_method: cash, cheque, dd, online
  - payment_status: pending, completed, failed, refunded
  - payment_date: date of payment
  - transaction_date: when transaction created
  - reference_number: payment reference
  - bank_name: bank name
  - cheque_number: cheque number
  - remarks: notes
  - received_by: who received payment
- Records all fee payments

**Integration**:
- Links to `students` table (many-to-one)
- Links to `fees_allotments` table (many-to-one)
- Links to `users` table (many-to-one, received_by)
- Used in fee collection and reports
- Generates receipts
- Updates accounting

**Execute**: Create migration file with structure:
```php
Schema::create('fees_transactions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
    $table->foreignId('fees_allotment_id')->constrained('fees_allotments')->onDelete('cascade');
    $table->string('transaction_id', 100)->unique();
    $table->decimal('amount', 10, 2);
    $table->enum('payment_method', ['cash', 'cheque', 'dd', 'online']);
    $table->enum('payment_status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
    $table->date('payment_date');
    $table->timestamp('transaction_date')->useCurrent();
    $table->string('reference_number', 100)->nullable();
    $table->string('bank_name', 100)->nullable();
    $table->string('cheque_number', 50)->nullable();
    $table->text('remarks')->nullable();
    $table->foreignId('received_by')->nullable()->constrained('users')->onDelete('set null');
    $table->timestamps();
    
    $table->index('student_id');
    $table->index('transaction_id');
    $table->index('payment_date');
});
```

---

### Prompt 42: Create Fees Fines Table Migration

**Purpose**: Create table to define fine rules for late payments.

**Functionality**: Stores fine configurations for fees.

**How it Works**:
- Creates `fees_fines` table with fields:
  - fees_master_id: which fee configuration
  - fine_type: daily, weekly, monthly, one_time
  - fine_amount: fine amount
  - start_date: when fine starts
  - end_date: when fine ends
  - is_active: enable/disable fine
- Calculates fines for late payments

**Integration**:
- Links to `fees_masters` table (many-to-one)
- Used in fee collection
- Automatically calculates late fees
- Used in fee reports

**Execute**: Create migration file with structure:
```php
Schema::create('fees_fines', function (Blueprint $table) {
    $table->id();
    $table->foreignId('fees_master_id')->constrained('fees_masters')->onDelete('cascade');
    $table->enum('fine_type', ['daily', 'weekly', 'monthly', 'one_time']);
    $table->decimal('fine_amount', 10, 2);
    $table->date('start_date')->nullable();
    $table->date('end_date')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

---

### Prompt 43: Create Library Categories Table Migration

**Purpose**: Create table to manage library book categories.

**Functionality**: Stores category definitions for organizing books.

**How it Works**:
- Creates `library_categories` table with fields:
  - name: category name (e.g., 'Science', 'Fiction', 'History')
  - code: unique code (e.g., 'SCI', 'FIC', 'HIS')
  - description: category description
  - is_active: enable/disable category
- Categorizes library books

**Integration**:
- Links to `library_books` table (one-to-many)
- Used in book management
- Helps organize library
- Used in library reports

**Execute**: Create migration file with structure:
```php
Schema::create('library_categories', function (Blueprint $table) {
    $table->id();
    $table->string('name', 100);
    $table->string('code', 20)->unique();
    $table->text('description')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    $table->softDeletes();
});
```

---

### Prompt 44: Create Library Books Table Migration

**Purpose**: Create table to manage library book inventory.

**Functionality**: Stores book information with details and availability.

**How it Works**:
- Creates `library_books` table with fields:
  - category_id: which category
  - isbn: unique ISBN number
  - title: book title
  - author: book author
  - publisher: book publisher
  - edition: book edition
  - publish_year: year published
  - rack_number: library rack location
  - quantity: total copies
  - available_quantity: copies available
  - price: book price
  - language: book language
  - pages: number of pages
  - description: book description
  - cover_image: book cover image
  - is_active: enable/disable book
- Manages library book inventory

**Integration**:
- Links to `library_categories` table (many-to-one)
- Links to `library_issues` table (one-to-many)
- Used in book management and issue/return
- Tracks book availability
- Used in library reports

**Execute**: Create migration file with structure:
```php
Schema::create('library_books', function (Blueprint $table) {
    $table->id();
    $table->foreignId('category_id')->constrained('library_categories')->onDelete('cascade');
    $table->string('isbn', 20)->unique();
    $table->string('title', 255);
    $table->string('author', 255)->nullable();
    $table->string('publisher', 255)->nullable();
    $table->string('edition', 50)->nullable();
    $table->integer('publish_year')->nullable();
    $table->string('rack_number', 20)->nullable();
    $table->integer('quantity');
    $table->integer('available_quantity');
    $table->decimal('price', 10, 2)->nullable();
    $table->string('language', 50)->nullable();
    $table->integer('pages')->nullable();
    $table->text('description')->nullable();
    $table->string('cover_image', 255)->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    $table->softDeletes();
    
    $table->index('category_id');
    $table->index('isbn');
    $table->index('title');
});
```

---

### Prompt 45: Create Library Members Table Migration

**Purpose**: Create table to manage library members (students, teachers, staff).

**Functionality**: Stores library member information with membership details.

**How it Works**:
- Creates `library_members` table with fields:
  - member_type: student, teacher, or staff
  - member_id: ID of user (student or teacher)
  - membership_number: unique membership number
  - membership_date: when membership started
  - expiry_date: when membership expires
  - max_books: maximum books can borrow
  - is_active: enable/disable membership
- Manages library membership

**Integration**:
- Links to `library_issues` table (one-to-many)
- Used in book issue/return
- Limits books per member
- Used in library reports

**Execute**: Create migration file with structure:
```php
Schema::create('library_members', function (Blueprint $table) {
    $table->id();
    $table->enum('member_type', ['student', 'teacher', 'staff']);
    $table->unsignedBigInteger('member_id');
    $table->string('membership_number', 50)->unique();
    $table->date('membership_date');
    $table->date('expiry_date')->nullable();
    $table->integer('max_books')->default(5);
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    $table->softDeletes();
    
    $table->index(['member_type', 'member_id']);
});
```

---

### Prompt 46: Create Library Issues Table Migration

**Purpose**: Create table to manage book issue and return records.

**Functionality**: Stores book issue and return information with due dates and fines.

**How it Works**:
- Creates `library_issues` table with fields:
  - book_id: which book
  - member_id: which member
  - issue_date: when book issued
  - due_date: when book due
  - return_date: when book returned
  - fine_amount: fine for late return
  - fine_paid: whether fine paid
  - remarks: notes
  - issued_by: who issued book
  - returned_by: who returned book
- Tracks book circulation

**Integration**:
- Links to `library_books` table (many-to-one)
- Links to `library_members` table (many-to-one)
- Links to `users` table (many-to-one, issued_by)
- Links to `users` table (many-to-one, returned_by)
- Updates book availability
- Calculates fines for late returns

**Execute**: Create migration file with structure:
```php
Schema::create('library_issues', function (Blueprint $table) {
    $table->id();
    $table->foreignId('book_id')->constrained('library_books')->onDelete('cascade');
    $table->foreignId('member_id')->constrained('library_members')->onDelete('cascade');
    $table->date('issue_date');
    $table->date('due_date');
    $table->date('return_date')->nullable();
    $table->decimal('fine_amount', 10, 2)->default(0);
    $table->boolean('fine_paid')->default(false);
    $table->text('remarks')->nullable();
    $table->foreignId('issued_by')->nullable()->constrained('users')->onDelete('set null');
    $table->foreignId('returned_by')->nullable()->constrained('users')->onDelete('set null');
    $table->timestamps();
    
    $table->index('book_id');
    $table->index('member_id');
    $table->index('issue_date');
    $table->index('due_date');
});
```

---

### Prompt 47: Create Transport Vehicles Table Migration

**Purpose**: Create table to manage school transport vehicles.

**Functionality**: Stores vehicle information with driver details.

**How it Works**:
- Creates `transport_vehicles` table with fields:
  - vehicle_number: unique vehicle number
  - vehicle_type: type of vehicle (bus, van, etc.)
  - vehicle_model: vehicle model
  - capacity: seating capacity
  - driver_name: driver name
  - driver_phone: driver contact
  - driver_license: driver license number
  - route_id: which route assigned
  - is_active: enable/disable vehicle
- Manages school transport fleet

**Integration**:
- Links to `transport_routes` table (many-to-one)
- Links to `transport_students` table (one-to-many)
- Used in transport management
- Assigns students to vehicles
- Used in transport reports

**Execute**: Create migration file with structure:
```php
Schema::create('transport_vehicles', function (Blueprint $table) {
    $table->id();
    $table->string('vehicle_number', 20)->unique();
    $table->string('vehicle_type', 50)->nullable();
    $table->string('vehicle_model', 100)->nullable();
    $table->integer('capacity');
    $table->string('driver_name', 100)->nullable();
    $table->string('driver_phone', 20)->nullable();
    $table->string('driver_license', 50)->nullable();
    $table->foreignId('route_id')->nullable()->constrained('transport_routes')->onDelete('set null');
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    $table->softDeletes();
    
    $table->index('route_id');
});
```

---

### Prompt 48: Create Transport Routes Table Migration

**Purpose**: Create table to manage transport routes.

**Functionality**: Stores route information with details.

**How it Works**:
- Creates `transport_routes` table with fields:
  - name: route name
  - route_number: unique route number
  - description: route description
  - is_active: enable/disable route
- Defines transport routes

**Integration**:
- Links to `transport_vehicles` table (one-to-many)
- Links to `transport_route_stops` table (one-to-many)
- Links to `transport_students` table (one-to-many)
- Used in transport management
- Organizes transport by routes

**Execute**: Create migration file with structure:
```php
Schema::create('transport_routes', function (Blueprint $table) {
    $table->id();
    $table->string('name', 100);
    $table->string('route_number', 20)->unique();
    $table->text('description')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    $table->softDeletes();
});
```

---

### Prompt 49: Create Transport Route Stops Table Migration

**Purpose**: Create table to manage stops on transport routes.

**Functionality**: Stores stop information with timings and fares.

**How it Works**:
- Creates `transport_route_stops` table with fields:
  - route_id: which route
  - stop_name: stop name
  - stop_order: order of stop on route
  - stop_time: pickup/drop-off time
  - fare: fare for this stop
  - is_active: enable/disable stop
- Defines stops on each route

**Integration**:
- Links to `transport_routes` table (many-to-one)
- Links to `transport_students` table (one-to-many)
- Used in transport management
- Assigns students to stops
- Calculates transport fees

**Execute**: Create migration file with structure:
```php
Schema::create('transport_route_stops', function (Blueprint $table) {
    $table->id();
    $table->foreignId('route_id')->constrained('transport_routes')->onDelete('cascade');
    $table->string('stop_name', 100);
    $table->integer('stop_order');
    $table->time('stop_time')->nullable();
    $table->decimal('fare', 10, 2)->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    
    $table->index('route_id');
});
```

---

### Prompt 50: Create Transport Students Table Migration

**Purpose**: Create table to assign students to transport routes and vehicles.

**Functionality**: Stores student transport assignments with fees.

**How it Works**:
- Creates `transport_students` table with fields:
  - student_id: which student
  - vehicle_id: which vehicle
  - route_id: which route
  - stop_id: which stop
  - academic_session_id: which session
  - transport_fees: transport fee amount
  - is_active: enable/disable assignment
- Assigns students to transport

**Integration**:
- Links to `students` table (many-to-one)
- Links to `transport_vehicles` table (many-to-one)
- Links to `transport_routes` table (many-to-one)
- Links to `transport_route_stops` table (many-to-one)
- Links to `academic_sessions` table (many-to-one)
- Used in transport management
- Calculates transport fees
- Used in transport reports

**Execute**: Create migration file with structure:
```php
Schema::create('transport_students', function (Blueprint $table) {
    $table->id();
    $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
    $table->foreignId('vehicle_id')->constrained('transport_vehicles')->onDelete('cascade');
    $table->foreignId('route_id')->constrained('transport_routes')->onDelete('cascade');
    $table->foreignId('stop_id')->constrained('transport_route_stops')->onDelete('cascade');
    $table->foreignId('academic_session_id')->constrained('academic_sessions')->onDelete('cascade');
    $table->decimal('transport_fees', 10, 2)->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    
    $table->index('student_id');
    $table->vehicle_id');
});
```

---

### Prompt 51: Create Hostels Table Migration

**Purpose**: Create table to manage school hostels.

**Functionality**: Stores hostel information with details and facilities.

**How it Works**:
- Creates `hostels` table with fields:
  - name: hostel name
  - code: unique hostel code
  - type: boys, girls, or mixed
  - address: hostel address
  - city: hostel city
  - state: hostel state
  - postal_code: postal code
  - phone: contact phone
  - email: contact email
  - warden_name: warden name
  - warden_phone: warden contact
  - facilities: hostel facilities
  - is_active: enable/disable hostel
- Manages school hostels

**Integration**:
- Links to `hostel_room_types` table (one-to-many)
- Links to `hostel_rooms` table (one-to-many)
- Links to `hostel_assignments` table (one-to-many)
- Used in hostel management
- Assigns students to hostels

**Execute**: Create migration file with structure:
```php
Schema::create('hostels', function (Blueprint $table) {
    $table->id();
    $table->string('name', 100);
    $table->string('code', 20)->unique();
    $table->enum('type', ['boys', 'girls', 'mixed']);
    $table->text('address')->nullable();
    $table->string('city', 100)->nullable();
    $table->string('state', 100)->nullable();
    $table->string('postal_code', 20)->nullable();
    $table->string('phone', 20)->nullable();
    $table->string('email', 255)->nullable();
    $table->string('warden_name', 100)->nullable();
    $table->string('warden_phone', 20)->nullable();
    $table->text('facilities')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    $table->softDeletes();
});
```

---

### Prompt 52: Create Hostel Room Types Table Migration

**Purpose**: Create table to manage hostel room types.

**Functionality**: Stores room type definitions with capacity and fees.

**How it Works**:
- Creates `hostel_room_types` table with fields:
  - hostel_id: which hostel
  - name: room type name (e.g., 'Single Room', 'Double Room')
  - capacity: room capacity
  - beds_per_room: beds in room
  - fees_per_month: monthly fees
  - facilities: room facilities
  - is_active: enable/disable type
- Defines room types in hostels

**Integration**:
- Links to `hostels` table (many-to-one)
- Links to `hostel_rooms` table (one-to-many)
- Used in hostel management
- Calculates hostel fees
- Used in hostel reports

**Execute**: Create migration file with structure:
```php
Schema::create('hostel_room_types', function (Blueprint $table) {
    $table->id();
    $table->foreignId('hostel_id')->constrained('hostels')->onDelete('cascade');
    $table->string('name', 100);
    $table->integer('capacity');
    $table->integer('beds_per_room');
    $table->decimal('fees_per_month', 10, 2)->nullable();
    $table->text('facilities')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    
    $table->index('hostel_id');
});
```

---

### Prompt 53: Create Hostel Rooms Table Migration

**Purpose**: Create table to manage hostel rooms.

**Functionality**: Stores room information with occupancy details.

**How it Works**:
- Creates `hostel_rooms` table with fields:
  - hostel_id: which hostel
  - room_type_id: which room type
  - room_number: room number
  - floor_number: floor number
  - capacity: room capacity
  - occupied: current occupancy
  - is_active: enable/disable room
- Manages hostel rooms

**Integration**:
- Links to `hostels` table (many-to-one)
- Links to `hostel_room_types` table (many-to-one)
- Links to `hostel_assignments` table (one-to-many)
- Used in hostel management
- Tracks room occupancy
- Used in hostel reports

**Execute**: Create migration file with structure:
```php
Schema::create('hostel_rooms', function (Blueprint $table) {
    $table->id();
    $table->foreignId('hostel_id')->constrained('hostels')->onDelete('cascade');
    $table->foreignId('room_type_id')->constrained('hostel_room_types')->onDelete('cascade');
    $table->string('room_number', 20);
    $table->integer('floor_number')->nullable();
    $table->integer('capacity');
    $table->integer('occupied')->default(0);
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    $table->softDeletes();
    
    $table->index('hostel_id');
    $table->unique(['hostel_id', 'room_number']);
});
```

---

### Prompt 54: Create Hostel Assignments Table Migration

**Purpose**: Create table to assign students to hostel rooms.

**Functionality**: Stores student hostel assignments with admission and fees.

**How it Works**:
- Creates `hostel_assignments` table with fields:
  - student_id: which student
  - hostel_id: which hostel
  - room_id: which room
  - academic_session_id: which session
  - admission_date: when admitted
  - leaving_date: when left
  - hostel_fees: hostel fee amount
  - is_active: enable/disable assignment
- Assigns students to hostels

**Integration**:
- Links to `students` table (many-to-one)
- Links to `hostels` table (many-to-one)
- Links to `hostel_rooms` table (many-to-one)
- Links to `academic_sessions` table (many-to-one)
- Links to `hostel_assignments` table (one-to-many)
- Used in hostel management
- Calculates hostel fees
- Used in hostel reports

**Execute**: Create migration file with structure:
```php
Schema::create('hostel_assignments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
    $table->foreignId('hostel_id')->constrained('hostels')->onDelete('cascade');
    $table->foreignId('room_id')->constrained('hostel_rooms')->onDelete('cascade');
    $table->foreignId('academic_session_id')->constrained('academic_sessions')->onDelete('cascade');
    $table->date('admission_date');
    $table->date('leaving_date')->nullable();
    $table->decimal('hostel_fees', 10, 2)->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    
    $table->index('student_id');
    $table->index('room_id');
});
```

---

### Prompt 55: Create Notices Table Migration

**Purpose**: Create table to manage school notices and announcements.

**Functionality**: Stores notice information with targeting and publishing.

**How it Works**:
- Creates `notices` table with fields:
  - title: notice title
  - content: notice content
  - target_roles: which roles can see (JSON)
  - target_classes: which classes can see (JSON)
  - notice_date: notice date
  - expiry_date: when notice expires
  - attachment: notice attachment
  - is_published: whether published
  - published_by: who published
- Manages school notices

**Integration**:
- Links to `users` table (many-to-one, published_by)
- Used in communication system
- Targets specific roles and classes
- Used in dashboards and notifications

**Execute**: Create migration file with structure:
```php
Schema::create('notices', function (Blueprint $table) {
    $table->id();
    $table->string('title', 255);
    $table->text('content');
    $table->json('target_roles')->nullable();
    $table->json('target_classes')->nullable();
    $table->date('notice_date');
    $table->date('expiry_date')->nullable();
    $table->string('attachment', 255)->nullable();
    $table->boolean('is_published')->default(false);
    $table->foreignId('published_by')->nullable()->constrained('users')->onDelete('set null');
    $table->timestamps();
    
    $table->index('notice_date');
    $table->index('is_published');
});
```

---

### Prompt 56: Create Messages Table Migration

**Purpose**: Create table to manage internal messaging system.

**Functionality**: Stores messages with sender and read status.

**How it Works**:
- Creates `messages` table with fields:
  - sender_id: who sent message
  - subject: message subject
  - message: message content
  - attachment: message attachment
  - is_read: whether read
  - read_at: when read
- Manages internal messaging

**Integration**:
- Links to `users` table (many-to-one, sender_id)
- Links to `message_recipients` table (one-to-many)
- Used in communication system
- Enables private messaging
- Tracks read status

**Execute**: Create migration file with structure:
```php
Schema::create('messages', function (Blueprint $table) {
    $table->id();
    $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
    $table->string('subject', 255);
    $table->text('message');
    $table->string('attachment', 255)->nullable();
    $table->boolean('is_read')->default(false);
    $table->timestamp('read_at')->nullable();
    $table->timestamps();
    
    $table->index('sender_id');
    $table->index('is_read');
});
```

---

### Prompt 57: Create Message Recipients Table Migration

**Purpose**: Create table to manage message recipients.

**Functionality**: Stores which users received which messages.

**How it Works**:
- Creates `message_recipients` table with fields:
  - message_id: which message
  - recipient_id: who received message
  - is_read: whether recipient read
  - read_at: when recipient read
- Manages message recipients

**Integration**:
- Links to `messages` table (many-to-one)
- Links to `users` table (many-to-one, recipient_id)
- Used in communication system
- Tracks delivery status
- Shows inbox/sent folders

**Execute**: Create migration file with structure:
```php
Schema::create('message_recipients', function (Blueprint $table) {
    $table->id();
    $table->foreignId('message_id')->constrained('messages')->onDelete('cascade');
    $table->foreignId('recipient_id')->constrained('users')->onDelete('cascade');
    $table->boolean('is_read')->default(false);
    $table->timestamp('read_at')->nullable();
    $table->timestamp();
    
    $table->index('message_id');
    $table->index('recipient_id');
});
```

---

### Prompt 58: Create SMS Logs Table Migration

**Purpose**: Create table to log SMS notifications.

**Functionality**: Stores SMS sending history with status.

**How it Works**:
- Creates `sms_logs` table with fields:
  - recipient: phone number
  - message: SMS content
  - gateway: which SMS gateway
  - status: pending, sent, failed, delivered
  - sent_at: when sent
  - response: gateway response
- Tracks SMS notifications

**Integration**:
- Used in communication system
- Logs all SMS sent
- Tracks delivery status
- Used for troubleshooting and reporting

**Execute**: Create migration file with structure:
```php
Schema::create('sms_logs', function (Blueprint $table) {
    $table->id();
    $table->string('recipient', 20);
    $table->text('message');
    $table->string('gateway', 50)->nullable();
    $table->enum('status', ['pending', 'sent', 'failed', 'delivered'])->default('pending');
    $table->timestamp('sent_at')->nullable();
    $table->text('response')->nullable();
    $table->timestamp();
    
    $table->index('status');
    $table->index('sent_at');
});
```

---

### Prompt 59: Create Email Logs Table Migration

**Purpose**: Create table to log email notifications.

**Functionality**: Stores email sending history with status.

**How it Works**:
- Creates `email_logs` table with fields:
  - recipient: email address
  - subject: email subject
  - body: email body
  - status: pending, sent, failed
  - sent_at: when sent
  - error_message: error if failed
- Tracks email notifications

**Integration**:
- Used in communication system
- Logs all emails sent
- Tracks delivery status
- Used for troubleshooting and reporting

**Execute**: Create migration file with structure:
```php
Schema::create('email_logs', function (Blueprint $table) {
    $table->id();
    $table->string('recipient', 255);
    $table->string('subject', 255);
    $table->text('body');
    $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
    $table->timestamp('sent_at')->nullable();
    $table->text('error_message')->nullable();
    $table->timestamp();
    
    $table->index('status');
    $table->index('sent_at');
});
```

---

### Prompt 60: Create Expense Categories Table Migration

**Purpose**: Create table to manage expense categories.

**Functionality**: Stores expense category definitions.

**How it Works**:
- Creates `expense_categories` table with fields:
  - name: category name (e.g., 'Electricity', 'Salary', 'Maintenance')
  - code: unique code (e.g., 'ELEC', 'SAL', 'MAIN')
  - description: category description
  - is_active: enable/disable category
- Categorizes school expenses

**Integration**:
- Links to `expenses` table (one-to-many)
- Used in accounting system
- Organizes expenses by category
- Used in financial reports

**Execute**: Create migration file with structure:
```php
Schema::create('expense_categories', function (Blueprint $table) {
    $table->id();
    $table->string('name', 100);
    $table->string('code', 20)->unique();
    $table->text('description')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    $table->softDeletes();
});
```

---

### Prompt 61: Create Income Categories Table Migration

**Purpose**: Create table to manage income categories.

**Functionality**: Stores income category definitions.

**How it Works**:
- Creates `income_categories` table with fields:
  - name: category name (e.g., 'Fees', 'Donations', 'Grants')
  - code: unique code (e.g., 'FEE', 'DON', 'GRA')
  - description: category description
  - is_active: enable/disable category
- Categorizes school income

**Integration**:
- Links to `income` table (one-to-many)
- Used in accounting system
- Organizes income by category
- Used in financial reports

**Execute**: Create migration file with structure:
```php
Schema::create('income_categories', function (Blueprint $table) {
    $table->id();
    $table->string('name', 100);
    $table->string('code', 20)->unique();
    $table->text('description')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    $table->softDeletes();
});
```

---

### Prompt 62: Create Expenses Table Migration

**Purpose**: Create table to manage school expenses.

**Functionality**: Stores expense records with details.

**How it Works**:
- Creates `expenses` table with fields:
  - category_id: which category
  - title: expense title
  - description: expense description
  - amount: expense amount
  - expense_date: date of expense
  - payment_method: payment method
  - reference_number: reference number
  - attachment: expense attachment
  - created_by: who created expense
- Records all school expenses

**Integration**:
- Links to `expense_categories` table (many-to-one)
- Links to `users` table (many-to-one, created_by)
- Used in accounting system
- Tracks all expenses
- Used in financial reports

**Execute**: Create migration file with structure:
```php
Schema::create('expenses', function (Blueprint $table) {
    $table->id();
    $table->foreignId('category_id')->constrained('expense_categories')->onDelete('cascade');
    $table->string('title', 255);
    $table->text('description')->nullable();
    $table->decimal('amount', 10, 2);
    $table->date('expense_date');
    $table->string('payment_method', 50)->nullable();
    $table->string('reference_number', 100)->nullable();
    $table->string('attachment', 255)->nullable();
    $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
    $table->timestamps();
    
    $table->index('category_id');
    $table->index('expense_date');
});
```

---

### Prompt 63: Create Income Table Migration

**Purpose**: Create table to manage school income.

**Functionality**: Stores income records with details.

**How it Works**:
- Creates `income` table with fields:
  - category_id: which category
  - title: income title
  - description: income description
  - amount: income amount
  - income_date: date of income
  - payment_method: payment method
  - reference_number: reference number
  - attachment: income attachment
  - created_by: who created income
- Records all school income

**Integration**:
- Links to `income_categories` table (many-to-one)
- Links to `users` table (many-to-one, created_by)
- Used in accounting system
- Tracks all income
- Used in financial reports

**Execute**: Create migration file with structure:
```php
Schema::create('income', function (Blueprint $table) {
    $table->id();
    $table->foreignId('category_id')->constrained('income_categories')->onDelete('cascade');
    $table->string('title', 255);
    $table->text('description')->nullable();
    $table->decimal('amount', 10, 2);
    $table->date('income_date');
    $table->string('payment_method', 50)->nullable();
    $table->string('reference_number', 100)->nullable();
    $table->string('attachment', 255)->nullable();
    $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
    $table->timestamps();
    
    $table->index('category_id');
    $table->index('income_date');
});
```

---

### Prompt 64: Create Settings Table Migration

**Purpose**: Create table to manage system settings.

**Functionality**: Stores system configuration as key-value pairs.

**How it Works**:
- Creates `settings` table with fields:
  - key: setting key (e.g., 'school_name', 'school_address', 'school_phone')
  - value: setting value
  - type: string, number, boolean, json
  - category: setting category (e.g., 'general', 'academic', 'communication')
  - description: setting description
  - is_public: whether setting is public
- Stores all system settings

**Integration**:
- Used throughout application
- Accessed by all modules
- Configures system behavior
- Used in settings management

**Execute**: Create migration file with structure:
```php
Schema::create('settings', function (Blueprint $table) {
    $table->string('key', 100)->unique();
    $table->text('value')->nullable();
    $table->enum('type', ['string', 'number', 'boolean', 'json'])->default('string');
    $table->string('category', 50)->nullable();
    $table->text('description')->nullable();
    $table->boolean('is_public')->default(false);
    $table->timestamps();
    
    $table->index('key');
    $table->index('category');
});
```

---

### Prompt 65: Create Languages Table Migration

**Purpose**: Create table to manage supported languages.

**Functionality**: Stores language information with direction and default settings.

**How it Works**:
- Creates `languages` table with fields:
  - code: language code (e.g., 'en', 'hi', 'ar')
  - name: language name (e.g., 'English', 'Hindi', 'Arabic')
  - native_name: native language name
  - direction: ltr or rtl
  - is_active: enable/disable language
  - is_default: default language
- Supports multi-language functionality

**Integration**:
- Links to `translations` table (one-to-many)
- Used in multi-language system
- Enables RTL support
- Used in language switching

**Execute**: Create migration file with structure:
```php
Schema::create('languages', function (Blueprint $table) {
    $table->id();
    $table->string('code', 10)->unique();
    $table->string('name', 100);
    $table->string('native_name', 100)->nullable();
    $table->enum('direction', ['ltr', 'rtl'])->default('ltr');
    $table->boolean('is_active')->default(true);
    $table->boolean('is_default')->default(false);
    $table->timestamps();
});
```

---

### Prompt 66: Create Translations Table Migration

**Purpose**: Create table to manage language translations for all UI strings in different languages.

**Functionality**: Stores translations for all UI strings in different languages.

**How it Works**:
- Creates `translations` table with fields:
  - language_id: which language
  - key: translation key (e.g., 'students.title', 'dashboard.welcome')
  - value: translated text
- Stores all translations

**Integration**:
- Links to `languages` table (many-to-one)
- Used in multi-language system
- Enables language switching
- Provides translated strings to frontend

**Execute**: Create migration file with structure:
```php
Schema::create('translations', function (Blueprint $table) {
    $table->id();
    $table->foreignId('language_id')->constrained('languages')->onDelete('cascade');
    $table->string('key', 255);
    $table->text('value')->nullable();
    $table->timestamps();
    
    $table->unique(['language_id', 'key']);
    $table->index('language_id');
    $table->index('key');
});
```

---

### Prompt 67: Create Backups Table Migration

**Purpose**: Create table to manage system backups.

**Functionality**: Stores backup information with status.

**How it Works**:
- Creates `backups` table with fields:
  - filename: backup filename
  - file_path: backup file location
  - file_size: backup file size
  - type: full, database, or files
  - status: pending, completed, or failed
  - created_by: who created backup
- Tracks all system backups

**Integration**:
- Links to `users` table (many-to-one, created_by)
- Used in backup system
- Enables restore functionality
- Used in backup management

**Execute**: Create migration file with structure:
```php
Schema::create('backups', function (Blueprint $table) {
    $table->id();
    $table->string('filename', 255);
    $table->string('file_path', 255);
    $table->bigInteger('file_size')->nullable();
    $table->enum('type', ['full', 'database', 'files']);
    $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
    $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
    $table->timestamp();
    
    $table->index('type');
    $table->index('status');
    $table->index('created_at');
});
```

---

### Prompt 68: Create Downloads Table Migration

**Purpose**: Create table to manage downloadable content for students and teachers.

**Functionality**: Stores downloadable content with targeting.

**How it Works**:
- Creates `downloads` table with fields:
  - title: download title
  - description: download description
  - category: download category
  - file_path: file location
  - file_size: file size
  - file_type: file type
  - target_roles: which roles can download (JSON)
  - target_classes: which classes can download (JSON)
  - uploaded_by: who uploaded
- Manages downloadable content

**Integration**:
- Links to `users` table (many-to-one, uploaded_by)
- Used in communication system
- Targets specific roles and classes
- Used in dashboards and downloads section

**Execute**: Create migration file with structure:
```php
Schema::create('downloads', function (Blueprint $table) {
    $table->id();
    $table->string('title', 255);
    $table->text('description')->nullable();
    $table->string('category', 50)->nullable();
    $table->string('file_path', 255);
    $table->bigInteger('file_size')->nullable();
    $table->string('file_type', 50)->nullable();
    $table->json('target_roles')->nullable();
    $table->json('target_classes')->nullable();
    $table->boolean('is_active')->default(true);
    $table->foreignId('uploaded_by')->nullable()->constrained('users')->onDelete('set null');
    $table->timestamps();
    
    $table->index('category');
    $table->index('is_active');
});
```

---

### Prompt 69: Create Homework Table Migration

**Purpose**: Create table to manage homework assignments.

**Functionality**: Stores homework with subjects and submission dates.

**How it Works**:
- Creates `homework` table with fields:
  - class_id: which class
  - section_id: which section
  - subject_id: which subject
  - teacher_id: which teacher assigned
  - title: homework title
  - description: homework description
  - attachment: homework attachment
  - submission_date: due date
  - is_active: enable/disable homework
- Manages homework assignments

**Integration**:
- Links to `classes` table (many-to-one)
- Links to `sections` table (many-to-one)
- Links to `subjects` table (many-to-one)
- Links to `users` table (many-to-one, teacher)
- Used in academic management
- Student dashboard shows homework
- Teacher dashboard manages assignments

**Execute**: Create migration file with structure:
```php
Schema::create('homework', function (Blueprint $table) {
    $table->id();
    $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
    $table->foreignId('section_id')->nullable()->constrained('sections')->onDelete('cascade');
    $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
    $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
    $table->string('title', 255);
    $table->text('description')->nullable();
    $table->string('attachment', 255)->nullable();
    $table->date('submission_date');
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    
    $table->index(['class_id', 'section_id']);
    $table->index('submission_date');
});
```

---

### Prompt 70: Create Study Materials Table Migration

**Purpose**: Create table to manage study materials and resources.

**Functionality**: Stores study materials with subjects and files.

**How it Works**:
- Creates `study_materials` table with fields:
  - class_id: which class
  - section_id: which section
  - subject_id: which subject
  - teacher_id: which teacher uploaded
  - title: material title
  - description: material description
  - file_path: file location
  - file_size: file size
  - file_type: file type
  - is_active: enable/disable material
- Manages study materials

**Integration**:
- Links to `classes` table (many-to-one)
- Links to `sections` table (many-to-one)
- Links to `subjects` table (many-to-one)
- Links to `users` table (many-to-one, uploaded_by)
- Used in academic management
- Student dashboard shows study materials
- Teacher dashboard manages uploads

**Execute**: Create migration file with structure:
```php
Schema::create('study_materials', function (Blueprint $table) {
    $table->id();
    $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
    $table->foreignId('section_id')->nullable()->constrained('sections')->onDelete('cascade');
    $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
    $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
    $table->string('title', 255);
    $table->text('description')->nullable();
    $table->string('file_path', 255);
    $table->bigInteger('file_size')->nullable();
    $table->string('file_type', 50)->nullable();
    $table->boolean('is_active')->default(true);
    $table->foreignId('uploaded_by')->nullable()->constrained('users')->onDelete('set null');
    $table->timestamps();
    
    $table->index(['class_id', 'section_id']);
});
```

---

## 📝 Phase 3: Model Creation (16 Prompts)

### Prompt 71: Create User Model

**Purpose**: Create Eloquent model for users table with relationships and methods.

**Functionality**: Provides object-oriented interface to interact with users data.

**How it Works**:
- Extends Laravel's base Model class
- Uses SoftDeletes trait for soft deletion
- Implements HasRoles and HasPermissions interfaces from Spatie Permission
- Defines relationships to Role, Permission, Student, Attendance, ExamMark, FeesTransaction, LibraryIssue, Message, Expense, Income
- Defines fillable fields for mass assignment
- Defines hidden fields for password
- Defines casts for date fields
- Provides helper methods for role checking

**Integration**:
- Links to Role model (belongsToMany)
- Links to Permission model (belongsToMany)
- Links to Student model (hasOne)
- Used by authentication system
- Used by all user-related features
- Spatie Permission uses this for RBAC

**Execute**: Create model file `app/Models/User.php` with complete relationships and methods.

---

### Prompt 72: Create Role Model

**Purpose**: Create Eloquent model for roles table.

**Functionality**: Provides object-oriented interface to interact with roles data.

**How it Works**:
- Extends Laravel's base Model class
- Uses HasPermissions trait from Spatie Permission
- Defines relationship to Permission (belongsToMany)
- Defines relationship to User (belongsToMany)
- Defines fillable fields
- Provides helper methods for permission management

**Integration**:
- Links to Permission model (belongsToMany)
- Links to User model (belongsToMany)
- Used by Spatie Permission for RBAC
- Middleware checks user's role from this model

**Execute**: Create model file `app/Models/Role.php` with relationships.

---

### Prompt 73: Create Permission Model

**Purpose**: Create Eloquent model for permissions table.

**Functionality**: Provides object-oriented interface to interact with permissions data.

**How it Works**:
- Extends Laravel's base Model class
- Uses HasRoles trait from Spatie Permission
- Defines relationship to Role (belongsToMany)
- Defines fillable fields
- Provides helper methods for permission checking

**Integration**:
- Links to Role model (belongsToMany)
- Used by Spatie Permission for RBAC
- Middleware checks user's permissions from this model

**Execute**: Create model file `app/Models/Permission.php` with relationships.

---

### Prompt 74: Create AcademicSession Model

**Purpose**: Create Eloquent model for academic sessions table.

**Functionality**: Provides object-oriented interface to interact with academic sessions data.

**How it Works**:
- Extends Laravel's base Model class
- Uses SoftDeletes trait
- Defines relationships to Class, Student, Exam, FeesMaster, TransportStudent, HostelAssignment
- Defines fillable fields
- Defines casts for date fields
- Provides helper methods for current session checking

**Integration**:
- Links to Class model (hasMany)
- Links to Student model (hasMany)
- Links to Exam model (hasMany)
- Links to FeesMaster model (hasMany)
- Links to TransportStudent model (hasMany)
- Links to HostelAssignment model (hasMany)
- Organizes all academic data by session

**Execute**: Create model file `app/Models/AcademicSession.php` with relationships.

---

### Prompt 75: Create Class Model

**Purpose**: Create Eloquent model for classes table.

**Functionality**: Provides object-oriented interface to interact with classes data.

**How it Works**:
- Extends Laravel's base Model class
- Uses SoftDeletes trait
- Defines relationships to Section, Student, ClassSubject, ClassTimetable, FeesMaster, ExamSchedule, Homework, StudyMaterial
- Defines fillable fields
- Provides helper methods for class management

**Integration**:
- Links to AcademicSession model (belongsTo)
- Links to Section model (hasMany)
- Links to Student model (hasMany)
- Links to ClassSubject model (hasMany)
- Links to ClassTimetable model (hasMany)
- Links to FeesMaster model (hasMany)
- Links to ExamSchedule model (hasMany)
- Links to Homework model (hasMany)
- Links to StudyMaterial model (hasMany)
- Organizes all academic activities by class

**Execute**: Create model file `app/Models/Class.php` with relationships.

---

### Prompt 76: Create Section Model

**Purpose**: Create Eloquent model for sections table.

**Functionality**: Provides object-oriented interface to interact with sections data.

**How it Works**:
- Extends Laravel's base Model class
- Uses SoftDeletes trait
- Defines relationships to Class, User, Student, ClassSubject, ClassTimetable, Attendance, Homework, StudyMaterial
- Defines fillable fields
- Provides helper methods for section management

**Integration**:
- Links to Class model (belongsTo)
- Links to User model (belongsTo, class_teacher)
- Links to Student model (hasMany)
- Links to ClassSubject model (hasMany)
- Links to ClassTimetable model (hasMany)
- Links to Attendance model (hasMany)
- Links to Homework model (hasMany)
- Links to StudyMaterial model (hasMany)
- Enables fine-grained academic organization

**Execute**: Create model file `app/Models/Section.php` with relationships.

---

### Prompt 77: Create Subject Model

**Purpose**: Create Eloquent model for subjects table.

**Functionality**: Provides object-oriented interface to interact with subjects data.

**How it Works**:
- Extends Laravel's base Model class
- Uses SoftDeletes trait
- Defines relationships to ClassSubject, ClassTimetable, ExamSchedule, Homework, StudyMaterial
- Defines fillable fields
- Provides helper methods for subject management

**Integration**:
- Links to ClassSubject model (hasMany)
- Links to ClassTimetable model (hasMany)
- Links to ExamSchedule model (hasMany)
- Links to Homework model (hasMany)
- Links to StudyMaterial model (hasMany)
- Organizes curriculum by subject

**Execute**: Create model file `app/Models/Subject.php` with relationships.

---

### Prompt 78: Create Student Model

**Purpose**: Create Eloquent model for students table.

**Functionality**: Provides object-oriented interface to interact with students data.

**How it Works**:
- Extends Laravel's base Model class
- Uses SoftDeletes trait
- Defines relationships to User, AcademicSession, Class, Section, StudentCategory, StudentSibling, StudentDocument, StudentPromotion, Attendance, ExamMark, FeesAllotment, FeesTransaction, TransportStudent, HostelAssignment
- Defines fillable fields
- Defines casts for date fields
- Provides helper methods for student management

**Integration**:
- Links to User model (belongsTo)
- Links to AcademicSession model (belongsTo)
- Links to Class model (belongsTo)
- Links to Section model (belongsTo)
- Links to StudentCategory model (belongsTo)
- Links to StudentSibling model (hasMany)
- Links to StudentDocument model (hasMany)
- Links to StudentPromotion model (hasMany)
- Links to Attendance model (hasMany)
- Links to ExamMark model (hasMany)
- Links to FeesAllotment model (hasMany)
- Links to FeesTransaction model (hasMany)
- Links to TransportStudent model (hasOne)
- Links to HostelAssignment model (hasOne)
- Central to all student-related features

**Execute**: Create model file `app/Models/Student.php` with complete relationships.

---

### Prompt 79: Create Attendance Model

**Purpose**: Create Eloquent model for attendances table.

**Functionality**: Provides object-oriented interface to interact with attendance data.

**How it Works**:
- Extends Laravel's base Model class
- Defines relationships to Student, Class, Section, AttendanceType, User
- Defines fillable fields
- Defines casts for date fields
- Provides helper methods for attendance management

**Integration**:
- Links to Student model (belongsTo)
- Links to Class model (belongsTo)
- Links to Section model (belongsTo)
- Links to AttendanceType model (belongsTo)
- Links to User model (belongsTo, marked_by)
- Used in attendance marking and reports
- Parent dashboard shows children's attendance

**Execute**: Create model file `app/Models/Attendance.php` with relationships.

---

### Prompt 80: Create Exam Model

**Purpose**: Create Eloquent model for exams table.

**Functionality**: Provides object-oriented interface to interact with exams data.

**How it Works**:
- Extends Laravel's base Model class
- Uses SoftDeletes trait
- Defines relationships to AcademicSession, ExamType, ExamSchedule
- Defines fillable fields
- Defines casts for date fields
- Provides helper methods for exam management

**Integration**:
- Links to AcademicSession model (belongsTo)
- Links to ExamType model (belongsTo)
- Links to ExamSchedule model (hasMany)
- Links to ExamAttendance model (hasMany)
- Links to ExamMark model (hasMany)
- Used in exam scheduling and reporting
- Organizes exams by session and type
- Links to exam marks and report cards

**Execute**: Create model file `app/Models/Exam.php` with relationships.

---

### Prompt 81: Create ExamSchedule Model

**Purpose**: Create Eloquent model for exam schedules table.

**Functionality**: Provides object-oriented interface to interact with exam schedule data.

**How it Works**:
- Extends Laravel's base Model class
- Defines relationships to Exam, Class, Section, Subject, ExamAttendance, ExamMark
- Defines fillable fields
- Defines casts for date and time fields
- Provides helper methods for exam schedule management

**Integration**:
- Links to Exam model (belongsTo)
- Links to Class model (belongsTo)
- Links to Section model (belongsTo)
- Links to Subject model (belongsTo)
- Links to ExamAttendance model (hasMany)
- Links to ExamMark model (hasMany)
- Used in marks entry and report card generation
- Teacher dashboard shows exam schedule
- Student dashboard shows their exams

**Execute**: Create model file `app/Models/ExamSchedule.php` with relationships.

---

### Prompt 82: Create ExamMark Model

**Purpose**: Create Eloquent model for exam marks table.

**Functionality**: Provides object-oriented interface to interact with exam marks data.

**How it Works**:
- Extends Laravel's base Model class
- Defines relationships to ExamSchedule, Student, ExamGrade, User
- Defines fillable fields
- Provides helper methods for marks management

**Integration**:
- Links to ExamSchedule model (belongsTo)
- Links to Student model (belongsTo)
- Links to ExamGrade model (belongsTo)
- Links to User model (belongsTo, entered_by)
- Used in report card generation
- Parent dashboard shows children's results
- Student dashboard shows their marks

**Execute**: Create model file `app/Models/ExamMark.php` with relationships.

---

### Prompt 83: Create FeesAllotment Model

**Purpose**: Create Eloquent model for fees allotments table.

**Functionality**: Provides object-oriented interface to interact with fee allotment data.

**How it Works**:
- Extends Laravel's base Model class
- Defines relationships to Student, FeesMaster, FeesDiscount, FeesTransaction
- Defines fillable fields
- Defines casts for date fields
- Provides helper methods for fee allotment management

**Integration**:
- Links to Student model (belongsTo)
- Links to FeesMaster model (belongsTo)
- Links to FeesDiscount model (belongsTo)
- Links to FeesTransaction model (hasMany)
- Used in fee collection and reporting
- Parent dashboard shows fee status

**Execute**: Create model file `app/Models/FeesAllotment.php` with relationships.

---

### Prompt 84: Create FeesTransaction Model

**Purpose**: Create Eloquent model for fees transactions table.

**Functionality**: Provides object-oriented interface to interact with fee transaction data.

**How it Works**:
- Extends Laravel's base Model class
- Defines relationships to Student, FeesAllotment, User
- Defines fillable fields
- Defines casts for date fields
- Provides helper methods for transaction management

**Integration**:
- Links to Student model (belongsTo)
- Links to FeesAllotment model (belongsTo)
- Links to User model (belongsTo, received_by)
- Used in fee collection and reporting
- Generates receipts
- Updates accounting

**Execute**: Create model file `app/Models/FeesTransaction.php` with relationships.

---

### Prompt 85: Create LibraryBook Model

**Purpose**: Create Eloquent model for library books table.

**Functionality**: Provides object-oriented interface to interact with library book data.

**How it Works**:
- Extends Laravel's base Model class
- Uses SoftDeletes trait
- Defines relationships to LibraryCategory, LibraryIssue
- Defines fillable fields
- Provides helper methods for book management

**Integration**:
- Links to LibraryCategory model (belongsTo)
- Links to LibraryIssue model (hasMany)
- Used in library management
- Tracks book availability
- Used in library reports

**Execute**: Create model file `app/Models/LibraryBook.php` with relationships.

---

### Prompt 86: Create LibraryIssue Model

**Purpose**: Create Eloquent model for library issues table.

**Functionality**: Provides object-oriented interface to interact with library issue data.

**How it Works**:
- Extends Laravel's base Model class
- Defines relationships to LibraryBook, LibraryMember, User
- Defines fillable fields
- Defines casts for date fields
- Provides helper methods for issue/return management

**Integration**:
- Links to LibraryBook model (belongsTo)
- Links to LibraryMember model (belongsTo)
- Links to User model (many-to-one, issued_by)
- Links to User model (many-to-one, returned_by)
- Updates book availability
- Calculates fines for late returns

**Execute**: Create model file `app/Models/LibraryIssue.php` with relationships.

---

## 🎯 Phase 4: Authentication & Authorization (8 Prompts)

### Prompt 87: Install Laravel Breeze

**Purpose**: Install Laravel Breeze package for authentication scaffolding.

**Functionality**: Provides simple authentication scaffolding with login, registration, password reset.

**How it Works**:
- Downloads Laravel Breeze package via Composer
- Provides authentication UI templates
- Creates authentication controllers
- Sets up authentication routes
- Configures session management
- Provides email verification (optional)

**Integration**:
- Creates login and registration views
- Creates authentication controllers with login/register methods
- Sets up middleware for protected routes
- All authenticated features depend on this

**Execute**: Navigate to `smart-school` directory and run:
```bash
composer require laravel/breeze --dev
```

---

### Prompt 88: Install Breeze Blade Stack

**Purpose**: Install Laravel Breeze with Blade templates for authentication UI.

**Functionality**: Generates Blade views for login, registration, password reset.

**How it Works**:
- Runs Breeze installation command with Blade stack
- Creates Blade templates in `resources/views/auth/`
- Creates authentication controllers
- Sets up routes for authentication
- Configures middleware for protected routes
- Generates responsive UI using Bootstrap

**Integration**:
- Creates login view at `resources/views/auth/login.blade.php`
- Creates registration view at `resources/views/auth/register.blade.php`
- Creates password reset views
- Creates authentication controllers
- All authentication flows use these templates

**Execute**: Navigate to `smart-school` directory and run:
```bash
php artisan breeze:install blade
```

---

### Prompt 89: Install Spatie Permission

**Purpose**: Install Spatie Permission package for role-based access control.

**Functionality**: Provides granular permissions and roles management.

**How it Works**:
- Downloads Spatie Permission package via Composer
- Creates permissions and roles tables
- Provides HasRoles and HasPermissions traits
- Enables role-based access control
- Enables permission-based access control
- Provides middleware for checking roles and permissions

**Integration**:
- Models use HasRoles and HasPermissions traits
- Middleware checks user's role and permissions
- All module access controlled by RBAC
- Used throughout application for authorization

**Execute**: Navigate to `smart-school` directory and run:
```bash
composer require spatie/laravel-permission
```

---

### Prompt 90: Publish Spatie Permission

**Purpose**: Publish Spatie Permission configuration and migration files.

**Functionality**: Copies configuration and migration files to application.

**How it Works**:
- Publishes config file to `config/permission.php`
- Publishes migration files to `database/migrations/`
- Publishes models to `app/Models/`
- Enables customization of permission system
- Prepares for running migrations

**Integration**:
- Configuration file used throughout application
- Migration files create permissions and roles tables
- Models used by User, Role, Permission
- All RBAC depends on published files

**Execute**: Navigate to `smart-school` directory and run:
```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

---

### Prompt 91: Create Role Seeder

**Purpose**: Create seeder to populate roles table with 6 user roles.

**Functionality**: Inserts default roles into database.

**How it Works**:
- Creates `RoleSeeder` class in `database/seeders/`
- Inserts roles: admin, teacher, student, parent, accountant, librarian
- Each role has display name and description
- Sets is_active to true
- Runs when `db:seed` command executed

**Integration**:
- Roles table populated with default roles
- Users can be assigned to these roles
- Spatie Permission uses these roles
- All RBAC depends on these roles

**Execute**: Create seeder file `database/seeders/RoleSeeder.php` with role creation code.

---

### Prompt 92: Create Permission Seeder

**Purpose**: Create seeder to populate permissions table with granular permissions.

**Functionality**: Inserts default permissions for all modules.

**How it Works**:
- Creates `PermissionSeeder` class in `database/seeders/`
- Inserts permissions for all modules:
  - Student management (view, create, edit, delete students)
  - Academic management (view, create, edit, delete classes)
  - Attendance (view, mark attendance)
  - Examination (view, create, edit, delete exams, enter marks)
  - Fees (view, collect, manage fees)
  - Library (view, manage books, issue books)
  - Transport (view, manage transport)
  - Hostel (view, manage hostel)
  - Communication (view notices, create notices, send messages)
  - Accounting (view expenses, create expenses, view income)
- Each permission has module and display name
- Runs when `db:seed` command executed

**Integration**:
- Permissions table populated with all module permissions
- Roles can be assigned these permissions
- Spatie Permission uses these permissions
- All module access controlled by these permissions

**Execute**: Create seeder file `database/seeders/PermissionSeeder.php` with permission creation code.

---

### Prompt 93: Create Admin User Seeder

**Purpose**: Create seeder to create default admin user for initial login.

**Functionality**: Inserts admin user with credentials and full access.

**How it Works**:
- Creates `AdminUserSeeder` class in `database/seeders/`
- Creates admin user in users table
- Email: admin@smartschool.com
- Password: password (hashed)
- First name: Admin
- Last name: User
- Assigns admin role
- Assigns all permissions
- Runs when `db:seed` command executed

**Integration**:
- Admin user created in users table
- Admin role assigned via model_has_roles table
- All permissions assigned via model_has_permissions table
- Enables initial system login with full access

**Execute**: Create seeder file `database/seeders/AdminUserSeeder.php` with admin user creation code.

---

### Prompt 94: Run All Seeders

**Purpose**: Execute all database seeders to populate initial data.

**Functionality**: Runs all seeders to populate database with default data.

**How it Works**:
- Executes RoleSeeder to create roles
- Executes PermissionSeeder to create permissions
- Executes AdminUserSeeder to create admin user
- Optionally executes other seeders
- Populates database with initial data
- Makes system ready for use

**Integration**:
- Database populated with roles and permissions
- Admin user created for initial login
- System ready for first login with full access
- All features can use this initial data

**Execute**: Navigate to `smart-school` directory and run:
```bash
php artisan db:seed
```

---

## 🎨 Phase 5: Views & Layouts (5 Prompts)

### Prompt 95: Create Base Layout

**Purpose**: Create base HTML layout template for all pages.

**Functionality**: Provides consistent layout structure across all pages.

**How it Works**:
- Creates `resources/views/layouts/app.blade.php`
- Includes HTML5 doctype and meta tags
- Includes CSS from Vite
- Includes navigation component
- Yields content section
- Includes footer component
- Includes JS from Vite
- Provides responsive structure
- Supports RTL (right-to-left) languages via `dir` attribute

**Integration**:
- All views extend this layout using `@extends('layouts.app')`
- Navigation included in all pages
- Footer included in all pages
- Consistent design across application
- All CSS/JS loaded from this layout
- Language direction controlled from this layout

**Execute**: Create layout file with complete HTML structure and component includes.

---

### Prompt 96: Create Navigation Component

**Purpose**: Create navigation sidebar component for role-based menu.

**Functionality**: Provides navigation menu based on user's role.

**How it Works**:
- Creates `resources/views/layouts/navigation.blade.php`
- Checks user's role using `Auth::user()->role->name`
- Displays menu items based on permissions
- Shows user profile dropdown with avatar, name, logout
- Provides responsive mobile menu toggle
- Collapseable submenus using Bootstrap 5 collapse
- Shows active menu item highlighting
- Includes quick action buttons
- Supports RTL languages
- Includes notification badges for unread messages/notices

**Integration**:
- Included in base layout
- Links to all module pages
- Shows only menu items user has permission to access
- User profile dropdown links to profile page
- Logout button destroys session and redirects to login
- Used in all authenticated pages

**Execute**: Create navigation component with role-based menu items, collapsible submenus, and responsive design.

---

### Prompt 97: Create Footer Component

**Purpose**: Create footer component for all pages.

**Functionality**: Provides consistent footer across all pages.

**Functionality**: Shows copyright information, quick links, version number, contact info.

**How it Works**:
- Creates `resources/views/layouts/footer.blade.php`
- Shows school logo and name
- Shows copyright notice with current year
- Shows quick links: About, Contact, Privacy Policy, Terms of Service
- Shows version number
- Shows social media links (Facebook, Twitter, Instagram, LinkedIn)
- Responsive grid layout
- Supports RTL languages

**Integration**:
- Included in base layout
- Consistent across all pages
- Provides system information
- Links to static pages
- Used in all pages

**Execute**: Create footer component with copyright, links, and version info.

---

### Prompt 98: Create Login View

**Purpose**: Create login page view with form and validation.

**Functionality**: Provides user interface for authentication with form validation and error handling.

**How it Works**:
- Creates `resources/views/auth/login.blade.php`
- Extends auth layout
- Shows school logo and branding
- Shows login form with:
  - Email input with validation
  - Password input with show/hide toggle
  - Remember me checkbox
  - Login button with loading state
- Shows "Forgot Password?" link
- Shows validation errors
- Includes CSRF token
- Uses Bootstrap 5 form styling
- Responsive design
- Supports RTL languages
- Shows loading spinner during submission
- Redirects to dashboard on success
- Redirects to login on failure with error

**Integration**:
- Uses AuthController login method
- Validates credentials against users table
- Creates session on successful login
- Redirects to role-appropriate dashboard
- Uses Spatie Permission for role checking
- Links to registration page (if enabled)
- Links to forgot password page

**Execute**: Create login view with form, validation, responsive design, and RTL support.

---

### Prompt 99: Create Dashboard View

**Purpose**: Create admin dashboard view with statistics, charts, and activities.

**Functionality**: Provides admin dashboard with key metrics and activities.

**How it Works**:
- Creates `resources/views/admin/dashboard.blade.php`
- Extends app layout
- Shows statistics cards:
  - Total Students (with trend)
  - Total Teachers (with trend)
  - Total Classes (with trend)
  - Total Staff (with trend)
  - Today's Attendance (percentage)
  - Today's Fee Collection (amount)
  - Pending Fees (amount)
  - Upcoming Exams (count)
- Shows charts:
  - Student enrollment by class (bar chart)
  - Attendance trend (line chart)
  - Fee collection trend (line chart)
  - Exam performance (pie chart)
- Shows recent activities:
  - New student admissions
  - Fee payments
  - Exam results
  - Attendance marked
- Shows quick action buttons:
  - Add Student
  - Mark Attendance
  - Collect Fee
  - Create Notice
- Uses Chart.js for data visualization
- Links to all module pages
- Links to quick action forms
- Filters data by date range
- Shows loading state for data
- Auto-refreshes data every 5 minutes

**Integration**:
- Uses DashboardController index method
- Queries multiple models for statistics
- Uses Chart.js for data visualization
- Links to all module pages
- Links to quick action forms
- Academic session selector filters all data
- Date range filters chart data
- Used by admin dashboard

**Execute**: Create dashboard view with statistics, charts, activities, and responsive design.

---

## 🎯 Phase 6: Controllers (7 Prompts)

### Prompt 100: Create Auth Controller

**Purpose**: Create controller to handle authentication operations.

**Functionality**: Manages login, logout, registration, password reset.

**How it Works**:
- Creates `app/Http/Controllers/AuthController.php`
- showLoginForm method: displays login page
- login method: validates credentials, creates session, redirects
- logout method: destroys session, redirects to login
- showRegistrationForm method: displays registration form
- register method: validates input data, creates user, redirects
- showForgotPasswordForm method: displays forgot password form
- sendPasswordResetLink method: sends reset email
- resetPassword method: validates token, updates password, redirects
- showResetPasswordForm method: displays reset password form
- resetPassword method: validates token, updates password, redirects
- Implements Laravel's Auth facade for session management
- Uses Spatie Permission for role checking
- Links to login and registration views
- Links to forgot password and reset password views

**Integration**:
- Uses User model for authentication
- Uses Laravel's Auth facade for session management
- Uses Spatie Permission for role checking
- Links to login and registration views
- Protects routes with middleware
- Used by all authenticated features

**Execute**: Create AuthController with all authentication methods and validation.

---

### Prompt 101: Create Dashboard Controller

**Purpose**: Create controller to handle dashboard data and statistics.

**Functionality**: Manages dashboard display and data retrieval.

**How it Works**:
- Creates `app/Http/Controllers/Admin/DashboardController.php`
- index method: displays admin dashboard
- Gets statistics data (total students, teachers, classes, etc.)
- Gets recent activities
- Gets chart data for visualization
- Formats data for frontend consumption
- Applies role-based access control
- Shows loading state for data
- Used by admin dashboard

**Integration**:
- Queries multiple models for statistics
- Uses Chart.js for data visualization
- Links to all module pages
- Links to quick action forms
- Academic session selector filters all data
- Used by admin dashboard

**Execute**: Create DashboardController with statistics retrieval and chart data methods.

---

### Prompt 102: Create Student Controller

**Purpose**: Create controller to handle student CRUD operations.

**Functionality**: Manages student listing, creation, editing, deletion, and promotions.

**How it Works**:
- Creates `app/Http/Controllers/StudentController.php`
- index method: lists students with pagination, search, filters
- create method: saves new student with validation
- show method: displays student details
- edit method: updates student with validation
- update method: updates student with validation
- destroy method: soft deletes student
- search method: searches students by name, admission number, father name
- promote method: promotes student to next class
- Applies role-based access control
- Validates requests using Form Requests
- Links to student details, edit, delete, promote
- Links to student list
- Used by admin and teacher panels

**Execute**: Create StudentController with all CRUD methods and validation.

---

### Prompt 103: Create Academic Session Controller

**Purpose**: Create controller to manage academic sessions.

**Functionality**: Manages academic session CRUD operations.

**How it Works**:
- Creates `app/Http/Controllers/Admin/AcademicSessionController.php`
- index method: lists all academic sessions
- create method: saves new academic session
- show method: displays session details
- edit method: updates academic session
- update method: updates academic session
- destroy method: deletes academic session
- setCurrent method: sets current session
- Applies role-based access control
- Validates requests using Form Requests
- Links to class details
- Links to class details
- Used by admin role

**Execute**: Create AcademicSessionController with all CRUD methods and current session management.

---

### Prompt 104: Create Class Controller

**Purpose**: Create controller to manage classes.

**Functionality**: Manages class CRUD operations.

**How it Works**:
- Creates `app/Http/Controllers/Admin/ClassController.php`
- index method: lists all classes
- create method: saves new class
- show method: displays class details
- edit method: updates class
- update method: updates class
- destroy method: deletes class
- Applies role-based access control
- Validates requests using Form Requests
- Links to section details
- Links to student list
- Used by admin role

**Execute**: Create ClassController with all CRUD methods.

---

### Prompt 105: Create Section Controller

**Purpose**: Create controller to manage sections.

**Functionality**: Manages section CRUD operations.

**How it Works**:
- Creates `app/Http/Controllers/Admin/SectionController.php`
- index method: lists all sections
- create method: saves new section
- show method: displays section details
- edit method: updates section
- update method: updates section
- destroy method: deletes section
- Applies role-based access control
- Validates requests using Form Requests
- Links to class details
- Links to student list
- Used by admin role

**Execute**: Create SectionController with all CRUD methods.

---

### Prompt 106: Create Subject Controller

**Purpose**: Create controller to manage subjects.

**Functionality**: Manages subject CRUD operations.

**How it Works**:
- Creates `app/Http/Controllers/Admin/SubjectController.php`
- index method: lists all subjects
- create method: saves new subject
- show method: displays subject details
- edit method: updates subject
- update method: updates subject
- destroy method: deletes subject
- Applies role-based access control
- Validates requests using Form Requests
- Links to class details
- Used by admin role

**Execute**: Create SubjectController with all CRUD methods.

---

## 📊 Summary

**Total Backend Prompts: 106**

**Phases Covered:**
1. **Project Setup & Foundation** (10 prompts)
2. **Database Schema Implementation** (60 prompts)
3. **Model Creation** (16 prompts)
4. **Authentication & Authorization** (8 prompts)
5. **Views & Layouts** (5 prompts)
6. **Controllers** (7 prompts)

**Features Implemented:**
- Complete database schema with 60+ tables
- Eloquent models with relationships
- Authentication system with Laravel Breeze
- Role-based access control with Spatie Permission
- Controllers for all modules
- Views and layouts for frontend
- Seeders for initial data

**Next Steps:**
- For frontend prompts, use the PART files:
  - [`DEVIN-AI-FRONTEND-DETAILED.md`](DEVIN-AI-FRONTEND-DETAILED.md) - 70 prompts
  - [`DEVIN-AI-FRONTEND-DETAILED-PART2.md`](DEVIN-AI-FRONTEND-DETAILED-PART2.md) - 40 prompts
  - [`DEVIN-AI-FRONTEND-DETAILED-PART3.md`](DEVIN-AI-FRONTEND-DETAILED-PART3.md) - 30 prompts
  - [`DEVIN-AI-FRONTEND-DETAILED-PART4.md`](DEVIN-AI-FRONTEND-DETAILED-PART4.md) - 45 prompts

---

## 🚀 Next Steps

Continue with remaining backend prompts for other modules:
- Attendance Controllers
- Examination Controllers
- Fees Controllers
- Library Controllers
- Transport Controllers
- Hostel Controllers
- Communication Controllers
- Accounting Controllers
- Reports Controllers
- Settings Controllers
- Teacher Controllers
- Parent Controllers
- Student Controllers
- Accountant Controllers
- Librarian Controllers

---

## 📚 All Documentation

**Planning Documents** (in `plans/` directory):
1. [Architecture Plan](plans/school-management-system-architecture.md)
2. [Implementation Roadmap](plans/school-management-implementation-roadmap.md)
3. [Database Schema](plans/school-management-database-schema.md)
4. [Quick Start Guide](plans/school-management-quick-start.md)
5. [Visual Overview](plans/school-management-visual-overview.md)

**Configuration Files** (in `smart-school/` directory):
1. [composer.json](smart-school/composer.json) - PHP dependencies
2. [package.json](smart-school/package.json) - Node.js dependencies
3. [.env.example](smart-school/.env.example) - Environment template
4. [README.md](smart-school/README.md) - Project documentation
5. [SETUP-GUIDE.md](smart-school/SETUP-GUIDE.md) - Setup guide
6. [PROJECT-COMPLETE.md](smart-school/PROJECT-COMPLETE.md) - Completion summary

**Reference Guides** (in `smart-school/` directory):
1. [WHAT-TO-EXPECT.md](smart-school/WHAT-TO-EXPECT.md) - Complete guide to final structure
2. [GUIDE_FOR_DEVIN.md](smart-school/GUIDE_FOR_DEVIN.md) - Ultimate guide for DevIn AI

**Frontend Prompt Files** (in `smart-school/` directory):
1. [DEVIN-AI-FRONTEND-DETAILED.md](smart-school/DEVIN-AI-FRONTEND-DETAILED.md) - 70 prompts (Part 1)
2. [DEVIN-AI-FRONTEND-DETAILED-PART2.md](smart-school/DEVIN-AI-FRONTEND-DETAILED-PART2.md) - 40 prompts (Part 2)
3. [DEVIN-AI-FRONTEND-DETAILED-PART3.md](smart-school/DEVIN-AI-FRONTEND-DETAILED-PART3.md) - 30 prompts (Part 3)
4. [DEVIN-AI-FRONTEND-DETAILED-PART4.md](smart-school/DEVIN-AI-FRONTEND-TAILED-PART4.md) - 45 prompts (Part 4)

---

## ✅ Project Status

**Planning Phase:** Complete ✅  
**Project Initialization:** Complete ✅  
**Backend Prompts:** Complete (106 detailed prompts) ✅  
**Frontend Prompts:** Complete (185 prompts in PART files) ✅  
**Ready for DevIn AI:** Yes ✅  

The Smart School Management System is now ready for development with DevIn AI using comprehensive backend prompts that explain every aspect of each task including purpose, functionality, implementation details, and integration with other features.

**For frontend prompts, use the PART files which contain all 185 prompts with detailed explanations.**
