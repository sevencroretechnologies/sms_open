# Smart School Management System - Frontend Detailed Prompts Part 3

This document continues with comprehensive, detailed prompts for building complete frontend UI for remaining modules of Smart School Management System using DevIn AI. Each prompt includes:
- **Purpose**: Why this prompt is needed
- **Functionality**: What exactly it does
- **How it Works**: Implementation details
- **Integration**: How it connects with other features

---

## 📋 Continue from Part 2

This document continues from [`DEVIN-AI-FRONTEND-DETAILED-PART2.md`](DEVIN-AI-FRONTEND-DETAILED-PART2.md) which covered:
- Attendance Management Views (10 prompts)
- Examination Management Views (15 prompts)
- Fees Management Views (15 prompts)

**Total in Part 2: 40 prompts**
**Total in Part 1 + Part 2: 110 prompts**

---

## 🎨 Phase 9: Library Management Views (10 Prompts)

### Prompt 111: Create Library Categories List View

**Purpose**: Create library categories listing page with CRUD operations.

**Functionality**: Provides library categories list with create, edit, delete functionality.

**How it Works**:
- Creates `resources/views/admin/library/categories.blade.php`
- Extends app layout
- Shows page header with title and "Add Category" button
- Shows table with columns:
  - Category Name
  - Code
  - Description
  - Status (active/inactive)
  - Actions (edit, delete)
- Shows validation errors
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state
- Shows empty state if no categories

**Integration**:
- Uses LibraryCategoryController index method
- Queries LibraryCategory model
- Links to create, edit, delete
- Used by librarian role

**Execute**: Create library categories list view with table, actions, and responsive design.

---

### Prompt 112: Create Library Categories Create View

**Purpose**: Create library category creation form.

**Functionality**: Provides form to create new library category.

**How it Works**:
- Creates `resources/views/admin/library/categories-create.blade.php`
- Extends app layout
- Shows page header with title and "Back to List" button
- Shows form with:
  - Category Name (required)
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
- Redirects to category list on success

**Integration**:
- Uses LibraryCategoryController store method
- Validates form fields
- Creates category in library_categories table
- Links to category list
- Used by librarian role

**Execute**: Create library categories create view with form, validation, and responsive design.

---

### Prompt 113: Create Library Books List View

**Purpose**: Create library books listing page with search, filter, and CRUD operations.

**Functionality**: Provides comprehensive book listing with advanced filtering.

**How it Works**:
- Creates `resources/views/admin/library/books.blade.php`
- Extends app layout
- Shows page header with title and "Add Book" button
- Shows search filter component with:
  - Search by title, author, ISBN
  - Filter by category
  - Filter by type (theory/practical)
  - Filter by availability (available/issued)
- Shows table with columns:
  - Cover Image
  - ISBN
  - Title
  - Author
  - Category
  - Type
  - Quantity
  - Available
  - Status (active/inactive)
  - Actions (view, edit, delete, issue)
- Shows "Issue Book" button for available books
- Shows bulk actions:
  - Delete selected
  - Export selected
- Shows pagination component
- Shows records per page selector
- Shows "Export All" button
- Shows "Import Books" button
- Uses Bootstrap 5 grid layout
- Responsive design (table scrolls on mobile)
- Supports RTL languages
- Shows loading state
- Shows empty state if no books

**Integration**:
- Uses LibraryBookController index method
- Queries LibraryBook model with filters
- Links to add book, edit, delete, issue
- Links to import/export functionality
- Used by librarian role

**Execute**: Create library books list view with search, filters, table, and responsive design.

---

### Prompt 114: Create Library Books Create View

**Purpose**: Create book creation form with image upload.

**Functionality**: Provides form to add new books to library.

**How it Works**:
- Creates `resources/views/admin/library/books-create.blade.php`
- Extends app layout
- Shows page header with title and "Back to List" button
- Shows form with:
  - Category (required, select)
  - ISBN (required, unique)
  - Title (required)
  - Author
  - Publisher
  - Edition
  - Publish Year (number)
  - Rack Number
  - Quantity (required, number)
  - Price (number)
  - Language (select)
  - Pages (number)
  - Description (textarea)
  - Cover Image (file upload with preview)
  - Status (active/inactive)
