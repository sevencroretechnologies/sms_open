# Smart School Management System - Frontend Detailed Prompts

This document contains comprehensive, detailed prompts for building the complete frontend UI for Smart School Management System using DevIn AI. Each prompt includes:
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

---

## 🎨 Phase 1: Layout & Components (20 Prompts)

### Prompt 1: Create Base Layout

**Purpose**: Create the fundamental HTML structure that all pages will extend, ensuring consistency across the application.

**Functionality**: Provides a master template with HTML5 structure, responsive meta tags, CSS/JS includes, and content sections.

**How it Works**:
- Creates `resources/views/layouts/app.blade.php`
- Includes HTML5 doctype and language attribute
- Adds responsive meta tags for mobile devices
- Includes CSRF token for form security
- Includes Vite compiled CSS in `<head>`
- Includes navigation component in sidebar
- Yields `content` section for page-specific content
- Includes footer component
- Includes Vite compiled JS before closing `</body>`
- Uses Bootstrap 5 grid system for responsive layout
- Implements mobile-first responsive design
- Supports RTL (right-to-left) languages via `dir` attribute

**Integration**:
- All views extend this layout using `@extends('layouts.app')`
- Navigation included in all authenticated pages
- Footer included in all pages
- All CSS/JS loaded from this layout
- Language direction controlled from this layout
- Provides consistent design across entire application

**Execute**: Create base layout with complete HTML structure, meta tags, and component includes.

---

### Prompt 2: Create Auth Layout

**Purpose**: Create a simplified layout for authentication pages (login, register, password reset) without navigation.

**Functionality**: Provides a centered, clean layout specifically for authentication forms.

**How it Works**:
- Creates `resources/views/layouts/auth.blade.php`
- Includes HTML5 doctype and meta tags
- Centers content vertically and horizontally using Flexbox
- Includes Vite compiled CSS
- Yields `content` section for auth forms
- Includes Vite compiled JS
- Uses Bootstrap 5 container for form centering
- Adds school logo or branding
- Provides clean, distraction-free authentication experience
- Supports RTL languages

**Integration**:
- Used by login, register, forgot password, reset password pages
- Excludes navigation and footer for simplified auth experience
- Consistent design across all auth pages
- Mobile-responsive authentication forms

**Execute**: Create auth layout with centered content and clean design.

---

### Prompt 3: Create Navigation Sidebar Component

**Purpose**: Create responsive navigation sidebar with role-based menu items for all 6 user roles.

**Functionality**: Displays navigation menu items based on user's role and permissions with collapsible submenus.

**How it Works**:
- Creates `resources/views/layouts/navigation.blade.php`
- Checks authenticated user's role using `Auth::user()->role->name`
- Displays role-appropriate menu items:
  - **Admin**: All modules (Dashboard, Students, Academic, Attendance, Exams, Fees, Library, Transport, Hostel, Communication, Accounting, Settings, Reports)
  - **Teacher**: Dashboard, My Classes, Attendance, Exams, Homework, Study Materials, Messages
  - **Student**: Dashboard, My Classes, Attendance, Exams, Homework, Study Materials, Fees, Library, Messages
  - **Parent**: Dashboard, My Children, Attendance, Exams, Fees, Messages
  - **Accountant**: Dashboard, Fees Collection, Income, Expenses, Reports
  - **Librarian**: Dashboard, Books, Issue/Return, Members, Reports
- Implements collapsible submenus using Bootstrap 5 collapse
- Shows active menu item highlighting
- Includes user profile dropdown with avatar, name, logout
- Implements mobile hamburger menu toggle
- Uses Alpine.js for interactive menu behavior
- Supports RTL layout for Arabic languages
- Shows notification badges for unread messages/notices
- Includes quick action buttons for common tasks

**Integration**:
- Included in base layout
- Links to all module pages and routes
- Shows only menu items user has permission to access
- User profile dropdown links to profile page
- Logout button destroys session and redirects to login
- Used in all authenticated pages
- Role-based menu items controlled by Spatie Permission

**Execute**: Create navigation sidebar with role-based menu items, collapsible submenus, and responsive design.

---

### Prompt 4: Create Top Header Component

**Purpose**: Create top header bar with user info, notifications, language switcher, and theme toggle.

**Functionality**: Displays global header with quick access to user profile, notifications, and system settings.

**How it Works**:
- Creates `resources/views/layouts/header.blade.php`
- Shows school logo and name
- Displays current academic session selector
- Shows user avatar and name with dropdown
- Includes notification bell with unread count
- Notification dropdown shows latest notices and messages
- Includes language switcher dropdown (73+ languages)
- Includes theme toggle (light/dark mode)
- Shows current date and time
- Includes help/support link
- Implements responsive behavior on mobile
- Uses Alpine.js for dropdown interactions
- Supports RTL languages

**Integration**:
- Included in base layout
- Academic session selector filters data throughout app
- User dropdown links to profile and settings
- Notifications link to notices and messages
- Language switcher changes app language
- Theme toggle persists user preference
- Used in all authenticated pages

**Execute**: Create top header component with user info, notifications, language switcher, and theme toggle.

---

### Prompt 5: Create Footer Component

**Purpose**: Create consistent footer across all pages with copyright, links, and system info.

**Functionality**: Displays footer with copyright, quick links, version number, and contact info.

**How it Works**:
- Creates `resources/views/layouts/footer.blade.php`
- Shows school name and logo
- Displays copyright notice with current year
- Shows quick links: About, Contact, Privacy Policy, Terms of Service
- Shows social media links (Facebook, Twitter, Instagram, LinkedIn)
- Displays system version number
- Shows contact information (phone, email, address)
- Includes "Back to Top" button
- Responsive grid layout using Bootstrap 5
- Supports RTL languages

**Integration**:
- Included in base layout
- Consistent across all pages
- Links to static pages
- Provides system information
- Used in all pages

**Execute**: Create footer component with copyright, links, and system information.

---

### Prompt 6: Create Alert Component

**Purpose**: Create reusable alert component for success, error, warning, and info messages.

**Functionality**: Provides consistent alert styling for flash messages and notifications.

**How it Works**:
- Creates `resources/views/components/alert.blade.php`
- Accepts type parameter (success, danger, warning, info)
- Accepts message parameter for alert text
- Accepts dismissible parameter for close button
- Uses Bootstrap 5 alert component
- Includes appropriate icon for each alert type:
  - Success: Check circle icon
  - Danger: Exclamation triangle icon
  - Warning: Exclamation circle icon
  - Info: Info circle icon
- Implements auto-dismiss after 5 seconds
- Supports RTL languages
- Accessible with proper ARIA attributes

**Integration**:
- Used throughout application for flash messages
- Displayed after form submissions
- Used for validation errors
- Used for success notifications
- Consistent alert styling across app

**Execute**: Create alert component with type-based styling, icons, and auto-dismiss.

---

### Prompt 7: Create Card Component

**Purpose**: Create reusable card component for displaying content with header, body, and footer.

**Functionality**: Provides consistent card styling for content containers.

**How it Works**:
- Creates `resources/views/components/card.blade.php`
- Accepts title parameter for card header
- Accepts optional icon parameter for header
- Accepts body content slot
- Accepts optional footer content slot
- Uses Bootstrap 5 card component
- Includes optional action buttons in header
- Supports collapsible card body
- Supports RTL languages
- Responsive design

**Integration**:
- Used throughout application for content containers
- Used in dashboard statistics cards
- Used in forms and data displays
- Consistent card styling across app

**Execute**: Create card component with header, body, footer, and optional features.

---

### Prompt 8: Create Table Component

**Purpose**: Create reusable table component with pagination, sorting, and filtering.

**Functionality**: Provides consistent table styling with advanced features.

**How it Works**:
- Creates `resources/views/components/table.blade.php`
- Accepts data parameter (collection)
- Accepts columns parameter (array of column definitions)
- Accepts sortable parameter for sortable columns
- Accepts filterable parameter for filterable columns
- Uses Bootstrap 5 table component
- Implements client-side sorting using Alpine.js
- Implements client-side filtering using Alpine.js
- Includes pagination controls
- Shows records per page selector
- Includes bulk action checkboxes
- Shows loading state
- Supports RTL languages
- Responsive table with horizontal scroll on mobile
- Empty state message when no data

**Integration**:
- Used in all listing pages (students, classes, exams, etc.)
- Provides consistent table styling
- Enables sorting and filtering without server requests
- Used in admin and user panels
- Supports bulk operations

**Execute**: Create table component with sorting, filtering, pagination, and responsive design.

---

### Prompt 9: Create Form Input Component

**Purpose**: Create reusable form input component with validation and help text.

**Functionality**: Provides consistent form input styling with validation feedback.

**How it Works**:
- Creates `resources/views/components/form-input.blade.php`
- Accepts name parameter for input name
- Accepts label parameter for input label
- Accepts type parameter (text, email, password, number, date, etc.)
- Accepts value parameter for default value
- Accepts placeholder parameter
- Accepts required parameter for required field
- Accepts help text parameter for field description
- Accepts error message parameter for validation errors
- Uses Bootstrap 5 form control
- Shows validation error styling
- Shows success validation styling
- Includes required field indicator
- Supports RTL languages
- Accessible with proper labels and ARIA attributes

