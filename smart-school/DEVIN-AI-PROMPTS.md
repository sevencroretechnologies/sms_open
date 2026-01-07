# Smart School Management System - DevIn AI Prompts Guide

This document contains detailed, small-task prompts for building the Smart School Management System using DevIn AI. Each prompt is designed to be executed independently and focuses on a single, specific task.

## 📋 How to Use This Guide

1. **Execute prompts in order** - Start from the beginning and work through sequentially
2. **Each prompt is independent** - Can be executed one at a time
3. **Verify completion** - Ensure each task is complete before moving to the next
4. **Reference documentation** - Use planning docs in `../plans/` for context

---

## 🚀 Phase 1: Project Setup & Foundation

### Prompt 1: Install Laravel Dependencies
```
Navigate to the smart-school directory and install all PHP dependencies using Composer.
Run: composer install
```

### Prompt 2: Install Node.js Dependencies
```
Navigate to the smart-school directory and install all Node.js dependencies using npm.
Run: npm install
```

### Prompt 3: Configure Environment File
```
Copy the .env.example file to .env in the smart-school directory.
Run: cp .env.example .env
```

### Prompt 4: Generate Application Key
```
Generate a new application key for the Laravel project in smart-school directory.
Run: php artisan key:generate
```

### Prompt 5: Create MySQL Database
```
Create a MySQL database named 'smart_school' with utf8mb4 character set and utf8mb4_unicode_ci collation.
SQL: CREATE DATABASE smart_school CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Prompt 6: Update Database Configuration
```
Update the .env file in smart-school directory with your MySQL database credentials:
- DB_DATABASE=smart_school
- DB_USERNAME=your_username
- DB_PASSWORD=your_password
```

### Prompt 7: Run Database Migrations
```
Run all database migrations for the smart-school project.
Run: php artisan migrate
```

### Prompt 8: Run Database Seeders
```
Run all database seeders for the smart-school project.
Run: php artisan db:seed
```

### Prompt 9: Build Frontend Assets
```
Build the frontend assets for the smart-school project using npm.
Run: npm run dev
```

### Prompt 10: Start Development Server
```
Start the Laravel development server for the smart-school project.
Run: php artisan serve
```

---

## 🗄️ Phase 2: Database Schema Implementation

### Prompt 11: Create Users Table Migration
```
Create a Laravel migration for the users table in smart-school project with the following structure:
- id (big integer, auto increment, primary key)
- uuid (char 36, unique, not null)
- role_id (big integer, foreign key to roles table)
- first_name (varchar 100, not null)
- last_name (varchar 100, not null)
- email (varchar 255, unique)
- phone (varchar 20, unique)
- username (varchar 50, unique)
- password (varchar 255, not null)
- avatar (varchar 255)
- date_of_birth (date)
- gender (enum: male, female, other)
- address (text)
- city (varchar 100)
- state (varchar 100)
- country (varchar 100)
- postal_code (varchar 20)
- is_active (boolean, default true)
- email_verified_at (timestamp, nullable)
- last_login_at (timestamp, nullable)
- remember_token (varchar 100)
- created_at and updated_at (timestamps)
- deleted_at (timestamp, nullable for soft delete)