- Shows cover image preview
- Shows validation errors
- Shows "Save" button with loading state
- Shows "Save & Add Another" button
- Shows "Cancel" button
- Uses Bootstrap 5 form styling
- Responsive design
- Supports RTL languages
- Shows loading spinner during submission
- Shows success message on successful creation
- Redirects to book list on success

**Integration**:
- Uses LibraryBookController store method
- Validates form fields
- Uploads cover image to storage
- Creates book in library_books table
- Links to book list
- Used by librarian role

**Execute**: Create library books create view with form, image upload, and responsive design.

---

### Prompt 115: Create Library Books Edit View

**Purpose**: Create book edit view with pre-filled data.

**Functionality**: Provides form to edit existing book details.

**How it Works**:
- Creates `resources/views/admin/library/books-edit.blade.php`
- Extends app layout
- Shows page header with title, book title, and "Back to List" button
- Shows book profile card with cover image and details
- Shows form (same as create view)
- All fields pre-filled with existing book data
- Shows validation errors
- Shows "Update" button with loading state
- Shows "Cancel" button
- Shows "Delete" button (opens confirmation modal)
- Uses Bootstrap 5 form styling
- Responsive design
- Supports RTL languages
- Shows loading spinner during submission
- Shows success message on successful update
- Redirects to book details on success

**Integration**:
- Uses LibraryBookController update method
- Validates form fields
- Updates book in library_books table
- Uploads new cover image if provided
- Links to book details
- Used by librarian role

**Execute**: Create library books edit view with pre-filled data, validation, and responsive design.

---

### Prompt 116: Create Library Book Details View

**Purpose**: Create book details view with issue history and availability.

**Functionality**: Shows comprehensive book information with issue history.

**How it Works**:
- Creates `resources/views/admin/library/books-show.blade.php`
- Extends app layout
- Shows page header with book title and actions:
  - Edit button
  - Delete button
  - Issue Book button
  - Print Details button
- Shows book profile card with:
  - Cover image
  - ISBN
  - Title
  - Author
  - Publisher
  - Edition
  - Publish Year
  - Category
  - Type
  - Language
  - Pages
  - Price
  - Rack Number
  - Quantity
  - Available
  - Description
- Shows tabs with:
  - **Issue History**
    - Issue history table with date, member, return date, fine
    - Export history button
  - **Current Issues**
    - Currently issued books table
    - Send reminder button
  - **Statistics**
    - Total issues
    - Total returns
    - Average issue duration
    - Most issued book chart
- Shows "Issue Book" button
- Shows "Print Barcode" button
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state
- Tab-based navigation

**Integration**:
- Uses LibraryBookController show method
- Queries LibraryBook model
- Queries LibraryIssue model for history
- Links to edit, delete, issue
- Used by librarian role

**Execute**: Create library book details view with tabs, history, and responsive design.

---

### Prompt 117: Create Library Members List View

**Purpose**: Create library members listing page with CRUD operations.

**Functionality**: Provides library members list with create, edit, delete functionality.

**How it Works**:
- Creates `resources/views/admin/library/members.blade.php`
- Extends app layout
- Shows page header with title and "Add Member" button
- Shows filter by member type
- Shows table with columns:
  - Membership Number
  - Member Type (student/teacher/staff)
  - Name
  - Class/Department
  - Membership Date
  - Expiry Date
  - Books Issued
  - Max Books
  - Status (active/inactive)
  - Actions (view, edit, delete, issue)
- Shows "Issue Book" button for each member
- Shows validation errors
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state
- Shows empty state if no members

**Integration**:
- Uses LibraryMemberController index method
- Queries LibraryMember model
- Links to create, edit, delete, issue
- Used by librarian role

**Execute**: Create library members list view with table, actions, and responsive design.

---

### Prompt 118: Create Library Members Create View

**Purpose**: Create library member creation form.

**Functionality**: Provides form to add new library member.

**How it Works**:
- Creates `resources/views/admin/library/members-create.blade.php`
- Extends app layout
- Shows page header with title and "Back to List" button
- Shows form with:
  - Member Type (required, select: student/teacher/staff)
  - Member (required, select based on type)
  - Membership Number (auto-generated)
  - Membership Date (required, date picker)
  - Expiry Date (date picker)
  - Max Books (number)
  - Status (active/inactive)
- Shows member details preview
- Shows validation errors
- Shows "Save" button with loading state
- Shows "Cancel" button
- Uses Bootstrap 5 form styling
- Responsive design
- Supports RTL languages
- Shows loading spinner during submission
- Shows success message on successful creation
- Redirects to member list on success

