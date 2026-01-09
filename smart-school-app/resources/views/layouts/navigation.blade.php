{{-- Sidebar Navigation Component --}}
{{-- Role-based navigation menu with collapsible submenus --}}
<aside class="sidebar" id="sidebar">
    <!-- Brand Logo -->
    <div class="sidebar-brand d-flex align-items-center">
        <i class="bi bi-mortarboard-fill text-primary me-2 fs-4"></i>
        <div>
            <h4 class="mb-0">Smart School</h4>
            <small>Management System</small>
        </div>
    </div>
    
    <!-- Navigation Menu -->
    <nav class="sidebar-menu">
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
        </a>
        
        @php
            $userRole = Auth::user()->roles->first()->name ?? 'student';
        @endphp
        
        {{-- Admin Menu Items --}}
        @if($userRole === 'admin')
            <div class="menu-header">Academic</div>
            
            @can('students.view')
            <a href="javascript:void(0)" class="nav-link sidebar-dropdown-toggle" onclick="toggleSubmenu('studentsMenu', this)">
                <i class="bi bi-people"></i>
                <span>Students</span>
                <i class="bi bi-chevron-down ms-auto small dropdown-icon"></i>
            </a>
            <div class="sidebar-submenu" id="studentsMenu" style="display: none;">
                <a href="{{ route('admin.students.index') }}" class="nav-link submenu-item">All Students</a>
                <a href="{{ route('admin.students.create') }}" class="nav-link submenu-item">Add Student</a>
                <a href="{{ route('admin.student-categories.index') }}" class="nav-link submenu-item">Student Categories</a>
                <a href="{{ route('admin.promotions.index') }}" class="nav-link submenu-item">Promotions</a>
            </div>
            @endcan
            
            @can('teachers.view')
            <a href="#teachersMenu" class="nav-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="teachersMenu">
                <i class="bi bi-person-badge"></i>
                <span>Teachers</span>
                <i class="bi bi-chevron-down ms-auto small"></i>
            </a>
            <div class="collapse" id="teachersMenu">
                <a href="#" class="nav-link ps-5">All Teachers</a>
                <a href="#" class="nav-link ps-5">Add Teacher</a>
            </div>
            @endcan
            
            @can('classes.view')
            <a href="#classesMenu" class="nav-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="classesMenu">
                <i class="bi bi-building"></i>
                <span>Classes</span>
                <i class="bi bi-chevron-down ms-auto small"></i>
            </a>
            <div class="collapse" id="classesMenu">
                <a href="{{ route('admin.classes.index') }}" class="nav-link ps-5">All Classes</a>
                <a href="{{ route('admin.sections.index') }}" class="nav-link ps-5">Sections</a>
                <a href="{{ route('admin.subjects.index') }}" class="nav-link ps-5">Subjects</a>
                <a href="{{ route('admin.timetables.index') }}" class="nav-link ps-5">Timetable</a>
            </div>
            @endcan
            
            @can('attendance.view')
            <a href="#attendanceMenu" class="nav-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="attendanceMenu">
                <i class="bi bi-calendar-check"></i>
                <span>Attendance</span>
                <i class="bi bi-chevron-down ms-auto small"></i>
            </a>
            <div class="collapse" id="attendanceMenu">
                <a href="{{ route('admin.attendance.mark') }}" class="nav-link ps-5">Mark Attendance</a>
                <a href="{{ route('admin.attendance.index') }}" class="nav-link ps-5">View Attendance</a>
                <a href="{{ route('admin.attendance.report') }}" class="nav-link ps-5">Attendance Report</a>
            </div>
            @endcan
            
            <div class="menu-header">Examination</div>
            
            @can('exams.view')
            <a href="#examsMenu" class="nav-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="examsMenu">
                <i class="bi bi-journal-text"></i>
                <span>Examinations</span>
                <i class="bi bi-chevron-down ms-auto small"></i>
            </a>
            <div class="collapse" id="examsMenu">
                <a href="{{ route('admin.exam-types.index') }}" class="nav-link ps-5">Exam Types</a>
                <a href="{{ route('admin.exams.index') }}" class="nav-link ps-5">Exam Schedule</a>
                <a href="{{ route('admin.exam-grades.index') }}" class="nav-link ps-5">Exam Grades</a>
                <a href="{{ route('admin.exams.index') }}" class="nav-link ps-5">Enter Marks</a>
                <a href="{{ route('admin.exams.index') }}" class="nav-link ps-5">Results</a>
            </div>
            @endcan
            
            <div class="menu-header">Finance</div>
            
            @can('fees.view')
            <a href="#feesMenu" class="nav-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="feesMenu">
                <i class="bi bi-currency-rupee"></i>
                <span>Fees</span>
                <i class="bi bi-chevron-down ms-auto small"></i>
            </a>
            <div class="collapse" id="feesMenu">
                <a href="{{ route('admin.fees-types.index') }}" class="nav-link ps-5">Fee Types</a>
                <a href="{{ route('admin.fees-groups.index') }}" class="nav-link ps-5">Fee Groups</a>
                <a href="{{ route('admin.fees-collection.index') }}" class="nav-link ps-5">Fee Collection</a>
                <a href="{{ route('admin.fees-discounts.index') }}" class="nav-link ps-5">Fee Discounts</a>
                <a href="{{ route('admin.fees-collection.report') }}" class="nav-link ps-5">Fee Reports</a>
            </div>
            @endcan
            
            <div class="menu-header">Services</div>
            
            @can('library.view')
            <a href="#libraryMenu" class="nav-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="libraryMenu">
                <i class="bi bi-book"></i>
                <span>Library</span>
                <i class="bi bi-chevron-down ms-auto small"></i>
            </a>
            <div class="collapse" id="libraryMenu">
                <a href="{{ route('admin.library-books.index') }}" class="nav-link ps-5">Books</a>
                <a href="{{ route('admin.library-issues.index') }}" class="nav-link ps-5">Issue/Return</a>
                <a href="{{ route('admin.library-members.index') }}" class="nav-link ps-5">Members</a>
            </div>
            @endcan
            
            @can('transport.view')
            <a href="#transportMenu" class="nav-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="transportMenu">
                <i class="bi bi-bus-front"></i>
                <span>Transport</span>
                <i class="bi bi-chevron-down ms-auto small"></i>
            </a>
            <div class="collapse" id="transportMenu">
                <a href="{{ route('admin.transport-routes.index') }}" class="nav-link ps-5">Routes</a>
                <a href="{{ route('admin.transport-vehicles.index') }}" class="nav-link ps-5">Vehicles</a>
                <a href="{{ route('admin.transport-students.index') }}" class="nav-link ps-5">Assign Students</a>
            </div>
            @endcan
            
            @can('hostel.view')
            <a href="#hostelMenu" class="nav-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="hostelMenu">
                <i class="bi bi-house"></i>
                <span>Hostel</span>
                <i class="bi bi-chevron-down ms-auto small"></i>
            </a>
            <div class="collapse" id="hostelMenu">
                <a href="{{ route('admin.hostel-buildings.index') }}" class="nav-link ps-5">Hostels</a>
                <a href="{{ route('admin.hostel-rooms.index') }}" class="nav-link ps-5">Rooms</a>
                <a href="{{ route('admin.hostel-assignments.index') }}" class="nav-link ps-5">Assignments</a>
            </div>
            @endcan
            
            <div class="menu-header">Communication</div>
            
            @can('notices.view')
            <a href="#noticesMenu" class="nav-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="noticesMenu">
                <i class="bi bi-megaphone"></i>
                <span>Notices</span>
                <i class="bi bi-chevron-down ms-auto small"></i>
            </a>
            <div class="collapse" id="noticesMenu">
                <a href="{{ route('admin.notices.index') }}" class="nav-link ps-5">All Notices</a>
                <a href="{{ route('admin.notices.create') }}" class="nav-link ps-5">Create Notice</a>
            </div>
            @endcan
            
            <div class="menu-header">Reports</div>
            
            @can('reports.view')
            <a href="#reportsMenu" class="nav-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="reportsMenu">
                <i class="bi bi-file-earmark-bar-graph"></i>
                <span>Reports</span>
                <i class="bi bi-chevron-down ms-auto small"></i>
            </a>
            <div class="collapse" id="reportsMenu">
                <a href="{{ route('admin.reports.index') }}" class="nav-link ps-5">Student Report</a>
                <a href="{{ route('admin.attendance.report') }}" class="nav-link ps-5">Attendance Report</a>
                <a href="{{ route('admin.fees-collection.report') }}" class="nav-link ps-5">Fee Report</a>
                <a href="{{ route('admin.reports.index') }}" class="nav-link ps-5">Exam Report</a>
            </div>
            @endcan
            
            <div class="menu-header">Settings</div>
            
            @can('settings.view')
            <a href="#settingsMenu" class="nav-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="settingsMenu">
                <i class="bi bi-gear"></i>
                <span>Settings</span>
                <i class="bi bi-chevron-down ms-auto small"></i>
            </a>
            <div class="collapse" id="settingsMenu">
                <a href="#" class="nav-link ps-5">General Settings</a>
                <a href="{{ route('admin.academic-sessions.index') }}" class="nav-link ps-5">Academic Sessions</a>
                <a href="{{ route('admin.settings.languages.index') }}" class="nav-link ps-5">Languages</a>
                <a href="#" class="nav-link ps-5">SMS Settings</a>
                <a href="#" class="nav-link ps-5">Email Settings</a>
            </div>
            @endcan
            
            @can('users.view')
            <a href="#usersMenu" class="nav-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="usersMenu">
                <i class="bi bi-person-gear"></i>
                <span>User Management</span>
                <i class="bi bi-chevron-down ms-auto small"></i>
            </a>
            <div class="collapse" id="usersMenu">
                <a href="#" class="nav-link ps-5">All Users</a>
                <a href="#" class="nav-link ps-5">Roles</a>
                <a href="#" class="nav-link ps-5">Permissions</a>
            </div>
            @endcan
        @endif
        
        {{-- Teacher Menu Items --}}
        @if($userRole === 'teacher')
            <div class="menu-header">My Classes</div>
            
            <a href="#" class="nav-link">
                <i class="bi bi-building"></i>
                <span>My Classes</span>
            </a>
            
            <a href="#" class="nav-link">
                <i class="bi bi-people"></i>
                <span>My Students</span>
            </a>
            
            <div class="menu-header">Attendance</div>
            
            <a href="#" class="nav-link">
                <i class="bi bi-calendar-check"></i>
                <span>Mark Attendance</span>
            </a>
            
            <a href="#" class="nav-link">
                <i class="bi bi-calendar-range"></i>
                <span>Attendance Report</span>
            </a>
            
            <div class="menu-header">Examination</div>
            
            <a href="#" class="nav-link">
                <i class="bi bi-journal-text"></i>
                <span>Exam Schedule</span>
            </a>
            
            <a href="#" class="nav-link">
                <i class="bi bi-pencil-square"></i>
                <span>Enter Marks</span>
            </a>
            
            <div class="menu-header">Communication</div>
            
            <a href="#" class="nav-link">
                <i class="bi bi-megaphone"></i>
                <span>Notices</span>
            </a>
            
            <a href="#" class="nav-link">
                <i class="bi bi-calendar-event"></i>
                <span>Events</span>
            </a>
        @endif
        
        {{-- Student Menu Items --}}
        @if($userRole === 'student')
            <div class="menu-header">Academic</div>
            
            <a href="#" class="nav-link">
                <i class="bi bi-calendar-check"></i>
                <span>My Attendance</span>
            </a>
            
            <a href="#" class="nav-link">
                <i class="bi bi-journal-text"></i>
                <span>Exam Schedule</span>
            </a>
            
            <a href="#" class="nav-link">
                <i class="bi bi-award"></i>
                <span>My Results</span>
            </a>
            
            <div class="menu-header">Finance</div>
            
            <a href="#" class="nav-link">
                <i class="bi bi-currency-rupee"></i>
                <span>My Fees</span>
            </a>
            
            <div class="menu-header">Services</div>
            
            <a href="#" class="nav-link">
                <i class="bi bi-book"></i>
                <span>Library</span>
            </a>
            
            <div class="menu-header">Communication</div>
            
            <a href="#" class="nav-link">
                <i class="bi bi-megaphone"></i>
                <span>Notices</span>
            </a>
            
            <a href="#" class="nav-link">
                <i class="bi bi-calendar-event"></i>
                <span>Events</span>
            </a>
        @endif
        
        {{-- Parent Menu Items --}}
        @if($userRole === 'parent')
            <div class="menu-header">My Children</div>
            
            <a href="#" class="nav-link">
                <i class="bi bi-people"></i>
                <span>Children</span>
            </a>
            
            <a href="#" class="nav-link">
                <i class="bi bi-calendar-check"></i>
                <span>Attendance</span>
            </a>
            
            <a href="#" class="nav-link">
                <i class="bi bi-award"></i>
                <span>Results</span>
            </a>
            
            <div class="menu-header">Finance</div>
            
            <a href="#" class="nav-link">
                <i class="bi bi-currency-rupee"></i>
                <span>Fees</span>
            </a>
            
            <div class="menu-header">Communication</div>
            
            <a href="#" class="nav-link">
                <i class="bi bi-megaphone"></i>
                <span>Notices</span>
            </a>
            
            <a href="#" class="nav-link">
                <i class="bi bi-calendar-event"></i>
                <span>Events</span>
            </a>
        @endif
        
        {{-- Accountant Menu Items --}}
        @if($userRole === 'accountant')
            <div class="menu-header">Finance</div>
            
            <a href="#feesMenu" class="nav-link" data-bs-toggle="collapse">
                <i class="bi bi-currency-rupee"></i>
                <span>Fees</span>
                <i class="bi bi-chevron-down ms-auto small"></i>
            </a>
            <div class="collapse" id="feesMenu">
                <a href="#" class="nav-link ps-5">Fee Collection</a>
                <a href="#" class="nav-link ps-5">Fee Types</a>
                <a href="#" class="nav-link ps-5">Fee Groups</a>
                <a href="#" class="nav-link ps-5">Discounts</a>
            </div>
            
            <div class="menu-header">Reports</div>
            
            <a href="#" class="nav-link">
                <i class="bi bi-file-earmark-bar-graph"></i>
                <span>Fee Reports</span>
            </a>
            
            <a href="#" class="nav-link">
                <i class="bi bi-graph-up"></i>
                <span>Collection Summary</span>
            </a>
        @endif
        
        {{-- Librarian Menu Items --}}
        @if($userRole === 'librarian')
            <div class="menu-header">Library</div>
            
            <a href="#booksMenu" class="nav-link" data-bs-toggle="collapse">
                <i class="bi bi-book"></i>
                <span>Books</span>
                <i class="bi bi-chevron-down ms-auto small"></i>
            </a>
            <div class="collapse" id="booksMenu">
                <a href="#" class="nav-link ps-5">All Books</a>
                <a href="#" class="nav-link ps-5">Add Book</a>
                <a href="#" class="nav-link ps-5">Categories</a>
            </div>
            
            <a href="#" class="nav-link">
                <i class="bi bi-arrow-left-right"></i>
                <span>Issue/Return</span>
            </a>
            
            <a href="#" class="nav-link">
                <i class="bi bi-person-badge"></i>
                <span>Members</span>
            </a>
            
            <div class="menu-header">Reports</div>
            
            <a href="#" class="nav-link">
                <i class="bi bi-file-earmark-bar-graph"></i>
                <span>Library Reports</span>
            </a>
        @endif
    </nav>
</aside>