Add indexes on: role_id, email, phone, username, is_active
```

### Prompt 12: Create Roles Table Migration
```
Create a Laravel migration for the roles table in smart-school project with the following structure:
- id (big integer, auto increment, primary key)
- name (varchar 50, unique, not null)
- display_name (varchar 100, not null)
- description (text)
- is_active (boolean, default true)
- created_at and updated_at (timestamps)
```

### Prompt 13: Create Permissions Table Migration
```
Create a Laravel migration for the permissions table in smart-school project with the following structure:
- id (big integer, auto increment, primary key)
- name (varchar 255, unique, not null)
- display_name (varchar 255, not null)
- module (varchar 50, not null)
- description (text)
- created_at and updated_at (timestamps)
Add index on: module
```

### Prompt 14: Create Role-Permission Pivot Table Migration
```
Create a Laravel migration for role_has_permissions pivot table in smart-school project with:
- role_id (big integer, foreign key to roles.id, on delete cascade)
- permission_id (big integer, foreign key to permissions.id, on delete cascade)
- Primary key on (role_id, permission_id)
```

### Prompt 15: Create Model-Permission Pivot Table Migration
```
Create a Laravel migration for model_has_permissions pivot table in smart-school project with:
- permission_id (big integer, foreign key to permissions.id, on delete cascade)
- model_type (varchar 255, not null)
- model_id (big integer, not null)
- Primary key on (permission_id, model_id, model_type)
```

### Prompt 16: Create Model-Role Pivot Table Migration
```
Create a Laravel migration for model_has_roles pivot table in smart-school project with:
- role_id (big integer, foreign key to roles.id, on delete cascade)
- model_type (varchar 255, not null)
- model_id (big integer, not null)
- Primary key on (role_id, model_id, model_type)
```

### Prompt 17: Create Academic Sessions Table Migration
```
Create a Laravel migration for academic_sessions table in smart-school project with:
- id (big integer, auto increment, primary key)
- name (varchar 50, not null)
- start_date (date, not null)
- end_date (date, not null)
- is_current (boolean, default false)
- is_active (boolean, default true)
- created_at and updated_at (timestamps)
- deleted_at (timestamp, nullable)
Add index on: is_current
```

### Prompt 18: Create Classes Table Migration
```
Create a Laravel migration for classes table in smart-school project with:
- id (big integer, auto increment, primary key)
- academic_session_id (big integer, foreign key to academic_sessions.id, on delete cascade)
- name (varchar 50, not null)
- display_name (varchar 100, not null)
- section_count (integer, default 1)
- order_index (integer, default 0)
- is_active (boolean, default true)
- created_at and updated_at (timestamps)
- deleted_at (timestamp, nullable)
Add indexes on: academic_session_id, name
```

### Prompt 19: Create Sections Table Migration
```
Create a Laravel migration for sections table in smart-school project with:
- id (big integer, auto increment, primary key)
- class_id (big integer, foreign key to classes.id, on delete cascade)
- name (varchar 10, not null)
- display_name (varchar 50, not null)
- capacity (integer, default 40)
- room_number (varchar 20)
- class_teacher_id (big integer, foreign key to users.id, on delete set null)
- is_active (boolean, default true)
- created_at and updated_at (timestamps)
- deleted_at (timestamp, nullable)
Add index on: class_id
```

### Prompt 20: Create Subjects Table Migration
```
Create a Laravel migration for subjects table in smart-school project with:
- id (big integer, auto increment, primary key)
- name (varchar 100, not null)
- code (varchar 20, unique, not null)
- type (enum: theory, practical, default theory)
- description (text)
- is_active (boolean, default true)
- created_at and updated_at (timestamps)
- deleted_at (timestamp, nullable)
Add index on: code
```

### Prompt 21: Create Class-Subjects Pivot Table Migration
```
Create a Laravel migration for class_subjects table in smart-school project with:
- id (big integer, auto increment, primary key)
- class_id (big integer, foreign key to classes.id, on delete cascade)
- section_id (big integer, foreign key to sections.id, on delete cascade, nullable)
- subject_id (big integer, foreign key to subjects.id, on delete cascade)
- teacher_id (big integer, foreign key to users.id, on delete set null, nullable)
- is_active (boolean, default true)
- created_at and updated_at (timestamps)
Add indexes on: class_id, section_id, subject_id
```

### Prompt 22: Create Class Timetables Table Migration
```
Create a Laravel migration for class_timetables table in smart-school project with:
- id (big integer, auto increment, primary key)
- class_id (big integer, foreign key to classes.id, on delete cascade)
- section_id (big integer, foreign key to sections.id, on delete cascade)
- day_of_week (enum: monday, tuesday, wednesday, thursday, friday, saturday, not null)
- period_number (integer, not null)
- subject_id (big integer, foreign key to subjects.id, on delete cascade)
- teacher_id (big integer, foreign key to users.id, on delete set null, nullable)
- room_number (varchar 20)
- start_time (time, not null)
- end_time (time, not null)
- is_active (boolean, default true)
- created_at and updated_at (timestamps)
Add indexes on: (class_id, section_id), day_of_week
```

### Prompt 23: Create Students Table Migration
```
Create a Laravel migration for students table in smart-school project with the following structure:
- id (big integer, auto increment, primary key)
- user_id (big integer, foreign key to users.id, on delete cascade)
- academic_session_id (big integer, foreign key to academic_sessions.id, on delete cascade)
- admission_number (varchar 50, unique, not null)
- roll_number (varchar 20)
- class_id (big integer, foreign key to classes.id, on delete set null)
- section_id (big integer, foreign key to sections.id, on delete set null)
- date_of_admission (date, not null)
- date_of_birth (date, not null)
- gender (enum: male, female, other, not null)
- blood_group (varchar 5)
- religion (varchar 50)
- caste (varchar 50)
- category_id (big integer, foreign key to student_categories.id)
- is_rte (boolean, default false)
- admission_type (varchar 50)
- previous_school_name (varchar 255)
- previous_school_address (text)
- transfer_certificate_number (varchar 100)
- transfer_certificate_date (date)
- father_name (varchar 100, not null)
- father_phone (varchar 20)
- father_occupation (varchar 100)
- father_qualification (varchar 100)
- mother_name (varchar 100)
- mother_phone (varchar 20)
- mother_occupation (varchar 100)
- mother_qualification (varchar 100)
- guardian_name (varchar 100)
- guardian_phone (varchar 20)
- guardian_relation (varchar 50)
- guardian_address (text)
- address (text, not null)
- city (varchar 100)
- state (varchar 100)
- country (varchar 100, default 'India')
- postal_code (varchar 20)
- emergency_contact_name (varchar 100)
- emergency_contact_phone (varchar 20)
- emergency_contact_relation (varchar 50)
- medical_notes (text)
- allergies (text)
- height (decimal 5,2)
- weight (decimal 5,2)
- identification_marks (text)
- nationality (varchar 50, default 'Indian')
- mother_tongue (varchar 50)
- is_active (boolean, default true)
- created_at and updated_at (timestamps)
- deleted_at (timestamp, nullable)
Add indexes on: admission_number, (class_id, section_id), academic_session_id
```

### Prompt 24: Create Student Siblings Table Migration
```
Create a Laravel migration for student_siblings table in smart-school project with:
- id (big integer, auto increment, primary key)
- student_id (big integer, foreign key to students.id, on delete cascade)
- sibling_id (big integer, foreign key to students.id, on delete cascade)
- relation (enum: brother, sister, not null)
- created_at (timestamp)
Add index on: student_id
```

### Prompt 25: Create Student Documents Table Migration
```
Create a Laravel migration for student_documents table in smart-school project with:
- id (big integer, auto increment, primary key)
- student_id (big integer, foreign key to students.id, on delete cascade)
- document_type (varchar 50, not null)
- document_name (varchar 255, not null)
- file_path (varchar 255, not null)
- file_size (integer)
- uploaded_by (big integer, foreign key to users.id, on delete set null)
- created_at and updated_at (timestamps)
Add index on: student_id
```

### Prompt 26: Create Student Categories Table Migration
```
Create a Laravel migration for student_categories table in smart-school project with:
- id (big integer, auto increment, primary key)
- name (varchar 100, not null)
- description (text)
- is_active (boolean, default true)
- created_at and updated_at (timestamps)
- deleted_at (timestamp, nullable)
```

### Prompt 27: Create Student Promotions Table Migration
```
Create a Laravel migration for student_promotions table in smart-school project with:
- id (big integer, auto increment, primary key)
- student_id (big integer, foreign key to students.id, on delete cascade)
- from_class_id (big integer, foreign key to classes.id, on delete cascade)
- from_section_id (big integer, foreign key to sections.id, on delete cascade, nullable)
- to_class_id (big integer, foreign key to classes.id, on delete cascade)
- to_section_id (big integer, foreign key to sections.id, on delete cascade, nullable)
- from_session_id (big integer, foreign key to academic_sessions.id, on delete cascade)
- to_session_id (big integer, foreign key to academic_sessions.id, on delete cascade)
- result (enum: promoted, detained, left, not null)
- remarks (text)
- promoted_by (big integer, foreign key to users.id, on delete set null)
- promoted_at (timestamp, default current timestamp)
Add indexes on: student_id, from_session_id, to_session_id
```

### Prompt 28: Create Attendance Types Table Migration
```
Create a Laravel migration for attendance_types table in smart-school project with:
- id (big integer, auto increment, primary key)
- name (varchar 50, not null)
- code (varchar 10, unique, not null)
- color (varchar 7, default '#000000')
- is_present (boolean, default false)
- is_active (boolean, default true)
- created_at and updated_at (timestamps)
```

### Prompt 29: Create Attendances Table Migration
```
Create a Laravel migration for attendances table in smart-school project with:
- id (big integer, auto increment, primary key)
- student_id (big integer, foreign key to students.id, on delete cascade)
- class_id (big integer, foreign key to classes.id, on delete cascade)
- section_id (big integer, foreign key to sections.id, on delete cascade)
- attendance_date (date, not null)
- attendance_type_id (big integer, foreign key to attendance_types.id, on delete cascade)
- remarks (text)
- marked_by (big integer, foreign key to users.id, on delete set null)
- created_at and updated_at (timestamps)
Add unique index on: (student_id, attendance_date)
Add indexes on: (class_id, section_id, attendance_date), attendance_date
```

### Prompt 30: Create Exam Types Table Migration
```
Create a Laravel migration for exam_types table in smart-school project with:
- id (big integer, auto increment, primary key)
- name (varchar 100, not null)
- code (varchar 20, unique, not null)
- description (text)
- is_active (boolean, default true)
- created_at and updated_at (timestamps)
- deleted_at (timestamp, nullable)
```

### Prompt 31: Create Exams Table Migration
```
Create a Laravel migration for exams table in smart-school project with:
- id (big integer, auto increment, primary key)
- academic_session_id (big integer, foreign key to academic_sessions.id, on delete cascade)
- exam_type_id (big integer, foreign key to exam_types.id, on delete cascade)
- name (varchar 255, not null)
- start_date (date, not null)
- end_date (date, not null)
- is_active (boolean, default true)
- created_at and updated_at (timestamps)
- deleted_at (timestamp, nullable)
Add indexes on: academic_session_id, exam_type_id
```

### Prompt 32: Create Exam Schedules Table Migration
```
Create a Laravel migration for exam_schedules table in smart-school project with:
- id (big integer, auto increment, primary key)
- exam_id (big integer, foreign key to exams.id, on delete cascade)
- class_id (big integer, foreign key to classes.id, on delete cascade)
- section_id (big integer, foreign key to sections.id, on delete cascade, nullable)
- subject_id (big integer, foreign key to subjects.id, on delete cascade)
- exam_date (date, not null)
- start_time (time, not null)
- end_time (time, not null)
- room_number (varchar 20)
- full_marks (decimal 5,2, not null)
- passing_marks (decimal 5,2, not null)
- created_at and updated_at (timestamps)
Add indexes on: exam_id, (class_id, section_id), exam_date
```

### Prompt 33: Create Exam Grades Table Migration
```
Create a Laravel migration for exam_grades table in smart-school project with:
- id (big integer, auto increment, primary key)
- name (varchar 10, not null)
- min_percentage (decimal 5,2, not null)
- max_percentage (decimal 5,2, not null)
- grade_point (decimal 3,2)
- remarks (varchar 255)
- is_active (boolean, default true)
- created_at and updated_at (timestamps)
```

### Prompt 34: Create Exam Attendance Table Migration
```
Create a Laravel migration for exam_attendance table in smart-school project with:
- id (big integer, auto increment, primary key)
- exam_schedule_id (big integer, foreign key to exam_schedules.id, on delete cascade)
- student_id (big integer, foreign key to students.id, on delete cascade)
- is_present (boolean, default true)
- remarks (text)
- marked_by (big integer, foreign key to users.id, on delete set null)
- created_at (timestamp)
Add unique index on: (exam_schedule_id, student_id)
```

### Prompt 35: Create Exam Marks Table Migration
```
Create a Laravel migration for exam_marks table in smart-school project with:
- id (big integer, auto increment, primary key)
- exam_schedule_id (big integer, foreign key to exam_schedules.id, on delete cascade)
- student_id (big integer, foreign key to students.id, on delete cascade)
- obtained_marks (decimal 5,2, not null)
- grade_id (big integer, foreign key to exam_grades.id, on delete set null)
- remarks (text)
- entered_by (big integer, foreign key to users.id, on delete set null)
- created_at and updated_at (timestamps)
Add unique index on: (exam_schedule_id, student_id)
Add indexes on: exam_schedule_id, student_id
```

### Prompt 36: Create Fees Types Table Migration
```
Create a Laravel migration for fees_types table in smart-school project with:
- id (big integer, auto increment, primary key)
- name (varchar 100, not null)
- code (varchar 20, unique, not null)
- description (text)
- is_active (boolean, default true)
- created_at and updated_at (timestamps)
- deleted_at (timestamp, nullable)
```

### Prompt 37: Create Fees Groups Table Migration
```
Create a Laravel migration for fees_groups table in smart-school project with:
- id (big integer, auto increment, primary key)
- name (varchar 100, not null)
- description (text)
- is_active (boolean, default true)
- created_at and updated_at (timestamps)
- deleted_at (timestamp, nullable)
```

### Prompt 38: Create Fees Masters Table Migration
```
Create a Laravel migration for fees_masters table in smart-school project with:
- id (big integer, auto increment, primary key)
- fees_type_id (big integer, foreign key to fees_types.id, on delete cascade)
- fees_group_id (big integer, foreign key to fees_groups.id, on delete set null)
- class_id (big integer, foreign key to classes.id, on delete cascade, nullable)
- section_id (big integer, foreign key to sections.id, on delete cascade, nullable)
- academic_session_id (big integer, foreign key to academic_sessions.id, on delete cascade)
- amount (decimal 10,2, not null)
- due_date (date)
- is_active (boolean, default true)
- created_at and updated_at (timestamps)
Add indexes on: (class_id, section_id), academic_session_id
```

### Prompt 39: Create Fees Discounts Table Migration
```
Create a Laravel migration for fees_discounts table in smart-school project with:
- id (big integer, auto increment, primary key)
- name (varchar 100, not null)
- code (varchar 20, unique, not null)
- discount_type (enum: percentage, fixed, not null)
- discount_value (decimal 5,2, not null)
- description (text)
- is_active (boolean, default true)
- created_at and updated_at (timestamps)
- deleted_at (timestamp, nullable)
```

### Prompt 40: Create Fees Allotments Table Migration
```
Create a Laravel migration for fees_allotments table in smart-school project with:
- id (big integer, auto increment, primary key)
- student_id (big integer, foreign key to students.id, on delete cascade)
- fees_master_id (big integer, foreign key to fees_masters.id, on delete cascade)
- discount_id (big integer, foreign key to fees_discounts.id, on delete set null)
- discount_amount (decimal 10,2, default 0)
- net_amount (decimal 10,2, not null)
- due_date (date)
- is_active (boolean, default true)
- created_at and updated_at (timestamps)
Add indexes on: student_id, fees_master_id
```

### Prompt 41: Create Fees Transactions Table Migration
```
Create a Laravel migration for fees_transactions table in smart-school project with:
- id (big integer, auto increment, primary key)
- student_id (big integer, foreign key to students.id, on delete cascade)
- fees_allotment_id (big integer, foreign key to fees_allotments.id, on delete cascade)
- transaction_id (varchar 100, unique, not null)
- amount (decimal 10,2, not null)
- payment_method (enum: cash, cheque, dd, online, not null)
- payment_status (enum: pending, completed, failed, refunded, default pending)
- payment_date (date, not null)
- transaction_date (timestamp, default current timestamp)
- reference_number (varchar 100)
- bank_name (varchar 100)
- cheque_number (varchar 50)
- remarks (text)
- received_by (big integer, foreign key to users.id, on delete set null)
- created_at and updated_at (timestamps)
Add indexes on: student_id, transaction_id, payment_date
```

### Prompt 42: Create Fees Fines Table Migration
```
Create a Laravel migration for fees_fines table in smart-school project with:
- id (big integer, auto increment, primary key)
- fees_master_id (big integer, foreign key to fees_masters.id, on delete cascade)
- fine_type (enum: daily, weekly, monthly, one_time, not null)
- fine_amount (decimal 10,2, not null)
- start_date (date)
- end_date (date)
- is_active (boolean, default true)
- created_at and updated_at (timestamps)
```

### Prompt 43: Create Library Categories Table Migration
```
Create a Laravel migration for library_categories table in smart-school project with:
- id (big integer, auto increment, primary key)
- name (varchar 100, not null)
- code (varchar 20, unique, not null)
- description (text)
- is_active (boolean, default true)
- created_at and updated_at (timestamps)
- deleted_at (timestamp, nullable)
```

### Prompt 44: Create Library Books Table Migration
```
Create a Laravel migration for library_books table in smart-school project with:
- id (big integer, auto increment, primary key)
- category_id (big integer, foreign key to library_categories.id, on delete cascade)
- isbn (varchar 20, unique)
- title (varchar 255, not null)
- author (varchar 255)
- publisher (varchar 255)
- edition (varchar 50)
- publish_year (integer)
- rack_number (varchar 20)
- quantity (integer, not null)
- available_quantity (integer, not null)
- price (decimal 10,2)
- language (varchar 50)
- pages (integer)
- description (text)
- cover_image (varchar 255)
- is_active (boolean, default true)
- created_at and updated_at (timestamps)
- deleted_at (timestamp, nullable)
Add indexes on: category_id, isbn, title
```

### Prompt 45: Create Library Members Table Migration
```
Create a Laravel migration for library_members table in smart-school project with:
- id (big integer, auto increment, primary key)
- member_type (enum: student, teacher, staff, not null)
- member_id (big integer, not null)
- membership_number (varchar 50, unique, not null)
- membership_date (date, not null)
- expiry_date (date)
- max_books (integer, default 5)
- is_active (boolean, default true)
- created_at and updated_at (timestamps)
- deleted_at (timestamp, nullable)
Add index on: (member_type, member_id)
```

### Prompt 46: Create Library Issues Table Migration
```
Create a Laravel migration for library_issues table in smart-school project with:
- id (big integer, auto increment, primary key)
- book_id (big integer, foreign key to library_books.id, on delete cascade)
- member_id (big integer, foreign key to library_members.id, on delete cascade)
- issue_date (date, not null)
- due_date (date, not null)
- return_date (date)
- fine_amount (decimal 10,2, default 0)
- fine_paid (boolean, default false)
- remarks (text)
- issued_by (big integer, foreign key to users.id, on delete set null)
- returned_by (big integer, foreign key to users.id, on delete set null)
- created_at and updated_at (timestamps)
Add indexes on: book_id, member_id, issue_date, due_date
```

### Prompt 47: Create Transport Vehicles Table Migration
```
Create a Laravel migration for transport_vehicles table in smart-school project with:
- id (big integer, auto increment, primary key)
- vehicle_number (varchar 20, unique, not null)
- vehicle_type (varchar 50)
- vehicle_model (varchar 100)
- capacity (integer, not null)
- driver_name (varchar 100)
- driver_phone (varchar 20)
- driver_license (varchar 50)
- route_id (big integer, foreign key to transport_routes.id, on delete set null)
- is_active (boolean, default true)
- created_at and updated_at (timestamps)
- deleted_at (timestamp, nullable)
Add index on: route_id
```

### Prompt 48: Create Transport Routes Table Migration
```
Create a Laravel migration for transport_routes table in smart-school project with:
- id (big integer, auto increment, primary key)
- name (varchar 100, not null)
- route_number (varchar 20, unique, not null)
- description (text)
- is_active (boolean, default true)
- created_at and updated_at (timestamps)
- deleted_at (timestamp, nullable)
```

### Prompt 49: Create Transport Route Stops Table Migration
```
Create a Laravel migration for transport_route_stops table in smart-school project with:
- id (big integer, auto increment, primary key)
- route_id (big integer, foreign key to transport_routes.id, on delete cascade)
- stop_name (varchar 100, not null)
- stop_order (integer, not null)
- stop_time (time)
- fare (decimal 10,2)
- is_active (boolean, default true)
- created_at and updated_at (timestamps)
Add index on: route_id
```

### Prompt 50: Create Transport Students Table Migration
```
Create a Laravel migration for transport_students table in smart-school project with:
- id (big integer, auto increment, primary key)
- student_id (big integer, foreign key to students.id, on delete cascade)
- vehicle_id (big integer, foreign key to transport_vehicles.id, on delete cascade)
- route_id (big integer, foreign key to transport_routes.id, on delete cascade)
- stop_id (big integer, foreign key to transport_route_stops.id, on delete cascade)
- academic_session_id (big integer, foreign key to academic_sessions.id, on delete cascade)
- transport_fees (decimal 10,2)
- is_active (boolean, default true)
- created_at and updated_at (timestamps)
Add indexes on: student_id, vehicle_id
```

### Prompt 51: Create Hostels Table Migration
```
Create a Laravel migration for hostels table in smart-school project with:
- id (big integer, auto increment, primary key)
- name (varchar 100, not null)
- code (varchar 20, unique, not null)
- type (enum: boys, girls, mixed, not null)
- address (text)
- city (varchar 100)
- state (varchar 100)
- postal_code (varchar 20)
- phone (varchar 20)
- email (varchar 255)
- warden_name (varchar 100)
- warden_phone (varchar 20)
- facilities (text)
- is_active (boolean, default true)
- created_at and updated_at (timestamps)
- deleted_at (timestamp, nullable)
```

### Prompt 52: Create Hostel Room Types Table Migration
```
Create a Laravel migration for hostel_room_types table in smart-school project with:
- id (big integer, auto increment, primary key)
- hostel_id (big integer, foreign key to hostels.id, on delete cascade)
- name (varchar 100, not null)
- capacity (integer, not null)
- beds_per_room (integer, not null)
- fees_per_month (decimal 10,2)
- facilities (text)
- is_active (boolean, default true)
- created_at and updated_at (timestamps)
Add index on: hostel_id
```

### Prompt 53: Create Hostel Rooms Table Migration
```
Create a Laravel migration for hostel_rooms table in smart-school project with:
- id (big integer, auto increment, primary key)
- hostel_id (big integer, foreign key to hostels.id, on delete cascade)
- room_type_id (big integer, foreign key to hostel_room_types.id, on delete cascade)
- room_number (varchar 20, not null)
- floor_number (integer)
- capacity (integer, not null)
- occupied (integer, default 0)
- is_active (boolean, default true)
- created_at and updated_at (timestamps)
- deleted_at (timestamp, nullable)
Add indexes on: hostel_id, unique on (hostel_id, room_number)
```

### Prompt 54: Create Hostel Assignments Table Migration
```
Create a Laravel migration for hostel_assignments table in smart-school project with:
- id (big integer, auto increment, primary key)
- student_id (big integer, foreign key to students.id, on delete cascade)
- hostel_id (big integer, foreign key to hostels.id, on delete cascade)
- room_id (big integer, foreign key to hostel_rooms.id, on delete cascade)
- academic_session_id (big integer, foreign key to academic_sessions.id, on delete cascade)
- admission_date (date, not null)
- leaving_date (date)
- hostel_fees (decimal 10,2)
- is_active (boolean, default true)
- created_at and updated_at (timestamps)
Add indexes on: student_id, room_id
```

### Prompt 55: Create Notices Table Migration
```
Create a Laravel migration for notices table in smart-school project with:
- id (big integer, auto increment, primary key)
- title (varchar 255, not null)
- content (text, not null)
- target_roles (json)
- target_classes (json)
- notice_date (date, not null)
- expiry_date (date)
- attachment (varchar 255)
- is_published (boolean, default false)
- published_by (big integer, foreign key to users.id, on delete set null)
- created_at and updated_at (timestamps)
Add indexes on: notice_date, is_published
```

### Prompt 56: Create Messages Table Migration
```
Create a Laravel migration for messages table in smart-school project with:
- id (big integer, auto increment, primary key)
- sender_id (big integer, foreign key to users.id, on delete cascade)
- subject (varchar 255, not null)
- message (text, not null)
- attachment (varchar 255)
- is_read (boolean, default false)
- read_at (timestamp, nullable)
- created_at and updated_at (timestamps)
Add indexes on: sender_id, is_read
```

### Prompt 57: Create Message Recipients Table Migration
```
Create a Laravel migration for message_recipients table in smart-school project with:
- id (big integer, auto increment, primary key)
- message_id (big integer, foreign key to messages.id, on delete cascade)
- recipient_id (big integer, foreign key to users.id, on delete cascade)
- is_read (boolean, default false)
- read_at (timestamp, nullable)
- created_at (timestamp)
Add indexes on: message_id, recipient_id
```

### Prompt 58: Create SMS Logs Table Migration
```
Create a Laravel migration for sms_logs table in smart-school project with:
- id (big integer, auto increment, primary key)
- recipient (varchar 20, not null)
- message (text, not null)
- gateway (varchar 50)
- status (enum: pending, sent, failed, delivered, default pending)
- sent_at (timestamp, nullable)
- response (text)
- created_at (timestamp)
Add indexes on: status, sent_at
```

### Prompt 59: Create Email Logs Table Migration
```
Create a Laravel migration for email_logs table in smart-school project with:
- id (big integer, auto increment, primary key)
- recipient (varchar 255, not null)
- subject (varchar 255, not null)
- body (text, not null)
- status (enum: pending, sent, failed, default pending)
- sent_at (timestamp, nullable)
- error_message (text)
- created_at (timestamp)
Add indexes on: status, sent_at
```

### Prompt 60: Create Expense Categories Table Migration
```
Create a Laravel migration for expense_categories table in smart-school project with:
- id (big integer, auto increment, primary key)
- name (varchar 100, not null)
- code (varchar 20, unique, not null)
- description (text)
- is_active (boolean, default true)
- created_at and updated_at (timestamps)
- deleted_at (timestamp, nullable)
```

### Prompt 61: Create Income Categories Table Migration
```
Create a Laravel migration for income_categories table in smart-school project with:
- id (big integer, auto increment, primary key)
- name (varchar 100, not null)
- code (varchar 20, unique, not null)
- description (text)
- is_active (boolean, default true)
- created_at and updated_at (timestamps)
- deleted_at (timestamp, nullable)
```

### Prompt 62: Create Expenses Table Migration
```
Create a Laravel migration for expenses table in smart-school project with:
- id (big integer, auto increment, primary key)
- category_id (big integer, foreign key to expense_categories.id, on delete cascade)
- title (varchar 255, not null)
- description (text)
- amount (decimal 10,2, not null)
- expense_date (date, not null)
- payment_method (varchar 50)
- reference_number (varchar 100)
- attachment (varchar 255)
- created_by (big integer, foreign key to users.id, on delete set null)
- created_at and updated_at (timestamps)
Add indexes on: category_id, expense_date
```

### Prompt 63: Create Income Table Migration
```
Create a Laravel migration for income table in smart-school project with:
- id (big integer, auto increment, primary key)
- category_id (big integer, foreign key to income_categories.id, on delete cascade)
- title (varchar 255, not null)
- description (text)
- amount (decimal 10,2, not null)
- income_date (date, not null)
- payment_method (varchar 50)
- reference_number (varchar 100)
- attachment (varchar 255)
- created_by (big integer, foreign key to users.id, on delete set null)
- created_at and updated_at (timestamps)
Add indexes on: category_id, income_date
```

### Prompt 64: Create Settings Table Migration
```
Create a Laravel migration for settings table in smart-school project with:
- id (big integer, auto increment, primary key)
- key (varchar 100, unique, not null)
- value (text)
- type (enum: string, number, boolean, json, default string)
- category (varchar 50)
- description (text)
- is_public (boolean, default false)
- created_at and updated_at (timestamps)
Add indexes on: key, category
```

### Prompt 65: Create Languages Table Migration
```
Create a Laravel migration for languages table in smart-school project with:
- id (big integer, auto increment, primary key)
- code (varchar 10, unique, not null)
- name (varchar 100, not null)
- native_name (varchar 100)
- direction (enum: ltr, rtl, default ltr)
- is_active (boolean, default true)
- is_default (boolean, default false)
- created_at and updated_at (timestamps)
```

### Prompt 66: Create Translations Table Migration
```
Create a Laravel migration for translations table in smart-school project with:
- id (big integer, auto increment, primary key)
- language_id (big integer, foreign key to languages.id, on delete cascade)
- key (varchar 255, not null)
- value (text)
- created_at and updated_at (timestamps)
Add unique index on: (language_id, key)
Add indexes on: language_id, key
```

### Prompt 67: Create Backups Table Migration
```
Create a Laravel migration for backups table in smart-school project with:
- id (big integer, auto increment, primary key)
- filename (varchar 255, not null)
- file_path (varchar 255, not null)
- file_size (big integer)
- type (enum: full, database, files, not null)
- status (enum: pending, completed, failed, default pending)
- created_by (big integer, foreign key to users.id, on delete set null)
- created_at (timestamp)
Add indexes on: type, status, created_at
```

### Prompt 68: Create Downloads Table Migration
```
Create a Laravel migration for downloads table in smart-school project with:
- id (big integer, auto increment, primary key)
- title (varchar 255, not null)
- description (text)
- category (varchar 50)
- file_path (varchar 255, not null)
- file_size (big integer)
- file_type (varchar 50)
- target_roles (json)
- target_classes (json)
- is_active (boolean, default true)
- uploaded_by (big integer, foreign key to users.id, on delete set null)
- created_at and updated_at (timestamps)
Add indexes on: category, is_active
```

### Prompt 69: Create Homework Table Migration
```
Create a Laravel migration for homework table in smart-school project with:
- id (big integer, auto increment, primary key)
- class_id (big integer, foreign key to classes.id, on delete cascade)
- section_id (big integer, foreign key to sections.id, on delete cascade, nullable)
- subject_id (big integer, foreign key to subjects.id, on delete cascade)
- teacher_id (big integer, foreign key to users.id, on delete cascade)
- title (varchar 255, not null)
- description (text)
- attachment (varchar 255)
- submission_date (date, not null)
- is_active (boolean, default true)
- created_at and updated_at (timestamps)
Add indexes on: (class_id, section_id), submission_date
```

### Prompt 70: Create Study Materials Table Migration
```
Create a Laravel migration for study_materials table in smart-school project with:
- id (big integer, auto increment, primary key)
- class_id (big integer, foreign key to classes.id, on delete cascade)
- section_id (big integer, foreign key to sections.id, on delete cascade, nullable)
- subject_id (big integer, foreign key to subjects.id, on delete cascade)
- teacher_id (big integer, foreign key to users.id, on delete cascade)
- title (varchar 255, not null)
- description (text)
- file_path (varchar 255, not null)
- file_size (big integer)
- file_type (varchar 50)
- is_active (boolean, default true)
- uploaded_by (big integer, foreign key to users.id, on delete set null)
- created_at and updated_at (timestamps)
Add indexes on: (class_id, section_id)
```

---

## 📝 Phase 3: Model Creation

### Prompt 71: Create User Model
```
Create a Laravel User model in smart-school project with:
- Use SoftDeletes trait
- Implement HasRoles and HasPermissions interfaces from Spatie Permission
- Define relationship to Role (belongsToMany)
- Define relationship to Permission (belongsToMany)
- Define relationship to Student (hasOne)
- Define relationship to Attendance (hasMany)
- Define relationship to ExamMark (hasMany)
- Define relationship to FeesTransaction (hasMany)
- Define relationship to LibraryIssue (hasMany)
- Define relationship to Message (hasMany)
- Define relationship to Expense (hasMany)
- Define relationship to Income (hasMany)
- Add fillable fields for mass assignment
- Add hidden fields for password
- Add casts for date fields
```

### Prompt 72: Create Role Model
```
Create a Laravel Role model in smart-school project with:
- Use HasPermissions trait from Spatie Permission
- Define relationship to Permission (belongsToMany)
- Define relationship to User (belongsToMany)
- Add fillable fields
```

### Prompt 73: Create Permission Model
```
Create a Laravel Permission model in smart-school project with:
- Use HasRoles trait from Spatie Permission
- Define relationship to Role (belongsToMany)
- Add fillable fields
```

### Prompt 74: Create AcademicSession Model
```
Create a Laravel AcademicSession model in smart-school project with:
- Use SoftDeletes trait
- Define relationship to Class (hasMany)
- Define relationship to Student (hasMany)
- Define relationship to Exam (hasMany)
- Define relationship to FeesMaster (hasMany)
- Define relationship to TransportStudent (hasMany)
- Define relationship to HostelAssignment (hasMany)
- Add fillable fields
- Add casts for date fields
```

### Prompt 75: Create Class Model
```
Create a Laravel Class model in smart-school project with:
- Use SoftDeletes trait
- Define relationship to AcademicSession (belongsTo)
- Define relationship to Section (hasMany)
- Define relationship to Student (hasMany)
- Define relationship to ClassSubject (hasMany)
- Define relationship to ClassTimetable (hasMany)
- Define relationship to FeesMaster (hasMany)
- Define relationship to ExamSchedule (hasMany)
- Define relationship to Homework (hasMany)
- Define relationship to StudyMaterial (hasMany)
- Add fillable fields
```

### Prompt 76: Create Section Model
```
Create a Laravel Section model in smart-school project with:
- Use SoftDeletes trait
- Define relationship to Class (belongsTo)
- Define relationship to User (belongsTo, class_teacher)
- Define relationship to Student (hasMany)
- Define relationship to ClassSubject (hasMany)
- Define relationship to ClassTimetable (hasMany)
- Define relationship to Attendance (hasMany)
- Define relationship to Homework (hasMany)
- Define relationship to StudyMaterial (hasMany)
- Add fillable fields
```

### Prompt 77: Create Subject Model
```
Create a Laravel Subject model in smart-school project with:
- Use SoftDeletes trait
- Define relationship to ClassSubject (hasMany)
- Define relationship to ClassTimetable (hasMany)
- Define relationship to ExamSchedule (hasMany)
- Define relationship to Homework (hasMany)
- Define relationship to StudyMaterial (hasMany)
- Add fillable fields
```

### Prompt 78: Create Student Model
```
Create a Laravel Student model in smart-school project with:
- Use SoftDeletes trait
- Define relationship to User (belongsTo)
- Define relationship to AcademicSession (belongsTo)
- Define relationship to Class (belongsTo)
- Define relationship to Section (belongsTo)
- Define relationship to StudentCategory (belongsTo)
- Define relationship to StudentSibling (hasMany)
- Define relationship to StudentDocument (hasMany)
- Define relationship to StudentPromotion (hasMany)
- Define relationship to Attendance (hasMany)
- Define relationship to ExamMark (hasMany)
- Define relationship to FeesAllotment (hasMany)
- Define relationship to FeesTransaction (hasMany)
- Define relationship to TransportStudent (hasOne)
- Define relationship to HostelAssignment (hasOne)
- Add fillable fields
- Add casts for date fields
```

### Prompt 79: Create Attendance Model
```
Create a Laravel Attendance model in smart-school project with:
- Define relationship to Student (belongsTo)
- Define relationship to Class (belongsTo)
- Define relationship to Section (belongsTo)
- Define relationship to AttendanceType (belongsTo)
- Define relationship to User (belongsTo, marked_by)
- Add fillable fields
- Add casts for date fields
```

### Prompt 80: Create Exam Model
```
Create a Laravel Exam model in smart-school project with:
- Use SoftDeletes trait
- Define relationship to AcademicSession (belongsTo)
- Define relationship to ExamType (belongsTo)
- Define relationship to ExamSchedule (hasMany)
- Add fillable fields
- Add casts for date fields
```

### Prompt 81: Create ExamSchedule Model
```
Create a Laravel ExamSchedule model in smart-school project with:
- Define relationship to Exam (belongsTo)
- Define relationship to Class (belongsTo)
- Define relationship to Section (belongsTo)
- Define relationship to Subject (belongsTo)
- Define relationship to ExamAttendance (hasMany)
- Define relationship to ExamMark (hasMany)
- Add fillable fields
- Add casts for date and time fields
```

### Prompt 82: Create ExamMark Model
```
Create a Laravel ExamMark model in smart-school project with:
- Define relationship to ExamSchedule (belongsTo)
- Define relationship to Student (belongsTo)
- Define relationship to ExamGrade (belongsTo)
- Define relationship to User (belongsTo, entered_by)
- Add fillable fields
```

### Prompt 83: Create FeesAllotment Model
```
Create a Laravel FeesAllotment model in smart-school project with:
- Define relationship to Student (belongsTo)
- Define relationship to FeesMaster (belongsTo)
- Define relationship to FeesDiscount (belongsTo)
- Define relationship to FeesTransaction (hasMany)
- Add fillable fields
- Add casts for date fields
```

### Prompt 84: Create FeesTransaction Model
```
Create a Laravel FeesTransaction model in smart-school project with:
- Define relationship to Student (belongsTo)
- Define relationship to FeesAllotment (belongsTo)
- Define relationship to User (belongsTo, received_by)
- Add fillable fields
- Add casts for date fields
```

### Prompt 85: Create LibraryBook Model
```
Create a Laravel LibraryBook model in smart-school project with:
- Use SoftDeletes trait
- Define relationship to LibraryCategory (belongsTo)
- Define relationship to LibraryIssue (hasMany)
- Add fillable fields
```

### Prompt 86: Create LibraryIssue Model
```
Create a Laravel LibraryIssue model in smart-school project with:
- Define relationship to LibraryBook (belongsTo)
- Define relationship to LibraryMember (belongsTo)
- Define relationship to User (belongsTo, issued_by)
- Define relationship to User (belongsTo, returned_by)
- Add fillable fields
- Add casts for date fields
```

---

## 🎨 Phase 4: Authentication & Authorization

### Prompt 87: Install Laravel Breeze
```
Install Laravel Breeze package in smart-school project for authentication scaffolding.
Run: composer require laravel/breeze --dev
```

### Prompt 88: Install Breeze Blade Stack
```
Install Laravel Breeze with Blade stack for authentication in smart-school project.
Run: php artisan breeze:install blade
```

### Prompt 89: Install Spatie Permission
```
Install Spatie Permission package in smart-school project for role-based access control.
Run: composer require spatie/laravel-permission
```

### Prompt 90: Publish Spatie Permission
```
Publish Spatie Permission configuration and migration files in smart-school project.
Run: php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

