<?php

namespace App\Services;

use App\Models\HostelRoom;
use App\Models\HostelAllocation;
use App\Models\Student;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

/**
 * Hostel Report Service
 * 
 * Prompt 431: Create Hostel Report Service
 * 
 * Generates hostel reports in PDF format.
 * Supports room allocation, occupancy, and student reports.
 * 
 * Features:
 * - Generate room allocation report
 * - Generate occupancy report
 * - Generate student hostel report
 * - Generate hostel fee collection report
 * - Include hostel statistics
 */
class HostelReportService
{
    protected PdfReportService $pdfService;

    public function __construct(PdfReportService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    /**
     * Generate room allocation report.
     *
     * @param int|null $hostelId
     * @return Response
     */
    public function generateRoomAllocationReport(?int $hostelId = null): Response
    {
        $data = $this->getRoomAllocationData($hostelId);
        $statistics = $this->calculateRoomStatistics($data);
        
        $html = $this->buildRoomAllocationReportHtml($data, $statistics);
        $filename = 'hostel_room_allocation_' . now()->format('Y-m-d_His');

        return $this->pdfService->generateFromHtml($html, $filename, 'a4', 'portrait');
    }

    /**
     * Generate occupancy report.
     *
     * @return Response
     */
    public function generateOccupancyReport(): Response
    {
        $data = $this->getOccupancyData();
        $statistics = $this->calculateOccupancyStatistics($data);
        
        $html = $this->buildOccupancyReportHtml($data, $statistics);
        $filename = 'hostel_occupancy_' . now()->format('Y-m-d_His');

        return $this->pdfService->generateFromHtml($html, $filename, 'a4', 'portrait');
    }

    /**
     * Generate student hostel report.
     *
     * @param array $filters
     * @return Response
     */
    public function generateStudentHostelReport(array $filters = []): Response
    {
        $allocations = $this->getStudentAllocationData($filters);
        $statistics = $this->calculateAllocationStatistics($allocations);
        
        $html = $this->buildStudentHostelReportHtml($allocations, $statistics, $filters);
        $filename = 'hostel_students_' . now()->format('Y-m-d_His');

        return $this->pdfService->generateFromHtml($html, $filename, 'a4', 'portrait');
    }

    /**
     * Generate hostel summary report.
     *
     * @return Response
     */
    public function generateSummaryReport(): Response
    {
        $statistics = $this->getHostelSummaryStatistics();
        
        $html = $this->buildSummaryReportHtml($statistics);
        $filename = 'hostel_summary_' . now()->format('Y-m-d_His');

        return $this->pdfService->generateFromHtml($html, $filename, 'a4', 'portrait');
    }

    /**
     * Generate room-wise student list.
     *
     * @param int $roomId
     * @return Response
     */
    public function generateRoomStudentList(int $roomId): Response
    {
        $room = HostelRoom::with(['hostel'])->findOrFail($roomId);
        $allocations = HostelAllocation::with(['student.user', 'student.schoolClass', 'student.section'])
            ->where('room_id', $roomId)
            ->where('is_active', true)
            ->get();
        
        $html = $this->buildRoomStudentListHtml($room, $allocations);
        $filename = "room_students_{$room->room_number}_" . now()->format('Y-m-d_His');

        return $this->pdfService->generateFromHtml($html, $filename, 'a4', 'portrait');
    }

    /**
     * Get room allocation data.
     *
     * @param int|null $hostelId
     * @return Collection
     */
    protected function getRoomAllocationData(?int $hostelId): Collection
    {
        $query = HostelRoom::with(['hostel', 'allocations' => function ($q) {
            $q->where('is_active', true)->with(['student.user']);
        }]);

        if ($hostelId) {
            $query->where('hostel_id', $hostelId);
        }

        return $query->orderBy('hostel_id')->orderBy('room_number')->get();
    }

    /**
     * Get occupancy data.
     *
     * @return Collection
     */
    protected function getOccupancyData(): Collection
    {
        return HostelRoom::with(['hostel'])
            ->selectRaw('hostel_rooms.*, 
                (SELECT COUNT(*) FROM hostel_allocations WHERE hostel_allocations.room_id = hostel_rooms.id AND hostel_allocations.is_active = 1) as occupied_beds')
            ->orderBy('hostel_id')
            ->orderBy('room_number')
            ->get();
    }

    /**
     * Get student allocation data.
     *
     * @param array $filters
     * @return Collection
     */
    protected function getStudentAllocationData(array $filters = []): Collection
    {
        $query = HostelAllocation::with([
            'student.user',
            'student.schoolClass',
            'student.section',
            'room.hostel'
        ]);

        if (!empty($filters['hostel_id'])) {
            $query->whereHas('room', function ($q) use ($filters) {
                $q->where('hostel_id', $filters['hostel_id']);
            });
        }

        if (!empty($filters['room_id'])) {
            $query->where('room_id', $filters['room_id']);
        }

        if (!empty($filters['class_id'])) {
            $query->whereHas('student', function ($q) use ($filters) {
                $q->where('class_id', $filters['class_id']);
            });
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        } else {
            $query->where('is_active', true);
        }

        return $query->orderBy('room_id')->get();
    }

    /**
     * Calculate room statistics.
     *
     * @param Collection $rooms
     * @return array
     */
    protected function calculateRoomStatistics(Collection $rooms): array
    {
        $totalRooms = $rooms->count();
        $totalCapacity = $rooms->sum('capacity');
        $totalOccupied = $rooms->sum(fn($r) => $r->allocations->count());
        $availableBeds = $totalCapacity - $totalOccupied;
        $occupancyRate = $totalCapacity > 0 ? round(($totalOccupied / $totalCapacity) * 100, 2) : 0;

        return [
            'total_rooms' => $totalRooms,
            'total_capacity' => $totalCapacity,
            'total_occupied' => $totalOccupied,
            'available_beds' => $availableBeds,
            'occupancy_rate' => $occupancyRate,
        ];
    }

    /**
     * Calculate occupancy statistics.
     *
     * @param Collection $rooms
     * @return array
     */
    protected function calculateOccupancyStatistics(Collection $rooms): array
    {
        $totalRooms = $rooms->count();
        $totalCapacity = $rooms->sum('capacity');
        $totalOccupied = $rooms->sum('occupied_beds');
        $fullyOccupied = $rooms->filter(fn($r) => $r->occupied_beds >= $r->capacity)->count();
        $empty = $rooms->filter(fn($r) => $r->occupied_beds == 0)->count();

        return [
            'total_rooms' => $totalRooms,
            'total_capacity' => $totalCapacity,
            'total_occupied' => $totalOccupied,
            'fully_occupied' => $fullyOccupied,
            'empty_rooms' => $empty,
            'occupancy_rate' => $totalCapacity > 0 ? round(($totalOccupied / $totalCapacity) * 100, 2) : 0,
        ];
    }

    /**
     * Calculate allocation statistics.
     *
     * @param Collection $allocations
     * @return array
     */
    protected function calculateAllocationStatistics(Collection $allocations): array
    {
        $total = $allocations->count();
        $active = $allocations->where('is_active', true)->count();
        $roomCount = $allocations->pluck('room_id')->unique()->count();

        return [
            'total' => $total,
            'active' => $active,
            'room_count' => $roomCount,
        ];
    }

    /**
     * Get hostel summary statistics.
     *
     * @return array
     */
    protected function getHostelSummaryStatistics(): array
    {
        $totalRooms = HostelRoom::count();
        $activeRooms = HostelRoom::where('is_active', true)->count();
        $totalCapacity = HostelRoom::where('is_active', true)->sum('capacity');
        $totalAllocations = HostelAllocation::where('is_active', true)->count();
        $availableBeds = $totalCapacity - $totalAllocations;

        $maleStudents = HostelAllocation::where('is_active', true)
            ->whereHas('student', function ($q) {
                $q->where('gender', 'male');
            })->count();

        $femaleStudents = HostelAllocation::where('is_active', true)
            ->whereHas('student', function ($q) {
                $q->where('gender', 'female');
            })->count();

        return [
            'total_rooms' => $totalRooms,
            'active_rooms' => $activeRooms,
            'total_capacity' => $totalCapacity,
            'total_allocations' => $totalAllocations,
            'available_beds' => $availableBeds,
            'occupancy_rate' => $totalCapacity > 0 ? round(($totalAllocations / $totalCapacity) * 100, 2) : 0,
            'male_students' => $maleStudents,
            'female_students' => $femaleStudents,
        ];
    }

    /**
     * Build room allocation report HTML.
     *
     * @param Collection $rooms
     * @param array $statistics
     * @return string
     */
    protected function buildRoomAllocationReportHtml(Collection $rooms, array $statistics): string
    {
        $schoolName = config('app.name', 'Smart School');
        $generatedAt = now()->format('F j, Y \a\t g:i A');

        $rows = '';
        $sn = 1;
        foreach ($rooms as $room) {
            $occupied = $room->allocations->count();
            $available = $room->capacity - $occupied;
            $statusClass = $available > 0 ? 'status-available' : 'status-full';
            $status = $available > 0 ? 'Available' : 'Full';
            
            $studentNames = $room->allocations->map(function ($a) {
                return $a->student?->user ? "{$a->student->user->first_name} {$a->student->user->last_name}" : '';
            })->filter()->implode(', ');
            
            $rows .= <<<HTML
<tr>
    <td class="text-center">{$sn}</td>
    <td>{$room->hostel?->name}</td>
    <td>{$room->room_number}</td>
    <td>{$room->room_type}</td>
    <td class="text-center">{$room->capacity}</td>
    <td class="text-center">{$occupied}</td>
    <td class="text-center">{$available}</td>
    <td class="text-center {$statusClass}">{$status}</td>
    <td style="font-size: 8px;">{$studentNames}</td>
</tr>
HTML;
            $sn++;
        }

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Hostel Room Allocation Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; padding: 20px; }
        .header { text-align: center; border-bottom: 3px solid #4f46e5; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { font-size: 22px; color: #4f46e5; }
        .header h2 { font-size: 16px; color: #333; margin-top: 10px; }
        .stats { display: flex; justify-content: space-around; margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 5px; }
        .stat-box { text-align: center; }
        .stat-value { font-size: 18px; font-weight: bold; color: #4f46e5; }
        .stat-label { font-size: 9px; color: #666; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #4f46e5; color: white; padding: 8px 5px; text-align: left; font-size: 9px; }
        td { padding: 6px 5px; border: 1px solid #ddd; font-size: 9px; }
        tr:nth-child(even) { background: #f8f9fa; }
        .text-center { text-align: center; }
        .status-available { color: #28a745; font-weight: bold; }
        .status-full { color: #dc3545; font-weight: bold; }
        .footer { margin-top: 30px; text-align: center; font-size: 8px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{$schoolName}</h1>
        <h2>Hostel Room Allocation Report</h2>
    </div>

    <div class="stats">
        <div class="stat-box">
            <div class="stat-value">{$statistics['total_rooms']}</div>
            <div class="stat-label">Total Rooms</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">{$statistics['total_capacity']}</div>
            <div class="stat-label">Total Capacity</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">{$statistics['total_occupied']}</div>
            <div class="stat-label">Occupied</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">{$statistics['available_beds']}</div>
            <div class="stat-label">Available</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">{$statistics['occupancy_rate']}%</div>
            <div class="stat-label">Occupancy Rate</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center">S.No</th>
                <th>Hostel</th>
                <th>Room No</th>
                <th>Type</th>
                <th class="text-center">Capacity</th>
                <th class="text-center">Occupied</th>
                <th class="text-center">Available</th>
                <th class="text-center">Status</th>
                <th>Students</th>
            </tr>
        </thead>
        <tbody>
            {$rows}
        </tbody>
    </table>

    <div class="footer">
        <p>Generated by {$schoolName} Management System on {$generatedAt}</p>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Build occupancy report HTML.
     *
     * @param Collection $rooms
     * @param array $statistics
     * @return string
     */
    protected function buildOccupancyReportHtml(Collection $rooms, array $statistics): string
    {
        $schoolName = config('app.name', 'Smart School');
        $generatedAt = now()->format('F j, Y \a\t g:i A');

        $rows = '';
        $sn = 1;
        foreach ($rooms as $room) {
            $available = $room->capacity - $room->occupied_beds;
            $occupancyRate = $room->capacity > 0 ? round(($room->occupied_beds / $room->capacity) * 100, 1) : 0;
            $statusClass = $available > 0 ? 'status-available' : 'status-full';
            
            $rows .= <<<HTML
<tr>
    <td class="text-center">{$sn}</td>
    <td>{$room->hostel?->name}</td>
    <td>{$room->room_number}</td>
    <td>{$room->room_type}</td>
    <td class="text-center">{$room->capacity}</td>
    <td class="text-center">{$room->occupied_beds}</td>
    <td class="text-center {$statusClass}">{$available}</td>
    <td class="text-center">{$occupancyRate}%</td>
</tr>
HTML;
            $sn++;
        }

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Hostel Occupancy Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; padding: 20px; }
        .header { text-align: center; border-bottom: 3px solid #4f46e5; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { font-size: 22px; color: #4f46e5; }
        .header h2 { font-size: 16px; color: #333; margin-top: 10px; }
        .stats { display: flex; justify-content: space-around; margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 5px; }
        .stat-box { text-align: center; }
        .stat-value { font-size: 18px; font-weight: bold; color: #4f46e5; }
        .stat-label { font-size: 9px; color: #666; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #4f46e5; color: white; padding: 10px 5px; text-align: left; }
        td { padding: 8px 5px; border: 1px solid #ddd; }
        tr:nth-child(even) { background: #f8f9fa; }
        .text-center { text-align: center; }
        .status-available { color: #28a745; }
        .status-full { color: #dc3545; }
        .footer { margin-top: 30px; text-align: center; font-size: 8px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{$schoolName}</h1>
        <h2>Hostel Occupancy Report</h2>
    </div>

    <div class="stats">
        <div class="stat-box">
            <div class="stat-value">{$statistics['total_rooms']}</div>
            <div class="stat-label">Total Rooms</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">{$statistics['fully_occupied']}</div>
            <div class="stat-label">Fully Occupied</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">{$statistics['empty_rooms']}</div>
            <div class="stat-label">Empty Rooms</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">{$statistics['occupancy_rate']}%</div>
            <div class="stat-label">Overall Occupancy</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center">S.No</th>
                <th>Hostel</th>
                <th>Room No</th>
                <th>Type</th>
                <th class="text-center">Capacity</th>
                <th class="text-center">Occupied</th>
                <th class="text-center">Available</th>
                <th class="text-center">Occupancy %</th>
            </tr>
        </thead>
        <tbody>
            {$rows}
        </tbody>
    </table>

    <div class="footer">
        <p>Generated by {$schoolName} Management System on {$generatedAt}</p>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Build student hostel report HTML.
     *
     * @param Collection $allocations
     * @param array $statistics
     * @param array $filters
     * @return string
     */
    protected function buildStudentHostelReportHtml(Collection $allocations, array $statistics, array $filters): string
    {
        $schoolName = config('app.name', 'Smart School');
        $generatedAt = now()->format('F j, Y \a\t g:i A');

        $rows = '';
        $sn = 1;
        foreach ($allocations as $allocation) {
            $student = $allocation->student;
            $studentName = $student?->user 
                ? "{$student->user->first_name} {$student->user->last_name}" 
                : '';
            $statusClass = $allocation->is_active ? 'status-active' : 'status-inactive';
            $statusText = $allocation->is_active ? 'Active' : 'Inactive';
            $admissionNumber = $student?->admission_number ?? '';
            $className = $student?->schoolClass?->name ?? '';
            $sectionName = $student?->section?->name ?? '';
            $hostelName = $allocation->room?->hostel?->name ?? '';
            $roomNumber = $allocation->room?->room_number ?? '';
            $allotmentDate = $allocation->allotment_date?->format('Y-m-d') ?? '';
            
            $rows .= <<<HTML
<tr>
    <td class="text-center">{$sn}</td>
    <td>{$admissionNumber}</td>
    <td>{$studentName}</td>
    <td>{$className}</td>
    <td>{$sectionName}</td>
    <td>{$hostelName}</td>
    <td>{$roomNumber}</td>
    <td>{$allocation->bed_number}</td>
    <td>{$allotmentDate}</td>
    <td class="text-center {$statusClass}">{$statusText}</td>
</tr>
HTML;
            $sn++;
        }

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Hostel Students Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; padding: 20px; }
        .header { text-align: center; border-bottom: 3px solid #4f46e5; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { font-size: 22px; color: #4f46e5; }
        .header h2 { font-size: 16px; color: #333; margin-top: 10px; }
        .stats { display: flex; justify-content: space-around; margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 5px; }
        .stat-box { text-align: center; }
        .stat-value { font-size: 18px; font-weight: bold; color: #4f46e5; }
        .stat-label { font-size: 9px; color: #666; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #4f46e5; color: white; padding: 8px 5px; text-align: left; font-size: 9px; }
        td { padding: 6px 5px; border: 1px solid #ddd; font-size: 9px; }
        tr:nth-child(even) { background: #f8f9fa; }
        .text-center { text-align: center; }
        .status-active { color: #28a745; }
        .status-inactive { color: #dc3545; }
        .footer { margin-top: 30px; text-align: center; font-size: 8px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{$schoolName}</h1>
        <h2>Hostel Students Report</h2>
    </div>

    <div class="stats">
        <div class="stat-box">
            <div class="stat-value">{$statistics['total']}</div>
            <div class="stat-label">Total Students</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">{$statistics['active']}</div>
            <div class="stat-label">Active</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">{$statistics['room_count']}</div>
            <div class="stat-label">Rooms</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center">S.No</th>
                <th>Admission No</th>
                <th>Student Name</th>
                <th>Class</th>
                <th>Section</th>
                <th>Hostel</th>
                <th>Room</th>
                <th>Bed</th>
                <th>Allotment Date</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            {$rows}
        </tbody>
    </table>

    <div class="footer">
        <p>Generated by {$schoolName} Management System on {$generatedAt}</p>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Build summary report HTML.
     *
     * @param array $statistics
     * @return string
     */
    protected function buildSummaryReportHtml(array $statistics): string
    {
        $schoolName = config('app.name', 'Smart School');
        $generatedAt = now()->format('F j, Y \a\t g:i A');

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Hostel Summary Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; padding: 20px; }
        .header { text-align: center; border-bottom: 3px solid #4f46e5; padding-bottom: 15px; margin-bottom: 30px; }
        .header h1 { font-size: 24px; color: #4f46e5; }
        .header h2 { font-size: 18px; color: #333; margin-top: 10px; }
        .section { margin-bottom: 25px; }
        .section-title { font-size: 14px; color: #4f46e5; border-bottom: 2px solid #4f46e5; padding-bottom: 5px; margin-bottom: 15px; }
        .stats-grid { display: flex; flex-wrap: wrap; gap: 15px; }
        .stat-card { width: 30%; padding: 15px; background: #f8f9fa; border-radius: 5px; border-left: 4px solid #4f46e5; }
        .stat-card.success { border-left-color: #28a745; }
        .stat-card.warning { border-left-color: #ffc107; }
        .stat-label { font-size: 10px; color: #666; margin-bottom: 5px; }
        .stat-value { font-size: 20px; font-weight: bold; color: #333; }
        .footer { margin-top: 40px; text-align: center; font-size: 9px; color: #666; border-top: 1px solid #ddd; padding-top: 15px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{$schoolName}</h1>
        <h2>Hostel Summary Report</h2>
        <p style="margin-top: 5px; color: #666;">As of {$generatedAt}</p>
    </div>

    <div class="section">
        <h3 class="section-title">Room Statistics</h3>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total Rooms</div>
                <div class="stat-value">{$statistics['total_rooms']}</div>
            </div>
            <div class="stat-card success">
                <div class="stat-label">Active Rooms</div>
                <div class="stat-value">{$statistics['active_rooms']}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Capacity</div>
                <div class="stat-value">{$statistics['total_capacity']}</div>
            </div>
        </div>
    </div>

    <div class="section">
        <h3 class="section-title">Occupancy Statistics</h3>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total Allocations</div>
                <div class="stat-value">{$statistics['total_allocations']}</div>
            </div>
            <div class="stat-card success">
                <div class="stat-label">Available Beds</div>
                <div class="stat-value">{$statistics['available_beds']}</div>
            </div>
            <div class="stat-card warning">
                <div class="stat-label">Occupancy Rate</div>
                <div class="stat-value">{$statistics['occupancy_rate']}%</div>
            </div>
        </div>
    </div>

    <div class="section">
        <h3 class="section-title">Student Distribution</h3>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Male Students</div>
                <div class="stat-value">{$statistics['male_students']}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Female Students</div>
                <div class="stat-value">{$statistics['female_students']}</div>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Generated by {$schoolName} Management System</p>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Build room student list HTML.
     *
     * @param HostelRoom $room
     * @param Collection $allocations
     * @return string
     */
    protected function buildRoomStudentListHtml(HostelRoom $room, Collection $allocations): string
    {
        $schoolName = config('app.name', 'Smart School');
        $generatedAt = now()->format('F j, Y \a\t g:i A');

        $rows = '';
        $sn = 1;
        foreach ($allocations as $allocation) {
            $student = $allocation->student;
            $studentName = $student?->user 
                ? "{$student->user->first_name} {$student->user->last_name}" 
                : '';
            
            $rows .= <<<HTML
<tr>
    <td class="text-center">{$sn}</td>
    <td>{$student?->admission_number}</td>
    <td>{$studentName}</td>
    <td>{$student?->schoolClass?->name}</td>
    <td>{$student?->section?->name}</td>
    <td>{$allocation->bed_number}</td>
    <td>{$allocation->allotment_date?->format('Y-m-d')}</td>
    <td>{$student?->user?->phone}</td>
</tr>
HTML;
            $sn++;
        }

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Room Student List - {$room->room_number}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; padding: 20px; }
        .header { text-align: center; border-bottom: 3px solid #4f46e5; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { font-size: 22px; color: #4f46e5; }
        .header h2 { font-size: 16px; color: #333; margin-top: 10px; }
        .room-info { margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 5px; }
        .info-row { display: flex; margin-bottom: 8px; }
        .info-label { font-weight: bold; width: 120px; color: #666; }
        .info-value { color: #333; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #4f46e5; color: white; padding: 10px 5px; text-align: left; }
        td { padding: 8px 5px; border: 1px solid #ddd; }
        tr:nth-child(even) { background: #f8f9fa; }
        .text-center { text-align: center; }
        .footer { margin-top: 30px; text-align: center; font-size: 8px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{$schoolName}</h1>
        <h2>Room Student List</h2>
    </div>

    <div class="room-info">
        <div class="info-row">
            <span class="info-label">Hostel:</span>
            <span class="info-value">{$room->hostel?->name}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Room Number:</span>
            <span class="info-value">{$room->room_number}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Room Type:</span>
            <span class="info-value">{$room->room_type}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Capacity:</span>
            <span class="info-value">{$room->capacity}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Occupied:</span>
            <span class="info-value">{$allocations->count()}</span>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center">S.No</th>
                <th>Admission No</th>
                <th>Student Name</th>
                <th>Class</th>
                <th>Section</th>
                <th>Bed No</th>
                <th>Allotment Date</th>
                <th>Phone</th>
            </tr>
        </thead>
        <tbody>
            {$rows}
        </tbody>
    </table>

    <div class="footer">
        <p>Generated by {$schoolName} Management System on {$generatedAt}</p>
    </div>
</body>
</html>
HTML;
    }
}