**Integration**:
- Used in all forms throughout application
- Consistent input styling
- Automatic validation error display
- Used in student admission, fee collection, etc.

**Execute**: Create form input component with validation, help text, and accessibility.

---

### Prompt 10: Create Form Select Component

**Purpose**: Create reusable select dropdown component with validation.

**Functionality**: Provides consistent select dropdown styling with options.

**How it Works**:
- Creates `resources/views/components/form-select.blade.php`
- Accepts name parameter for select name
- Accepts label parameter for select label
- Accepts options parameter (array of value => label pairs)
- Accepts selected parameter for default selected value
- Accepts placeholder parameter for default option
- Accepts required parameter for required field
- Accepts help text parameter
- Accepts error message parameter
- Uses Bootstrap 5 form select
- Shows validation error styling
- Includes search/filter functionality using Select2 or similar
- Supports RTL languages
- Supports optgroups

**Integration**:
- Used in all forms with select dropdowns
- Consistent select styling
- Used in class selection, subject selection, etc.

**Execute**: Create form select component with options, search, and validation.

---

### Prompt 11: Create Form Datepicker Component

**Purpose**: Create reusable datepicker component for date selection with validation.

**Functionality**: Provides consistent datepicker with calendar UI.

**How it Works**:
- Creates `resources/views/components/form-datepicker.blade.php`
- Accepts name parameter for input name
- Accepts label parameter for input label
- Accepts value parameter for default date
- Accepts min_date parameter for minimum date
- Accepts max_date parameter for maximum date
- Accepts required parameter
- Accepts help text parameter
- Accepts error message parameter
- Uses Bootstrap 5 datepicker (flatpickr or similar)
- Shows calendar popup on click
- Supports date range selection
- Shows validation error styling
- Supports RTL languages
- Accessible with proper labels

**Integration**:
- Used in all date input fields
- Consistent datepicker UI
- Used in admission date, exam date, fee due date, etc.

**Execute**: Create form datepicker component with calendar UI and validation.

---

### Prompt 12: Create Form File Upload Component

**Purpose**: Create reusable file upload component with drag-and-drop and preview.

**Functionality**: Provides consistent file upload UI with preview and validation.

**How it Works**:
- Creates `resources/views/components/form-file-upload.blade.php`
- Accepts name parameter for input name
- Accepts label parameter for upload label
- Accepts accept parameter for accepted file types
- Accepts multiple parameter for multiple file upload
- Accepts max_size parameter for maximum file size
- Accepts preview parameter to show preview
- Accepts error message parameter
- Uses Bootstrap 5 form control
- Implements drag-and-drop functionality
- Shows file preview for images
- Shows file name and size
- Shows upload progress bar
- Shows validation errors
- Supports RTL languages
- Accessible with proper labels

**Integration**:
- Used in all file upload forms
- Used in student documents, homework attachments, etc.
- Consistent upload UI
- Drag-and-drop support

**Execute**: Create form file upload component with drag-and-drop, preview, and validation.

---

### Prompt 14: Create Pagination Component

**Purpose**: Create reusable pagination component for navigating through paginated data.

**Functionality**: Provides consistent pagination UI with page numbers and navigation buttons.

**How it Works**:
- Creates `resources/views/components/pagination.blade.php`
- Accepts data parameter (Laravel paginator)
- Shows current page and total pages
- Shows first and last page buttons
- Shows previous and next buttons
- Shows page number links
- Shows ellipsis for large page ranges
- Highlights current page
- Uses Bootstrap 5 pagination component
- Supports RTL languages
- Accessible with proper ARIA attributes
- Shows "Showing X to Y of Z entries" text

**Integration**:
- Used in all paginated listing pages
- Consistent pagination UI
- Used in student list, exam list, fee list, etc.

**Execute**: Create pagination component with page numbers, navigation buttons, and accessibility.

---

### Prompt 15: Create Modal Component

**Purpose**: Create reusable modal component for dialogs and forms.

**Functionality**: Provides consistent modal UI with header, body, and footer.

**How it Works**:
- Creates `resources/views/components/modal.blade.php`
- Accepts id parameter for modal ID
- Accepts title parameter for modal title
- Accepts size parameter (sm, md, lg, xl)
- Accepts body content slot
- Accepts footer content slot
- Accepts show_close_button parameter
- Uses Bootstrap 5 modal component
- Implements backdrop click to close
- Implements ESC key to close
- Supports RTL languages
- Accessible with proper ARIA attributes
- Shows loading state
- Supports multiple modals

**Integration**:
- Used throughout application for dialogs
- Used in confirmations, forms, details views
- Consistent modal UI
- Used in delete confirmations, quick actions, etc.

**Execute**: Create modal component with header, body, footer, and accessibility.

---

### Prompt 16: Create Loading Spinner Component

**Purpose**: Create reusable loading spinner for showing loading states.

**Functionality**: Provides consistent loading animation for async operations.

**How it Works**:
- Creates `resources/views/components/loading-spinner.blade.php`
- Accepts size parameter (sm, md, lg)
- Accepts color parameter (primary, secondary, success, etc.)
- Accepts text parameter for loading message
- Uses Bootstrap 5 spinner component
- Shows rotating spinner animation
- Shows optional loading text
- Supports RTL languages
- Accessible with proper ARIA attributes

**Integration**:
- Used throughout application for loading states
- Used in form submissions, data loading, etc.
- Consistent loading animation

**Execute**: Create loading spinner component with size, color, and text options.

---

### Prompt 17: Create Empty State Component

**Purpose**: Create reusable empty state component for when no data is available.

**Functionality**: Provides consistent empty state UI with illustration and action button.

**How it Works**:
- Creates `resources/views/components/empty-state.blade.php`
- Accepts title parameter for empty state title
- Accepts message parameter for empty state message
- Accepts icon parameter for empty state icon
- Accepts action_text parameter for action button text
- Accepts action_url parameter for action button URL
- Uses Bootstrap 5 utilities
- Shows centered empty state
- Shows icon or illustration
- Shows descriptive message
- Shows action button if provided
- Supports RTL languages
- Responsive design

**Integration**:
- Used in all listing pages when no data
- Used in search results, filtered lists, etc.
- Consistent empty state UI

**Execute**: Create empty state component with icon, message, and action button.

---

### Prompt 18: Create Search Filter Component

**Purpose**: Create reusable search and filter component for data tables.

**Functionality**: Provides search input and filter dropdowns for filtering data.

**How it Works**:
- Creates `resources/views/components/search-filter.blade.php`
- Accepts search_placeholder parameter
- Accepts filters parameter (array of filter definitions)
- Accepts on_search callback parameter
- Accepts on_filter callback parameter
- Uses Bootstrap 5 form controls
- Shows search input with search icon
- Shows filter dropdowns for each filter
- Implements real-time search using Alpine.js
- Shows active filter count
- Shows clear filters button
- Supports RTL languages
- Responsive design (collapses filters on mobile)

**Integration**:
- Used in all listing pages
- Used in student list, exam list, fee list, etc.
- Consistent search/filter UI
- Real-time filtering

**Execute**: Create search filter component with search input and filter dropdowns.

---

### Prompt 19: Create Breadcrumb Component

**Purpose**: Create reusable breadcrumb component for page navigation hierarchy.

**Functionality**: Provides consistent breadcrumb UI showing current page location.

**How it Works**:
- Creates `resources/views/components/breadcrumb.blade.php`
- Accepts items parameter (array of breadcrumb items)
- Each item has label and url parameters
- Last item is current page (no link)
- Uses Bootstrap 5 breadcrumb component
- Shows home icon for root
- Shows separator between items
- Supports RTL languages
- Accessible with proper ARIA attributes
- Responsive design (collapses on mobile)

**Integration**:
- Used in all pages to show navigation hierarchy
- Consistent breadcrumb UI
- Helps users understand page location

**Execute**: Create breadcrumb component with navigation hierarchy and accessibility.

---

### Prompt 20: Create Chart Component

**Purpose**: Create reusable chart component for data visualization using Chart.js.

**Functionality**: Provides consistent chart UI for displaying statistics and analytics.

**How it Works**:
- Creates `resources/views/components/chart.blade.php`
- Accepts type parameter (bar, line, pie, doughnut, etc.)
- Accepts data parameter (chart data)
- Accepts options parameter (chart configuration)
- Accepts height parameter for chart height
- Uses Chart.js library
- Renders canvas element for chart
- Initializes Chart.js instance with Alpine.js
- Shows tooltip on hover
- Shows legend
- Supports RTL languages
- Responsive chart (resizes with container)
- Shows loading state
- Supports multiple datasets

**Integration**:
- Used in dashboard and reports
- Used in student statistics, fee analytics, etc.
- Consistent chart UI
- Interactive charts

**Execute**: Create chart component with Chart.js, multiple types, and responsive design.

---