### Prompt 91: Create Role Seeder
```
Create a RoleSeeder in smart-school project database/seeders directory that creates the following roles:
- admin
- teacher
- student
- parent
- accountant
- librarian
```

### Prompt 92: Create Permission Seeder
```
Create a PermissionSeeder in smart-school project database/seeders directory that creates permissions for all modules:
- Student management permissions (view students, create students, edit students, delete students)
- Academic management permissions (view classes, create classes, edit classes, delete classes)
- Attendance permissions (view attendance, mark attendance)
- Examination permissions (view exams, create exams, edit exams, delete exams, enter marks)
- Fees permissions (view fees, collect fees, manage fees)
- Library permissions (view library, manage books, issue books)
- Transport permissions (view transport, manage transport)
- Hostel permissions (view hostel, manage hostel)
- Communication permissions (view notices, create notices, send messages)
- Accounting permissions (view expenses, create expenses, view income)
```

### Prompt 93: Create Admin User Seeder
```
Create an AdminUserSeeder in smart-school project database/seeders directory that creates a default admin user:
- Email: admin@smartschool.com
- Password: password (hashed)
- First name: Admin
- Last name: User
- Assign admin role
- Assign all permissions
```

### Prompt 94: Run All Seeders
```
Run all database seeders in smart-school project.
Run: php artisan db:seed
```

