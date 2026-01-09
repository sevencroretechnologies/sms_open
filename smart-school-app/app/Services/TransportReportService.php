<?php

namespace App\Services;

use App\Models\TransportRoute;
use App\Models\TransportVehicle;
use App\Models\TransportAssignment;
use App\Models\Student;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

/**
 * Transport Report Service
 * 
 * Prompt 430: Create Transport Report Service
 * 
 * Generates transport reports in PDF format.
 * Supports route, vehicle, and student transport reports.
 * 
 * Features:
 * - Generate route-wise student list
 * - Generate vehicle assignment report
 * - Generate transport fee collection report
 * - Generate driver/vehicle details report
 * - Include transport statistics
 */
class TransportReportService
{
    protected PdfReportService $pdfService;

    public function __construct(PdfReportService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    /**
     * Generate route-wise student list report.
     *
     * @param int|null $routeId
     * @return Response
     */
    public function generateRouteStudentReport(?int $routeId = null): Response
    {
        $data = $this->getRouteStudentData($routeId);
        
        $html = $this->buildRouteStudentReportHtml($data);
        $filename = 'route_students_' . now()->format('Y-m-d_His');

        return $this->pdfService->generateFromHtml($html, $filename, 'a4', 'portrait');
    }

    /**
     * Generate vehicle details report.
     *
     * @return Response
     */
    public function generateVehicleReport(): Response
    {
        $vehicles = $this->getVehicleData();
        $statistics = $this->calculateVehicleStatistics($vehicles);
        
        $html = $this->buildVehicleReportHtml($vehicles, $statistics);
        $filename = 'transport_vehicles_' . now()->format('Y-m-d_His');

        return $this->pdfService->generateFromHtml($html, $filename, 'a4', 'landscape');
    }

    /**
     * Generate transport assignment report.
     *
     * @param array $filters
     * @return Response
     */
    public function generateAssignmentReport(array $filters = []): Response
    {
        $assignments = $this->getAssignmentData($filters);
        $statistics = $this->calculateAssignmentStatistics($assignments);
        
        $html = $this->buildAssignmentReportHtml($assignments, $statistics, $filters);
        $filename = 'transport_assignments_' . now()->format('Y-m-d_His');

        return $this->pdfService->generateFromHtml($html, $filename, 'a4', 'portrait');
    }

    /**
     * Generate route details report.
     *
     * @return Response
     */
    public function generateRouteDetailsReport(): Response
    {
        $routes = $this->getRouteDetailsData();
        $statistics = $this->calculateRouteStatistics($routes);
        
        $html = $this->buildRouteDetailsReportHtml($routes, $statistics);
        $filename = 'transport_routes_' . now()->format('Y-m-d_His');

        return $this->pdfService->generateFromHtml($html, $filename, 'a4', 'portrait');
    }

    /**
     * Generate transport summary report.
     *
     * @return Response
     */
    public function generateSummaryReport(): Response
    {
        $statistics = $this->getTransportSummaryStatistics();
        
        $html = $this->buildSummaryReportHtml($statistics);
        $filename = 'transport_summary_' . now()->format('Y-m-d_His');

        return $this->pdfService->generateFromHtml($html, $filename, 'a4', 'portrait');
    }

    /**
     * Get route-wise student data.
     *
     * @param int|null $routeId
     * @return Collection
     */
    protected function getRouteStudentData(?int $routeId): Collection
    {
        $query = TransportRoute::with(['stopPoints', 'vehicle']);

        if ($routeId) {
            $query->where('id', $routeId);
        }

        $routes = $query->where('is_active', true)->orderBy('name')->get();

        return $routes->map(function ($route) {
            $assignments = TransportAssignment::with(['student.user', 'student.schoolClass', 'student.section', 'stopPoint'])
                ->where('route_id', $route->id)
                ->where('is_active', true)
                ->get();

            return [
                'route' => $route,
                'students' => $assignments,
                'student_count' => $assignments->count(),
            ];
        });
    }

    /**
     * Get vehicle data.
     *
     * @return Collection
     */
    protected function getVehicleData(): Collection
    {
        return TransportVehicle::with(['routes'])
            ->orderBy('vehicle_number')
            ->get();
    }

    /**
     * Get assignment data.
     *
     * @param array $filters
     * @return Collection
     */
    protected function getAssignmentData(array $filters = []): Collection
    {
        $query = TransportAssignment::with([
            'student.user',
            'student.schoolClass',
            'student.section',
            'route',
            'stopPoint'
        ]);

        if (!empty($filters['route_id'])) {
            $query->where('route_id', $filters['route_id']);
        }

        if (!empty($filters['class_id'])) {
            $query->whereHas('student', function ($q) use ($filters) {
                $q->where('class_id', $filters['class_id']);
            });
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->orderBy('route_id')->get();
    }

    /**
     * Get route details data.
     *
     * @return Collection
     */
    protected function getRouteDetailsData(): Collection
    {
        return TransportRoute::with(['vehicle', 'stopPoints'])
            ->orderBy('name')
            ->get();
    }

    /**
     * Calculate vehicle statistics.
     *
     * @param Collection $vehicles
     * @return array
     */
    protected function calculateVehicleStatistics(Collection $vehicles): array
    {
        $total = $vehicles->count();
        $active = $vehicles->where('is_active', true)->count();
        $inactive = $vehicles->where('is_active', false)->count();
        $totalCapacity = $vehicles->sum('seating_capacity');

        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $inactive,
            'total_capacity' => $totalCapacity,
        ];
    }

    /**
     * Calculate assignment statistics.
     *
     * @param Collection $assignments
     * @return array
     */
    protected function calculateAssignmentStatistics(Collection $assignments): array
    {
        $total = $assignments->count();
        $active = $assignments->where('is_active', true)->count();
        $inactive = $assignments->where('is_active', false)->count();
        $routeCount = $assignments->pluck('route_id')->unique()->count();

        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $inactive,
            'route_count' => $routeCount,
        ];
    }