**Integration**:
- Uses LibraryMemberController store method
- Validates form fields
- Creates member in library_members table
- Links to member list
- Used by librarian role

**Execute**: Create library members create view with form, validation, and responsive design.

---

### Prompt 119: Create Library Issue Book View

**Purpose**: Create book issue view for issuing books to members.

**Functionality**: Provides interface to issue books to library members.

**How it Works**:
- Creates `resources/views/admin/library/issue.blade.php`
- Extends app layout
- Shows page header with title and "Back to List" button
- Shows issue form with:
  - Book (required, select with search)
  - Member (required, select with search)
  - Issue Date (required, date picker, defaults to today)
  - Due Date (required, date picker, auto-calculated based on member type)
  - Remarks
- Shows book details card:
  - Cover image
  - Title
  - Author
  - ISBN
  - Available quantity
- Shows member details card:
  - Name
  - Membership Number
  - Books Issued
  - Max Books
  - Can Issue (yes/no)
- Shows validation errors
- Shows "Issue Book" button with loading state
- Shows "Cancel" button
- Uses Bootstrap 5 form styling
- Responsive design
- Supports RTL languages
- Shows loading spinner during submission
- Shows success message on successful issue
- Redirects to issue list on success

**Integration**:
- Uses LibraryIssueController store method
- Validates form fields
- Creates issue in library_issues table
- Updates book available quantity
- Updates member books issued count
- Links to issue list
- Used by librarian role

**Execute**: Create library issue book view with form, details, and responsive design.

---

### Prompt 120: Create Library Return Book View

**Purpose**: Create book return view for processing returns and fines.

**Functionality**: Provides interface to return books and calculate fines.

**How it Works**:
- Creates `resources/views/admin/library/return.blade.php`
- Extends app layout
- Shows page header with title and "Back to List" button
- Shows search form:
  - Search by issue ID, book ISBN, member name
- Shows issue details card:
  - Book details (cover, title, author, ISBN)
  - Member details (name, membership number)
  - Issue Date
  - Due Date
  - Days Overdue (calculated)
  - Fine per Day
  - Total Fine (calculated)
- Shows return form with:
  - Return Date (required, date picker, defaults to today)
  - Fine Amount (number input, auto-calculated)
  - Fine Paid (checkbox)
  - Remarks
- Shows validation errors
- Shows "Return Book" button with loading state
- Shows "Cancel" button
- Uses Bootstrap 5 form styling
- Responsive design
- Supports RTL languages
- Shows loading spinner during submission
- Shows success message on successful return
- Redirects to issue list on success

**Integration**:
- Uses LibraryIssueController return method
- Validates form fields
- Updates library_issues table with return date and fine
- Updates book available quantity
- Updates member books issued count
- Links to issue list
- Used by librarian role

**Execute**: Create library return book view with form, fine calculation, and responsive design.

---

## 🎨 Phase 10: Transport Management Views (10 Prompts)

### Prompt 121: Create Transport Routes List View

**Purpose**: Create transport routes listing page with CRUD operations.

**Functionality**: Provides transport routes list with create, edit, delete functionality.

**How it Works**:
- Creates `resources/views/admin/transport/routes.blade.php`
- Extends app layout
- Shows page header with title and "Add Route" button
- Shows table with columns:
  - Route Name
  - Route Number
  - Description
  - Stops Count
  - Students Count
  - Vehicles Count
  - Status (active/inactive)
  - Actions (view, edit, delete)
- Shows validation errors
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state
- Shows empty state if no routes

**Integration**:
- Uses TransportRouteController index method
- Queries TransportRoute model
- Links to create, edit, delete
- Used by admin role

**Execute**: Create transport routes list view with table, actions, and responsive design.

---

### Prompt 122: Create Transport Routes Create View

**Purpose**: Create transport route creation form.

**Functionality**: Provides form to create new transport route.

**How it Works**:
- Creates `resources/views/admin/transport/routes-create.blade.php`
- Extends app layout
- Shows page header with title and "Back to List" button
- Shows form with:
  - Route Name (required)
  - Route Number (required, unique)
  - Description
  - Status (active/inactive)