## 🎨 Phase 2: Authentication Views (5 Prompts)

### Prompt 21: Create Login View

**Purpose**: Create login page with email/password form and validation.

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
- Shows flash messages (success/error)
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

### Prompt 22: Create Registration View

**Purpose**: Create registration page with user registration form.

**Functionality**: Provides user interface for new user registration.

**How it Works**:
- Creates `resources/views/auth/register.blade.php`
- Extends auth layout
- Shows registration form with:
  - First name input
  - Last name input
  - Email input with validation
  - Phone input with validation
  - Password input with strength indicator
  - Confirm password input
  - Terms and conditions checkbox
  - Register button with loading state
- Shows password strength meter
- Shows validation errors
- Shows flash messages
- Includes CSRF token
- Uses Bootstrap 5 form styling
- Responsive design
- Supports RTL languages
- Shows loading spinner during submission
- Redirects to login on success with success message

**Integration**:
- Uses AuthController register method
- Validates input data
- Creates user in users table
- Assigns default role (student/parent)
- Sends verification email (if enabled)
- Redirects to login page
- Links to login page

**Execute**: Create registration view with form, password strength, validation, and responsive design.

---

### Prompt 23: Create Forgot Password View

**Purpose**: Create forgot password page with email input.

**Functionality**: Provides user interface for password reset request.

**How it Works**:
- Creates `resources/views/auth/forgot-password.blade.php`
- Extends auth layout
- Shows forgot password form with:
  - Email input with validation
  - Send Reset Link button with loading state
- Shows validation errors
- Shows flash messages
- Includes CSRF token
- Uses Bootstrap 5 form styling
- Responsive design
- Supports RTL languages
- Shows loading spinner during submission
- Shows success message after sending
- Redirects to login on success

**Integration**:
- Uses AuthController sendPasswordResetLink method
- Validates email
- Sends password reset email
- Creates password reset token
- Redirects to login page
- Links to login page

**Execute**: Create forgot password view with email input and validation.

---

### Prompt 24: Create Reset Password View

**Purpose**: Create reset password page with new password form.

**Functionality**: Provides user interface for setting new password.

**How it Works**:
- Creates `resources/views/auth/reset-password.blade.php`
- Extends auth layout
- Shows reset password form with:
  - Email input (read-only from token)
  - New password input with show/hide toggle
  - Confirm password input
  - Reset Password button with loading state
- Shows password strength meter
- Shows validation errors
- Shows flash messages
- Includes CSRF token
- Includes reset token
- Uses Bootstrap 5 form styling
- Responsive design
- Supports RTL languages
- Shows loading spinner during submission
- Redirects to login on success

**Integration**:
- Uses AuthController resetPassword method
- Validates token and email
- Updates user password in users table
- Invalidates reset token
- Redirects to login page
- Links to login page

**Execute**: Create reset password view with password inputs, strength meter, and validation.

---

### Prompt 25: Create Email Verification View

**Purpose**: Create email verification page with resend verification link option.

**Functionality**: Provides user interface for email verification status.

**How it Works**:
- Creates `resources/views/auth/verify-email.blade.php`
- Extends auth layout
- Shows verification status message
- Shows "Resend Verification Email" button
- Shows countdown timer for resend
- Shows flash messages
- Uses Bootstrap 5 styling
- Responsive design
- Supports RTL languages
- Shows loading spinner during resend
- Redirects to dashboard if already verified

**Integration**:
- Uses AuthController resendVerificationEmail method
- Sends verification email
- Updates email verification status
- Redirects to dashboard on verification
- Links to login page

**Execute**: Create email verification view with resend button and countdown timer.

---

## 🎨 Phase 3: Dashboard Views (10 Prompts)

### Prompt 26: Create Admin Dashboard View

**Purpose**: Create admin dashboard with statistics, charts, and activities.

**Functionality**: Provides comprehensive overview of school operations with key metrics and visualizations.

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
- Shows quick actions:
  - Add Student
  - Mark Attendance
  - Collect Fee
  - Create Notice
- Shows upcoming events:
  - Exams
  - Holidays
  - Meetings
- Shows notices list with pagination
- Shows academic session selector
- Shows date range filter for charts
- Uses Chart.js for visualizations
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
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
- Used by admin role

**Execute**: Create admin dashboard with statistics, charts, activities, and responsive design.

---

### Prompt 27: Create Teacher Dashboard View

**Purpose**: Create teacher dashboard with class schedule, attendance, and tasks.

**Functionality**: Provides teacher-specific overview with today's schedule, classes, and pending tasks.

**How it Works**:
- Creates `resources/views/teacher/dashboard.blade.php`
- Extends app layout
- Shows teacher profile card with photo and info
- Shows today's schedule:
  - Timetable with periods
  - Current period highlighted
  - Subject, class, room info
  - Time remaining
- Shows my classes list:
  - Class and section
  - Total students
  - Subject taught
  - Link to class details
- Shows pending tasks:
  - Attendance to mark
  - Homework to check
  - Marks to enter
  - Reports to generate
- Shows quick actions:
  - Mark Attendance
  - Create Homework
  - Upload Study Material
  - Send Message
- Shows notices relevant to teachers
- Shows upcoming exams for teacher's subjects
- Shows recent messages from students/parents
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state
- Auto-refreshes schedule

**Integration**:
- Uses TeacherDashboardController index method
- Queries ClassTimetable model for schedule
- Queries Attendance model for pending attendance
- Queries Homework model for pending homework
- Queries ExamSchedule model for upcoming exams
- Links to class details, attendance, homework
- Used by teacher role

**Execute**: Create teacher dashboard with schedule, classes, tasks, and responsive design.

---

### Prompt 28: Create Student Dashboard View

**Purpose**: Create student dashboard with academics, attendance, and notices.

**Functionality**: Provides student-specific overview with academic performance, attendance, and school updates.

**How it Works**:
- Creates `resources/views/student/dashboard.blade.php`
- Extends app layout
- Shows student profile card with photo and info
- Shows academic summary:
  - Current class and section
  - Roll number
  - Academic session
- Shows attendance summary:
  - Overall attendance percentage
  - Present days
  - Absent days
  - Monthly attendance chart
- Shows exam results:
  - Latest exam results
  - Overall percentage
  - Grade obtained
  - Subject-wise performance
- Shows fee status:
  - Total fees
  - Paid amount
  - Pending amount
  - Due dates
- Shows homework list:
  - Pending homework
  - Submitted homework
  - Due dates
- Shows study materials:
  - Latest materials
  - Subject-wise materials
- Shows notices relevant to students
- Shows upcoming events
- Shows transport info (if applicable)
- Shows library info (books issued)
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state

**Integration**:
- Uses StudentDashboardController index method
- Queries Student model for profile
- Queries Attendance model for attendance summary
- Queries ExamMark model for results
- Queries FeesAllotment model for fee status
- Queries Homework model for homework
- Queries StudyMaterial model for materials
- Links to detailed views
- Used by student role

**Execute**: Create student dashboard with academics, attendance, fees, and responsive design.

---

### Prompt 29: Create Parent Dashboard View

**Purpose**: Create parent dashboard with children's academics, attendance, and fees.

**Functionality**: Provides parent-specific overview with all children's information.

**How it Works**:
- Creates `resources/views/parent/dashboard.blade.php`
- Extends app layout
- Shows parent profile card with info
- Shows children list with tabs:
  - Child 1 (Name, Class, Section)
  - Child 2 (Name, Class, Section)
  - Child 3 (Name, Class, Section)
- For each child shows:
  - Attendance summary
  - Exam results
  - Fee status
  - Homework
  - Notices
- Shows quick actions:
  - Pay Fees
  - View Attendance
  - View Results
  - Send Message
- Shows school notices
- Shows upcoming events
- Shows payment history
- Shows messages from teachers
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state
- Tab-based navigation for multiple children

**Integration**:
- Uses ParentDashboardController index method
- Queries Student model for children
- Queries Attendance model for attendance
- Queries ExamMark model for results
- Queries FeesAllotment model for fee status
- Links to detailed views
- Used by parent role

**Execute**: Create parent dashboard with children tabs, academics, fees, and responsive design.

---

### Prompt 30: Create Accountant Dashboard View

**Purpose**: Create accountant dashboard with fee collection, income, and expenses.

**Functionality**: Provides accountant-specific overview with financial statistics and pending tasks.

**How it Works**:
- Creates `resources/views/accountant/dashboard.blade.php`
- Extends app layout
- Shows financial statistics cards:
  - Today's Collection (amount)
  - This Month Collection (amount)
  - This Year Collection (amount)
  - Pending Fees (amount)
  - Total Income (amount)
  - Total Expenses (amount)
  - Net Balance (amount)
- Shows charts:
  - Fee collection trend (line chart)
  - Income vs Expenses (bar chart)
  - Fee collection by class (pie chart)
- Shows pending fee collections:
  - Student list with pending fees
  - Due dates
  - Amount due
  - Send reminder button
- Shows recent transactions:
  - Fee payments
  - Income entries
  - Expense entries
