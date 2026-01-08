# Session 23 Continuation Guide

## Session Overview
- **Session Number**: 23
- **Prompts**: 232-241 (10 prompts)
- **Phase**: Frontend Phase 13 - Hostel Management Views
- **Previous Session**: Session 22 - Transport Management Views (Completed)

## Prerequisites
- Session 22 must be completed (Transport Management Views)
- All transport views should be working correctly
- Test routes for transport views should be functional

## Tasks for Session 23

### Frontend Phase 13: Hostel Management Views (Prompts 232-241)

| Prompt # | Description | File Path |
|----------|-------------|-----------|
| 232 | Hostels List View | `resources/views/admin/hostel/hostels.blade.php` |
| 233 | Hostels Create View | `resources/views/admin/hostel/hostels-create.blade.php` |
| 234 | Hostel Details View | `resources/views/admin/hostel/hostels-show.blade.php` |
| 235 | Room Types List View | `resources/views/admin/hostel/room-types.blade.php` |
| 236 | Room Types Create View | `resources/views/admin/hostel/room-types-create.blade.php` |
| 237 | Rooms List View | `resources/views/admin/hostel/rooms.blade.php` |
| 238 | Rooms Create View | `resources/views/admin/hostel/rooms-create.blade.php` |
| 239 | Room Details View | `resources/views/admin/hostel/rooms-show.blade.php` |
| 240 | Hostel Assignments List View | `resources/views/admin/hostel/assignments.blade.php` |
| 241 | Hostel Assignment Create View | `resources/views/admin/hostel/assignments-create.blade.php` |

## Implementation Guidelines

### View Requirements
Each view should include:
- Extend the app layout (`@extends('layouts.app')`)
- Page header with breadcrumbs
- Alert messages for success/error
- Loading states and empty states
- Responsive design (Bootstrap 5.3)
- RTL language support
- Alpine.js for interactivity

### Hostels List View (Prompt 232)
- Statistics cards: Total Hostels, Total Rooms, Total Capacity, Occupied Beds
- Table columns: Hostel Name, Type (Boys/Girls/Co-ed), Warden Name, Rooms Count, Capacity, Occupied, Status, Actions
- Search and filter functionality
- CRUD operations with delete confirmation

### Hostels Create View (Prompt 233)
- Form fields: Hostel Name, Type, Address, Warden Name, Warden Phone, Warden Email, Facilities, Description, Status
- Preview card showing hostel details
- Facilities multi-select (WiFi, Laundry, Mess, etc.)
- Validation and error handling

### Hostel Details View (Prompt 234)
- Hostel profile card with all details
- Warden information
- Room statistics
- Occupancy chart
- Recent assignments

### Room Types List View (Prompt 235)
- Statistics cards: Total Types, Single Rooms, Double Rooms, Dormitory
- Table columns: Type Name, Description, Capacity, Monthly Fee, Status, Actions
- CRUD operations

### Room Types Create View (Prompt 236)
- Form fields: Type Name, Description, Capacity, Monthly Fee, Amenities, Status
- Preview card showing room type details
- Amenities multi-select

### Rooms List View (Prompt 237)
- Filter by hostel, room type, floor, status
- Table columns: Room Number, Hostel, Type, Floor, Capacity, Occupied, Available, Status, Actions
- Occupancy indicators (color-coded)
- CRUD operations

### Rooms Create View (Prompt 238)
- Form fields: Room Number, Hostel, Room Type, Floor, Capacity, Description, Status
- Preview card showing room details
- Auto-calculate available beds

### Room Details View (Prompt 239)
- Room profile card with all details
- Current occupants list
- Bed availability visualization
- Assignment history

### Hostel Assignments List View (Prompt 240)
- Filter by hostel, room, student, status
- Table columns: Student Name, Class, Hostel, Room, Bed Number, Check-in Date, Check-out Date, Monthly Fee, Status, Actions
- Bulk actions for check-out
- Export options

### Hostel Assignment Create View (Prompt 241)
- Student selection (search/filter)
- Hostel and room selection
- Bed assignment
- Fee configuration
- Check-in date
- Preview of assignment

## Components to Use
- `x-card` - Card component
- `x-alert` - Alert messages
- `x-form-input` - Form inputs
- `x-form-select` - Select dropdowns
- `x-form-datepicker` - Date pickers
- `x-modal-dialog` - Modal dialogs
- `x-empty-state` - Empty state displays
- `x-pagination` - Pagination

## Testing Requirements
1. Create test routes with mock data in `routes/web.php`
2. Test all views visually
3. Verify responsive design
4. Check RTL support
5. Record testing video

## After Completion
1. Update PROGRESS.md with session completion
2. Create SESSION-24-CONTINUATION.md
3. Create PR with all changes
4. Wait for CI checks
5. Share PR link and testing video with user

## Reference Files
- Main prompts: `smart-school/DEVIN-AI-FRONTEND-DETAILED-PART3.md`
- Existing hostel migrations: `database/migrations/*hostel*`
- Similar views for reference: `resources/views/admin/transport/`

## Continuation Prompt
To continue with Session 23, use:
```
Continue with Session 23 (Frontend Prompts 232+) for the Smart School Management System

Repository: 01fe23bcs183/sms_open

Reference the SESSION-23-CONTINUATION.md file for context and the smart-school/DEVIN-AI-FRONTEND-DETAILED-PART3.md file for detailed prompt specifications.

Tasks for this session:
- Continue Frontend Phase 13: Hostel Management Views (Prompts 232-241)
- Create hostel management views
- Create room types and rooms management views
- Create hostel assignment views

After completing tasks:
1. Verify all views work correctly
2. Update PROGRESS.md with session completion
3. Create SESSION-24-CONTINUATION.md for the next session
4. Create a PR with all changes
```