---

## 🎨 Phase 5: Views & Layouts

### Prompt 95: Create Base Layout
```
Create a base layout file at resources/views/layouts/app.blade.php in smart-school project with:
- HTML5 doctype
- Meta tags for viewport and charset
- Title with app name
- Include CSS from Vite
- Include navigation component
- Yield content section
- Include footer component
- Include JS from Vite
```

### Prompt 96: Create Navigation Component
```
Create a navigation component at resources/views/layouts/navigation.blade.php in smart-school project with:
- Sidebar navigation
- Links to all modules based on user role
- User profile dropdown
- Logout button
- Responsive mobile menu
```

### Prompt 97: Create Footer Component
```
Create a footer component at resources/views/layouts/footer.blade.php in smart-school project with:
- Copyright information
- Quick links
- Version number
- Social media links (optional)
```

### Prompt 98: Create Login View
```
Create a login view at resources/views/auth/login.blade.php in smart-school project with:
- Login form with email and password fields
- Remember me checkbox
- Forgot password link
- Form validation error display
- CSRF token
- Responsive design using Bootstrap 5
```

### Prompt 99: Create Dashboard View
```
Create a dashboard view at resources/views/admin/dashboard.blade.php in smart-school project with:
- Statistics cards (total students, total teachers, total classes, etc.)
- Recent activities list
- Quick action buttons
- Charts for data visualization
- Responsive grid layout
```