- Shows quick actions:
  - Collect Fee
  - Add Income
  - Add Expense
  - Generate Report
- Shows fee collection summary by class
- Shows upcoming fee due dates
- Uses Chart.js for visualizations
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state

**Integration**:
- Uses AccountantDashboardController index method
- Queries FeesTransaction model for collections
- Queries Income model for income
- Queries Expenses model for expenses
- Queries FeesAllotment model for pending fees
- Uses Chart.js for visualizations
- Links to fee collection, reports
- Used by accountant role

**Execute**: Create accountant dashboard with financial stats, charts, transactions, and responsive design.

---

### Prompt 31: Create Librarian Dashboard View

**Purpose**: Create librarian dashboard with books, issues, and returns.

**Functionality**: Provides librarian-specific overview with library statistics and pending tasks.

**How it Works**:
- Creates `resources/views/librarian/dashboard.blade.php`
- Extends app layout
- Shows library statistics cards:
  - Total Books (count)
  - Books Issued (count)
  - Books Available (count)
  - Total Members (count)
  - Overdue Books (count)
  - Today's Issues (count)
  - Today's Returns (count)
- Shows charts:
  - Books by category (pie chart)
  - Issue trend (line chart)
  - Popular books (bar chart)
- Shows pending returns:
  - Student list with overdue books
  - Due dates
  - Fine amount
  - Send reminder button
- Shows recent issues:
  - Book issued to student
  - Issue date
  - Due date
- Shows recent returns:
  - Book returned by student
  - Return date
  - Fine paid
- Shows quick actions:
  - Issue Book
  - Return Book
  - Add Book
  - Add Member
- Shows book availability by category
- Shows member statistics
- Uses Chart.js for visualizations
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state

**Integration**:
- Uses LibrarianDashboardController index method
- Queries LibraryBook model for book stats
- Queries LibraryIssue model for issues/returns
- Queries LibraryMember model for members
- Uses Chart.js for visualizations
- Links to book management, issue/return
- Used by librarian role

**Execute**: Create librarian dashboard with library stats, charts, issues, and responsive design.

---

### Prompt 32: Create Dashboard Statistics Card Component

**Purpose**: Create reusable statistics card component for dashboard metrics.

**Functionality**: Provides consistent card styling for dashboard statistics with trend indicators.

**How it Works**:
- Creates `resources/views/components/dashboard-stat-card.blade.php`
- Accepts title parameter for card title
- Accepts value parameter for card value
- Accepts icon parameter for card icon
- Accepts color parameter (primary, success, warning, danger, info)
- Accepts trend parameter (up, down, neutral)
- Accepts trend_value parameter for trend percentage
- Accepts link parameter for detail page
- Uses Bootstrap 5 card component
- Shows icon with color background
- Shows value with formatting (numbers, currency, percentage)
- Shows trend indicator with arrow and color
- Shows hover effect
- Clickable if link provided
- Supports RTL languages
- Responsive design

**Integration**:
- Used in all dashboard views
- Used in admin, teacher, student, parent, accountant, librarian dashboards
- Consistent statistics card styling
- Shows trends and comparisons

**Execute**: Create dashboard statistics card component with icon, value, trend, and link.

---

### Prompt 33: Create Activity Feed Component

**Purpose**: Create reusable activity feed component for recent activities.

**Functionality**: Shows list of recent activities with timestamps and icons.

**How it Works**:
- Creates `resources/views/components/activity-feed.blade.php`
- Accepts activities parameter (array of activity items)
- Each activity has:
  - Type (student, fee, exam, attendance, etc.)
  - Description
  - Timestamp
  - User who performed action
  - Link to details (optional)
- Shows activity icon based on type
- Shows activity description
- Shows relative time (e.g., "2 hours ago")
- Shows user avatar and name
- Shows hover effect
- Clickable if link provided
- Supports RTL languages
- Responsive design
- Shows "View All" link if provided
- Shows empty state if no activities

**Integration**:
- Used in all dashboard views
- Shows recent activities across modules
- Links to detailed views
- Consistent activity feed styling

**Execute**: Create activity feed component with icons, timestamps, and links.

---

### Prompt 34: Create Quick Actions Component

**Purpose**: Create reusable quick actions component for common tasks.

**Functionality**: Shows grid of quick action buttons for frequently used features.

**How it Works**:
- Creates `resources/views/components/quick-actions.blade.php`
- Accepts actions parameter (array of action items)
- Each action has:
  - Title
  - Icon
  - Color
  - URL or modal trigger
- Shows grid of action buttons
- Each button shows icon and title
- Shows hover effect
- Opens modal or navigates to URL
- Supports RTL languages
- Responsive grid (adjusts columns based on screen size)
- Shows loading state if action requires data loading

**Integration**:
- Used in all dashboard views
- Provides quick access to common tasks
- Opens modals for quick actions
- Navigates to full forms for complex actions

**Execute**: Create quick actions component with icon buttons and responsive grid.

---

### Prompt 35: Create Upcoming Events Component

**Purpose**: Create reusable upcoming events component for calendar events.

**Functionality**: Shows list of upcoming events with dates and details.

**How it Works**:
- Creates `resources/views/components/upcoming-events.blade.php`
- Accepts events parameter (array of event items)
- Each event has:
  - Title
  - Date
  - Time
  - Type (exam, holiday, meeting, etc.)
  - Description
  - Link to details
- Shows event icon based on type
- Shows event date and time
- Shows event title
- Shows event description
- Shows "View All" link if provided
- Shows hover effect
- Clickable if link provided
- Supports RTL languages
- Responsive design
- Shows empty state if no events

**Integration**:
- Used in all dashboard views
- Shows upcoming exams, holidays, meetings
- Links to detailed views
- Consistent events styling

**Execute**: Create upcoming events component with dates, types, and links.

---

## 🎨 Phase 4: Student Management Views (15 Prompts)

### Prompt 36: Create Student List View

**Purpose**: Create student listing page with search, filter, and pagination.

**Functionality**: Provides comprehensive student list with advanced filtering and search capabilities.

**How it Works**:
- Creates `resources/views/admin/students/index.blade.php`
- Extends app layout
- Shows page header with title and "Add Student" button
- Shows search filter component with:
  - Search by name, admission number, roll number, father name
  - Filter by class
  - Filter by section
  - Filter by academic session
  - Filter by category
  - Filter by gender
  - Filter by status (active/inactive)
- Shows table component with columns:
  - Photo (avatar)
  - Admission Number
  - Roll Number
  - Name
  - Class
  - Section
  - Father's Name
  - Phone
  - Status (active/inactive)
  - Actions (view, edit, delete, promote)
- Shows bulk actions:
  - Delete selected
  - Promote selected
  - Export selected
- Shows pagination component
- Shows records per page selector
- Shows "Export All" button
- Shows "Import Students" button
- Uses Bootstrap 5 grid layout
- Responsive design (table scrolls on mobile)
- Supports RTL languages
- Shows loading state
- Shows empty state if no students

**Integration**:
- Uses StudentController index method
- Queries Student model with filters
- Uses table component for display
- Links to student details, edit, delete, promote
- Links to add student form
- Links to export/import functionality
- Used by admin role

**Execute**: Create student list view with search, filters, table, and responsive design.

---

### Prompt 37: Create Student Create View

**Purpose**: Create student admission form with all required fields and validation.

**Functionality**: Provides comprehensive student admission form with multi-step wizard.

**How it Works**:
- Creates `resources/views/admin/students/create.blade.php`
- Extends app layout
- Shows page header with title and "Back to List" button
- Shows multi-step form wizard with tabs:
  - **Step 1: Personal Information**
    - First Name (required)
    - Last Name (required)
    - Date of Birth (required)
    - Gender (required)
    - Blood Group
    - Religion
    - Caste
    - Nationality
    - Mother Tongue
    - Photo upload
  - **Step 2: Academic Information**
    - Academic Session (required)
    - Admission Number (auto-generated)
    - Roll Number
    - Class (required)
    - Section (required)
    - Date of Admission (required)
    - Admission Type
    - Previous School Name
    - Transfer Certificate Number
    - RTE Status
  - **Step 3: Family Information**
    - Father's Name (required)
    - Father's Phone (required)
    - Father's Occupation
    - Father's Email
    - Mother's Name (required)
    - Mother's Phone (required)
    - Mother's Occupation
    - Mother's Email
    - Guardian Name
    - Guardian Phone
    - Guardian Relation
  - **Step 4: Address Information**
    - Address (required)
    - City (required)
    - State (required)
    - Country
    - Postal Code (required)
  - **Step 5: Emergency Contact**
    - Emergency Contact Name (required)
    - Emergency Contact Phone (required)
    - Emergency Contact Relation (required)
  - **Step 6: Documents**
    - Birth Certificate (upload)
    - Transfer Certificate (upload)
    - Photo (upload)
    - Other Documents (multiple upload)
  - **Step 7: Transport & Hostel**
    - Transport Required (yes/no)
    - If yes: Route, Stop, Vehicle
    - Hostel Required (yes/no)
    - If yes: Hostel, Room Type, Room Number
  - **Step 8: Review & Submit**
    - Review all entered information
    - Submit button with loading state
