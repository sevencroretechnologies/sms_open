# Smart School Management System - What to Expect & Final Structure

This document provides a comprehensive overview of what to expect from the Smart School Management System project, including the final project structure, database schema, naming conventions, and deliverables.

---

## 📋 Table of Contents

1. [Project Overview](#project-overview)
2. [Technology Stack](#technology-stack)
3. [Final Project Structure](#final-project-structure)
4. [Database Schema](#database-schema)
5. [Naming Conventions](#naming-conventions)
6. [Expected Features](#expected-features)
7. [User Roles & Permissions](#user-roles--permissions)
8. [API Endpoints](#api-endpoints)
9. [Frontend Pages](#frontend-pages)
10. [Deliverables](#deliverables)

---

## 🎯 Project Overview

### Project Name
**Smart School Management System**

### Project Description
A comprehensive school management system built with Laravel (PHP) and Bootstrap 5 that manages all aspects of school operations including student management, academics, attendance, examinations, fees, library, transport, hostel, communication, and accounting.

### Target Users
- **School Administrators**: Manage all school operations
- **Teachers**: Manage classes, attendance, exams, homework, study materials
- **Students**: View academics, attendance, results, fees, homework
- **Parents**: Monitor children's academics, attendance, fees, results
- **Accountants**: Manage fee collection, income, expenses
- **Librarians**: Manage library books, issues, returns

### Key Features
- Multi-language support (73+ languages including RTL)
- Role-based access control (6 user roles)
- Responsive design (mobile, tablet, desktop)
- Real-time notifications (SMS, Email)
- Comprehensive reporting (PDF, Excel, CSV)
- Data backup and restore
- Multi-academic session management

---

## 💻 Technology Stack

### Backend
- **Framework**: Laravel 11.x
- **PHP Version**: 8.2+
- **Database**: MySQL 8.0+ or PostgreSQL 13+
- **Cache**: Redis 7.0+
- **Queue**: Redis or Database

### Frontend
- **CSS Framework**: Bootstrap 5.3+
- **JavaScript Framework**: Alpine.js 3.x
- **Charts**: Chart.js 4.x
- **Icons**: Bootstrap Icons or FontAwesome 6.x
- **Rich Text Editor**: TinyMCE or CKEditor
- **Date/Time Picker**: Flatpickr or Bootstrap Datepicker
- **File Upload**: Dropzone.js
- **PDF Generation**: DomPDF
- **Excel Export**: Laravel Excel (PHPSpreadsheet)
- **Notifications**: SweetAlert2

### Authentication & Authorization
- **Authentication**: Laravel Breeze
- **Authorization**: Spatie Permission (Role-Based Access Control)

### Third-Party Integrations
- **SMS Gateway**: Twilio, MSG91, or similar
- **Email Service**: SMTP, SendGrid, Mailgun, or similar
- **File Storage**: Local, AWS S3, or similar

---

## 📁 Final Project Structure

```
smart-school/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── AcademicSessionController.php
│   │   │   │   ├── ClassController.php
│   │   │   │   ├── SectionController.php
│   │   │   │   ├── SubjectController.php
│   │   │   │   ├── ClassSubjectController.php
│   │   │   │   ├── ClassTimetableController.php
│   │   │   │   ├── StudentController.php
│   │   │   │   ├── StudentSiblingController.php
│   │   │   │   ├── StudentDocumentController.php
│   │   │   │   ├── StudentPromotionController.php
│   │   │   │   ├── AttendanceController.php
│   │   │   │   ├── AttendanceTypeController.php
│   │   │   │   ├── ExamController.php
│   │   │   │   ├── ExamTypeController.php
│   │   │   │   ├── ExamScheduleController.php
│   │   │   │   ├── ExamAttendanceController.php
│   │   │   │   ├── ExamMarkController.php
│   │   │   │   ├── ExamGradeController.php
│   │   │   │   ├── FeesTypeController.php
│   │   │   │   ├── FeesGroupController.php
│   │   │   │   ├── FeesMasterController.php
│   │   │   │   ├── FeesDiscountController.php
│   │   │   │   ├── FeesAllotmentController.php
│   │   │   │   ├── FeesTransactionController.php
│   │   │   │   ├── LibraryCategoryController.php
│   │   │   │   ├── LibraryBookController.php
│   │   │   │   ├── LibraryMemberController.php
│   │   │   │   ├── LibraryIssueController.php
│   │   │   │   ├── TransportRouteController.php
│   │   │   │   ├── TransportRouteStopController.php
│   │   │   │   ├── TransportVehicleController.php
│   │   │   │   ├── TransportStudentController.php
│   │   │   │   ├── HostelController.php
│   │   │   │   ├── HostelRoomTypeController.php
│   │   │   │   ├── HostelRoomController.php
│   │   │   │   ├── HostelAssignmentController.php
│   │   │   │   ├── NoticeController.php
│   │   │   │   ├── MessageController.php
│   │   │   │   ├── MessageRecipientController.php
│   │   │   │   ├── SmsLogController.php
│   │   │   │   ├── EmailLogController.php
│   │   │   │   ├── DownloadController.php
│   │   │   │   ├── HomeworkController.php
│   │   │   │   ├── StudyMaterialController.php
│   │   │   │   ├── ExpenseCategoryController.php
│   │   │   │   ├── ExpenseController.php
│   │   │   │   ├── IncomeCategoryController.php
│   │   │   │   ├── IncomeController.php
│   │   │   │   ├── BackupController.php
│   │   │   │   ├── ReportController.php
│   │   │   │   ├── SettingsController.php
│   │   │   │   ├── LanguageController.php
│   │   │   │   ├── TranslationController.php
│   │   │   │   ├── UserController.php
│   │   │   │   ├── ProfileController.php
│   │   │   │   └── DashboardController.php
│   │   │   ├── Teacher/
│   │   │   │   ├── TeacherDashboardController.php
│   │   │   │   ├── ClassController.php
│   │   │   │   ├── SectionController.php
│   │   │   │   ├── SubjectController.php
│   │   │   │   ├── AttendanceController.php
│   │   │   │   ├── ExamScheduleController.php
│   │   │   │   ├── ExamMarkController.php
│   │   │   │   ├── HomeworkController.php
│   │   │   │   ├── StudyMaterialController.php
│   │   │   │   └── MessageController.php
│   │   │   ├── Student/
│   │   │   │   ├── StudentDashboardController.php
│   │   │   │   ├── AttendanceController.php
│   │   │   │   ├── ExamMarkController.php
│   │   │   │   ├── FeesAllotmentController.php
│   │   │   │   ├── HomeworkController.php
│   │   │   │   ├── StudyMaterialController.php
│   │   │   │   ├── LibraryIssueController.php
│   │   │   │   ├── TransportStudentController.php
│   │   │   │   ├── HostelAssignmentController.php
│   │   │   │   └── MessageController.php
│   │   │   ├── Parent/
│   │   │   │   ├── ParentDashboardController.php
│   │   │   │   ├── StudentController.php
│   │   │   │   ├── AttendanceController.php
│   │   │   │   ├── ExamMarkController.php
│   │   │   │   ├── FeesAllotmentController.php
│   │   │   │   ├── FeesTransactionController.php
│   │   │   │   ├── HomeworkController.php
│   │   │   │   ├── StudyMaterialController.php
│   │   │   │   ├── NoticeController.php
│   │   │   │   └── MessageController.php
│   │   │   ├── Accountant/
│   │   │   │   ├── AccountantDashboardController.php
│   │   │   │   ├── FeesAllotmentController.php
│   │   │   │   ├── FeesTransactionController.php
│   │   │   │   ├── IncomeController.php
│   │   │   │   ├── ExpenseController.php
│   │   │   │   ├── ReportController.php
│   │   │   │   └── SettingsController.php
│   │   │   ├── Librarian/
│   │   │   │   ├── LibrarianDashboardController.php
│   │   │   │   ├── LibraryCategoryController.php
│   │   │   │   ├── LibraryBookController.php
│   │   │   │   ├── LibraryMemberController.php
│   │   │   │   ├── LibraryIssueController.php
│   │   │   │   └── ReportController.php
│   │   │   ├── AuthController.php
│   │   │   └── Controller.php
│   │   ├── Middleware/
│   │   │   ├── Authenticate.php
│   │   │   ├── CheckRole.php
│   │   │   ├── CheckPermission.php
│   │   │   ├── SetLanguage.php
│   │   │   └── SetTheme.php
│   │   ├── Requests/
│   │   │   ├── Auth/
│   │   │   │   ├── LoginRequest.php
│   │   │   │   ├── RegisterRequest.php
│   │   │   │   └── ForgotPasswordRequest.php
│   │   │   ├── Student/
│   │   │   │   ├── StoreStudentRequest.php
│   │   │   │   ├── UpdateStudentRequest.php
│   │   │   │   └── PromoteStudentRequest.php
│   │   │   ├── Attendance/
│   │   │   │   └── StoreAttendanceRequest.php
│   │   │   ├── Exam/
│   │   │   │   └── StoreExamMarkRequest.php
│   │   │   ├── Fees/
│   │   │   │   ├── StoreFeesAllotmentRequest.php
│   │   │   │   └── StoreFeesTransactionRequest.php
│   │   │   └── ...
│   │   └── Kernel.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Role.php
│   │   ├── Permission.php
│   │   ├── AcademicSession.php
│   │   ├── Class.php
│   │   ├── Section.php
│   │   ├── Subject.php
│   │   ├── ClassSubject.php
│   │   ├── ClassTimetable.php
│   │   ├── Student.php
│   │   ├── StudentSibling.php
│   │   ├── StudentDocument.php
│   │   ├── StudentCategory.php
│   │   ├── StudentPromotion.php
│   │   ├── AttendanceType.php
│   │   ├── Attendance.php
│   │   ├── ExamType.php
│   │   ├── Exam.php
│   │   ├── ExamSchedule.php
│   │   ├── ExamGrade.php
│   │   ├── ExamAttendance.php
│   │   ├── ExamMark.php
│   │   ├── FeesType.php
│   │   ├── FeesGroup.php
│   │   ├── FeesMaster.php
│   │   ├── FeesDiscount.php
│   │   ├── FeesAllotment.php
│   │   ├── FeesTransaction.php
│   │   ├── LibraryCategory.php
│   │   ├── LibraryBook.php
│   │   ├── LibraryMember.php
│   │   ├── LibraryIssue.php
│   │   ├── TransportRoute.php
│   │   ├── TransportRouteStop.php
│   │   ├── TransportVehicle.php
│   │   ├── TransportStudent.php
│   │   ├── Hostel.php
│   │   ├── HostelRoomType.php
│   │   ├── HostelRoom.php
│   │   ├── HostelAssignment.php
│   │   ├── Notice.php
│   │   ├── Message.php
│   │   ├── MessageRecipient.php
│   │   ├── SmsLog.php
│   │   ├── EmailLog.php
│   │   ├── Download.php
│   │   ├── Homework.php
│   │   ├── StudyMaterial.php
│   │   ├── ExpenseCategory.php
│   │   ├── Expense.php
│   │   ├── IncomeCategory.php
│   │   ├── Income.php
│   │   ├── Setting.php
│   │   ├── Language.php
│   │   ├── Translation.php
│   │   └── Backup.php
│   ├── Services/
│   │   ├── SmsService.php
│   │   ├── EmailService.php
│   │   ├── ExportService.php
│   │   ├── ReportService.php
│   │   ├── BackupService.php
│   │   └── NotificationService.php
│   └── Providers/
│       ├── AppServiceProvider.php
│       ├── AuthServiceProvider.php
│       ├── BroadcastServiceProvider.php
│       ├── EventServiceProvider.php
│       └── RouteServiceProvider.php
├── bootstrap/
│   ├── app.php
│   └── cache/
├── config/
│   ├── app.php
│   ├── auth.php
│   ├── cache.php
│   ├── database.php
│   ├── filesystems.php
│   ├── logging.php
│   ├── mail.php
│   ├── queue.php
│   ├── services.php
│   ├── session.php
│   ├── view.php
│   └── permission.php
├── database/
│   ├── migrations/
│   │   ├── 2024_01_01_000001_create_users_table.php
│   │   ├── 2024_01_01_000002_create_roles_table.php
│   │   ├── 2024_01_01_000003_create_permissions_table.php
│   │   ├── 2024_01_01_000004_create_role_has_permissions_table.php
│   │   ├── 2024_01_01_000005_create_model_has_permissions_table.php
│   │   ├── 2024_01_01_000006_create_model_has_roles_table.php
│   │   ├── 2024_01_01_000007_create_academic_sessions_table.php
│   │   ├── 2024_01_01_000008_create_classes_table.php
│   │   ├── 2024_01_01_000009_create_sections_table.php
│   │   ├── 2024_01_01_000010_create_subjects_table.php
│   │   ├── 2024_01_01_000011_create_class_subjects_table.php
│   │   ├── 2024_01_01_000012_create_class_timetables_table.php
│   │   ├── 2024_01_01_000013_create_students_table.php
│   │   ├── 2024_01_01_000014_create_student_siblings_table.php
│   │   ├── 2024_01_01_000015_create_student_documents_table.php
│   │   ├── 2024_01_01_000016_create_student_categories_table.php
│   │   ├── 2024_01_01_000017_create_student_promotions_table.php
│   │   ├── 2024_01_01_000018_create_attendance_types_table.php
│   │   ├── 2024_01_01_000019_create_attendances_table.php
│   │   ├── 2024_01_01_000020_create_exam_types_table.php
│   │   ├── 2024_01_01_000021_create_exams_table.php
│   │   ├── 2024_01_01_000022_create_exam_schedules_table.php
│   │   ├── 2024_01_01_000023_create_exam_grades_table.php
│   │   ├── 2024_01_01_000024_create_exam_attendance_table.php
│   │   ├── 2024_01_01_000025_create_exam_marks_table.php
│   │   ├── 2024_01_01_000026_create_fees_types_table.php
│   │   ├── 2024_01_01_000027_create_fees_groups_table.php
│   │   ├── 2024_01_01_000028_create_fees_masters_table.php
│   │   ├── 2024_01_01_000029_create_fees_discounts_table.php
│   │   ├── 2024_01_01_000030_create_fees_allotments_table.php
│   │   ├── 2024_01_01_000031_create_fees_transactions_table.php
│   │   ├── 2024_01_01_000032_create_fees_fines_table.php
│   │   ├── 2024_01_01_000033_create_library_categories_table.php
│   │   ├── 2024_01_01_000034_create_library_books_table.php
│   │   ├── 2024_01_01_000035_create_library_members_table.php
│   │   ├── 2024_01_01_000036_create_library_issues_table.php
│   │   ├── 2024_01_01_000037_create_transport_routes_table.php
│   │   ├── 2024_01_01_000038_create_transport_route_stops_table.php
│   │   ├── 2024_01_01_000039_create_transport_vehicles_table.php
│   │   ├── 2024_01_01_000040_create_transport_students_table.php
│   │   ├── 2024_01_01_000041_create_hostels_table.php
│   │   ├── 2024_01_01_000042_create_hostel_room_types_table.php
│   │   ├── 2024_01_01_000043_create_hostel_rooms_table.php
│   │   ├── 2024_01_01_000044_create_hostel_assignments_table.php
│   │   ├── 2024_01_01_000045_create_notices_table.php
│   │   ├── 2024_01_01_000046_create_messages_table.php
│   │   ├── 2024_01_01_000047_create_message_recipients_table.php
│   │   ├── 2024_01_01_000048_create_sms_logs_table.php
│   │   ├── 2024_01_01_000049_create_email_logs_table.php
│   │   ├── 2024_01_01_000050_create_expense_categories_table.php
│   │   ├── 2024_01_01_000051_create_income_categories_table.php
│   │   ├── 2024_01_01_000052_create_expenses_table.php
│   │   ├── 2024_01_01_000053_create_income_table.php
│   │   ├── 2024_01_01_000054_create_settings_table.php
│   │   ├── 2024_01_01_000055_create_languages_table.php
│   │   ├── 2024_01_01_000056_create_translations_table.php
│   │   ├── 2024_01_01_000057_create_backups_table.php
│   │   ├── 2024_01_01_000058_create_downloads_table.php
│   │   ├── 2024_01_01_000059_create_homework_table.php
│   │   └── 2024_01_01_000060_create_study_materials_table.php
│   ├── seeders/
│   │   ├── DatabaseSeeder.php
│   │   ├── RoleSeeder.php
│   │   ├── PermissionSeeder.php
│   │   ├── AdminUserSeeder.php
│   │   ├── AcademicSessionSeeder.php
│   │   ├── AttendanceTypeSeeder.php
│   │   ├── ExamGradeSeeder.php
│   │   ├── LanguageSeeder.php
│   │   └── TranslationSeeder.php
│   └── factories/
│       ├── UserFactory.php
│       ├── StudentFactory.php
│       └── ...
├── public/
│   ├── index.php
│   ├── assets/
│   │   ├── css/
│   │   ├── js/
│   │   └── images/
│   ├── storage/
│   │   ├── uploads/
│   │   │   ├── students/
│   │   │   ├── documents/
│   │   │   ├── library/
│   │   │   ├── notices/
│   │   │   ├── messages/
│   │   │   ├── homework/
│   │   │   ├── study-materials/
│   │   │   ├── downloads/
│   │   │   ├── reports/
│   │   │   └── backups/
│   │   └── exports/
│   └── favicon.ico
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   ├── app.blade.php
│   │   │   ├── auth.blade.php
│   │   │   ├── navigation.blade.php
│   │   ├── header.blade.php
│   │   └── footer.blade.php
│   │   ├── components/
│   │   │   ├── alert.blade.php
│   │   │   ├── card.blade.php
│   │   │   ├── table.blade.php
│   │   │   ├── form-input.blade.php
│   │   │   ├── form-select.blade.php
│   │   │   ├── form-datepicker.blade.php
│   │   │   ├── form-file-upload.blade.php
│   │   │   ├── pagination.blade.php
│   │   │   ├── modal.blade.php
│   │   │   ├── loading-spinner.blade.php
│   │   │   ├── empty-state.blade.php
│   │   │   ├── search-filter.blade.php
│   │   │   ├── breadcrumb.blade.php
│   │   │   └── chart.blade.php
│   │   ├── auth/
│   │   │   ├── login.blade.php
│   │   │   ├── register.blade.php
│   │   │   ├── forgot-password.blade.php
│   │   │   ├── reset-password.blade.php
│   │   │   └── verify-email.blade.php
│   │   ├── admin/
│   │   │   ├── dashboard.blade.php
│   │   │   ├── students/
│   │   │   │   ├── index.blade.php
│   │   │   │   ├── create.blade.php
│   │   │   │   ├── edit.blade.php
│   │   │   │   ├── show.blade.php
│   │   │   │   ├── promote.blade.php
│   │   │   │   ├── import.blade.php
│   │   │   │   ├── export.blade.php
│   │   │   │   ├── siblings.blade.php
│   │   │   │   ├── documents.blade.php
│   │   │   │   ├── attendance.blade.php
│   │   │   │   ├── results.blade.php
│   │   │   │   ├── fees.blade.php
│   │   │   │   ├── transport.blade.php
│   │   │   │   ├── hostel.blade.php
│   │   │   │   └── library.blade.php
│   │   │   ├── academic-sessions/
│   │   │   ├── classes/
│   │   │   ├── sections/
│   │   │   ├── subjects/
│   │   │   ├── class-subjects/
│   │   │   ├── class-timetable/
│   │   │   ├── attendance/
│   │   │   ├── exams/
│   │   │   ├── fees/
│   │   │   ├── library/
│   │   │   ├── transport/
│   │   │   ├── hostels/
│   │   │   ├── notices/
│   │   │   ├── messages/
│   │   │   ├── downloads/
│   │   │   ├── reports/
│   │   │   ├── settings/
│   │   │   └── users/
│   │   ├── teacher/
│   │   │   ├── dashboard.blade.php
│   │   │   ├── classes/
│   │   │   ├── attendance/
│   │   │   ├── exams/
│   │   │   ├── homework/
│   │   │   ├── study-materials/
│   │   │   └── messages/
│   │   ├── student/
│   │   │   ├── dashboard.blade.php
│   │   │   ├── attendance/
│   │   │   ├── exams/
│   │   │   ├── fees/
│   │   │   ├── homework/
│   │   │   ├── study-materials/
│   │   │   ├── library/
│   │   │   ├── transport/
│   │   │   ├── hostel/
│   │   │   └── messages/
│   │   ├── parent/
│   │   │   ├── dashboard.blade.php
│   │   │   ├── students/
│   │   │   ├── attendance/
│   │   │   ├── exams/
│   │   │   ├── fees/
│   │   │   ├── homework/
│   │   │   ├── study-materials/
│   │   │   ├── notices/
│   │   │   └── messages/
│   │   ├── accountant/
│   │   │   ├── dashboard.blade.php
│   │   │   ├── fees/
│   │   │   ├── income/
│   │   │   ├── expenses/
│   │   │   ├── reports/
│   │   │   └── settings/
│   │   └── librarian/
│   │       ├── dashboard.blade.php
│   │       ├── books/
│   │       ├── members/
│   │       ├── issues/
│   │       └── reports/
│   ├── lang/
│   │   ├── en/
│   │   │   ├── auth.php
│   │   │   ├── pagination.php
│   │   │   ├── passwords.php
│   │   │   └── validation.php
│   │   ├── hi/
│   │   ├── ar/
│   │   └── ... (73 languages)
│   └── css/
│       └── app.css
├── routes/
│   ├── web.php
│   └── api.php
├── storage/
│   ├── app/
│   │   └── public/
│   ├── framework/
│   │   ├── cache/
│   │   ├── sessions/
│   │   └── views/
│   └── logs/
├── tests/
│   ├── Unit/
│   └── Feature/
├── .env.example
├── .gitignore
├── artisan
├── composer.json
├── composer.lock
├── package.json
├── package-lock.json
├── phpunit.xml
├── README.md
├── SETUP-GUIDE.md
├── PROJECT-COMPLETE.md
├── WHAT-TO-EXPECT.md
├── DEVIN-AI-PROMPTS.md
├── DEVIN-AI-ENHANCED-PROMPTS.md
├── DEVIN-AI-COMPLETE-PROMPTS.md
├── DEVIN-AI-FRONTEND-DETAILED.md
├── DEVIN-AI-FRONTEND-DETAILED-PART2.md
├── DEVIN-AI-FRONTEND-DETAILED-PART3.md
├── DEVIN-AI-FRONTEND-DETAILED-PART4.md
└── vite.config.js
```

---

## 🗄️ Database Schema

### Complete Database Structure

The database will contain **60+ tables** organized into the following categories:

#### 1. Authentication & Authorization (6 tables)
- **users**: User accounts for all 6 roles
- **roles**: 6 user roles (admin, teacher, student, parent, accountant, librarian)
- **permissions**: Granular permissions for all modules
- **role_has_permissions**: Role-permission relationships
- **model_has_permissions**: Direct permission assignments to users
- **model_has_roles**: Role assignments to users

#### 2. Academic Structure (6 tables)
- **academic_sessions**: Academic years/sessions (e.g., 2023-24, 2024-25)
- **classes**: Classes/grades (e.g., Class 1, Class 10)
- **sections**: Sections within classes (e.g., Class 1-A, Class 1-B)
- **subjects**: Subjects (e.g., Mathematics, Science, English)
- **class_subjects**: Subject assignments to classes/sections with teachers
- **class_timetables**: Weekly class schedules with periods and subjects

#### 3. Student Management (6 tables)
- **students**: Comprehensive student information (40+ fields)
- **student_siblings**: Sibling relationships between students
- **student_documents**: Uploaded documents for students
- **student_categories**: Student categories for grouping
- **student_promotions**: Student promotion history
- **student_documents**: Student documents (birth certificate, photos, etc.)

#### 4. Attendance System (2 tables)
- **attendance_types**: Attendance types (Present, Absent, Late, Leave, Holiday)
- **attendances**: Daily attendance records for students

#### 5. Examination System (6 tables)
- **exam_types**: Exam types (Midterm, Final, Unit Test, etc.)
- **exams**: Exams within academic sessions
- **exam_schedules**: Exam schedules for classes, sections, subjects
- **exam_grades**: Grade ranges (A, B, C, D, F)
- **exam_attendance**: Student attendance for exams
- **exam_marks**: Student marks for exams

#### 6. Fees Management (7 tables)
- **fees_types**: Fee types (Tuition, Library, Transport, etc.)
- **fees_groups**: Fee groups for organization
- **fees_masters**: Fee configurations for classes/sections
- **fees_discounts**: Discount rules (Sibling, Staff Child, etc.)
- **fees_allotments**: Fee allotments to students
- **fees_transactions**: Fee payment transactions
- **fees_fines**: Fine rules for late payments

#### 7. Library Management (4 tables)
- **library_categories**: Book categories
- **library_books**: Book inventory
- **library_members**: Library members (students, teachers, staff)
- **library_issues**: Book issue and return records

#### 8. Transport Management (4 tables)
- **transport_routes**: Transport routes
- **transport_route_stops**: Stops on routes
- **transport_vehicles**: School vehicles
- **transport_students**: Student transport assignments

#### 9. Hostel Management (4 tables)
- **hostels**: School hostels
- **hostel_room_types**: Room types (Single, Double, etc.)
- **hostel_rooms**: Hostel rooms
- **hostel_assignments**: Student hostel assignments

#### 10. Communication System (5 tables)
- **notices**: School notices and announcements
- **messages**: Internal messages
- **message_recipients**: Message recipients
- **sms_logs**: SMS sending logs
- **email_logs**: Email sending logs

#### 11. Accounting System (4 tables)
- **expense_categories**: Expense categories
- **income_categories**: Income categories
- **expenses**: Expense records
- **income**: Income records

#### 12. System Configuration (5 tables)
- **settings**: System settings (key-value pairs)
- **languages**: Supported languages (73+)
- **translations**: Language translations
- **backups**: System backups
- **downloads**: Downloadable content

#### 13. Academic Resources (2 tables)
- **homework**: Homework assignments
- **study_materials**: Study materials and resources

### Database Naming Conventions

#### Table Names
- **Plural snake_case**: `users`, `students`, `academic_sessions`
- **Pivot tables**: `role_has_permissions`, `model_has_roles`, `class_subjects`
- **Foreign key suffix**: `_id` (e.g., `user_id`, `class_id`)

#### Column Names
- **snake_case**: `first_name`, `last_name`, `date_of_birth`
- **Boolean columns**: `is_active`, `is_present`, `is_current`
- **Timestamp columns**: `created_at`, `updated_at`, `deleted_at`
- **Foreign keys**: `user_id`, `class_id`, `section_id`, etc.

#### Index Names
- **Table_column_index**: `users_email_index`, `students_class_id_index`
- **Composite indexes**: `attendances_class_id_section_id_attendance_date_index`

#### Migration File Names
- **Date_time_description**: `2024_01_01_000001_create_users_table.php`

### Expected Database Tables

```sql
-- Authentication & Authorization
users
roles
permissions
role_has_permissions
model_has_permissions
model_has_roles

-- Academic Structure
academic_sessions
classes
sections
subjects
class_subjects
class_timetables

-- Student Management
students
student_siblings
student_documents
student_categories
student_promotions

-- Attendance System
attendance_types
attendances

-- Examination System
exam_types
exams
exam_schedules
exam_grades
exam_attendance
exam_marks

-- Fees Management
fees_types
fees_groups
fees_masters
fees_discounts
fees_allotments
fees_transactions
fees_fines

-- Library Management
library_categories
library_books
library_members
library_issues

-- Transport Management
transport_routes
transport_route_stops
transport_vehicles
transport_students

-- Hostel Management
hostels
hostel_room_types
hostel_rooms
hostel_assignments

-- Communication System
notices
messages
message_recipients
sms_logs
email_logs

-- Accounting System
expense_categories
income_categories
expenses
income

-- System Configuration
settings
languages
translations
backups
downloads

-- Academic Resources
homework
study_materials
```

### Key Relationships

#### Users
- **Belongs To**: Role
- **Has One**: Student
- **Has Many**: Messages, FeesTransactions, Expenses, Income

#### Students
- **Belongs To**: User, AcademicSession, Class, Section, StudentCategory
- **Has Many**: StudentSiblings, StudentDocuments, StudentPromotions, Attendances, ExamMarks, FeesAllotments, TransportStudent, HostelAssignment

#### Classes
- **Belongs To**: AcademicSession
- **Has Many**: Sections, Students, ClassSubjects, ClassTimetables

#### Sections
- **Belongs To**: Class
- **Has Many**: Students, ClassSubjects, ClassTimetables, Attendances

#### Subjects
- **Has Many**: ClassSubjects, ClassTimetables, ExamSchedules, Homework, StudyMaterials

#### ExamMarks
- **Belongs To**: ExamSchedule, Student, ExamGrade

#### FeesAllotments
- **Belongs To**: Student, FeesMaster, FeesDiscount
- **Has Many**: FeesTransactions

---

## 📝 Naming Conventions

### File Naming

#### Controllers
- **PascalCase**: `StudentController.php`, `FeesAllotmentController.php`
- **Role-based folders**: `Admin/StudentController.php`, `Teacher/AttendanceController.php`

#### Models
- **PascalCase**: `Student.php`, `FeesAllotment.php`
- **Singular**: `Student.php` (not `Students.php`)

#### Views
- **kebab-case**: `students/index.blade.php`, `fees-allotments/create.blade.php`
- **Folder structure**: `admin/students/index.blade.php`

#### Migrations
- **Date_time_description**: `2024_01_01_000001_create_users_table.php`
- **Snake_case**: `create_users_table.php`

#### Seeders
- **PascalCase**: `RoleSeeder.php`, `AdminUserSeeder.php`

### Code Naming

#### Classes
- **PascalCase**: `class Student`, `class FeesAllotmentController`

#### Methods
- **camelCase**: `public function index()`, `public function storeStudent()`

#### Variables
- **camelCase**: `$student`, `$feesAllotment`, `$academicSession`

#### Constants
- **UPPER_SNAKE_CASE**: `const MAX_UPLOAD_SIZE = 10485760;`

### Database Naming

#### Tables
- **Plural snake_case**: `users`, `students`, `academic_sessions`

#### Columns
- **snake_case**: `first_name`, `last_name`, `date_of_birth`

#### Foreign Keys
- **Table_name_id**: `user_id`, `class_id`, `section_id`

#### Pivot Tables
- **table1_table2**: `role_has_permissions`, `model_has_roles`

### Routes

#### Web Routes
- **kebab-case**: `/students`, `/fees-allotments`, `/academic-sessions`
- **Resource routes**: `Route::resource('students', StudentController::class);`

#### API Routes
- **kebab-case**: `/api/students`, `/api/fees-allotments`
- **Versioned**: `/api/v1/students`

### Environment Variables

#### .env File
- **UPPER_SNAKE_CASE**: `DB_DATABASE`, `APP_NAME`, `MAIL_DRIVER`
- **Prefix**: `DB_`, `MAIL_`, `SMS_`

---

## ✨ Expected Features

### 1. User Management
- Multi-role user accounts (6 roles)
- User profile management
- Password reset functionality
- Email verification
- Two-factor authentication (optional)

### 2. Academic Management
- Academic session management
- Class and section management
- Subject management
- Class-subject assignment with teachers
- Class timetable creation and management

### 3. Student Management
- Student admission with multi-step form
- Student profile management
- Student document management
- Student sibling tracking
- Student promotion management
- Student import/export (Excel, CSV)
- Student search and filtering

### 4. Attendance Management
- Daily attendance marking
- Attendance type management
- Attendance calendar view
- Attendance reports with charts
- SMS/Email notifications for absent students
- Attendance statistics and analytics

### 5. Examination Management
- Exam type management
- Exam creation and scheduling
- Exam attendance tracking
- Marks entry with grade calculation
- Report card generation
- Exam results with class-wise performance
- Exam statistics and analytics

### 6. Fees Management
- Fee type and group management
- Fee configuration for classes/sections
- Fee discount management
- Fee allotment to students
- Fee collection with receipt generation
- Fee transaction tracking
- Fee reports with charts
- Fee due reminders via SMS/Email

### 7. Library Management
- Book category management
- Book inventory management
- Library member management
- Book issue and return
- Fine calculation for late returns
- Library reports and statistics

### 8. Transport Management
- Transport route management
- Route stop management
- Transport vehicle management
- Student transport assignment
- Transport reports and statistics

### 9. Hostel Management
- Hostel management
- Room type management
- Room management
- Student hostel assignment
- Hostel reports and statistics

### 10. Communication System
- Notice creation and publishing
- Targeted notices by role/class
- Internal messaging system
- SMS notifications
- Email notifications
- Downloadable content management

### 11. Academic Resources
- Homework assignment and management
- Study material upload and management
- Homework submission tracking
- Resource categorization

### 12. Accounting System
- Income category management
- Income entry and tracking
- Expense category management
- Expense entry and tracking
- Balance sheet generation
- Financial reports with charts

### 13. Reporting System
- Student reports
- Attendance reports
- Exam reports
- Fee reports
- Library reports
- Transport reports
- Hostel reports
- Accounting reports
- Custom report builder
- Export to Excel, PDF, CSV

### 14. System Settings
- General settings (school info, academic settings)
- System settings (security, backup, email, SMS)
- Language settings (73+ languages)
- Translation management
- Theme settings (colors, layout)
- Notification settings (templates)
- Backup management
- Role and permission management
- User management

### 15. Multi-Language Support
- 73+ languages including RTL
- Language switcher
- Translation management
- RTL layout support

### 16. Data Management
- Data import/export
- Data backup
- Data restore
- Data cleanup

---

## 👥 User Roles & Permissions

### 1. Administrator
**Full Access**: All modules and features

**Key Permissions**:
- Manage users, roles, permissions
- Manage academic sessions, classes, sections, subjects
- Manage all student data
- Manage attendance, exams, fees
- Manage library, transport, hostel
- Manage communication, accounting
- Manage system settings
- Generate all reports
- Backup and restore data

### 2. Teacher
**Limited Access**: Assigned classes and subjects

**Key Permissions**:
- View assigned classes and students
- Mark attendance for assigned classes
- Enter marks for assigned subjects
- Create and manage homework
- Upload study materials
- Send messages to students/parents
- View exam schedules
- View student results
- View student attendance

### 3. Student
**Limited Access**: Own data only

**Key Permissions**:
- View own profile
- View own attendance
- View own exam results
- View own fees
- View homework assignments
- View study materials
- View library books
- View transport details
- View hostel details
- Send messages to teachers
- Download study materials

### 4. Parent
**Limited Access**: Children's data only

**Key Permissions**:
- View children's profiles
- View children's attendance
- View children's exam results
- View children's fees
- Pay children's fees
- View children's homework
- View study materials
- View notices
- Send messages to teachers
- Download reports

### 5. Accountant
**Limited Access**: Financial modules only

**Key Permissions**:
- Collect fees
- View fee reports
- Manage income entries
- Manage expense entries
- View financial reports
- Generate financial statements
- Send fee reminders

### 6. Librarian
**Limited Access**: Library module only

**Key Permissions**:
- Manage library books
- Manage library members
- Issue and return books
- View library reports
- Generate library statements

---

## 🔌 API Endpoints

### Authentication
- `POST /api/login` - User login
- `POST /api/register` - User registration
- `POST /api/logout` - User logout
- `POST /api/forgot-password` - Forgot password
- `POST /api/reset-password` - Reset password
- `POST /api/verify-email` - Email verification

### Users
- `GET /api/users` - List users
- `POST /api/users` - Create user
- `GET /api/users/{id}` - Get user details
- `PUT /api/users/{id}` - Update user
- `DELETE /api/users/{id}` - Delete user

### Students
- `GET /api/students` - List students
- `POST /api/students` - Create student
- `GET /api/students/{id}` - Get student details
- `PUT /api/students/{id}` - Update student
- `DELETE /api/students/{id}` - Delete student
- `POST /api/students/import` - Import students
- `GET /api/students/export` - Export students
- `POST /api/students/{id}/promote` - Promote student

### Attendance
- `GET /api/attendance` - List attendance
- `POST /api/attendance` - Mark attendance
- `GET /api/attendance/{id}` - Get attendance details
- `PUT /api/attendance/{id}` - Update attendance
- `GET /api/attendance/calendar` - Get attendance calendar
- `GET /api/attendance/report` - Get attendance report

### Exams
- `GET /api/exams` - List exams
- `POST /api/exams` - Create exam
- `GET /api/exams/{id}` - Get exam details
- `PUT /api/exams/{id}` - Update exam
- `DELETE /api/exams/{id}` - Delete exam
- `GET /api/exams/{id}/schedule` - Get exam schedule
- `POST /api/exams/{id}/schedule` - Create exam schedule
- `POST /api/exams/marks` - Enter marks
- `GET /api/exams/report-card/{studentId}` - Get report card

### Fees
- `GET /api/fees/allotments` - List fee allotments
- `POST /api/fees/allotments` - Allot fees
- `GET /api/fees/transactions` - List transactions
- `POST /api/fees/collect` - Collect fee
- `GET /api/fees/receipt/{id}` - Get receipt
- `POST /api/fees/refund/{id}` - Refund fee
- `GET /api/fees/report` - Get fee report

### Library
- `GET /api/library/books` - List books
- `POST /api/library/books` - Create book
- `GET /api/library/books/{id}` - Get book details
- `PUT /api/library/books/{id}` - Update book
- `DELETE /api/library/books/{id}` - Delete book
- `POST /api/library/issue` - Issue book
- `POST /api/library/return` - Return book
- `GET /api/library/report` - Get library report

### Reports
- `GET /api/reports/student` - Generate student report
- `GET /api/reports/attendance` - Generate attendance report
- `GET /api/reports/exam` - Generate exam report
- `GET /api/reports/fees` - Generate fee report
- `GET /api/reports/library` - Generate library report
- `GET /api/reports/transport` - Generate transport report
- `GET /api/reports/hostel` - Generate hostel report
- `GET /api/reports/accounting` - Generate accounting report

### Settings
- `GET /api/settings/general` - Get general settings
- `PUT /api/settings/general` - Update general settings
- `GET /api/settings/system` - Get system settings
- `PUT /api/settings/system` - Update system settings
- `GET /api/settings/languages` - Get languages
- `GET /api/settings/translations` - Get translations
- `PUT /api/settings/translations` - Update translations
- `GET /api/settings/backup` - Get backups
- `POST /api/settings/backup` - Create backup
- `POST /api/settings/restore` - Restore backup

---

## 🎨 Frontend Pages

### Authentication Pages
- `/login` - Login page
- `/register` - Registration page
- `/forgot-password` - Forgot password page
- `/reset-password/{token}` - Reset password page
- `/verify-email/{id}/{hash}` - Email verification page

### Admin Pages
- `/admin/dashboard` - Admin dashboard
- `/admin/students` - Student list
- `/admin/students/create` - Create student
- `/admin/students/{id}` - Student details
- `/admin/students/{id}/edit` - Edit student
- `/admin/students/{id}/promote` - Promote student
- `/admin/students/import` - Import students
- `/admin/students/export` - Export students
- `/admin/academic-sessions` - Academic sessions list
- `/admin/academic-sessions/create` - Create academic session
- `/admin/classes` - Classes list
- `/admin/classes/create` - Create class
- `/admin/sections` - Sections list
- `/admin/sections/create` - Create section
- `/admin/subjects` - Subjects list
- `/admin/subjects/create` - Create subject
- `/admin/attendance` - Attendance list
- `/admin/attendance/mark` - Mark attendance
- `/admin/attendance/calendar` - Attendance calendar
- `/admin/attendance/report` - Attendance report
- `/admin/exams` - Exams list
- `/admin/exams/create` - Create exam
- `/admin/exams/{id}/schedule` - Exam schedule
- `/admin/exams/marks` - Marks entry
- `/admin/exams/report-card/{studentId}` - Report card
- `/admin/fees` - Fees list
- `/admin/fees/allot` - Allot fees
- `/admin/fees/collect` - Collect fee
- `/admin/fees/report` - Fee report
- `/admin/library` - Library books list
- `/admin/library/create` - Create book
- `/admin/library/issue` - Issue book
- `/admin/library/return` - Return book
- `/admin/transport` - Transport list
- `/admin/transport/routes` - Routes list
- `/admin/transport/vehicles` - Vehicles list
- `/admin/transport/assign` - Assign transport
- `/admin/hostels` - Hostels list
- `/admin/hostels/create` - Create hostel
- `/admin/hostels/assign` - Assign hostel
- `/admin/notices` - Notices list
- `/admin/notices/create` - Create notice
- `/admin/messages` - Messages inbox
- `/admin/messages/compose` - Compose message
- `/admin/messages/sent` - Sent messages
- `/admin/downloads` - Downloads list
- `/admin/downloads/create` - Create download
- `/admin/homework` - Homework list
- `/admin/homework/create` - Create homework
- `/admin/study-materials` - Study materials list
- `/admin/study-materials/create` - Create study material
- `/admin/income` - Income list
- `/admin/income/create` - Create income
- `/admin/expenses` - Expenses list
- `/admin/expenses/create` - Create expense
- `/admin/reports` - Reports dashboard
- `/admin/reports/student` - Student report
- `/admin/reports/attendance` - Attendance report
- `/admin/reports/exam` - Exam report
- `/admin/reports/fees` - Fee report
- `/admin/reports/library` - Library report
- `/admin/reports/transport` - Transport report
- `/admin/reports/hostel` - Hostel report
- `/admin/reports/accounting` - Accounting report
- `/admin/settings/general` - General settings
- `/admin/settings/system` - System settings
- `/admin/settings/languages` - Language settings
- `/admin/settings/translations` - Translation management
- `/admin/settings/theme` - Theme settings
- `/admin/settings/notifications` - Notification settings
- `/admin/settings/backups` - Backup management
- `/admin/settings/permissions` - Role permissions
- `/admin/settings/users` - User management
- `/admin/settings/profile` - Profile settings

### Teacher Pages
- `/teacher/dashboard` - Teacher dashboard
- `/teacher/classes` - My classes
- `/teacher/attendance/mark` - Mark attendance
- `/teacher/exams/schedule` - Exam schedule
- `/teacher/exams/marks` - Enter marks
- `/teacher/homework` - Homework list
- `/teacher/homework/create` - Create homework
- `/teacher/study-materials` - Study materials list
- `/teacher/study-materials/create` - Create study material
- `/teacher/messages` - Messages
- `/teacher/messages/compose` - Compose message
- `/teacher/profile` - Profile settings

### Student Pages
- `/student/dashboard` - Student dashboard
- `/student/attendance` - My attendance
- `/student/exams` - My exams
- `/student/exams/results` - My results
- `/student/fees` - My fees
- `/student/homework` - My homework
- `/student/study-materials` - Study materials
- `/student/library` - Library
- `/student/transport` - Transport
- `/student/hostel` - Hostel
- `/student/messages` - Messages
- `/student/profile` - Profile settings

### Parent Pages
- `/parent/dashboard` - Parent dashboard
- `/parent/students` - My children
- `/parent/students/{id}/attendance` - Child's attendance
- `/parent/students/{id}/exams` - Child's exams
- `/parent/students/{id}/results` - Child's results
- `/parent/students/{id}/fees` - Child's fees
- `/parent/students/{id}/pay-fees` - Pay fees
- `/parent/students/{id}/homework` - Child's homework
- `/parent/students/{id}/study-materials` - Study materials
- `/parent/notices` - Notices
- `/parent/messages` - Messages
- `/parent/messages/compose` - Compose message
- `/parent/profile` - Profile settings

### Accountant Pages
- `/accountant/dashboard` - Accountant dashboard
- `/accountant/fees` - Fees list
- `/accountant/fees/collect` - Collect fee
- `/accountant/fees/transactions` - Transactions
- `/accountant/fees/report` - Fee report
- `/accountant/income` - Income list
- `/accountant/income/create` - Create income
- `/accountant/expenses` - Expenses list
- `/accountant/expenses/create` - Create expense
- `/accountant/reports` - Reports
- `/accountant/reports/accounting` - Accounting report
- `/accountant/settings/profile` - Profile settings

### Librarian Pages
- `/librarian/dashboard` - Librarian dashboard
- `/librarian/books` - Books list
- `/librarian/books/create` - Create book
- `/librarian/members` - Members list
- `/librarian/members/create` - Create member
- `/librarian/issue` - Issue book
- `/librarian/return` - Return book
- `/librarian/reports` - Reports
- `/librarian/reports/library` - Library report
- `/librarian/profile` - Profile settings

---

## 📦 Deliverables

### 1. Source Code
- Complete Laravel application
- All controllers, models, views, migrations
- All services, middleware, requests
- All configurations

### 2. Database
- Complete database schema
- All migrations
- All seeders
- SQL export file

### 3. Documentation
- README.md with installation instructions
- SETUP-GUIDE.md with detailed setup steps
- API documentation
- User manual
- Developer documentation

### 4. Configuration Files
- .env.example
- composer.json
- package.json
- vite.config.js

### 5. DevIn AI Prompts
- Complete backend prompts (106 prompts)
- Complete frontend prompts (185 prompts)
- Enhanced prompts with explanations

### 6. Planning Documents
- Architecture plan
- Implementation roadmap
- Database schema
- Visual diagrams
- Quick start guide

### 7. Testing
- Unit tests
- Feature tests
- Integration tests

### 8. Deployment Guide
- Server requirements
- Deployment steps
- Configuration guide
- Troubleshooting guide

---

## 🎯 Expected Final Outcome

### A Fully Functional School Management System With:

1. **Complete User Management**
   - 6 user roles with granular permissions
   - Role-based access control
   - User profile management

2. **Comprehensive Academic Management**
   - Multi-academic session support
   - Class, section, subject management
   - Timetable creation and management

3. **Complete Student Lifecycle Management**
   - Admission with multi-step form
   - Profile management
   - Promotion tracking
   - Document management

4. **Attendance System**
   - Daily attendance marking
   - Attendance calendar
   - Attendance reports
   - SMS/Email notifications

5. **Examination System**
   - Exam scheduling
   - Marks entry
   - Grade calculation
   - Report card generation

6. **Fees Management**
   - Fee configuration
   - Fee allotment
   - Fee collection
   - Receipt generation

7. **Library Management**
   - Book inventory
   - Issue/return tracking
   - Fine calculation
   - Library reports

8. **Transport Management**
   - Route management
   - Vehicle management
   - Student assignment
   - Transport reports

9. **Hostel Management**
   - Hostel management
   - Room management
   - Student assignment
   - Hostel reports

10. **Communication System**
    - Notice management
    - Internal messaging
    - SMS/Email notifications

11. **Accounting System**
    - Income tracking
    - Expense tracking
    - Financial reports
    - Balance sheets

12. **Comprehensive Reporting**
    - Student reports
    - Attendance reports
    - Exam reports
    - Fee reports
    - Library reports
    - Transport reports
    - Hostel reports
    - Accounting reports
    - Custom report builder

13. **Multi-Language Support**
    - 73+ languages
    - RTL support
    - Translation management

14. **System Settings**
    - General settings
    - System configuration
    - Theme customization
    - Backup management

15. **Responsive Design**
    - Mobile-friendly
    - Tablet-friendly
    - Desktop-friendly
    - RTL support

16. **Data Management**
    - Import/export
    - Backup/restore
    - Data cleanup

---

## 🚀 Ready for Development

The Smart School Management System is now fully planned and ready for implementation with DevIn AI using the comprehensive prompts provided in the prompt guides.

**Total Prompts Available:**
- Backend Prompts: 106
- Frontend Prompts: 185
- Total: 291 prompts

All prompts include detailed explanations of purpose, functionality, implementation details, and integration with other features.

**Happy Building!** 🎉
