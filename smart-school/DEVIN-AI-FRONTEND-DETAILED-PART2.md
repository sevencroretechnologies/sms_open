# Smart School Management System - Frontend Detailed Prompts Part 2

This document continues with comprehensive, detailed prompts for building complete frontend UI for remaining modules of Smart School Management System using DevIn AI. Each prompt includes:
- **Purpose**: Why this prompt is needed
- **Functionality**: What exactly it does
- **How it Works**: Implementation details
- **Integration**: How it connects with other features

---

## 📋 Continue from Part 1

This document continues from [`DEVIN-AI-FRONTEND-DETAILED.md`](DEVIN-AI-FRONTEND-DETAILED.md) which covered:
- Layout & Components (20 prompts)
- Authentication Views (5 prompts)
- Dashboard Views (10 prompts)
- Student Management Views (15 prompts)
- Academic Management Views (20 prompts)

**Total in Part 1: 70 prompts**

---

## 🎨 Phase 6: Attendance Management Views (10 Prompts)

### Prompt 71: Create Attendance Marking View

**Purpose**: Create attendance marking interface for teachers to mark daily attendance.

**Functionality**: Provides interface for teachers to mark attendance for their classes.

**How it Works**:
- Creates `resources/views/teacher/attendance/mark.blade.php`
- Extends app layout
- Shows page header with title and "Back to Dashboard" button
- Shows filter form:
  - Academic Session (select)
  - Class (select, filtered by teacher's classes)
  - Section (select)
  - Date (date picker, defaults to today)
- Shows student list with:
  - Photo (avatar)
  - Roll Number
  - Name
  - Attendance Type dropdown (Present, Absent, Late, Leave, Holiday)
  - Remarks textarea
- Shows attendance summary:
  - Total Students
  - Present Count
  - Absent Count
  - Late Count
  - Leave Count
- Shows "Mark All Present" button
- Shows "Mark All Absent" button
- Shows "Save Attendance" button with loading state
- Shows "Cancel" button
- Shows validation errors
- Shows "Send SMS Notification" checkbox
- Shows "Send Email Notification" checkbox
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading spinner during submission
- Shows success message on successful marking
- Redirects to attendance list on success

**Integration**:
- Uses AttendanceController store method
- Validates attendance data
- Creates attendance records in attendances table
- Sends SMS notifications if checked
- Sends email notifications if checked
- Links to attendance list
- Used by teacher role

**Execute**: Create attendance marking view with student list, attendance types, and responsive design.

---

### Prompt 72: Create Attendance List View

**Purpose**: Create attendance list view with search, filter, and export.

**Functionality**: Provides comprehensive attendance listing with advanced filtering.

**How it Works**:
- Creates `resources/views/admin/attendance/index.blade.php`
- Extends app layout
- Shows page header with title and "Mark Attendance" button
- Shows search filter component with:
  - Search by student name, roll number, admission number
  - Filter by academic session
  - Filter by class
  - Filter by section
  - Filter by date range
  - Filter by attendance type
  - Filter by status (all/present/absent/late)
- Shows table with columns:
  - Date
  - Class
  - Section
  - Student Photo
  - Roll Number
  - Student Name
  - Father's Name
  - Attendance Type
  - Remarks
  - Marked By
  - Actions (view, edit)
- Shows bulk actions:
  - Export selected
  - Print selected
- Shows pagination component
- Shows records per page selector
- Shows "Export All" button
- Shows "Print Report" button
- Uses Bootstrap 5 grid layout
- Responsive design (table scrolls on mobile)
- Supports RTL languages
- Shows loading state
- Shows empty state if no attendance

**Integration**:
- Uses AttendanceController index method
- Queries Attendance model with filters
- Links to mark attendance, edit attendance
- Links to export/print functionality
- Used by admin, teacher roles

**Execute**: Create attendance list view with search, filters, table, and responsive design.

---

### Prompt 73: Create Attendance Edit View

**Purpose**: Create attendance edit view for correcting attendance records.

**Functionality**: Provides interface to edit previously marked attendance.

**How it Works**:
- Creates `resources/views/admin/attendance/edit.blade.php`
- Extends app layout
- Shows page header with title and "Back to List" button
- Shows attendance details card:
  - Date
  - Class
  - Section
  - Marked By
  - Marked At
- Shows student list with:
  - Photo (avatar)
  - Roll Number
  - Name
  - Current Attendance Type
  - Attendance Type dropdown (Present, Absent, Late, Leave, Holiday)
  - Remarks textarea
- Shows attendance summary:
  - Total Students
  - Present Count
  - Absent Count
  - Late Count
  - Leave Count
- Shows "Update Attendance" button with loading state
- Shows "Cancel" button
- Shows validation errors
- Shows reason textarea for editing (required)
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading spinner during submission
- Shows success message on successful update
- Redirects to attendance list on success

**Integration**:
- Uses AttendanceController update method
- Validates attendance data
- Updates attendance records in attendances table
- Logs edit reason
- Links to attendance list
- Used by admin role

**Execute**: Create attendance edit view with student list, attendance types, and responsive design.

---

### Prompt 74: Create Student Attendance Calendar View

**Purpose**: Create student attendance calendar with color-coded attendance.

**Functionality**: Shows student attendance in calendar format with color coding.

**How it Works**:
- Creates `resources/views/admin/attendance/calendar.blade.php`
- Extends app layout
- Shows page header with title and "Back to List" button
- Shows filter form:
  - Academic Session (select)
  - Class (select)
  - Section (select)
  - Student (select)
  - Month selector
  - Year selector
- Shows attendance calendar:
  - Monthly calendar grid
  - Each day shows attendance color:
    - Green: Present
    - Red: Absent
    - Yellow: Late
    - Blue: Leave
    - Gray: Holiday/Weekend
  - Click on day to show details
- Shows attendance summary cards:
  - Total Days
  - Present Days
  - Absent Days
  - Late Days
  - Leave Days
  - Attendance Percentage
- Shows attendance legend
- Shows "Export Calendar" button
- Shows "Print Calendar" button
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state
- Shows modal with day details on click

**Integration**:
- Uses AttendanceController calendar method
- Queries Attendance model for student
- Shows attendance calendar
- Links to export/print
- Used by admin, teacher, parent, student roles

**Execute**: Create student attendance calendar view with color-coded days and responsive design.

---

### Prompt 75: Create Attendance Report View

**Purpose**: Create attendance report view with statistics and charts.

**Functionality**: Shows comprehensive attendance reports with analytics.

**How it Works**:
- Creates `resources/views/admin/attendance/report.blade.php`
- Extends app layout
- Shows page header with title and "Back to List" button
- Shows filter form:
  - Academic Session (select)
  - Class (select)
  - Section (select)
  - Date Range
  - Report Type (daily, monthly, yearly)
- Shows statistics cards:
  - Total Students
  - Overall Attendance Percentage
  - Present Percentage
  - Absent Percentage
  - Late Percentage
  - Leave Percentage
- Shows charts:
  - Attendance trend (line chart)
  - Attendance by type (pie chart)
  - Class-wise attendance (bar chart)
  - Section-wise attendance (bar chart)
- Shows attendance table:
  - Student Name
  - Roll Number
  - Total Days
  - Present Days
  - Absent Days
  - Late Days
  - Leave Days
  - Attendance Percentage
- Shows "Export Report" button (PDF/Excel)
- Shows "Print Report" button
- Uses Chart.js for visualizations
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state

**Integration**:
- Uses AttendanceController report method
- Queries Attendance model with filters
- Calculates statistics
- Shows charts
- Links to export/print
- Used by admin, teacher roles

**Execute**: Create attendance report view with statistics, charts, and responsive design.

---

### Prompt 76: Create Attendance Types Management View

**Purpose**: Create attendance types management view with CRUD operations.

**Functionality**: Provides interface to manage attendance types (Present, Absent, Late, etc.).

**How it Works**:
- Creates `resources/views/admin/attendance-types/index.blade.php`
- Extends app layout
- Shows page header with title and "Add Type" button
- Shows table with columns:
  - Type Name
  - Code
  - Color
  - Is Present (yes/no)
  - Status (active/inactive)
  - Actions (edit, delete)
- Shows color picker for each type
- Shows "Is Present" checkbox for each type
- Shows validation errors
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state
- Shows empty state if no types

**Integration**:
- Uses AttendanceTypeController index method
- Queries AttendanceType model
- Links to create, edit, delete
- Used by admin role

**Execute**: Create attendance types management view with table, color picker, and responsive design.

---

### Prompt 77: Create Attendance Types Create View

**Purpose**: Create attendance type creation form.

**Functionality**: Provides form to create new attendance type.

**How it Works**:
- Creates `resources/views/admin/attendance-types/create.blade.php`
- Extends app layout
- Shows page header with title and "Back to List" button
- Shows form with:
  - Type Name (required)
  - Code (required, unique)
  - Color (color picker)
  - Is Present (checkbox)
  - Status (active/inactive)
- Shows color preview
- Shows validation errors
- Shows "Save" button with loading state
- Shows "Cancel" button
- Uses Bootstrap 5 form styling
- Responsive design
- Supports RTL languages
- Shows loading spinner during submission
- Shows success message on successful creation
- Redirects to type list on success

**Integration**:
- Uses AttendanceTypeController store method
- Validates form fields
- Creates attendance type in attendance_types table
- Links to type list
- Used by admin role

**Execute**: Create attendance types create view with form, color picker, and responsive design.

---

### Prompt 78: Create Attendance Print View

**Purpose**: Create printable attendance report view.

**Functionality**: Provides printer-friendly version of attendance report.

**How it Works**:
- Creates `resources/views/admin/attendance/print.blade.php`
- Shows school header with logo and name
- Shows report title
- Shows filter criteria
- Shows attendance table
- Shows attendance summary
- Shows attendance charts
- Shows school contact info
- Print-optimized layout (no navigation, minimal styling)
- Uses Bootstrap 5 grid layout
- Supports RTL languages

**Integration**:
- Uses AttendanceController print method
- Queries Attendance model
- Shows printable report
- Used by admin, teacher roles

**Execute**: Create attendance print view with optimized layout.

---

### Prompt 79: Create Attendance Export View

**Purpose**: Create attendance export view with format options.

**Functionality**: Provides attendance export functionality with filters and format options.

**How it Works**:
- Creates `resources/views/admin/attendance/export.blade.php`
- Extends app layout
- Shows page header with title and "Back to List" button
- Shows export filters:
  - Academic Session
  - Class
  - Section
  - Date Range
  - Attendance Type
- Shows export options:
  - Export format (Excel, PDF, CSV)
  - Include student details checkbox
  - Include remarks checkbox
  - Include summary checkbox
- Shows "Export" button with loading state
- Shows "Cancel" button
- Uses Bootstrap 5 form styling
- Responsive design
- Supports RTL languages
- Shows loading spinner during export
- Downloads file on successful export

**Integration**:
- Uses AttendanceController export method
- Queries Attendance model with filters
- Generates Excel/PDF/CSV file
- Downloads file to user
- Links to attendance list
- Used by admin, teacher roles

**Execute**: Create attendance export view with filters, format options, and responsive design.

---

### Prompt 80: Create Attendance SMS Notification View

**Purpose**: Create attendance SMS notification view for sending alerts to parents.

**Functionality**: Provides interface to send SMS notifications for absent students.

**How it Works**:
- Creates `resources/views/admin/attendance/sms.blade.php`
- Extends app layout
- Shows page header with title and "Back to List" button
- Shows filter form:
  - Date
  - Class
  - Section
  - Attendance Type (Absent/Late)
- Shows student list with:
  - Photo (avatar)
  - Roll Number
  - Student Name
  - Father's Name
  - Phone Number
  - Attendance Type
  - Checkbox for selection
- Shows "Select All Absent" button
- Shows message template:
  - Pre-defined templates
  - Custom message textarea
  - Placeholders: {student_name}, {class}, {section}, {date}, {attendance_type}
- Shows preview of message
- Shows "Send SMS" button with loading state
- Shows "Cancel" button
- Shows validation errors
- Shows SMS cost estimate
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading spinner during sending
- Shows success message with sent count
- Shows error message with failed count

**Integration**:
- Uses AttendanceController sendSMS method
- Queries Attendance model
- Queries Student model for phone numbers
- Sends SMS via SMS gateway
- Creates SMS logs in sms_logs table
- Links to attendance list
- Used by admin, teacher roles

**Execute**: Create attendance SMS notification view with student list, message template, and responsive design.

---

## 🎨 Phase 7: Examination Management Views (15 Prompts)

### Prompt 81: Create Exam Types List View

**Purpose**: Create exam types listing page with CRUD operations.

**Functionality**: Provides exam types list with create, edit, delete functionality.

**How it Works**:
- Creates `resources/views/admin/exam-types/index.blade.php`
- Extends app layout
- Shows page header with title and "Add Type" button
- Shows table with columns:
  - Type Name
  - Code
  - Description
  - Status (active/inactive)
  - Actions (edit, delete)
- Shows validation errors
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state
- Shows empty state if no types

**Integration**:
- Uses ExamTypeController index method
- Queries ExamType model
- Links to create, edit, delete
- Used by admin role

**Execute**: Create exam types list view with table, actions, and responsive design.

---

### Prompt 82: Create Exam Types Create View

**Purpose**: Create exam type creation form.

**Functionality**: Provides form to create new exam type.

**How it Works**:
- Creates `resources/views/admin/exam-types/create.blade.php`
- Extends app layout
- Shows page header with title and "Back to List" button
- Shows form with:
  - Type Name (required)
  - Code (required, unique)
  - Description
  - Status (active/inactive)
- Shows validation errors
- Shows "Save" button with loading state
- Shows "Cancel" button
- Uses Bootstrap 5 form styling
- Responsive design
- Supports RTL languages
- Shows loading spinner during submission
- Shows success message on successful creation
- Redirects to type list on success

**Integration**:
- Uses ExamTypeController store method
- Validates form fields
- Creates exam type in exam_types table
- Links to type list
- Used by admin role

**Execute**: Create exam types create view with form, validation, and responsive design.

---

### Prompt 83: Create Exams List View

**Purpose**: Create exams listing page with CRUD operations.

**Functionality**: Provides exams list with create, edit, delete, and schedule management.

**How it Works**:
- Creates `resources/views/admin/exams/index.blade.php`
- Extends app layout
- Shows page header with title and "Add Exam" button
- Shows filter by academic session
- Shows table with columns:
  - Exam Name
  - Exam Type
  - Academic Session
  - Start Date
  - End Date
  - Status (upcoming, ongoing, completed)
  - Actions (edit, delete, schedule, results)
- Shows "Schedule Exam" button for each exam
- Shows "View Results" button for completed exams
- Shows validation errors
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state
- Shows empty state if no exams

**Integration**:
- Uses ExamController index method
- Queries Exam model with academic session
- Links to create, edit, delete, schedule, results
- Used by admin role

**Execute**: Create exams list view with table, actions, and responsive design.

---

### Prompt 84: Create Exams Create View

**Purpose**: Create exam creation form.

**Functionality**: Provides form to create new exam.

**How it Works**:
- Creates `resources/views/admin/exams/create.blade.php`
- Extends app layout
- Shows page header with title and "Back to List" button
- Shows form with:
  - Academic Session (required, select)
  - Exam Type (required, select)
  - Exam Name (required)
  - Start Date (required, date picker)
  - End Date (required, date picker)
  - Description
  - Status (active/inactive)
- Shows validation errors
- Shows "Save" button with loading state
- Shows "Cancel" button
- Uses Bootstrap 5 form styling
- Responsive design
- Supports RTL languages
- Shows loading spinner during submission
- Shows success message on successful creation
- Redirects to exam list on success

**Integration**:
- Uses ExamController store method
- Validates form fields
- Creates exam in exams table
- Links to exam list
- Used by admin role

**Execute**: Create exams create view with form, validation, and responsive design.

---

### Prompt 85: Create Exam Schedule View

**Purpose**: Create exam schedule view with class, subject, and time details.

**Functionality**: Provides interface to schedule exams for classes and subjects.

**How it Works**:
- Creates `resources/views/admin/exams/schedule.blade.php`
- Extends app layout
- Shows page header with exam name and "Back to Exam" button
- Shows exam details card:
  - Exam Name
  - Exam Type
  - Academic Session
  - Start Date
  - End Date
- Shows filter form:
  - Class (select)
  - Section (select, optional)
- Shows subject list with:
  - Subject Name
  - Subject Code
  - Exam Date (date picker)
  - Start Time (time picker)
  - End Time (time picker)
  - Room Number
  - Full Marks
  - Passing Marks
  - Actions (remove)
- Shows "Add Subject" button
- Shows modal for adding subject:
  - Subject dropdown
  - Exam Date
  - Start Time
  - End Time
  - Room Number
  - Full Marks
  - Passing Marks
  - Add button
- Shows validation errors
- Shows "Save Schedule" button with loading state
- Shows "Auto-Generate" button (generates from class subjects)
- Shows "Clear Schedule" button
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state
- Shows empty state if no subjects

**Integration**:
- Uses ExamScheduleController store method
- Creates exam_schedule records
- Links to exam details
- Used by admin role

**Execute**: Create exam schedule view with subject list, time details, and responsive design.

---

### Prompt 86: Create Exam Attendance View

**Purpose**: Create exam attendance marking view for students.

**Functionality**: Provides interface to mark student attendance for exams.

**How it Works**:
- Creates `resources/views/admin/exams/attendance.blade.php`
- Extends app layout
- Shows page header with exam name and "Back to Schedule" button
- Shows exam schedule details:
  - Subject
  - Class
  - Section
  - Exam Date
  - Time
- Shows student list with:
  - Photo (avatar)
  - Roll Number
  - Name
  - Is Present (checkbox)
  - Remarks textarea
- Shows attendance summary:
  - Total Students
  - Present Count
  - Absent Count
- Shows "Mark All Present" button
- Shows "Save Attendance" button with loading state
- Shows "Cancel" button
- Shows validation errors
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading spinner during submission
- Shows success message on successful marking
- Redirects to schedule view on success

**Integration**:
- Uses ExamAttendanceController store method
- Creates exam_attendance records
- Links to exam schedule
- Used by teacher role

**Execute**: Create exam attendance view with student list and responsive design.

---

### Prompt 87: Create Marks Entry View

**Purpose**: Create marks entry view for entering student exam marks.

**Functionality**: Provides interface for teachers to enter marks for their subjects.

**How it Works**:
- Creates `resources/views/teacher/exams/marks.blade.php`
- Extends app layout
- Shows page header with exam name and "Back to Dashboard" button
- Shows filter form:
  - Academic Session (select)
  - Class (select, filtered by teacher's classes)
  - Section (select)
  - Subject (select, filtered by teacher's subjects)
  - Exam (select)
- Shows exam schedule details:
  - Subject
  - Full Marks
  - Passing Marks
  - Exam Date
- Shows student list with:
  - Photo (avatar)
  - Roll Number
  - Name
  - Obtained Marks (number input)
  - Grade (auto-calculated)
  - Remarks textarea
- Shows marks summary:
  - Total Students
  - Marks Entered Count
  - Average Marks
  - Highest Marks
  - Lowest Marks
- Shows "Auto-Calculate Grades" button
- Shows "Save Marks" button with loading state
- Shows "Cancel" button
- Shows validation errors
- Shows "Import Marks" button (Excel upload)
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading spinner during submission
- Shows success message on successful entry
- Redirects to marks list on success

**Integration**:
- Uses ExamMarkController store method
- Creates exam_mark records
- Calculates grades based on percentage
- Links to marks list
- Used by teacher role

**Execute**: Create marks entry view with student list, marks input, and responsive design.

---

### Prompt 88: Create Marks List View

**Purpose**: Create marks list view with search, filter, and edit.

**Functionality**: Provides comprehensive marks listing with advanced filtering.

**How it Works**:
- Creates `resources/views/admin/exams/marks.blade.php`
- Extends app layout
- Shows page header with title and "Enter Marks" button
- Shows search filter component with:
  - Search by student name, roll number
  - Filter by academic session
  - Filter by class
  - Filter by section
  - Filter by subject
  - Filter by exam
  - Filter by grade
- Shows table with columns:
  - Exam
  - Subject
  - Class
  - Section
  - Student Photo
  - Roll Number
  - Student Name
  - Full Marks
  - Obtained Marks
  - Percentage
  - Grade
  - Remarks
  - Entered By
  - Actions (view, edit)
- Shows bulk actions:
  - Export selected
  - Print selected
- Shows pagination component
- Shows records per page selector
- Shows "Export All" button
- Shows "Print Report" button
- Uses Bootstrap 5 grid layout
- Responsive design (table scrolls on mobile)
- Supports RTL languages
- Shows loading state
- Shows empty state if no marks

**Integration**:
- Uses ExamMarkController index method
- Queries ExamMark model with filters
- Links to enter marks, edit marks
- Links to export/print functionality
- Used by admin, teacher roles

**Execute**: Create marks list view with search, filters, table, and responsive design.

---

### Prompt 89: Create Marks Edit View

**Purpose**: Create marks edit view for correcting entered marks.

**Functionality**: Provides interface to edit previously entered marks.

**How it Works**:
- Creates `resources/views/admin/exams/marks-edit.blade.php`
- Extends app layout
- Shows page header with title and "Back to List" button
- Shows exam schedule details:
  - Exam
  - Subject
  - Full Marks
  - Passing Marks
- Shows student list with:
  - Photo (avatar)
  - Roll Number
  - Name
  - Current Obtained Marks
  - Obtained Marks (number input)
  - Grade (auto-calculated)
  - Remarks textarea
- Shows marks summary:
  - Total Students
  - Average Marks
  - Highest Marks
  - Lowest Marks
- Shows "Update Marks" button with loading state
- Shows "Cancel" button
- Shows validation errors
- Shows reason textarea for editing (required)
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading spinner during submission
- Shows success message on successful update
- Redirects to marks list on success

**Integration**:
- Uses ExamMarkController update method
- Validates marks data
- Updates exam_mark records
- Logs edit reason
- Links to marks list
- Used by admin, teacher roles

**Execute**: Create marks edit view with student list, marks input, and responsive design.

---

### Prompt 90: Create Exam Grades Management View

**Purpose**: Create exam grades management view with CRUD operations.

**Functionality**: Provides interface to manage grade ranges (A, B, C, D, F).

**How it Works**:
- Creates `resources/views/admin/exam-grades/index.blade.php`
- Extends app layout
- Shows page header with title and "Add Grade" button
- Shows table with columns:
  - Grade Name
  - Min Percentage
  - Max Percentage
  - Grade Point
  - Remarks
  - Status (active/inactive)
  - Actions (edit, delete)
- Shows grade preview with color coding
- Shows validation errors
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state
- Shows empty state if no grades

**Integration**:
- Uses ExamGradeController index method
- Queries ExamGrade model
- Links to create, edit, delete
- Used by admin role

**Execute**: Create exam grades management view with table, color coding, and responsive design.

---

### Prompt 91: Create Exam Grades Create View

**Purpose**: Create exam grade creation form.

**Functionality**: Provides form to create new grade range.

**How it Works**:
- Creates `resources/views/admin/exam-grades/create.blade.php`
- Extends app layout
- Shows page header with title and "Back to List" button
- Shows form with:
  - Grade Name (required, e.g., A, B, C)
  - Min Percentage (required, number)
  - Max Percentage (required, number)
  - Grade Point (number, optional)
  - Remarks
  - Status (active/inactive)
- Shows grade preview
- Shows validation errors
- Shows "Save" button with loading state
- Shows "Cancel" button
- Uses Bootstrap 5 form styling
- Responsive design
- Supports RTL languages
- Shows loading spinner during submission
- Shows success message on successful creation
- Redirects to grade list on success

**Integration**:
- Uses ExamGradeController store method
- Validates form fields
- Creates exam grade in exam_grades table
- Links to grade list
- Used by admin role

**Execute**: Create exam grades create view with form, preview, and responsive design.

---

### Prompt 92: Create Report Card View

**Purpose**: Create student report card view with exam results and grades.

**Functionality**: Shows comprehensive report card with all exam results.

**How it Works**:
- Creates `resources/views/admin/exams/report-card.blade.php`
- Extends app layout
- Shows page header with title and "Back to Student" button
- Shows student profile card:
  - Photo
  - Name
  - Class
  - Section
  - Roll Number
  - Academic Session
- Shows exam filter:
  - Filter by exam
  - Filter by exam type
- Shows report card with:
  - School header with logo and name
  - Student details
  - Academic session
  - Exam details
  - Subject-wise results table:
    - Subject
    - Full Marks
    - Obtained Marks
    - Percentage
    - Grade
    - Remarks
  - Summary:
    - Total Marks
    - Obtained Marks
    - Overall Percentage
    - Overall Grade
    - Grade Point Average
    - Class Rank
    - Section Rank
  - Attendance summary
  - Teacher remarks
  - Principal remarks
  - Grade distribution chart
- Shows "Print Report Card" button
- Shows "Download PDF" button
- Shows "Send to Parent" button (email)
- Uses Chart.js for grade distribution
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state

**Integration**:
- Uses ExamMarkController reportCard method
- Queries ExamMark model for student
- Calculates grades and ranks
- Shows report card
- Links to print/download
- Used by admin, teacher, parent, student roles

**Execute**: Create report card view with results, summary, charts, and responsive design.

---

### Prompt 93: Create Exam Results View

**Purpose**: Create exam results view with class-wise performance.

**Functionality**: Shows exam results for entire class with statistics.

**How it Works**:
- Creates `resources/views/admin/exams/results.blade.php`
- Extends app layout
- Shows page header with exam name and "Back to Exam" button
- Shows filter form:
  - Class (select)
  - Section (select)
  - Subject (select, optional)
- Shows exam details card:
  - Exam Name
  - Exam Type
  - Subject
  - Full Marks
  - Passing Marks
- Shows statistics cards:
  - Total Students
  - Appeared Students
  - Passed Students
  - Failed Students
  - Pass Percentage
  - Average Marks
  - Highest Marks
  - Lowest Marks
- Shows charts:
  - Grade distribution (pie chart)
  - Pass/Fail distribution (pie chart)
  - Marks distribution (bar chart)
- Shows student results table:
  - Roll Number
  - Student Name
  - Obtained Marks
  - Percentage
  - Grade
  - Class Rank
  - Section Rank
  - Actions (view report card)
- Shows "Export Results" button (PDF/Excel)
- Shows "Print Results" button
- Shows "Send Results to Parents" button (bulk email)
- Uses Chart.js for visualizations
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state

**Integration**:
- Uses ExamMarkController results method
- Queries ExamMark model with filters
- Calculates statistics and ranks
- Shows charts
- Links to export/print
- Used by admin, teacher roles

**Execute**: Create exam results view with statistics, charts, table, and responsive design.

---

### Prompt 94: Create Exam Print View

**Purpose**: Create printable exam report view.

**Functionality**: Provides printer-friendly version of exam report.

**How it Works**:
- Creates `resources/views/admin/exams/print.blade.php`
- Shows school header with logo and name
- Shows report title
- Shows exam details
- Shows student results table
- Shows statistics summary
- Shows grade distribution chart
- Shows school contact info
- Print-optimized layout (no navigation, minimal styling)
- Uses Bootstrap 5 grid layout
- Supports RTL languages

**Integration**:
- Uses ExamMarkController print method
- Queries ExamMark model
- Shows printable report
- Used by admin, teacher roles

**Execute**: Create exam print view with optimized layout.

---

### Prompt 95: Create Exam Export View

**Purpose**: Create exam export view with format options.

**Functionality**: Provides exam results export functionality with filters and format options.

**How it Works**:
- Creates `resources/views/admin/exams/export.blade.php`
- Extends app layout
- Shows page header with title and "Back to List" button
- Shows export filters:
  - Academic Session
  - Class
  - Section
  - Subject
  - Exam
  - Grade
- Shows export options:
  - Export format (Excel, PDF, CSV)
  - Include student details checkbox
  - Include remarks checkbox
  - Include summary checkbox
  - Include grade distribution checkbox
- Shows "Export" button with loading state
- Shows "Cancel" button
- Uses Bootstrap 5 form styling
- Responsive design
- Supports RTL languages
- Shows loading spinner during export
- Downloads file on successful export

**Integration**:
- Uses ExamMarkController export method
- Queries ExamMark model with filters
- Generates Excel/PDF/CSV file
- Downloads file to user
- Links to exam list
- Used by admin, teacher roles

**Execute**: Create exam export view with filters, format options, and responsive design.

---

## 🎨 Phase 8: Fees Management Views (15 Prompts)

### Prompt 96: Create Fees Types List View

**Purpose**: Create fees types listing page with CRUD operations.

**Functionality**: Provides fees types list with create, edit, delete functionality.

**How it Works**:
- Creates `resources/views/admin/fees-types/index.blade.php`
- Extends app layout
- Shows page header with title and "Add Type" button
- Shows table with columns:
  - Type Name
  - Code
  - Description
  - Status (active/inactive)
  - Actions (edit, delete)
- Shows validation errors
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state
- Shows empty state if no types

**Integration**:
- Uses FeesTypeController index method
- Queries FeesType model
- Links to create, edit, delete
- Used by admin role

**Execute**: Create fees types list view with table, actions, and responsive design.

---

### Prompt 97: Create Fees Types Create View

**Purpose**: Create fees type creation form.

**Functionality**: Provides form to create new fees type.

**How it Works**:
- Creates `resources/views/admin/fees-types/create.blade.php`
- Extends app layout
- Shows page header with title and "Back to List" button
- Shows form with:
  - Type Name (required)
  - Code (required, unique)
  - Description
  - Status (active/inactive)
- Shows validation errors
- Shows "Save" button with loading state
- Shows "Cancel" button
- Uses Bootstrap 5 form styling
- Responsive design
- Supports RTL languages
- Shows loading spinner during submission
- Shows success message on successful creation
- Redirects to type list on success

**Integration**:
- Uses FeesTypeController store method
- Validates form fields
- Creates fees type in fees_types table
- Links to type list
- Used by admin role

**Execute**: Create fees types create view with form, validation, and responsive design.

---

### Prompt 98: Create Fees Groups List View

**Purpose**: Create fees groups listing page with CRUD operations.

**Functionality**: Provides fees groups list with create, edit, delete functionality.

**How it Works**:
- Creates `resources/views/admin/fees-groups/index.blade.php`
- Extends app layout
- Shows page header with title and "Add Group" button
- Shows table with columns:
  - Group Name
  - Description
  - Status (active/inactive)
  - Actions (edit, delete)
- Shows validation errors
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state
- Shows empty state if no groups

**Integration**:
- Uses FeesGroupController index method
- Queries FeesGroup model
- Links to create, edit, delete
- Used by admin role

**Execute**: Create fees groups list view with table, actions, and responsive design.

---

### Prompt 99: Create Fees Groups Create View

**Purpose**: Create fees group creation form.

**Functionality**: Provides form to create new fees group.

**How it Works**:
- Creates `resources/views/admin/fees-groups/create.blade.php`
- Extends app layout
- Shows page header with title and "Back to List" button
- Shows form with:
  - Group Name (required)
  - Description
  - Status (active/inactive)
- Shows validation errors
- Shows "Save" button with loading state
- Shows "Cancel" button
- Uses Bootstrap 5 form styling
- Responsive design
- Supports RTL languages
- Shows loading spinner during submission
- Shows success message on successful creation
- Redirects to group list on success

**Integration**:
- Uses FeesGroupController store method
- Validates form fields
- Creates fees group in fees_groups table
- Links to group list
- Used by admin role

**Execute**: Create fees groups create view with form, validation, and responsive design.

---

### Prompt 100: Create Fees Masters List View

**Purpose**: Create fees masters listing page with CRUD operations.

**Functionality**: Provides fees configuration list with create, edit, delete functionality.

**How it Works**:
- Creates `resources/views/admin/fees-masters/index.blade.php`
- Extends app layout
- Shows page header with title and "Add Fee" button
- Shows filter by academic session
- Shows table with columns:
  - Fee Type
  - Fee Group
  - Class
  - Section
  - Academic Session
  - Amount
  - Due Date
  - Status (active/inactive)
  - Actions (edit, delete, allot)
- Shows "Allot to Students" button for each fee
- Shows validation errors
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state
- Shows empty state if no fees

**Integration**:
- Uses FeesMasterController index method
- Queries FeesMaster model with academic session
- Links to create, edit, delete, allot
- Used by admin role

**Execute**: Create fees masters list view with table, actions, and responsive design.

---

### Prompt 101: Create Fees Masters Create View

**Purpose**: Create fees master creation form.

**Functionality**: Provides form to create new fee configuration.

**How it Works**:
- Creates `resources/views/admin/fees-masters/create.blade.php`
- Extends app layout
- Shows page header with title and "Back to List" button
- Shows form with:
  - Fee Type (required, select)
  - Fee Group (select, optional)
  - Academic Session (required, select)
  - Class (select, optional)
  - Section (select, optional)
  - Amount (required, number)
  - Due Date (date picker)
  - Status (active/inactive)
- Shows validation errors
- Shows "Save" button with loading state
- Shows "Save & Allot" button
- Shows "Cancel" button
- Uses Bootstrap 5 form styling
- Responsive design
- Supports RTL languages
- Shows loading spinner during submission
- Shows success message on successful creation
- Redirects to fee list on success

**Integration**:
- Uses FeesMasterController store method
- Validates form fields
- Creates fee in fees_masters table
- Links to fee list
- Used by admin role

**Execute**: Create fees masters create view with form, validation, and responsive design.

---

### Prompt 102: Create Fees Discounts List View

**Purpose**: Create fees discounts listing page with CRUD operations.

**Functionality**: Provides fees discounts list with create, edit, delete functionality.

**How it Works**:
- Creates `resources/views/admin/fees-discounts/index.blade.php`
- Extends app layout
- Shows page header with title and "Add Discount" button
- Shows table with columns:
  - Discount Name
  - Code
  - Discount Type (percentage/fixed)
  - Discount Value
  - Description
  - Status (active/inactive)
  - Actions (edit, delete)
- Shows validation errors
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state
- Shows empty state if no discounts

**Integration**:
- Uses FeesDiscountController index method
- Queries FeesDiscount model
- Links to create, edit, delete
- Used by admin role

**Execute**: Create fees discounts list view with table, actions, and responsive design.

---

### Prompt 103: Create Fees Discounts Create View

**Purpose**: Create fees discount creation form.

**Functionality**: Provides form to create new fee discount.

**How it Works**:
- Creates `resources/views/admin/fees-discounts/create.blade.php`
- Extends app layout
- Shows page header with title and "Back to List" button
- Shows form with:
  - Discount Name (required)
  - Code (required, unique)
  - Discount Type (required, select: percentage/fixed)
  - Discount Value (required, number)
  - Description
  - Status (active/inactive)
- Shows discount preview
- Shows validation errors
- Shows "Save" button with loading state
- Shows "Cancel" button
- Uses Bootstrap 5 form styling
- Responsive design
- Supports RTL languages
- Shows loading spinner during submission
- Shows success message on successful creation
- Redirects to discount list on success

**Integration**:
- Uses FeesDiscountController store method
- Validates form fields
- Creates fee discount in fees_discounts table
- Links to discount list
- Used by admin role

**Execute**: Create fees discounts create view with form, preview, and responsive design.

---

### Prompt 104: Create Fees Allotment View

**Purpose**: Create fees allotment view for assigning fees to students.

**Functionality**: Provides interface to allot fees to students with discounts.

**How it Works**:
- Creates `resources/views/admin/fees/allot.blade.php`
- Extends app layout
- Shows page header with title and "Back to List" button
- Shows filter form:
  - Academic Session (select)
  - Fee Master (select)
  - Class (select)
  - Section (select)
  - Discount (select, optional)
- Shows student list with:
  - Photo (avatar)
  - Roll Number
  - Name
  - Fee Amount
  - Discount (select)
  - Discount Amount (auto-calculated)
  - Net Amount (auto-calculated)
  - Due Date
  - Checkbox for selection
- Shows allotment summary:
  - Total Students
  - Selected Students
  - Total Amount
  - Total Discount
  - Net Amount
- Shows "Select All" checkbox
- Shows "Allot Fees" button with loading state
- Shows "Cancel" button
- Shows validation errors
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading spinner during allotment
- Shows success message with count of allotted students
- Redirects to allotment list on success

**Integration**:
- Uses FeesAllotmentController store method
- Creates fees_allotment records
- Applies discounts
- Links to allotment list
- Used by admin role

**Execute**: Create fees allotment view with student list, discounts, and responsive design.

---

### Prompt 105: Create Fees Collection View

**Purpose**: Create fees collection view for collecting payments from students.

**Functionality**: Provides interface for accountants to collect fee payments.

**How it Works**:
- Creates `resources/views/accountant/fees/collect.blade.php`
- Extends app layout
- Shows page header with title and "Back to Dashboard" button
- Shows search form:
  - Search by student name, admission number, roll number
  - Filter by class
  - Filter by section
- Shows student list with pending fees:
  - Photo (avatar)
  - Roll Number
  - Name
  - Class
  - Section
  - Pending Amount
  - Actions (collect fee)
- Shows "Collect Fee" button for each student
- Shows modal for collecting fee:
  - Student details
  - Pending fees list with checkboxes
  - Selected fees amount
  - Payment Method (cash, cheque, dd, online)
  - Payment Date
  - Reference Number
  - Bank Name
  - Cheque Number
  - Remarks
  - Collect button with loading state
- Shows validation errors
- Shows receipt preview
- Shows "Print Receipt" button
- Shows "Send Receipt" button (email/SMS)
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state
- Shows empty state if no pending fees

**Integration**:
- Uses FeesTransactionController store method
- Creates fees_transaction records
- Updates fees_allotment status
- Generates receipt
- Sends receipt via email/SMS
- Links to transaction list
- Used by accountant role

**Execute**: Create fees collection view with student list, payment form, and responsive design.

---

### Prompt 106: Create Fees Transactions List View

**Purpose**: Create fees transactions listing page with search, filter, and export.

**Functionality**: Provides comprehensive transaction listing with advanced filtering.

**How it Works**:
- Creates `resources/views/accountant/fees/transactions.blade.php`
- Extends app layout
- Shows page header with title and "Collect Fee" button
- Shows search filter component with:
  - Search by student name, transaction ID
  - Filter by date range
  - Filter by class
  - Filter by section
  - Filter by payment method
  - Filter by payment status
- Shows statistics cards:
  - Today's Collection
  - This Month Collection
  - This Year Collection
  - Total Collection
  - Pending Collection
- Shows table with columns:
  - Transaction ID
  - Date
  - Student Photo
  - Student Name
  - Class
  - Section
  - Fee Type
  - Amount
  - Payment Method
  - Payment Status
  - Receipt
  - Actions (view, print, refund)
- Shows bulk actions:
  - Export selected
  - Print selected
- Shows pagination component
- Shows records per page selector
- Shows "Export All" button
- Shows "Print Report" button
- Uses Bootstrap 5 grid layout
- Responsive design (table scrolls on mobile)
- Supports RTL languages
- Shows loading state
- Shows empty state if no transactions

**Integration**:
- Uses FeesTransactionController index method
- Queries FeesTransaction model with filters
- Links to collect fee, view transaction, print receipt
- Links to export/print functionality
- Used by accountant role

**Execute**: Create fees transactions list view with search, filters, table, and responsive design.

---

### Prompt 107: Create Fees Receipt View

**Purpose**: Create fee receipt view with transaction details.

**Functionality**: Shows printable fee receipt with all transaction details.

**How it Works**:
- Creates `resources/views/accountant/fees/receipt.blade.php`
- Shows school header with logo and name
- Shows receipt title
- Shows receipt number and date
- Shows student details:
  - Photo
  - Name
  - Admission Number
  - Roll Number
  - Class
  - Section
- Shows payment details:
  - Transaction ID
  - Payment Date
  - Payment Method
  - Reference Number
  - Bank Name
  - Cheque Number
- Shows fee details table:
  - Fee Type
  - Amount
  - Discount
  - Net Amount
- Shows total amount in words
- Shows school contact info
- Shows "Print Receipt" button
- Shows "Download PDF" button
- Shows "Send Receipt" button (email/SMS)
- Print-optimized layout
- Uses Bootstrap 5 grid layout
- Supports RTL languages

**Integration**:
- Uses FeesTransactionController receipt method
- Queries FeesTransaction model
- Shows receipt
- Links to print/download
- Used by accountant, parent roles

**Execute**: Create fee receipt view with details, print, and responsive design.

---

### Prompt 108: Create Fees Refund View

**Purpose**: Create fee refund view for processing refunds.

**Functionality**: Provides interface to process fee refunds.

**How it Works**:
- Creates `resources/views/accountant/fees/refund.blade.php`
- Extends app layout
- Shows page header with title and "Back to Transaction" button
- Shows transaction details card:
  - Transaction ID
  - Student Name
  - Fee Type
  - Amount Paid
  - Payment Method
  - Payment Date
- Shows refund form:
  - Refund Amount (number input)
  - Refund Reason (required, textarea)
  - Refund Method (select)
  - Refund Date
  - Reference Number
  - Bank Name
  - Account Number
  - Remarks
- Shows validation errors
- Shows "Process Refund" button with loading state
- Shows "Cancel" button
- Uses Bootstrap 5 form styling
- Responsive design
- Supports RTL languages
- Shows loading spinner during processing
- Shows success message on successful refund
- Redirects to transaction list on success

**Integration**:
- Uses FeesTransactionController refund method
- Updates fees_transaction status to refunded
- Creates new refund transaction
- Links to transaction list
- Used by accountant role

**Execute**: Create fee refund view with form, validation, and responsive design.

---

### Prompt 109: Create Fees Report View

**Purpose**: Create fees report view with statistics and charts.

**Functionality**: Shows comprehensive fees reports with analytics.

**How it Works**:
- Creates `resources/views/accountant/fees/report.blade.php`
- Extends app layout
- Shows page header with title and "Back to Dashboard" button
- Shows filter form:
  - Academic Session (select)
  - Class (select)
  - Section (select)
  - Date Range
  - Report Type (daily, monthly, yearly)
- Shows statistics cards:
  - Total Fees
  - Collected Amount
  - Pending Amount
  - Refunded Amount
  - Collection Percentage
- Shows charts:
  - Collection trend (line chart)
  - Collection by class (bar chart)
  - Collection by payment method (pie chart)
  - Pending fees by class (bar chart)
- Shows class-wise collection table:
  - Class
  - Section
  - Total Fees
  - Collected
  - Pending
  - Collection Percentage
- Shows "Export Report" button (PDF/Excel)
- Shows "Print Report" button
- Uses Chart.js for visualizations
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state

**Integration**:
- Uses FeesTransactionController report method
- Queries FeesTransaction and FeesAllotment models
- Calculates statistics
- Shows charts
- Links to export/print
- Used by accountant role

**Execute**: Create fees report view with statistics, charts, table, and responsive design.

---

### Prompt 110: Create Fees Due Reminder View

**Purpose**: Create fees due reminder view for sending reminders to parents.

**Functionality**: Provides interface to send fee due reminders via SMS/email.

**How it Works**:
- Creates `resources/views/accountant/fees/reminder.blade.php`
- Extends app layout
- Shows page header with title and "Back to Dashboard" button
- Shows filter form:
  - Academic Session (select)
  - Class (select)
  - Section (select)
  - Due Date Range
  - Overdue Days (number input)
- Shows student list with pending fees:
  - Photo (avatar)
  - Roll Number
  - Name
  - Father's Name
  - Phone Number
  - Email
  - Fee Type
  - Pending Amount
  - Due Date
  - Overdue Days
  - Checkbox for selection
- Shows reminder summary:
  - Total Students
  - Selected Students
  - Total Pending Amount
- Shows "Select All" checkbox
- Shows reminder options:
  - Send SMS (checkbox)
  - Send Email (checkbox)
  - Message template (textarea with placeholders)
  - Email template (textarea with placeholders)
- Shows preview of message
- Shows "Send Reminder" button with loading state
- Shows "Cancel" button
- Shows validation errors
- Shows SMS cost estimate
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading spinner during sending
- Shows success message with sent count
- Shows error message with failed count

**Integration**:
- Uses FeesAllotmentController sendReminder method
- Queries FeesAllotment model
- Queries Student model for contact info
- Sends SMS via SMS gateway
- Sends email via email service
- Creates SMS and email logs
- Links to fees list
- Used by accountant role

**Execute**: Create fees due reminder view with student list, message template, and responsive design.

---

## 📊 Summary

**Total Frontend Prompts in Part 2: 40**

**Phases Covered in Part 2:**
6. **Attendance Management Views** (10 prompts)
7. **Examination Management Views** (15 prompts)
8. **Fees Management Views** (15 prompts)

**Total Frontend Prompts (Part 1 + Part 2): 110**

**Features Implemented:**
- Attendance marking with student list
- Attendance calendar with color coding
- Attendance reports with charts
- Exam scheduling with subjects and times
- Marks entry with auto-grade calculation
- Report cards with ranks and statistics
- Exam results with class-wise performance
- Fees configuration with types and groups
- Fees allotment with discounts
- Fee collection with receipt generation
- Fee refunds processing
- Fee reports with analytics
- Fee due reminders via SMS/email

**Next Phases:**
- Library Management Views
- Transport Management Views
- Hostel Management Views
- Communication Views
- Accounting Views
- Reports Views
- Settings Views

---

## 🚀 Continue with More Frontend Prompts

This document covers 40 additional detailed frontend prompts. Continue with remaining modules to complete entire frontend implementation.

**Happy Building with DevIn AI!** 🚀