- Shows validation errors for each step
- Shows progress indicator
- Shows "Previous" and "Next" buttons for navigation
- Shows "Save Draft" button
- Shows "Cancel" button
- Uses Bootstrap 5 form styling
- Responsive design
- Supports RTL languages
- Shows loading spinner during submission
- Shows success message on successful admission
- Redirects to student list on success

**Integration**:
- Uses StudentController store method
- Validates all form fields
- Creates user account in users table
- Creates student record in students table
- Uploads documents to storage
- Creates transport/hostel assignments if required
- Links to student list
- Used by admin role

**Execute**: Create student create view with multi-step form wizard, validation, and responsive design.

---

### Prompt 38: Create Student Edit View

**Purpose**: Create student edit form with pre-filled data and validation.

**Functionality**: Provides student edit form with all fields pre-populated.

**How it Works**:
- Creates `resources/views/admin/students/edit.blade.php`
- Extends app layout
- Shows page header with title, student name, and "Back to List" button
- Shows student profile card with photo and basic info
- Shows multi-step form wizard (same as create view)
- All fields pre-filled with existing student data
- Shows validation errors
- Shows progress indicator
- Shows "Update" button with loading state
- Shows "Cancel" button
- Shows "Delete" button (opens confirmation modal)
- Uses Bootstrap 5 form styling
- Responsive design
- Supports RTL languages
- Shows loading spinner during submission
- Shows success message on successful update
- Redirects to student details on success

**Integration**:
- Uses StudentController update method
- Validates all form fields
- Updates student record in students table
- Updates user account in users table
- Uploads new documents if provided
- Updates transport/hostel assignments
- Links to student details
- Used by admin role

**Execute**: Create student edit view with pre-filled data, validation, and responsive design.

---

### Prompt 39: Create Student Details View

**Purpose**: Create student profile page with all information and related data.

**Functionality**: Shows comprehensive student profile with academics, attendance, fees, etc.

**How it Works**:
- Creates `resources/views/admin/students/show.blade.php`
- Extends app layout
- Shows page header with student name and actions:
  - Edit button
  - Delete button
  - Promote button
  - Print Profile button
  - Send Message button
- Shows student profile card with:
  - Photo
  - Name
  - Admission Number
  - Roll Number
  - Class
  - Section
  - Academic Session
  - Status
- Shows tabs with:
  - **Personal Information**
    - Personal details
    - Family details
    - Address
    - Emergency contact
  - **Academic Information**
    - Class and section
    - Admission details
    - Previous school
    - Siblings list
  - **Documents**
    - List of uploaded documents
    - Download links
    - Upload new document button
  - **Attendance**
    - Attendance summary card
    - Monthly attendance chart
    - Attendance table with date and status
    - Export attendance button
  - **Exam Results**
    - Exam results table
    - Subject-wise performance
    - Overall percentage
    - Grade obtained
    - Print report card button
  - **Fees**
    - Fee status card
    - Fee allotments table
    - Payment history table
    - Pay fee button
    - Print receipt button
  - **Transport**
    - Transport details
    - Route and stop info
    - Vehicle info
    - Driver contact
  - **Hostel**
    - Hostel details
    - Room info
    - Warden contact
  - **Library**
    - Library membership
    - Books issued
    - Books returned
    - Fine paid
  - **Homework**
    - Homework list
    - Status (pending/submitted)
    - Due dates
  - **Study Materials**
    - Materials list
    - Subject-wise materials
    - Download links
- Shows activity feed for this student
- Shows quick actions:
  - Mark Attendance
  - Enter Marks
  - Collect Fee
  - Send Message
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state
- Tab-based navigation

**Integration**:
- Uses StudentController show method
- Queries Student model for profile
- Queries Attendance model for attendance
- Queries ExamMark model for results
- Queries FeesAllotment model for fees
- Queries TransportStudent model for transport
- Queries HostelAssignment model for hostel
- Queries LibraryIssue model for library
- Queries Homework model for homework
- Queries StudyMaterial model for materials
- Links to edit, delete, promote
- Links to mark attendance, enter marks, collect fee
- Used by admin, teacher, parent roles

**Execute**: Create student details view with tabs, profile, academics, and responsive design.

---

### Prompt 40: Create Student Promote View

**Purpose**: Create student promotion form with class and session selection.

**Functionality**: Provides bulk promotion form for promoting students to next class.

**How it Works**:
- Creates `resources/views/admin/students/promote.blade.php`
- Extends app layout
- Shows page header with title and "Back to List" button
- Shows promotion form with:
  - From Academic Session (select)
  - To Academic Session (select)
  - From Class (select)
  - From Section (select)
  - To Class (select)
  - To Section (select)
  - Promotion Date (date picker)
- Shows student list with:
  - Checkbox for selection
  - Photo
  - Admission Number
  - Name
  - Current Class
  - Current Section
  - Result dropdown (Promoted/Detained/Left)
  - Remarks textarea
- Shows "Select All" checkbox
- Shows bulk action buttons:
  - Promote All
  - Detain All
  - Leave All
- Shows validation errors
- Shows "Promote Selected" button with loading state
- Shows "Cancel" button
- Uses Bootstrap 5 form styling
- Responsive design
- Supports RTL languages
- Shows loading spinner during promotion
- Shows success message on successful promotion
- Redirects to student list on success

**Integration**:
- Uses StudentController promote method
- Validates promotion data
- Creates promotion records in student_promotions table
- Updates student class and section in students table
- Updates student academic session in students table
- Links to student list
- Used by admin role

**Execute**: Create student promote view with selection, results, and responsive design.

---

### Prompt 41: Create Student Import View

**Purpose**: Create student import form with Excel/CSV upload and template download.

**Functionality**: Provides bulk student import functionality.

**How it Works**:
- Creates `resources/views/admin/students/import.blade.php`
- Extends app layout
- Shows page header with title and "Back to List" button
- Shows import instructions:
  - Download template link
  - Fill template with student data
  - Upload filled file
  - Review and import
- Shows template download section with:
  - Download Excel template button
  - Download CSV template button
  - Template format instructions
- Shows file upload section with:
  - File upload component (drag-and-drop)
  - Accepted file types (.xlsx, .xls, .csv)
  - Maximum file size (10MB)
  - Upload button with loading state
- Shows preview section (after upload):
  - Table with imported data
  - Validation errors highlighted
  - Editable fields
  - Remove row button
- Shows import options:
  - Skip existing students checkbox
  - Create user accounts checkbox
  - Send welcome email checkbox
- Shows validation errors
- Shows "Import" button with loading state
- Shows "Cancel" button
- Uses Bootstrap 5 form styling
- Responsive design
- Supports RTL languages
- Shows loading spinner during import
- Shows success message with count of imported students
- Shows error message with failed records

**Integration**:
- Uses StudentController import method
- Validates uploaded file
- Parses Excel/CSV file
- Validates student data
- Creates user accounts in users table
- Creates student records in students table
- Uploads documents if provided
- Links to student list
- Used by admin role

**Execute**: Create student import view with template download, file upload, and preview.

---

### Prompt 42: Create Student Export View

**Purpose**: Create student export form with filters and format selection.

**Functionality**: Provides student export functionality with filters and format options.

**How it Works**:
- Creates `resources/views/admin/students/export.blade.php`
- Extends app layout
- Shows page header with title and "Back to List" button
- Shows export filters:
  - Filter by class
  - Filter by section
  - Filter by academic session
  - Filter by category
  - Filter by gender
  - Filter by status
- Shows export options:
  - Export format (Excel, PDF, CSV)
  - Include photo checkbox
  - Include documents checkbox
  - Include attendance checkbox
  - Include results checkbox
  - Include fees checkbox
- Shows field selection:
  - Select which fields to export
  - Reorder fields with drag-and-drop
- Shows "Export" button with loading state
- Shows "Cancel" button
- Uses Bootstrap 5 form styling
- Responsive design
- Supports RTL languages
- Shows loading spinner during export
- Downloads file on successful export

**Integration**:
- Uses StudentController export method
- Queries Student model with filters
- Generates Excel/PDF/CSV file
- Downloads file to user
- Links to student list
- Used by admin role

**Execute**: Create student export view with filters, format options, and field selection.

---

### Prompt 43: Create Student Siblings View

**Purpose**: Create student siblings management view with add/remove functionality.

**Functionality**: Shows and manages student siblings.

**How it Works**:
- Creates `resources/views/admin/students/siblings.blade.php`
- Extends app layout
- Shows page header with student name and "Back to Details" button
- Shows siblings list with:
  - Sibling photo
  - Sibling name
  - Class and section
  - Relation (brother/sister)
  - Actions (view, remove)
- Shows "Add Sibling" button
- Shows modal for adding sibling:
  - Search student by name/admission number
  - Select student from search results
  - Select relation (brother/sister)
  - Add button
- Shows validation errors
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state
- Shows empty state if no siblings

**Integration**:
- Uses StudentSiblingController store method
- Creates sibling record in student_siblings table
- Links to student details
- Used by admin role