---

## 🎯 Phase 6: Controllers

### Prompt 100: Create Auth Controller
```
Create an AuthController in smart-school project app/Http/Controllers directory with:
- showLoginForm method to display login page
- login method to handle login authentication
- logout method to handle logout
- showRegistrationForm method to display registration form
- register method to handle user registration
- showForgotPasswordForm method to display forgot password form
- sendPasswordResetLink method to send reset link
- showResetPasswordForm method to display reset password form
- resetPassword method to handle password reset
```

### Prompt 101: Create Dashboard Controller
```
Create a DashboardController in smart-school project app/Http/Controllers/Admin directory with:
- index method to display admin dashboard
- Get statistics data (total students, teachers, classes, etc.)
- Get recent activities
- Get chart data for visualization
```

### Prompt 102: Create Student Controller
```
Create a StudentController in smart-school project app/Http/Controllers directory with:
- index method to list all students with pagination, search, and filters
- create method to show student admission form
- store method to save new student with validation
- show method to display student details
- edit method to show student edit form
- update method to update student with validation
- destroy method to delete student (soft delete)
- search method to search students by name, admission number, father name
- promote method to promote student to next class
```

### Prompt 103: Create Academic Session Controller
```
Create an AcademicSessionController in smart-school project app/Http/Controllers/Admin directory with:
- index method to list all academic sessions
- create method to show create form
- store method to save new academic session
- show method to display academic session details
- edit method to show edit form
- update method to update academic session
- destroy method to delete academic session
- setCurrent method to set current academic session
```

