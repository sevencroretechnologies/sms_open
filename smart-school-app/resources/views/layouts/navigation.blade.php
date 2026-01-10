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
            <a href="javascript:void(0)" class="nav-link sidebar-toggle" onclick="toggleSubmenu(this)">
                <i class="bi bi-people"></i>
                <span>Students</span>
                <i class="bi bi-chevron-down ms-auto small toggle-icon"></i>
            </a>
            <div class="sidebar-submenu" style="display: none; background: rgba(0,0,0,0.2); margin-left: 15px; border-left: 2px solid #4f46e5;">
                <a href="{{ route('admin.students.index') }}" class="nav-link" style="padding: 8px 15px; font-size: 14px; color: #e2e8f0;">• All Students</a>
                <a href="{{ route('admin.students.create') }}" class="nav-link" style="padding: 8px 15px; font-size: 14px; color: #e2e8f0;">• Add Student</a>
                <a href="{{ route('admin.student-categories.index') }}" class="nav-link" style="padding: 8px 15px; font-size: 14px; color: #e2e8f0;">• Student Categories</a>
                <a href="{{ route('admin.promotions.index') }}" class="nav-link" style="padding: 8px 15px; font-size: 14px; color: #e2e8f0;">• Promotions</a>
            </div>
            @endcan
            
            @can('teachers.view')
            <a href="javascript:void(0)" class="nav-link sidebar-toggle" onclick="toggleSubmenu(this)">
                <i class="bi bi-person-badge"></i>
                <span>Teachers</span>
                <i class="bi bi-chevron-down ms-auto small toggle-icon"></i>
            </a>
            <div class="sidebar-submenu" style="display: none; background: rgba(0,0,0,0.2); margin-left: 15px; border-left: 2px solid #4f46e5;">
                <a href="{{ route('admin.teachers.index') }}" class="nav-link" style="padding: 8px 15px; font-size: 14px; color: #e2e8f0;">• All Teachers</a>
                <a href="{{ route('admin.teachers.create') }}" class="nav-link" style="padding: 8px 15px; font-size: 14px; color: #e2e8f0;">• Add Teacher</a>
            </div>
            @endcan
            
            @can('classes.view')
            <a href="javascript:void(0)" class="nav-link sidebar-toggle" onclick="toggleSubmenu(this)">
                <i class="bi bi-building"></i>
                <span>Classes</span>
                <i class="bi bi-chevron-down ms-auto small toggle-icon"></i>
            </a>
            <div class="sidebar-submenu" style="display: none; background: rgba(0,0,0,0.2); margin-left: 15px; border-left: 2px solid #4f46e5;">
                <a href="{{ route('admin.classes.index') }}" class="nav-link" style="padding: 8px 15px; font-size: 14px; color: #e2e8f0;">• All Classes</a>
                <a href="{{ route('admin.sections.index') }}" class="nav-link" style="padding: 8px 15px; font-size: 14px; color: #e2e8f0;">• Sections</a>
                <a href="{{ route('admin.subjects.index') }}" class="nav-link" style="padding: 8px 15px; font-size: 14px; color: #e2e8f0;">• Subjects</a>
                <a href="{{ route('admin.timetables.index') }}" class="nav-link" style="padding: 8px 15px; font-size: 14px; color: #e2e8f0;">• Timetable</a>
            </div>
            @endcan
            
            @can('attendance.view')
            <a href="javascript:void(0)" class="nav-link sidebar-toggle" onclick="toggleSubmenu(this)">
                <i class="bi bi-calendar-check"></i>
                <span>Attendance</span>
                <i class="bi bi-chevron-down ms-auto small toggle-icon"></i>
            </a>
            <div class="sidebar-submenu" style="display: none; background: rgba(0,0,0,0.2); margin-left: 15px; border-left: 2px solid #4f46e5;">
                <a href="{{ route('admin.attendance.mark') }}" class="nav-link" style="padding: 8px 15px; font-size: 14px; color: #e2e8f0;">• Mark Attendance</a>
                <a href="{{ route('admin.attendance.index') }}" class="nav-link" style="padding: 8px 15px; font-size: 14px; color: #e2e8f0;">• View Attendance</a>
                <a href="{{ route('admin.attendance.report') }}" class="nav-link" style="padding: 8px 15px; font-size: 14px; color: #e2e8f0;">• Attendance Report</a>
            </div>
            @endcan
            
            <div class="menu-header">Examination</div>
            
            @can('exams.view')
            <a href="javascript:void(0)" class="nav-link sidebar-toggle" onclick="toggleSubmenu(this)">
                <i class="bi bi-journal-text"></i>
                <span>Examinations</span>
                <i class="bi bi-chevron-down ms-auto small toggle-icon"></i>
            </a>
            <div class="sidebar-submenu" style="display: none; background: rgba(0,0,0,0.2); margin-left: 15px; border-left: 2px solid #4f46e5;">
                <a href="{{ route('admin.exam-types.index') }}" class="nav-link" style="padding: 8px 15px; font-size: 14px; color: #e2e8f0;">• Exam Types</a>
                <a href="{{ route('admin.exams.index') }}" class="nav-link" style="padding: 8px 15px; font-size: 14px; color: #e2e8f0;">• Exam Schedule</a>
                <a href="{{ route('admin.exam-grades.index') }}" class="nav-link" style="padding: 8px 15px; font-size: 14px; color: #e2e8f0;">• Exam Grades</a>
                <a href="{{ route('admin.exams.index') }}" class="nav-link" style="padding: 8px 15px; font-size: 14px; color: #e2e8f0;">• Enter Marks</a>
                <a href="{{ route('admin.exams.index') }}" class="nav-link" style="padding: 8px 15px; font-size: 14px; color: #e2e8f0;">• Results</a>
            </div>
            @endcan
            
            <div class="menu-header">Finance</div>
            
            @can('fees.view')
            <a href="javascript:void(0)" class="nav-link sidebar-toggle" onclick="toggleSubmenu(this)">
                <i class="bi bi-currency-rupee"></i>
                <span>Fees</span>
                <i class="bi bi-chevron-down ms-auto small toggle-icon"></i>
            </a>
            <div class="sidebar-submenu" style="display: none; background: rgba(0,0,0,0.2); margin-left: 15px; border-left: 2px solid #4f46e5;">
                <a href="{{ route('admin.fees-types.index') }}" class="nav-link" style="padding: 8px 15px; font-size: 14px; color: #e2e8f0;">• Fee Types</a>
                <a href="{{ route('admin.fees-groups.index') }}" class="nav-link" style="padding: 8px 15px; font-size: 14px; color: #e2e8f0;">• Fee Groups</a>
                <a href="{{ route('admin.fees-collection.index') }}" class="nav-link" style="padding: 8px 15px; font-size: 14px; color: #e2e8f0;">• Fee Collection</a>
                <a href="{{ route('admin.fees-discounts.index') }}" class="nav-link" style="padding: 8px 15px; font-size: 14px; color: #e2e8f0;">• Fee Discounts</a>
                <a href="{{ route('admin.fees-collection.report') }}" class="nav-link" style="padding: 8px 15px; font-size: 14px; color: #e2e8f0;">• Fee Reports</a>
            </div>
            @endcan
            
            <div class="menu-header">Services</div>
            
            @can('library.view')
            <a href="javascript:void(0)" class="nav-link sidebar-toggle" onclick="toggleSubmenu(this)">
                <i class="bi bi-book"></i>
                <span>Library</span>
                <i class="bi bi-chevron-down ms-auto small toggle-icon"></i>
            </a>
            <div class="sidebar-submenu" style="display: none; background: rgba(0,0,0,0.2); margin-left: 15px; border-left: 2px solid #4f46e5;">
                <a href="{{ route('admin.library-books.index') }}" class="nav-link" style="padding: 8px 15px; font-size: 14px; color: #e2e8f0;">• Books</a>
                <a href="{{ route('admin.library-issues.index') }}" class="nav-link" style="padding: 8px 15px; font-size: 14px; color: #e2e8f0;">• Issue/Return</a>
                <a href="{{ route('admin.library-members.index') }}" class="nav-link" style="padding: 8px 15px; font-size: 14px; color: #e2e8f0;">• Members</a>
            </div>
            @endcan
            
            @can('transport.view')
            <a href="javascript:void(0)" class="nav-link sidebar-toggle" onclick="toggleSubmenu(this)">
                <i class="bi bi-bus-front"></i>
                <span>Transport</span>
                <i class="bi bi-chevron-down ms-auto small toggle-icon"></i>
            </a>
            <div class="sidebar-submenu" style="display: none; background: rgba(0,0,0,0.2); margin-left: 15px; border-left: 2px solid #4f46e5;">
                <a href="{{ route('admin.transport-routes.index') }}" class="nav-link" style="padding: 8px 15px; font-size: 14px; color: #e2e8f0;">• Routes</a>
                <a href="{{ route('admin.transport-vehicles.index') }}" class="nav-link" style="padding: 8px 15px; font-size: 14px; color: #e2e8f0;">• Vehicles</a>
                <a href="{{ route('admin.transport-students.index') }}" class="nav-link" style="padding: 8px 15px; font-size: 14px; color: #e2e8f0;">• Assign Students</a>
            </div>
            @endcan
            
            @can('hostel.view')
            <a href="javascript:void(0)" class="nav-link sidebar-toggle" onclick="toggleSubmenu(this)">
                <i class="bi bi-house"></i>
                <span>Hostel</span>
                <i class="bi bi-chevron-down ms-auto small toggle-icon"></i>
            </a>
            <div class="sidebar-submenu" style="display: none; background: rgba(0,0,0,0.2); margin-left: 15px; border-left: 2px solid #4f46e5;">
                <a href="{{ route('admin.hostel-buildings.index') }}" class="nav-link" style="padding: 8px 15px; font-size: 14px; color: #e2e8f0;">• Hostels</a>
                <a href="{{ route('admin.hostel-rooms.index') }}" class="nav-link" style="padding: 8px 15px; font-size: 14px; color: #e2e8f0;">• Rooms</a>
                <a href="{{ route('admin.hostel-assignments.index') }}" class="nav-link" style="padding: 8px 15px; font-size: 14px; color: #e2e8f0;">• Assignments</a>
            </div>
            @endcan
            
            <div class="menu-header">Communication</div>
            
            @can('notices.view')
            <a href="javascript:void(0)" class="nav-link sidebar-toggle" onclick="toggleSubmenu(this)">
                <i class="bi bi-megaphone"></i>
                <span>Notices</span>
                <i class="bi bi-chevron-down ms-auto small toggle-icon"></i>
            </a>
            <div class="sidebar-submenu" style="display: none; background: rgba(0,0,0,0.2); margin-left: 15px; border-left: 2px solid #4f46e5;">
                <a href="{{ route('admin.notices.index') }}" class="nav-link" style="padding: 8px 15px; font-size: 14px; color: #e2e8f0;">• All Notices</a>
                <a href="{{ route('admin.notices.create') }}" class="nav-link" style="padding: 8px 15px; font-size: 14px; color: #e2e8f0;">• Create Notice</a>
            </div>
            @endcan
            
            <div class="menu-header">Reports</div>
            
            @can('reports.view')
            <a href="javascript:void(0)" class="nav-link sidebar-toggle" onclick="toggleSubmenu(this)">
                <i class="bi bi-file-earmark-bar-graph"></i>
                <span>Reports</span>
                <i class="bi bi-chevron-down ms-auto small toggle-icon"></i>
            </a>
            <div class="sidebar-submenu" style="display: none; background: rgba(0,0,0,0.2); margin-left: 15px; border-left: 2px solid #4f46e5;">
                <a href="{{ route('admin.reports.index') }}" class="nav-link" style="padding: 8px 15px; font-size: 14px; color: #e2e8f0;">• Student Report</a>
                <a href="{{ route('admin.attendance.report') }}" class="nav-link" style="padding: 8px 15px; font-size: 14px; color: #e2e8f0;">• Attendance Report</a>
                <a href="{{ route('admin.fees-collection.report') }}" class="nav-link" style="padding: 8px 15px; font-size: 14px; color: #e2e8f0;">• Fee Report</a>
                <a href="{{ route('admin.reports.index') }}" class="nav-link" style="padding: 8px 15px; font-size: 14px; color: #e2e8f0;">• Exam Report</a>
            </div>
            @endcan
            
            <div class="menu-header">Settings</div>
            
            @can('settings.view')
            <a href="javascript:void(0)" class="nav-link sidebar-toggle" onclick="toggleSubmenu(this)">
                <i class="bi bi-gear"></i>
                <span>Settings</span>
                <i class="bi bi-chevron-down ms-auto small toggle-icon"></i>
            </a>
            <div class="sidebar-submenu" style="display: none; background: rgba(0,0,0,0.2); margin-left: 15px; border-left: 2px solid #4f46e5;">
                <a href="{{ route('admin.settings.general') }}" class="nav-link" style="padding: 8px 15px; font-size: 14px; color: #e2e8f0;">• General Settings</a>
                <a href="{{ route('admin.academic-sessions.index') }}" class="nav-link" style="padding: 8px 15px; font-size: 14px; color: #e2e8f0;">• Academic Sessions</a>
                <a href="{{ route('admin.settings.languages.index') }}" class="nav-link" style="padding: 8px 15px; font-size: 14px; color: #e2e8f0;">• Languages</a>
                <a href="{{ route('admin.sms.settings') }}" class="nav-link" style="padding: 8px 15px; font-size: 14px; color: #e2e8f0;">• SMS Settings</a>
                <a href="{{ route('admin.email.settings') }}" class="nav-link" style="padding: 8px 15px; font-size: 14px; color: #e2e8f0;">• Email Settings</a>
            </div>
            @endcan
            
            @can('users.view')
            <a href="javascript:void(0)" class="nav-link sidebar-toggle" onclick="toggleSubmenu(this)">
                <i class="bi bi-person-gear"></i>
                <span>User Management</span>
                <i class="bi bi-chevron-down ms-auto small toggle-icon"></i>
            </a>
            <div class="sidebar-submenu" style="display: none; background: rgba(0,0,0,0.2); margin-left: 15px; border-left: 2px solid #4f46e5;">
                <a href="{{ route('admin.users.index') }}" class="nav-link" style="padding: 8px 15px; font-size: 14px; color: #e2e8f0;">• All Users</a>
                <a href="{{ route('admin.roles.index') }}" class="nav-link" style="padding: 8px 15px; font-size: 14px; color: #e2e8f0;">• Roles</a>
                <a href="{{ route('admin.settings.permissions') }}" class="nav-link" style="padding: 8px 15px; font-size: 14px; color: #e2e8f0;">• Permissions</a>
            </div>
            @endcan
        @endif
        
        {{-- Teacher Menu Items --}}
        @if($userRole === 'teacher')
            <div class="menu-header">My Classes</div>
            
            <a href="{{ route('teacher.timetable.index') }}" class="nav-link">
                <i class="bi bi-building"></i>
                <span>My Classes</span>
            </a>
            
            <a href="{{ route('teacher.students.index') }}" class="nav-link">
                <i class="bi bi-people"></i>
                <span>My Students</span>
            </a>
            
            <div class="menu-header">Attendance</div>
            
            <a href="{{ route('teacher.attendance.mark') }}" class="nav-link">
                <i class="bi bi-calendar-check"></i>
                <span>Mark Attendance</span>
            </a>
            
            <a href="{{ route('teacher.attendance.report') }}" class="nav-link">
                <i class="bi bi-calendar-range"></i>
                <span>Attendance Report</span>
            </a>
            
            <div class="menu-header">Examination</div>
            
            <a href="{{ route('teacher.exams.index') }}" class="nav-link">
                <i class="bi bi-journal-text"></i>
                <span>Exam Schedule</span>
            </a>
            
            <a href="{{ route('teacher.exams.index') }}" class="nav-link">
                <i class="bi bi-pencil-square"></i>
                <span>Enter Marks</span>
            </a>
            
            <div class="menu-header">Communication</div>
            
            <a href="{{ route('teacher.notices.index') }}" class="nav-link">
                <i class="bi bi-megaphone"></i>
                <span>Notices</span>
            </a>
            
            <a href="{{ route('teacher.messages.index') }}" class="nav-link">
                <i class="bi bi-calendar-event"></i>
                <span>Messages</span>
            </a>
        @endif
        
        {{-- Student Menu Items --}}
        @if($userRole === 'student')
            <div class="menu-header">Academic</div>
            
            <a href="{{ route('student.attendance.index') }}" class="nav-link">
                <i class="bi bi-calendar-check"></i>
                <span>My Attendance</span>
            </a>
            
            <a href="{{ route('student.exams.index') }}" class="nav-link">
                <i class="bi bi-journal-text"></i>
                <span>Exam Schedule</span>
            </a>
            
            <a href="{{ route('student.exams.index') }}" class="nav-link">
                <i class="bi bi-award"></i>
                <span>My Results</span>
            </a>
            
            <div class="menu-header">Finance</div>
            
            <a href="{{ route('student.fees.index') }}" class="nav-link">
                <i class="bi bi-currency-rupee"></i>
                <span>My Fees</span>
            </a>
            
            <div class="menu-header">Services</div>
            
            <a href="{{ route('student.library.index') }}" class="nav-link">
                <i class="bi bi-book"></i>
                <span>Library</span>
            </a>
            
            <div class="menu-header">Communication</div>
            
            <a href="{{ route('student.notices.index') }}" class="nav-link">
                <i class="bi bi-megaphone"></i>
                <span>Notices</span>
            </a>
            
            <a href="{{ route('student.messages.index') }}" class="nav-link">
                <i class="bi bi-calendar-event"></i>
                <span>Messages</span>
            </a>
        @endif
        
        {{-- Parent Menu Items --}}
        @if($userRole === 'parent')
            <div class="menu-header">My Children</div>
            
            <a href="{{ route('parent.children.index') }}" class="nav-link">
                <i class="bi bi-people"></i>
                <span>Children</span>
            </a>
            
            <a href="{{ route('parent.children.index') }}" class="nav-link">
                <i class="bi bi-calendar-check"></i>
                <span>Attendance</span>
            </a>
            
            <a href="{{ route('parent.children.index') }}" class="nav-link">
                <i class="bi bi-award"></i>
                <span>Results</span>
            </a>
            
            <div class="menu-header">Finance</div>
            
            <a href="{{ route('parent.fees.index') }}" class="nav-link">
                <i class="bi bi-currency-rupee"></i>
                <span>Fees</span>
            </a>
            
            <div class="menu-header">Communication</div>
            
            <a href="{{ route('parent.notices.index') }}" class="nav-link">
                <i class="bi bi-megaphone"></i>
                <span>Notices</span>
            </a>
            
            <a href="{{ route('parent.messages.index') }}" class="nav-link">
                <i class="bi bi-calendar-event"></i>
                <span>Messages</span>
            </a>
        @endif
        
        {{-- Accountant Menu Items --}}
        @if($userRole === 'accountant')
            <div class="menu-header">Finance</div>
            
            <a href="javascript:void(0)" class="nav-link sidebar-toggle" onclick="toggleSubmenu(this)">
                <i class="bi bi-currency-rupee"></i>
                <span>Fees</span>
                <i class="bi bi-chevron-down ms-auto small toggle-icon"></i>
            </a>
            <div class="sidebar-submenu" style="display: none; background: rgba(0,0,0,0.2); margin-left: 15px; border-left: 2px solid #4f46e5;">
                <a href="{{ route('accountant.fees-collection.index') }}" class="nav-link" style="padding: 8px 15px; font-size: 14px; color: #e2e8f0;">• Fee Collection</a>
                <a href="{{ route('accountant.fees-reports.index') }}" class="nav-link" style="padding: 8px 15px; font-size: 14px; color: #e2e8f0;">• Fee Reports</a>
                <a href="{{ route('accountant.fees-reports.due') }}" class="nav-link" style="padding: 8px 15px; font-size: 14px; color: #e2e8f0;">• Due Fees</a>
                <a href="{{ route('accountant.fees-reports.defaulters') }}" class="nav-link" style="padding: 8px 15px; font-size: 14px; color: #e2e8f0;">• Defaulters</a>
            </div>
            
            <div class="menu-header">Reports</div>
            
            <a href="{{ route('accountant.fees-reports.index') }}" class="nav-link">
                <i class="bi bi-file-earmark-bar-graph"></i>
                <span>Fee Reports</span>
            </a>
            
            <a href="{{ route('accountant.fees-reports.collection') }}" class="nav-link">
                <i class="bi bi-graph-up"></i>
                <span>Collection Summary</span>
            </a>
        @endif
        
        {{-- Librarian Menu Items --}}
        @if($userRole === 'librarian')
            <div class="menu-header">Library</div>
            
            <a href="javascript:void(0)" class="nav-link sidebar-toggle" onclick="toggleSubmenu(this)">
                <i class="bi bi-book"></i>
                <span>Books</span>
                <i class="bi bi-chevron-down ms-auto small toggle-icon"></i>
            </a>
            <div class="sidebar-submenu" style="display: none; background: rgba(0,0,0,0.2); margin-left: 15px; border-left: 2px solid #4f46e5;">
                <a href="{{ route('librarian.books.index') }}" class="nav-link" style="padding: 8px 15px; font-size: 14px; color: #e2e8f0;">• All Books</a>
                <a href="{{ route('librarian.books.create') }}" class="nav-link" style="padding: 8px 15px; font-size: 14px; color: #e2e8f0;">• Add Book</a>
                <a href="{{ route('librarian.categories.index') }}" class="nav-link" style="padding: 8px 15px; font-size: 14px; color: #e2e8f0;">• Categories</a>
            </div>
            
            <a href="{{ route('librarian.issues.index') }}" class="nav-link">
                <i class="bi bi-arrow-left-right"></i>
                <span>Issue/Return</span>
            </a>
            
            <a href="{{ route('librarian.members.index') }}" class="nav-link">
                <i class="bi bi-person-badge"></i>
                <span>Members</span>
            </a>
            
            <div class="menu-header">Reports</div>
            
            <a href="{{ route('librarian.reports.index') }}" class="nav-link">
                <i class="bi bi-file-earmark-bar-graph"></i>
                <span>Library Reports</span>
            </a>
        @endif
    </nav>
</aside>