**Execute**: Create student siblings view with list, add modal, and responsive design.

---

### Prompt 44: Create Student Documents View

**Purpose**: Create student documents management view with upload/download.

**Functionality**: Shows and manages student documents.

**How it Works**:
- Creates `resources/views/admin/students/documents.blade.php`
- Extends app layout
- Shows page header with student name and "Back to Details" button
- Shows documents list with:
  - Document type
  - Document name
  - File size
  - Upload date
  - Actions (download, delete)
- Shows "Upload Document" button
- Shows modal for uploading document:
  - Document type dropdown
  - File upload component
  - Upload button with loading state
- Shows validation errors
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state
- Shows empty state if no documents

**Integration**:
- Uses StudentDocumentController store method
- Uploads file to storage
- Creates document record in student_documents table
- Links to student details
- Used by admin role

**Execute**: Create student documents view with list, upload modal, and responsive design.

---

### Prompt 45: Create Student Attendance View

**Purpose**: Create student attendance view with calendar and statistics.

**Functionality**: Shows student attendance with calendar visualization and statistics.

**How it Works**:
- Creates `resources/views/admin/students/attendance.blade.php`
- Extends app layout
- Shows page header with student name and "Back to Details" button
- Shows attendance summary cards:
  - Overall attendance percentage
  - Total present days
  - Total absent days
  - Total late days
- Shows attendance calendar:
  - Monthly calendar view
  - Color-coded attendance (present=green, absent=red, late=yellow)
  - Month selector
  - Year selector
  - Legend
- Shows attendance table:
  - Date
  - Day
  - Attendance type
  - Remarks
  - Marked by
  - Actions (edit)
- Shows attendance chart:
  - Monthly attendance trend line chart
  - Attendance by type pie chart
- Shows "Mark Attendance" button
- Shows "Export Attendance" button
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state
- Uses Chart.js for visualizations

**Integration**:
- Uses AttendanceController index method
- Queries Attendance model for student
- Shows attendance calendar
- Shows attendance statistics
- Links to mark attendance
- Links to export attendance
- Used by admin, teacher, parent roles

**Execute**: Create student attendance view with calendar, statistics, charts, and responsive design.

---

### Prompt 46: Create Student Results View

**Purpose**: Create student exam results view with subject-wise performance.

**Functionality**: Shows student exam results with grades and performance analysis.

**How it Works**:
- Creates `resources/views/admin/students/results.blade.php`
- Extends app layout
- Shows page header with student name and "Back to Details" button
- Shows results summary cards:
  - Overall percentage
  - Overall grade
  - Total exams
  - Average marks
- Shows exam filter:
  - Filter by academic session
  - Filter by exam type
  - Filter by exam
- Shows results table:
  - Exam
  - Subject
  - Full Marks
  - Obtained Marks
  - Percentage
  - Grade
  - Remarks
- Shows subject-wise performance chart:
  - Bar chart showing marks by subject
  - Line chart showing trend across exams
- Shows "Print Report Card" button
- Shows "Export Results" button
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state
- Uses Chart.js for visualizations

**Integration**:
- Uses ExamMarkController index method
- Queries ExamMark model for student
- Shows exam results
- Calculates grades
- Links to print report card
- Links to export results
- Used by admin, teacher, parent, student roles

**Execute**: Create student results view with table, charts, and responsive design.

---

### Prompt 47: Create Student Fees View

**Purpose**: Create student fees view with fee status and payment history.

**Functionality**: Shows student fee allotments, payments, and pending fees.

**How it Works**:
- Creates `resources/views/admin/students/fees.blade.php`
- Extends app layout
- Shows page header with student name and "Back to Details" button
- Shows fee summary cards:
  - Total fees
  - Paid amount
  - Pending amount
  - Overdue amount
- Shows fee allotments table:
  - Fee type
  - Fee group
  - Amount
  - Discount
  - Net amount
  - Due date
  - Status (paid/pending/overdue)
  - Actions (pay, view details)
- Shows payment history table:
  - Transaction ID
  - Fee type
  - Amount
  - Payment date
  - Payment method
  - Receipt
- Shows "Collect Fee" button
- Shows "Send Reminder" button
- Shows "Print Receipt" button
- Shows "Export Fees" button
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state

**Integration**:
- Uses FeesAllotmentController index method
- Queries FeesAllotment model for student
- Queries FeesTransaction model for payments
- Shows fee status
- Links to collect fee
- Links to send reminder
- Links to print receipt
- Used by admin, accountant, parent roles

**Execute**: Create student fees view with allotments, payments, and responsive design.

---

### Prompt 48: Create Student Transport View

**Purpose**: Create student transport view with route and vehicle details.

**Functionality**: Shows student transport assignment and details.

**How it Works**:
- Creates `resources/views/admin/students/transport.blade.php`
- Extends app layout
- Shows page header with student name and "Back to Details" button
- Shows transport details card:
  - Route name
  - Stop name
  - Stop time
  - Vehicle number
  - Vehicle type
  - Driver name
  - Driver phone
  - Transport fees
- Shows "Edit Transport" button
- Shows "Remove Transport" button
- Shows "Print Transport Card" button
- Shows route map (if available)
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state
- Shows empty state if no transport

**Integration**:
- Uses TransportStudentController index method
- Queries TransportStudent model for student
- Shows transport details
- Links to edit transport
- Links to print transport card
- Used by admin, parent roles

**Execute**: Create student transport view with details, edit, and responsive design.

---

### Prompt 49: Create Student Hostel View

**Purpose**: Create student hostel view with room and warden details.

**Functionality**: Shows student hostel assignment and details.

**How it Works**:
- Creates `resources/views/admin/students/hostel.blade.php`
- Extends app layout
- Shows page header with student name and "Back to Details" button
- Shows hostel details card:
  - Hostel name
  - Room number
  - Room type
  - Floor number
  - Capacity
  - Occupancy
  - Hostel fees
  - Warden name
  - Warden phone
- Shows "Edit Hostel" button
- Shows "Remove Hostel" button
- Shows "Print Hostel Card" button
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state
- Shows empty state if no hostel

**Integration**:
- Uses HostelAssignmentController index method
- Queries HostelAssignment model for student
- Shows hostel details
- Links to edit hostel
- Links to print hostel card
- Used by admin, parent roles

**Execute**: Create student hostel view with details, edit, and responsive design.

---

### Prompt 50: Create Student Library View

**Purpose**: Create student library view with issued books and history.

**Functionality**: Shows student library membership and book issue history.

**How it Works**:
- Creates `resources/views/admin/students/library.blade.php`
- Extends app layout
- Shows page header with student name and "Back to Details" button
- Shows library summary cards:
  - Membership number
  - Books issued
  - Books returned
  - Fine paid
  - Books limit
- Shows currently issued books table:
  - Book title
  - Author
  - Issue date
  - Due date
  - Days remaining
  - Actions (return, renew)
- Shows book history table:
  - Book title
  - Issue date
  - Return date
  - Fine amount
  - Fine paid
- Shows "Issue Book" button
- Shows "Export History" button
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state
- Shows empty state if no books

**Integration**:
- Uses LibraryIssueController index method
- Queries LibraryIssue model for student
- Shows issued books
- Shows book history
- Links to issue book
- Links to return book
- Used by admin, librarian, student roles

**Execute**: Create student library view with issued books, history, and responsive design.

---

## 🎨 Phase 5: Academic Management Views (20 Prompts)

### Prompt 51: Create Academic Sessions List View

**Purpose**: Create academic sessions listing page with CRUD operations.

**Functionality**: Provides academic sessions list with create, edit, delete, and set current functionality.

**How it Works**:
- Creates `resources/views/admin/academic-sessions/index.blade.php`
- Extends app layout
- Shows page header with title and "Add Session" button
- Shows table with columns:
  - Session Name
  - Start Date
  - End Date
  - Is Current (badge)
  - Status (active/inactive)
  - Actions (edit, delete, set current)
- Shows "Set as Current" button for non-current sessions
- Shows validation errors
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state
- Shows empty state if no sessions

**Integration**:
- Uses AcademicSessionController index method
- Queries AcademicSession model
- Links to create, edit, delete, set current
- Used by admin role

**Execute**: Create academic sessions list view with table, actions, and responsive design.

---

### Prompt 52: Create Academic Sessions Create View

**Purpose**: Create academic session creation form.

**Functionality**: Provides form to create new academic session.

**How it Works**:
- Creates `resources/views/admin/academic-sessions/create.blade.php`
- Extends app layout
- Shows page header with title and "Back to List" button
- Shows form with:
  - Session Name (required)
  - Start Date (required)
  - End Date (required)
  - Is Current (checkbox)
  - Status (active/inactive)
- Shows validation errors
- Shows "Save" button with loading state
- Shows "Cancel" button
- Uses Bootstrap 5 form styling
- Responsive design
- Supports RTL languages
- Shows loading spinner during submission
- Shows success message on successful creation
- Redirects to session list on success