### Prompt 104: Create Class Controller
```
Create a ClassController in smart-school project app/Http/Controllers/Admin directory with:
- index method to list all classes
- create method to show create form
- store method to save new class
- show method to display class details
- edit method to show edit form
- update method to update class
- destroy method to delete class
```

### Prompt 105: Create Section Controller
```
Create a SectionController in smart-school project app/Http/Controllers/Admin directory with:
- index method to list all sections
- create method to show create form
- store method to save new section
- show method to display section details
- edit method to show edit form
- update method to update section
- destroy method to delete section
```

### Prompt 106: Create Subject Controller
```
Create a SubjectController in smart-school project app/Http/Controllers/Admin directory with:
- index method to list all subjects
- create method to show create form
- store method to save new subject
- show method to display subject details
- edit method to show edit form
- update method to update subject
- destroy method to delete subject
```

---

## 🚀 Next Steps

Continue with prompts for:
- Attendance controllers and views
- Examination controllers and views
- Fees controllers and views
- Library controllers and views
- Transport controllers and views
- Hostel controllers and views
- Communication controllers and views
- Accounting controllers and views
- Report generation
- Multi-language support
- Testing and deployment

Each prompt should be small, focused, and executable independently.

---

## 📞 Support

For questions or issues:
1. Review planning documents in `../plans/` directory
2. Check Laravel documentation
3. Refer to implementation roadmap

**Happy Building with DevIn AI!** 🚀