- Shows validation errors
- Shows "Save" button with loading state
- Shows "Save & Add Stops" button
- Shows "Cancel" button
- Uses Bootstrap 5 form styling
- Responsive design
- Supports RTL languages
- Shows loading spinner during submission
- Shows success message on successful creation
- Redirects to route list on success

**Integration**:
- Uses TransportRouteController store method
- Validates form fields
- Creates route in transport_routes table
- Links to route list
- Used by admin role

**Execute**: Create transport routes create view with form, validation, and responsive design.

---

### Prompt 123: Create Transport Route Stops View

**Purpose**: Create route stops management view with add/remove functionality.

**Functionality**: Shows and manages stops on a transport route.

**How it Works**:
- Creates `resources/views/admin/transport/stops.blade.php`
- Extends app layout
- Shows page header with route name and "Back to Route" button
- Shows route details card:
  - Route Name
  - Route Number
  - Description
- Shows stops list with:
  - Stop Name
  - Stop Order
  - Stop Time
  - Fare
  - Students Count
  - Actions (edit, remove, move up, move down)
- Shows "Add Stop" button
- Shows modal for adding stop:
  - Stop Name (required)
  - Stop Order (auto-calculated)
  - Stop Time (time picker)
  - Fare (number)
  - Add button
- Shows validation errors
- Shows "Reorder Stops" button (drag-and-drop)
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state
- Shows empty state if no stops

**Integration**:
- Uses TransportRouteStopController store method
- Creates stop in transport_route_stops table
- Links to route details
- Used by admin role

**Execute**: Create transport route stops view with list, add modal, and responsive design.

---

### Prompt 124: Create Transport Vehicles List View

**Purpose**: Create transport vehicles listing page with CRUD operations.

**Functionality**: Provides transport vehicles list with create, edit, delete functionality.

**How it Works**:
- Creates `resources/views/admin/transport/vehicles.blade.php`
- Extends app layout
- Shows page header with title and "Add Vehicle" button
- Shows filter by route
- Shows table with columns:
  - Vehicle Number
  - Vehicle Type
  - Vehicle Model
  - Capacity
  - Driver Name
  - Driver Phone
  - Route
  - Students Count
  - Status (active/inactive)
  - Actions (view, edit, delete)
- Shows validation errors
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state
- Shows empty state if no vehicles

**Integration**:
- Uses TransportVehicleController index method
- Queries TransportVehicle model with route filter
- Links to create, edit, delete
- Used by admin role

**Execute**: Create transport vehicles list view with table, actions, and responsive design.

---

### Prompt 125: Create Transport Vehicles Create View

**Purpose**: Create transport vehicle creation form.

**Functionality**: Provides form to add new transport vehicle.

**How it Works**:
- Creates `resources/views/admin/transport/vehicles-create.blade.php`
- Extends app layout
- Shows page header with title and "Back to List" button
- Shows form with:
  - Vehicle Number (required, unique)
  - Vehicle Type
  - Vehicle Model
  - Capacity (required, number)
  - Driver Name
  - Driver Phone
  - Driver License
  - Route (select)
  - Status (active/inactive)
- Shows validation errors
- Shows "Save" button with loading state
- Shows "Save & Assign Students" button
- Shows "Cancel" button
- Uses Bootstrap 5 form styling
- Responsive design
- Supports RTL languages
- Shows loading spinner during submission
- Shows success message on successful creation
- Redirects to vehicle list on success

**Integration**:
- Uses TransportVehicleController store method
- Validates form fields
- Creates vehicle in transport_vehicles table
- Links to vehicle list
- Used by admin role

**Execute**: Create transport vehicles create view with form, validation, and responsive design.

---

### Prompt 126: Create Transport Students Assign View

**Purpose**: Create transport student assignment view with route and stop selection.

**Functionality**: Provides interface to assign students to transport routes and stops.

**How it Works**:
- Creates `resources/views/admin/transport/assign.blade.php`
- Extends app layout
- Shows page header with title and "Back to List" button
- Shows filter form:
  - Academic Session (select)
  - Class (select)
  - Section (select)
  - Route (select)
- Shows student list with:
  - Photo (avatar)
  - Roll Number
  - Name
  - Father's Name
  - Phone
  - Current Route (if assigned)
  - Current Stop (if assigned)
  - Vehicle (select)
  - Route (select)
  - Stop (select)
  - Transport Fees (number)
  - Checkbox for selection
- Shows assignment summary:
  - Total Students
  - Selected Students
  - Total Fees