**Integration**:
- Uses AcademicSessionController store method
- Validates form fields
- Creates academic session in academic_sessions table
- Links to session list
- Used by admin role

**Execute**: Create academic sessions create view with form, validation, and responsive design.

---

### Prompt 53: Create Classes List View

**Purpose**: Create classes listing page with CRUD operations.

**Functionality**: Provides classes list with create, edit, delete, and sections management.

**How it Works**:
- Creates `resources/views/admin/classes/index.blade.php`
- Extends app layout
- Shows page header with title and "Add Class" button
- Shows table with columns:
  - Class Name
  - Display Name
  - Academic Session
  - Section Count
  - Student Count
  - Status (active/inactive)
  - Actions (edit, delete, manage sections)
- Shows "Manage Sections" button for each class
- Shows validation errors
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state
- Shows empty state if no classes

**Integration**:
- Uses ClassController index method
- Queries Class model with academic session
- Links to create, edit, delete, manage sections
- Used by admin role

**Execute**: Create classes list view with table, actions, and responsive design.

---

### Prompt 54: Create Classes Create View

**Purpose**: Create class creation form.

**Functionality**: Provides form to create new class.

**How it Works**:
- Creates `resources/views/admin/classes/create.blade.php`
- Extends app layout
- Shows page header with title and "Back to List" button
- Shows form with:
  - Academic Session (required, select)
  - Class Name (required)
  - Display Name (required)
  - Section Count (required, number)
  - Order Index (number)
  - Status (active/inactive)
- Shows validation errors
- Shows "Save" button with loading state
- Shows "Cancel" button
- Uses Bootstrap 5 form styling
- Responsive design
- Supports RTL languages
- Shows loading spinner during submission
- Shows success message on successful creation
- Redirects to class list on success

**Integration**:
- Uses ClassController store method
- Validates form fields
- Creates class in classes table
- Links to class list
- Used by admin role

**Execute**: Create classes create view with form, validation, and responsive design.

---

### Prompt 55: Create Sections List View

**Purpose**: Create sections listing page with CRUD operations.

**Functionality**: Provides sections list with create, edit, delete, and student management.

**How it Works**:
- Creates `resources/views/admin/sections/index.blade.php`
- Extends app layout
- Shows page header with title and "Add Section" button
- Shows filter by class
- Shows table with columns:
  - Class
  - Section Name
  - Display Name
  - Class Teacher
  - Capacity
  - Student Count
  - Room Number
  - Status (active/inactive)
  - Actions (edit, delete, view students)
- Shows "View Students" button for each section
- Shows validation errors
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state
- Shows empty state if no sections

**Integration**:
- Uses SectionController index method
- Queries Section model with class filter
- Links to create, edit, delete, view students
- Used by admin role

**Execute**: Create sections list view with table, actions, and responsive design.

---

### Prompt 56: Create Sections Create View

**Purpose**: Create section creation form.

**Functionality**: Provides form to create new section.

**How it Works**:
- Creates `resources/views/admin/sections/create.blade.php`
- Extends app layout
- Shows page header with title and "Back to List" button
- Shows form with:
  - Class (required, select)
  - Section Name (required)
  - Display Name (required)
  - Capacity (number)
  - Room Number
  - Class Teacher (select from teachers)
  - Status (active/inactive)
- Shows validation errors
- Shows "Save" button with loading state
- Shows "Cancel" button
- Uses Bootstrap 5 form styling
- Responsive design
- Supports RTL languages
- Shows loading spinner during submission
- Shows success message on successful creation
- Redirects to section list on success

**Integration**:
- Uses SectionController store method
- Validates form fields
- Creates section in sections table
- Links to section list
- Used by admin role

**Execute**: Create sections create view with form, validation, and responsive design.

---

### Prompt 57: Create Subjects List View

**Purpose**: Create subjects listing page with CRUD operations.

**Functionality**: Provides subjects list with create, edit, delete functionality.

**How it Works**:
- Creates `resources/views/admin/subjects/index.blade.php`
- Extends app layout
- Shows page header with title and "Add Subject" button
- Shows table with columns:
  - Subject Name
  - Subject Code
  - Type (theory/practical)
  - Description
  - Status (active/inactive)
  - Actions (edit, delete)
- Shows validation errors
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state
- Shows empty state if no subjects

**Integration**:
- Uses SubjectController index method
- Queries Subject model
- Links to create, edit, delete
- Used by admin role

**Execute**: Create subjects list view with table, actions, and responsive design.

---

### Prompt 58: Create Subjects Create View

**Purpose**: Create subject creation form.

**Functionality**: Provides form to create new subject.

**How it Works**:
- Creates `resources/views/admin/subjects/create.blade.php`
- Extends app layout
- Shows page header with title and "Back to List" button
- Shows form with:
  - Subject Name (required)
  - Subject Code (required, unique)
  - Type (theory/practical)
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
- Redirects to subject list on success

**Integration**:
- Uses SubjectController store method
- Validates form fields
- Creates subject in subjects table
- Links to subject list
- Used by admin role

**Execute**: Create subjects create view with form, validation, and responsive design.

---

### Prompt 59: Create Class Subjects Assign View

**Purpose**: Create view to assign subjects to classes and sections with teachers.

**Functionality**: Provides interface to assign subjects to classes/sections with teacher assignments.

**How it Works**:
- Creates `resources/views/admin/class-subjects/assign.blade.php`
- Extends app layout
- Shows page header with title and "Back to List" button
- Shows filter form:
  - Academic Session (select)
  - Class (select)
  - Section (select, optional)
- Shows subjects list with:
  - Subject name
  - Teacher assignment dropdown
  - Is Active checkbox
  - Actions (remove)
- Shows "Add Subject" button
- Shows modal for adding subject:
  - Subject dropdown
  - Teacher dropdown
  - Add button
- Shows validation errors
- Shows "Save Assignments" button with loading state
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state
- Shows empty state if no subjects

**Integration**:
- Uses ClassSubjectController store method
- Creates class_subject records in class_subjects table
- Links to class details
- Used by admin role

**Execute**: Create class subjects assign view with teacher assignments and responsive design.

---

### Prompt 60: Create Class Timetable View

**Purpose**: Create class timetable view with weekly schedule grid.

**Functionality**: Shows weekly class timetable with periods and subjects.

**How it Works**:
- Creates `resources/views/admin/class-timetable/show.blade.php`
- Extends app layout
- Shows page header with class/section name and actions:
  - Edit Timetable button
  - Print Timetable button
  - Export Timetable button
- Shows filter form:
  - Academic Session (select)
  - Class (select)
  - Section (select)
- Shows timetable grid:
  - Rows: Periods (1, 2, 3, etc.)
  - Columns: Days (Monday to Saturday)
  - Cells: Subject, Teacher, Room, Time
  - Color-coded by subject
- Shows legend for subject colors
- Shows period timings
- Shows "Add Period" button
- Shows "Clear Timetable" button
- Uses Bootstrap 5 grid layout
- Responsive design (scrollable on mobile)
- Supports RTL languages
- Shows loading state
- Shows empty state if no timetable

**Integration**:
- Uses ClassTimetableController index method
- Queries ClassTimetable model with class/section
- Shows timetable grid
- Links to edit timetable
- Links to print/export timetable
- Used by admin, teacher, student roles

**Execute**: Create class timetable view with grid, colors, and responsive design.

---

### Prompt 61: Create Class Timetable Edit View

**Purpose**: Create timetable editing view with drag-and-drop functionality.

**Functionality**: Provides interface to create/edit class timetable with drag-and-drop.

**How it Works**:
- Creates `resources/views/admin/class-timetable/edit.blade.php`
- Extends app layout
- Shows page header with title and "Back to Timetable" button
- Shows filter form:
  - Academic Session (select)
  - Class (select)
  - Section (select)
- Shows period settings:
  - Number of periods (number input)
  - Period duration (number input)
  - Break time (time input)
  - School start time (time input)
- Shows timetable grid:
  - Rows: Periods (editable)
  - Columns: Days (Monday to Saturday)
  - Cells: Subject dropdown, Teacher dropdown, Room input, Time inputs
  - Drag-and-drop support for subjects
- Shows "Save Timetable" button with loading state
- Shows "Clear All" button
- Shows "Auto-Generate" button (generates from class subjects)
- Shows validation errors
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state

**Integration**:
- Uses ClassTimetableController update method
- Creates/updates class_timetable records
- Links to timetable view
- Used by admin role

**Execute**: Create class timetable edit view with drag-and-drop and responsive design.

---

### Prompt 62: Create Class Students View

**Purpose**: Create view to show students in a class/section with actions.

**Functionality**: Shows list of students in a class/section with management options.

**How it Works**:
- Creates `resources/views/admin/classes/students.blade.php`
- Extends app layout
- Shows page header with class/section name and actions:
  - Add Student button
  - Promote All button
  - Export Students button
- Shows search filter component
- Shows table with columns:
  - Photo (avatar)
  - Roll Number
  - Name
  - Father's Name
  - Phone
  - Status
  - Actions (view, edit, delete, promote)
- Shows bulk actions:
  - Delete selected
  - Promote selected