    /**
     * Calculate route statistics.
     *
     * @param Collection $routes
     * @return array
     */
    protected function calculateRouteStatistics(Collection $routes): array
    {
        $total = $routes->count();
        $active = $routes->where('is_active', true)->count();
        $totalStops = $routes->sum(fn($r) => $r->stopPoints->count());
        $totalDistance = $routes->sum('total_distance');

        return [
            'total' => $total,
            'active' => $active,
            'total_stops' => $totalStops,
            'total_distance' => $totalDistance,
        ];
    }

    /**
     * Get transport summary statistics.
     *
     * @return array
     */
    protected function getTransportSummaryStatistics(): array
    {
        $totalRoutes = TransportRoute::count();
        $activeRoutes = TransportRoute::where('is_active', true)->count();
        $totalVehicles = TransportVehicle::count();
        $activeVehicles = TransportVehicle::where('is_active', true)->count();
        $totalAssignments = TransportAssignment::count();
        $activeAssignments = TransportAssignment::where('is_active', true)->count();
        $totalCapacity = TransportVehicle::where('is_active', true)->sum('seating_capacity');

        $studentsByRoute = TransportAssignment::where('is_active', true)
            ->selectRaw('route_id, count(*) as count')
            ->groupBy('route_id')
            ->get();

        return [
            'total_routes' => $totalRoutes,
            'active_routes' => $activeRoutes,
            'total_vehicles' => $totalVehicles,
            'active_vehicles' => $activeVehicles,
            'total_assignments' => $totalAssignments,
            'active_assignments' => $activeAssignments,
            'total_capacity' => $totalCapacity,
            'utilization' => $totalCapacity > 0 ? round(($activeAssignments / $totalCapacity) * 100, 2) : 0,
        ];
    }