- Shows "Select All" checkbox
- Shows "Assign Transport" button with loading state
- Shows "Remove Transport" button
- Shows validation errors
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading spinner during assignment
- Shows success message with count of assigned students
- Redirects to student list on success

**Integration**:
- Uses TransportStudentController store method
- Creates/updates transport_student records
- Links to student list
- Used by admin role

**Execute**: Create transport students assign view with student list, route/stop selection, and responsive design.

---

### Prompt 127: Create Transport Students List View

**Purpose**: Create transport students listing page with search and filter.

**Functionality**: Provides comprehensive transport student listing with advanced filtering.

**How it Works**:
- Creates `resources/views/admin/transport/students.blade.php`
- Extends app layout
- Shows page header with title and "Assign Transport" button
- Shows search filter component with:
  - Search by student name, roll number, admission number
  - Filter by academic session
  - Filter by route
  - Filter by vehicle
  - Filter by stop
- Shows table with columns:
  - Photo (avatar)
  - Roll Number
  - Name
  - Class
  - Section
  - Route
  - Stop
  - Vehicle
  - Vehicle Number
  - Driver Name
  - Driver Phone
  - Transport Fees
  - Actions (view, edit, remove)
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
- Shows empty state if no students

**Integration**:
- Uses TransportStudentController index method
- Queries TransportStudent model with filters
- Links to assign transport, edit, remove
- Links to export/print functionality
- Used by admin role

**Execute**: Create transport students list view with search, filters, table, and responsive design.

---

### Prompt 128: Create Transport Route Details View

**Purpose**: Create transport route details view with stops, vehicles, and students.

**Functionality**: Shows comprehensive route information with all related data.

**How it Works**:
- Creates `resources/views/admin/transport/routes-show.blade.php`
- Extends app layout
- Shows page header with route name and actions:
  - Edit Route button
  - Delete Route button
  - Add Stop button
  - Print Route button
- Shows route details card:
  - Route Name
  - Route Number
  - Description
  - Total Stops
  - Total Students
  - Total Vehicles
- Shows tabs with:
  - **Stops**
    - Stops table with order, name, time, fare, students
    - Add Stop button
  - **Vehicles**
    - Vehicles table with number, type, capacity, driver
    - Add Vehicle button
  - **Students**
    - Students table with name, class, stop, fees
    - Export Students button
- Shows "Export Route" button
- Shows "Print Route" button
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state
- Tab-based navigation

**Integration**:
- Uses TransportRouteController show method
- Queries TransportRoute model
- Queries TransportRouteStop model for stops
- Queries TransportVehicle model for vehicles
- Queries TransportStudent model for students
- Links to edit, add stop, add vehicle
- Used by admin role

**Execute**: Create transport route details view with tabs, stops, vehicles, and responsive design.

---

### Prompt 129: Create Transport Vehicle Details View

**Purpose**: Create transport vehicle details view with driver and students.

**Functionality**: Shows comprehensive vehicle information with driver details and assigned students.

**How it Works**:
- Creates `resources/views/admin/transport/vehicles-show.blade.php`
- Extends app layout
- Shows page header with vehicle number and actions:
  - Edit Vehicle button
  - Delete Vehicle button
  - Print Vehicle Card button
- Shows vehicle details card:
  - Vehicle Number
  - Vehicle Type
  - Vehicle Model
  - Capacity
  - Driver Name
  - Driver Phone
  - Driver License
  - Route
  - Students Assigned
- Shows driver details card:
  - Driver Name
  - Driver Phone
  - Driver License
  - Contact Actions (call, message)
- Shows students table:
  - Photo (avatar)
  - Roll Number
  - Name
  - Class
  - Section
  - Stop
  - Stop Time
  - Transport Fees
- Shows "Send Route to Driver" button (SMS)
- Shows "Export Students" button
- Shows "Print Vehicle Card" button
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state

**Integration**:
- Uses TransportVehicleController show method
- Queries TransportVehicle model
- Queries TransportStudent model for students
- Links to edit, delete
- Used by admin role

**Execute**: Create transport vehicle details view with driver, students, and responsive design.

---

### Prompt 130: Create Transport Report View

**Purpose**: Create transport report view with statistics and charts.

**Functionality**: Shows comprehensive transport reports with analytics.