- Shows pagination component
- Shows records per page selector
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state
- Shows empty state if no students

**Integration**:
- Uses ClassController students method
- Queries Student model with class/section filter
- Links to student details, edit, delete, promote
- Links to add student
- Used by admin, teacher roles

**Execute**: Create class students view with table, actions, and responsive design.

---

### Prompt 63: Create Class Subjects View

**Purpose**: Create view to show subjects assigned to a class/section.

**Functionality**: Shows list of subjects assigned to class/section with teacher assignments.

**How it Works**:
- Creates `resources/views/admin/classes/subjects.blade.php`
- Extends app layout
- Shows page header with class/section name and actions:
  - Assign Subject button
  - Print Subject List button
- Shows table with columns:
  - Subject Name
  - Subject Code
  - Type (theory/practical)
  - Teacher Assigned
  - Status (active/inactive)
  - Actions (edit, remove)
- Shows validation errors
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state
- Shows empty state if no subjects

**Integration**:
- Uses ClassController subjects method
- Queries ClassSubject model with class/section
- Links to assign subject
- Links to edit subject assignment
- Used by admin, teacher roles

**Execute**: Create class subjects view with table, actions, and responsive design.

---

### Prompt 64: Create Class Statistics View

**Purpose**: Create class statistics view with charts and analytics.

**Functionality**: Shows comprehensive statistics for a class/section.

**How it Works**:
- Creates `resources/views/admin/classes/statistics.blade.php`
- Extends app layout
- Shows page header with class/section name and "Back to Class" button
- Shows statistics cards:
  - Total Students
  - Male Students
  - Female Students
  - Average Attendance
  - Average Marks
  - Pass Percentage
- Shows charts:
  - Student distribution by gender (pie chart)
  - Student distribution by category (pie chart)
  - Attendance trend (line chart)
  - Exam performance trend (line chart)
  - Subject-wise performance (bar chart)
- Shows top performers list:
  - Top 10 students by marks
  - Top 10 students by attendance
- Shows subject-wise statistics:
  - Average marks per subject
  - Pass percentage per subject
- Shows "Export Statistics" button
- Uses Chart.js for visualizations
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state

**Integration**:
- Uses ClassController statistics method
- Queries Student, Attendance, ExamMark models
- Calculates statistics
- Shows charts
- Links to export statistics
- Used by admin, teacher roles

**Execute**: Create class statistics view with charts, analytics, and responsive design.

---

### Prompt 65: Create Class Timetable Print View

**Purpose**: Create printable class timetable view.

**Functionality**: Provides printer-friendly version of class timetable.

**How it Works**:
- Creates `resources/views/admin/class-timetable/print.blade.php`
- Shows school header with logo and name
- Shows class/section name
- Shows academic session
- Shows timetable grid (same as show view)
- Shows legend
- Shows period timings
- Shows school contact info
- Print-optimized layout (no navigation, minimal styling)
- Uses Bootstrap 5 grid layout
- Supports RTL languages

**Integration**:
- Uses ClassTimetableController print method
- Queries ClassTimetable model
- Shows printable timetable
- Used by admin, teacher, student, parent roles

**Execute**: Create class timetable print view with optimized layout.

---

### Prompt 66: Create Class Timetable Export View

**Purpose**: Create class timetable export functionality.

**Functionality**: Provides timetable export in PDF/Excel format.

**How it Works**:
- Creates `resources/views/admin/class-timetable/export.blade.php`
- Extends app layout
- Shows page header with title and "Back to Timetable" button
- Shows export options:
  - Export format (PDF, Excel)
  - Include legend checkbox
  - Include period timings checkbox
  - Include school header checkbox
- Shows "Export" button with loading state
- Shows "Cancel" button
- Uses Bootstrap 5 form styling
- Responsive design
- Supports RTL languages
- Shows loading spinner during export
- Downloads file on successful export

**Integration**:
- Uses ClassTimetableController export method
- Generates PDF/Excel file
- Downloads file to user
- Links to timetable view
- Used by admin, teacher, student, parent roles

**Execute**: Create class timetable export view with format options.

---

### Prompt 67: Create Section Students View

**Purpose**: Create view to show students in a section with actions.

**Functionality**: Shows list of students in a section with management options.

**How it Works**:
- Creates `resources/views/admin/sections/students.blade.php`
- Extends app layout
- Shows page header with section name and actions:
  - Add Student button
  - Promote All button
  - Export Students button
- Shows search filter component
- Shows table with columns:
  - Photo (avatar)
  - Roll Number
  - Name
  - Father's Name
  - Phone
  - Status
  - Actions (view, edit, delete, promote)
- Shows bulk actions:
  - Delete selected
  - Promote selected
- Shows pagination component
- Shows records per page selector
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state
- Shows empty state if no students

**Integration**:
- Uses SectionController students method
- Queries Student model with section filter
- Links to student details, edit, delete, promote
- Links to add student
- Used by admin, teacher roles

**Execute**: Create section students view with table, actions, and responsive design.

---

### Prompt 68: Create Section Subjects View

**Purpose**: Create view to show subjects assigned to a section.

**Functionality**: Shows list of subjects assigned to section with teacher assignments.

**How it Works**:
- Creates `resources/views/admin/sections/subjects.blade.php`
- Extends app layout
- Shows page header with section name and actions:
  - Assign Subject button
  - Print Subject List button
- Shows table with columns:
  - Subject Name
  - Subject Code
  - Type (theory/practical)
  - Teacher Assigned
  - Status (active/inactive)
  - Actions (edit, remove)
- Shows validation errors
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state
- Shows empty state if no subjects

**Integration**:
- Uses SectionController subjects method
- Queries ClassSubject model with section
- Links to assign subject
- Links to edit subject assignment
- Used by admin, teacher roles

**Execute**: Create section subjects view with table, actions, and responsive design.

---

### Prompt 69: Create Section Statistics View

**Purpose**: Create section statistics view with charts and analytics.

**Functionality**: Shows comprehensive statistics for a section.

**How it Works**:
- Creates `resources/views/admin/sections/statistics.blade.php`
- Extends app layout
- Shows page header with section name and "Back to Section" button
- Shows statistics cards:
  - Total Students
  - Male Students
  - Female Students
  - Average Attendance
  - Average Marks
  - Pass Percentage
- Shows charts:
  - Student distribution by gender (pie chart)
  - Student distribution by category (pie chart)
  - Attendance trend (line chart)
  - Exam performance trend (line chart)
  - Subject-wise performance (bar chart)
- Shows top performers list:
  - Top 10 students by marks
  - Top 10 students by attendance
- Shows subject-wise statistics:
  - Average marks per subject
  - Pass percentage per subject
- Shows "Export Statistics" button
- Uses Chart.js for visualizations
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state

**Integration**:
- Uses SectionController statistics method
- Queries Student, Attendance, ExamMark models
- Calculates statistics
- Shows charts
- Links to export statistics
- Used by admin, teacher roles

**Execute**: Create section statistics view with charts, analytics, and responsive design.

---

### Prompt 70: Create Subject Details View

**Purpose**: Create subject details view with classes and teachers.

**Functionality**: Shows subject details with assigned classes and teachers.

**How it Works**:
- Creates `resources/views/admin/subjects/show.blade.php`
- Extends app layout
- Shows page header with subject name and actions:
  - Edit Subject button
  - Delete Subject button
- Shows subject details card:
  - Subject Name
  - Subject Code
  - Type (theory/practical)
  - Description
- Shows assigned classes table:
  - Class
  - Section
  - Teacher Assigned
  - Status
  - Actions (view class)
- Shows "Assign to Class" button
- Shows validation errors
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state
- Shows empty state if no classes

**Integration**:
- Uses SubjectController show method
- Queries Subject model
- Queries ClassSubject model for assigned classes
- Links to edit, delete subject
- Links to assign to class
- Used by admin role

**Execute**: Create subject details view with assigned classes and responsive design.

---

## 📊 Summary

**Total Frontend Prompts: 70**

**Phases Covered:**
1. **Layout & Components** (20 prompts)
2. **Authentication Views** (5 prompts)
3. **Dashboard Views** (10 prompts)
4. **Student Management Views** (15 prompts)
5. **Academic Management Views** (20 prompts)

**Features Implemented:**
- Responsive design with Bootstrap 5
- RTL language support
- Interactive components with Alpine.js
- Charts with Chart.js
- Multi-step forms
- Drag-and-drop functionality
- Real-time search and filtering
- Pagination
- File upload with preview
- Modal dialogs
- Loading states
- Empty states
- Validation feedback
- Accessibility features

**Next Phases:**
- Attendance Management Views
- Examination Management Views
- Fees Management Views
- Library Management Views
- Transport Management Views
- Hostel Management Views
- Communication Views
- Accounting Views
- Reports Views
- Settings Views

---

## 🚀 Continue with More Frontend Prompts

This document covers the first 70 detailed frontend prompts. Continue with additional prompts for remaining modules to complete the entire frontend implementation.

**Happy Building with DevIn AI!** 🚀