    /**
     * Build route student report HTML.
     *
     * @param Collection $data
     * @return string
     */
    protected function buildRouteStudentReportHtml(Collection $data): string
    {
        $schoolName = config('app.name', 'Smart School');
        $generatedAt = now()->format('F j, Y \a\t g:i A');

        $routeSections = '';
        foreach ($data as $routeData) {
            $route = $routeData['route'];
            $students = $routeData['students'];
            
            $studentRows = '';
            $sn = 1;
            foreach ($students as $assignment) {
                $student = $assignment->student;
                $studentName = $student?->user 
                    ? "{$student->user->first_name} {$student->user->last_name}" 
                    : '';
                
                $studentRows .= <<<HTML
<tr>
    <td class="text-center">{$sn}</td>
    <td>{$student?->admission_number}</td>
    <td>{$studentName}</td>
    <td>{$student?->schoolClass?->name}</td>
    <td>{$student?->section?->name}</td>
    <td>{$assignment->stopPoint?->name}</td>
    <td>{$assignment->pickup_time}</td>
    <td>{$assignment->drop_time}</td>
</tr>
HTML;
                $sn++;
            }

            $routeSections .= <<<HTML
<div class="route-section">
    <h3 class="route-title">{$route->name} ({$routeData['student_count']} Students)</h3>
    <p class="route-info">Vehicle: {$route->vehicle?->vehicle_number} | Driver: {$route->vehicle?->driver_name}</p>
    <table>
        <thead>
            <tr>
                <th class="text-center">S.No</th>
                <th>Admission No</th>
                <th>Student Name</th>
                <th>Class</th>
                <th>Section</th>
                <th>Stop Point</th>
                <th>Pickup</th>
                <th>Drop</th>
            </tr>
        </thead>
        <tbody>
            {$studentRows}
        </tbody>
    </table>
</div>
HTML;
        }

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Route-wise Student List</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; padding: 20px; }
        .header { text-align: center; border-bottom: 3px solid #4f46e5; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { font-size: 22px; color: #4f46e5; }
        .header h2 { font-size: 16px; color: #333; margin-top: 10px; }
        .route-section { margin-bottom: 25px; page-break-inside: avoid; }
        .route-title { font-size: 13px; color: #4f46e5; background: #f8f9fa; padding: 8px; border-left: 4px solid #4f46e5; }
        .route-info { font-size: 9px; color: #666; padding: 5px 8px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #4f46e5; color: white; padding: 8px 5px; text-align: left; font-size: 9px; }
        td { padding: 6px 5px; border: 1px solid #ddd; font-size: 9px; }
        tr:nth-child(even) { background: #f8f9fa; }
        .text-center { text-align: center; }
        .footer { margin-top: 30px; text-align: center; font-size: 8px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{$schoolName}</h1>
        <h2>Route-wise Student List</h2>
    </div>

    {$routeSections}

    <div class="footer">
        <p>Generated by {$schoolName} Management System on {$generatedAt}</p>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Build vehicle report HTML.
     *
     * @param Collection $vehicles
     * @param array $statistics
     * @return string
     */
    protected function buildVehicleReportHtml(Collection $vehicles, array $statistics): string
    {
        $schoolName = config('app.name', 'Smart School');
        $generatedAt = now()->format('F j, Y \a\t g:i A');

        $rows = '';
        $sn = 1;
        foreach ($vehicles as $vehicle) {
            $statusClass = $vehicle->is_active ? 'status-active' : 'status-inactive';
            $statusText = $vehicle->is_active ? 'Active' : 'Inactive';
            $insuranceStatus = $vehicle->insurance_expiry && $vehicle->insurance_expiry->isPast() ? 'Expired' : 'Valid';
            $insuranceClass = $insuranceStatus === 'Expired' ? 'text-danger' : 'text-success';
            $insuranceExpiry = $vehicle->insurance_expiry?->format('Y-m-d') ?? '';
            $fitnessExpiry = $vehicle->fitness_expiry?->format('Y-m-d') ?? '';
            
            $rows .= <<<HTML
<tr>
    <td class="text-center">{$sn}</td>
    <td>{$vehicle->vehicle_number}</td>
    <td>{$vehicle->vehicle_type}</td>
    <td>{$vehicle->driver_name}</td>
    <td>{$vehicle->driver_phone}</td>
    <td class="text-center">{$vehicle->seating_capacity}</td>
    <td>{$insuranceExpiry}</td>
    <td class="{$insuranceClass}">{$insuranceStatus}</td>
    <td>{$fitnessExpiry}</td>
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
    <title>Transport Vehicles Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9px; padding: 15px; }
        .header { text-align: center; border-bottom: 3px solid #4f46e5; padding-bottom: 10px; margin-bottom: 15px; }
        .header h1 { font-size: 18px; color: #4f46e5; }
        .header h2 { font-size: 14px; color: #333; margin-top: 5px; }
        .stats { display: flex; justify-content: space-around; margin-bottom: 15px; padding: 10px; background: #f8f9fa; border-radius: 5px; }
        .stat-box { text-align: center; }
        .stat-value { font-size: 16px; font-weight: bold; color: #4f46e5; }
        .stat-label { font-size: 8px; color: #666; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #4f46e5; color: white; padding: 6px 4px; text-align: left; font-size: 8px; }
        td { padding: 5px 4px; border: 1px solid #ddd; font-size: 8px; }
        tr:nth-child(even) { background: #f8f9fa; }
        .text-center { text-align: center; }
        .status-active { color: #28a745; }
        .status-inactive { color: #dc3545; }
        .text-success { color: #28a745; }
        .text-danger { color: #dc3545; }
        .footer { margin-top: 20px; text-align: center; font-size: 8px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{$schoolName}</h1>
        <h2>Transport Vehicles Report</h2>
    </div>

    <div class="stats">
        <div class="stat-box">
            <div class="stat-value">{$statistics['total']}</div>
            <div class="stat-label">Total Vehicles</div>
        </div>
        <div class="stat-box">
            <div class="stat-value" style="color: #28a745;">{$statistics['active']}</div>
            <div class="stat-label">Active</div>
        </div>
        <div class="stat-box">
            <div class="stat-value" style="color: #dc3545;">{$statistics['inactive']}</div>
            <div class="stat-label">Inactive</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">{$statistics['total_capacity']}</div>
            <div class="stat-label">Total Capacity</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center">S.No</th>
                <th>Vehicle No</th>
                <th>Type</th>
                <th>Driver</th>
                <th>Phone</th>
                <th class="text-center">Capacity</th>
                <th>Insurance Expiry</th>
                <th>Insurance Status</th>
                <th>Fitness Expiry</th>
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
     * Build assignment report HTML.
     *
     * @param Collection $assignments
     * @param array $statistics
     * @param array $filters
     * @return string
     */
    protected function buildAssignmentReportHtml(Collection $assignments, array $statistics, array $filters): string
    {
        $schoolName = config('app.name', 'Smart School');
        $generatedAt = now()->format('F j, Y \a\t g:i A');

        $rows = '';
        $sn = 1;
        foreach ($assignments as $assignment) {
            $student = $assignment->student;
            $studentName = $student?->user 
                ? "{$student->user->first_name} {$student->user->last_name}" 
                : '';
            $statusClass = $assignment->is_active ? 'status-active' : 'status-inactive';
            $statusText = $assignment->is_active ? 'Active' : 'Inactive';
            $admissionNumber = $student?->admission_number ?? '';
            $className = $student?->schoolClass?->name ?? '';
            $sectionName = $student?->section?->name ?? '';
            $routeName = $assignment->route?->name ?? '';
            $stopPointName = $assignment->stopPoint?->name ?? '';
            
            $rows .= <<<HTML
<tr>
    <td class="text-center">{$sn}</td>
    <td>{$admissionNumber}</td>
    <td>{$studentName}</td>
    <td>{$className}</td>
    <td>{$sectionName}</td>
    <td>{$routeName}</td>
    <td>{$stopPointName}</td>
    <td>{$assignment->pickup_time}</td>
    <td>{$assignment->drop_time}</td>
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
    <title>Transport Assignments Report</title>
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
        <h2>Transport Assignments Report</h2>
    </div>

    <div class="stats">
        <div class="stat-box">
            <div class="stat-value">{$statistics['total']}</div>
            <div class="stat-label">Total Assignments</div>
        </div>
        <div class="stat-box">
            <div class="stat-value" style="color: #28a745;">{$statistics['active']}</div>
            <div class="stat-label">Active</div>
        </div>
        <div class="stat-box">
            <div class="stat-value" style="color: #dc3545;">{$statistics['inactive']}</div>
            <div class="stat-label">Inactive</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">{$statistics['route_count']}</div>
            <div class="stat-label">Routes</div>
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
                <th>Route</th>
                <th>Stop Point</th>
                <th>Pickup</th>
                <th>Drop</th>
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
     * Build route details report HTML.
     *
     * @param Collection $routes
     * @param array $statistics
     * @return string
     */
    protected function buildRouteDetailsReportHtml(Collection $routes, array $statistics): string
    {
        $schoolName = config('app.name', 'Smart School');
        $generatedAt = now()->format('F j, Y \a\t g:i A');

        $rows = '';
        $sn = 1;
        foreach ($routes as $route) {
            $statusClass = $route->is_active ? 'status-active' : 'status-inactive';
            $statusText = $route->is_active ? 'Active' : 'Inactive';
            $stopCount = $route->stopPoints->count();
            $vehicleNumber = $route->vehicle?->vehicle_number ?? '';
            $driverName = $route->vehicle?->driver_name ?? '';
            
            $rows .= <<<HTML
<tr>
    <td class="text-center">{$sn}</td>
    <td>{$route->name}</td>
    <td>{$route->start_point}</td>
    <td>{$route->end_point}</td>
    <td class="text-center">{$stopCount}</td>
    <td class="text-center">{$route->total_distance} km</td>
    <td>{$vehicleNumber}</td>
    <td>{$driverName}</td>
    <td>{$route->start_time}</td>
    <td>{$route->end_time}</td>
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
    <title>Transport Routes Report</title>
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
        <h2>Transport Routes Report</h2>
    </div>

    <div class="stats">
        <div class="stat-box">
            <div class="stat-value">{$statistics['total']}</div>
            <div class="stat-label">Total Routes</div>
        </div>
        <div class="stat-box">
            <div class="stat-value" style="color: #28a745;">{$statistics['active']}</div>
            <div class="stat-label">Active</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">{$statistics['total_stops']}</div>
            <div class="stat-label">Total Stops</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">{$statistics['total_distance']} km</div>
            <div class="stat-label">Total Distance</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center">S.No</th>
                <th>Route Name</th>
                <th>Start Point</th>
                <th>End Point</th>
                <th class="text-center">Stops</th>
                <th class="text-center">Distance</th>
                <th>Vehicle</th>
                <th>Driver</th>
                <th>Start Time</th>
                <th>End Time</th>
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
    <title>Transport Summary Report</title>
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
        <h2>Transport Summary Report</h2>
        <p style="margin-top: 5px; color: #666;">As of {$generatedAt}</p>
    </div>

    <div class="section">
        <h3 class="section-title">Routes</h3>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total Routes</div>
                <div class="stat-value">{$statistics['total_routes']}</div>
            </div>
            <div class="stat-card success">
                <div class="stat-label">Active Routes</div>
                <div class="stat-value">{$statistics['active_routes']}</div>
            </div>
        </div>
    </div>

    <div class="section">
        <h3 class="section-title">Vehicles</h3>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total Vehicles</div>
                <div class="stat-value">{$statistics['total_vehicles']}</div>
            </div>
            <div class="stat-card success">
                <div class="stat-label">Active Vehicles</div>
                <div class="stat-value">{$statistics['active_vehicles']}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Seating Capacity</div>
                <div class="stat-value">{$statistics['total_capacity']}</div>
            </div>
        </div>
    </div>

    <div class="section">
        <h3 class="section-title">Student Assignments</h3>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total Assignments</div>
                <div class="stat-value">{$statistics['total_assignments']}</div>
            </div>
            <div class="stat-card success">
                <div class="stat-label">Active Assignments</div>
                <div class="stat-value">{$statistics['active_assignments']}</div>
            </div>
            <div class="stat-card warning">
                <div class="stat-label">Capacity Utilization</div>
                <div class="stat-value">{$statistics['utilization']}%</div>
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
}