**How it Works**:
- Creates `resources/views/admin/transport/report.blade.php`
- Extends app layout
- Shows page header with title and "Back to List" button
- Shows filter form:
  - Academic Session (select)
  - Route (select)
  - Vehicle (select)
  - Report Type (daily, monthly, yearly)
- Shows statistics cards:
  - Total Routes
  - Total Vehicles
  - Total Students
  - Total Transport Fees
  - Collected Fees
  - Pending Fees
- Shows charts:
  - Students by route (pie chart)
  - Students by stop (bar chart)
  - Vehicle capacity utilization (bar chart)
  - Fee collection trend (line chart)
- Shows route-wise table:
  - Route Name
  - Stops Count
  - Students Count
  - Vehicles Count
  - Total Fees
  - Collected Fees
- Shows "Export Report" button (PDF/Excel)
- Shows "Print Report" button
- Uses Chart.js for visualizations
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state

**Integration**:
- Uses TransportStudentController report method
- Queries TransportRoute, TransportVehicle, TransportStudent models
- Calculates statistics
- Shows charts
- Links to export/print
- Used by admin role

**Execute**: Create transport report view with statistics, charts, table, and responsive design.

---

## 🎨 Phase 11: Hostel Management Views (10 Prompts)

### Prompt 131: Create Hostels List View

**Purpose**: Create hostels listing page with CRUD operations.

**Functionality**: Provides hostels list with create, edit, delete functionality.

**How it Works**:
- Creates `resources/views/admin/hostels.blade.php`
- Extends app layout
- Shows page header with title and "Add Hostel" button
- Shows table with columns:
  - Hostel Name
  - Code
  - Type (boys/girls/mixed)
  - City
  - State
  - Phone
  - Rooms Count
  - Capacity
  - Occupancy
  - Status (active/inactive)
  - Actions (view, edit, delete)
- Shows validation errors
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state
- Shows empty state if no hostels

**Integration**:
- Uses HostelController index method
- Queries Hostel model
- Links to create, edit, delete
- Used by admin role

**Execute**: Create hostels list view with table, actions, and responsive design.

---

### Prompt 132: Create Hostels Create View

**Purpose**: Create hostel creation form.

**Functionality**: Provides form to create new hostel.

**How it Works**:
- Creates `resources/views/admin/hostels-create.blade.php`
- Extends app layout
- Shows page header with title and "Back to List" button
- Shows form with:
  - Hostel Name (required)
  - Code (required, unique)
  - Type (required, select: boys/girls/mixed)
  - Address (textarea)
  - City
  - State
  - Postal Code
  - Phone
  - Email
  - Warden Name
  - Warden Phone
  - Facilities (textarea)
  - Status (active/inactive)
- Shows validation errors
- Shows "Save" button with loading state
- Shows "Save & Add Rooms" button
- Shows "Cancel" button
- Uses Bootstrap 5 form styling
- Responsive design
- Supports RTL languages
- Shows loading spinner during submission
- Shows success message on successful creation
- Redirects to hostel list on success

**Integration**:
- Uses HostelController store method
- Validates form fields
- Creates hostel in hostels table
- Links to hostel list
- Used by admin role

**Execute**: Create hostels create view with form, validation, and responsive design.

---

### Prompt 133: Create Hostel Room Types List View

**Purpose**: Create hostel room types listing page with CRUD operations.

**Functionality**: Provides room types list with create, edit, delete functionality.

**How it Works**:
- Creates `resources/views/admin/hostels/room-types.blade.php`
- Extends app layout
- Shows page header with hostel name and "Add Room Type" button
- Shows table with columns:
  - Room Type Name
  - Capacity
  - Beds Per Room
  - Fees Per Month
  - Rooms Count
  - Students Count
  - Status (active/inactive)
  - Actions (edit, delete)
- Shows validation errors
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state
- Shows empty state if no room types

**Integration**:
- Uses HostelRoomTypeController index method
- Queries HostelRoomType model
- Links to create, edit, delete
- Used by admin role

**Execute**: Create hostel room types list view with table, actions, and responsive design.

---

### Prompt 134: Create Hostel Room Types Create View

**Purpose**: Create hostel room type creation form.

**Functionality**: Provides form to create new room type.

**How it Works**:
- Creates `resources/views/admin/hostels/room-types-create.blade.php`
- Extends app layout
- Shows page header with title and "Back to List" button
- Shows form with:
  - Hostel (required, select)
  - Room Type Name (required)
  - Capacity (required, number)
  - Beds Per Room (required, number)
  - Fees Per Month (number)
  - Facilities (textarea)
  - Status (active/inactive)
- Shows validation errors
- Shows "Save" button with loading state
- Shows "Save & Add Rooms" button
- Shows "Cancel" button
- Uses Bootstrap 5 form styling
- Responsive design
- Supports RTL languages
- Shows loading spinner during submission
- Shows success message on successful creation
- Redirects to room type list on success

**Integration**:
- Uses HostelRoomTypeController store method
- Validates form fields
- Creates room type in hostel_room_types table
- Links to room type list
- Used by admin role

**Execute**: Create hostel room types create view with form, validation, and responsive design.

---

### Prompt 135: Create Hostel Rooms List View

**Purpose**: Create hostel rooms listing page with CRUD operations.

**Functionality**: Provides rooms list with create, edit, delete functionality.

**How it Works**:
- Creates `resources/views/admin/hostels/rooms.blade.php`
- Extends app layout
- Shows page header with hostel name and "Add Room" button
- Shows filter by room type
- Shows table with columns:
  - Room Number
  - Room Type
  - Floor Number
  - Capacity
  - Occupied
  - Available Beds
  - Status (active/inactive)
  - Actions (view, edit, delete)
- Shows validation errors
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state
- Shows empty state if no rooms

**Integration**:
- Uses HostelRoomController index method
- Queries HostelRoom model with room type filter
- Links to create, edit, delete
- Used by admin role

**Execute**: Create hostel rooms list view with table, actions, and responsive design.

---

### Prompt 136: Create Hostel Rooms Create View

**Purpose**: Create hostel room creation form.

**Functionality**: Provides form to create new room.

**How it Works**:
- Creates `resources/views/admin/hostels/rooms-create.blade.php`
- Extends app layout
- Shows page header with title and "Back to List" button
- Shows form with:
  - Hostel (required, select)
  - Room Type (required, select)
  - Room Number (required)
  - Floor Number (number)
  - Capacity (required, number)
  - Status (active/inactive)
- Shows validation errors
- Shows "Save" button with loading state
- Shows "Save & Add Another" button
- Shows "Cancel" button
- Uses Bootstrap 5 form styling
- Responsive design
- Supports RTL languages
- Shows loading spinner during submission
- Shows success message on successful creation
- Redirects to room list on success

**Integration**:
- Uses HostelRoomController store method
- Validates form fields
- Creates room in hostel_rooms table
- Links to room list
- Used by admin role

**Execute**: Create hostel rooms create view with form, validation, and responsive design.

---

### Prompt 137: Create Hostel Assign View

**Purpose**: Create hostel student assignment view with room selection.

**Functionality**: Provides interface to assign students to hostel rooms.

**How it Works**:
- Creates `resources/views/admin/hostels/assign.blade.php`
- Extends app layout
- Shows page header with title and "Back to List" button
- Shows filter form:
  - Academic Session (select)
  - Class (select)
  - Section (select)
  - Hostel (select)
  - Room Type (select)
- Shows student list with:
  - Photo (avatar)
  - Roll Number
  - Name
  - Father's Name
  - Phone
  - Current Hostel (if assigned)
  - Current Room (if assigned)
  - Hostel (select)
  - Room Type (select)
  - Room (select)
  - Admission Date (date picker)
  - Hostel Fees (number)
  - Checkbox for selection
- Shows assignment summary:
  - Total Students
  - Selected Students
  - Total Fees
- Shows "Select All" checkbox
- Shows "Assign Hostel" button with loading state
- Shows "Remove Hostel" button
- Shows validation errors
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading spinner during assignment
- Shows success message with count of assigned students
- Redirects to student list on success

**Integration**:
- Uses HostelAssignmentController store method
- Creates/updates hostel_assignment records
- Updates room occupancy
- Links to student list
- Used by admin role

**Execute**: Create hostel assign view with student list, room selection, and responsive design.

---

### Prompt 138: Create Hostel Students List View

**Purpose**: Create hostel students listing page with search and filter.

**Functionality**: Provides comprehensive hostel student listing with advanced filtering.

**How it Works**:
- Creates `resources/views/admin/hostels/students.blade.php`
- Extends app layout
- Shows page header with title and "Assign Hostel" button
- Shows search filter component with:
  - Search by student name, roll number, admission number
  - Filter by academic session
  - Filter by hostel
  - Filter by room type
  - Filter by room
- Shows table with columns:
  - Photo (avatar)
  - Roll Number
  - Name
  - Class
  - Section
  - Hostel
  - Room Number
  - Room Type
  - Floor Number
  - Admission Date
  - Hostel Fees
  - Actions (view, edit, remove)
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
- Shows empty state if no students

**Integration**:
- Uses HostelAssignmentController index method
- Queries HostelAssignment model with filters
- Links to assign hostel, edit, remove
- Links to export/print functionality
- Used by admin role

**Execute**: Create hostel students list view with search, filters, table, and responsive design.

---

### Prompt 139: Create Hostel Room Details View

**Purpose**: Create hostel room details view with occupants and facilities.

**Functionality**: Shows comprehensive room information with occupants.

**How it Works**:
- Creates `resources/views/admin/hostels/rooms-show.blade.php`
- Extends app layout
- Shows page header with room number and actions:
  - Edit Room button
  - Delete Room button
  - Print Room Card button
- Shows room details card:
  - Room Number
  - Room Type
  - Floor Number
  - Capacity
  - Occupied
  - Available Beds
  - Hostel
- Shows room type details:
  - Room Type Name
  - Beds Per Room
  - Fees Per Month
  - Facilities
- Shows occupants table:
  - Photo (avatar)
  - Roll Number
  - Name
  - Class
  - Section
  - Admission Date
  - Hostel Fees
  - Actions (view student, remove)
- Shows "Add Occupant" button
- Shows "Print Room Card" button
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state
- Shows empty state if no occupants

**Integration**:
- Uses HostelRoomController show method
- Queries HostelRoom model
- Queries HostelAssignment model for occupants
- Links to edit, add occupant
- Used by admin role

**Execute**: Create hostel room details view with occupants, facilities, and responsive design.

---

### Prompt 140: Create Hostel Report View

**Purpose**: Create hostel report view with statistics and charts.

**Functionality**: Shows comprehensive hostel reports with analytics.

**How it Works**:
- Creates `resources/views/admin/hostels/report.blade.php`
- Extends app layout
- Shows page header with title and "Back to List" button
- Shows filter form:
  - Academic Session (select)
  - Hostel (select)
  - Room Type (select)
  - Report Type (daily, monthly, yearly)
- Shows statistics cards:
  - Total Hostels
  - Total Rooms
  - Total Beds
  - Total Students
  - Occupancy Percentage
  - Total Hostel Fees
  - Collected Fees
  - Pending Fees
- Shows charts:
  - Students by hostel (pie chart)
  - Students by room type (bar chart)
  - Room occupancy by floor (bar chart)
  - Fee collection trend (line chart)
- Shows hostel-wise table:
  - Hostel Name
  - Rooms Count
  - Capacity
  - Occupied
  - Occupancy Percentage
  - Total Fees
  - Collected Fees
- Shows "Export Report" button (PDF/Excel)
- Shows "Print Report" button
- Uses Chart.js for visualizations
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state

**Integration**:
- Uses HostelAssignmentController report method
- Queries Hostel, HostelRoom, HostelAssignment models
- Calculates statistics
- Shows charts
- Links to export/print
- Used by admin role

**Execute**: Create hostel report view with statistics, charts, table, and responsive design.

---

## 📊 Summary

**Total Frontend Prompts in Part 3: 30**

**Phases Covered in Part 3:**
9. **Library Management Views** (10 prompts)
10. **Transport Management Views** (10 prompts)
11. **Hostel Management Views** (10 prompts)

**Total Frontend Prompts (Part 1 + Part 2 + Part 3): 140**

**Features Implemented:**
- Library book management with categories
- Book issue and return with fines
- Library member management
- Transport route and stop management
- Transport vehicle management
- Student transport assignment
- Transport reports with analytics
- Hostel management with rooms and room types
- Student hostel assignment
- Hostel reports with occupancy analytics

**Next Phases:**
- Communication Views (Notices, Messages, SMS, Email)
- Accounting Views (Income, Expenses)
- Reports Views
- Settings Views

---

## 🚀 Continue with More Frontend Prompts

This document covers 30 additional detailed frontend prompts. Continue with remaining modules to complete entire frontend implementation.

**Happy Building with DevIn AI!** 🚀
