<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\Attendance;
use App\Models\ExamMark;
use App\Models\ExamSchedule;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the teacher dashboard with statistics and data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $teacher = Auth::user();
        $currentSession = AcademicSession::getCurrentSession();

        $myClasses = $this->getMyClasses($teacher);
        $todaySchedule = $this->getTodaySchedule($teacher);
        $attendanceSummary = $this->getAttendanceSummary($teacher);
        $recentActivities = $this->getRecentActivities($teacher);
        $upcomingExams = $this->getUpcomingExams($teacher);
        $chartData = $this->getChartData($teacher);

        return view('teacher.dashboard', compact(
            'teacher',
            'currentSession',
            'myClasses',
            'todaySchedule',
            'attendanceSummary',
            'recentActivities',
            'upcomingExams',
            'chartData'
        ));
    }

    /**
     * Get the classes assigned to this teacher.
     *
     * @param  \App\Models\User  $teacher
     * @return \Illuminate\Support\Collection
     */
    protected function getMyClasses($teacher): \Illuminate\Support\Collection
    {
        $classTeacherSections = Section::where('class_teacher_id', $teacher->id)
            ->with(['schoolClass', 'students' => function ($query) {
                $query->active();
            }])
            ->active()
            ->get();

        $subjectClasses = DB::table('class_subjects')
            ->where('teacher_id', $teacher->id)
            ->join('school_classes', 'class_subjects.class_id', '=', 'school_classes.id')
            ->join('sections', 'class_subjects.section_id', '=', 'sections.id')
            ->join('subjects', 'class_subjects.subject_id', '=', 'subjects.id')
            ->select(
                'school_classes.id as class_id',
                'school_classes.name as class_name',
                'school_classes.display_name as class_display_name',
                'sections.id as section_id',
                'sections.name as section_name',
                'sections.display_name as section_display_name',
                'subjects.id as subject_id',
                'subjects.name as subject_name'
            )
            ->get();

        $classes = collect();

        foreach ($classTeacherSections as $section) {
            $classes->push([
                'class_id' => $section->class_id,
                'class_name' => $section->schoolClass->display_name,
                'section_id' => $section->id,
                'section_name' => $section->display_name,
                'student_count' => $section->students->count(),
                'is_class_teacher' => true,
                'subject' => 'Class Teacher',
            ]);
        }

        foreach ($subjectClasses as $subjectClass) {
            $studentCount = Student::where('class_id', $subjectClass->class_id)
                ->where('section_id', $subjectClass->section_id)
                ->active()
                ->count();

            $exists = $classes->first(function ($item) use ($subjectClass) {
                return $item['class_id'] == $subjectClass->class_id 
                    && $item['section_id'] == $subjectClass->section_id
                    && $item['subject'] == $subjectClass->subject_name;
            });

            if (!$exists) {
                $classes->push([
                    'class_id' => $subjectClass->class_id,
                    'class_name' => $subjectClass->class_display_name,
                    'section_id' => $subjectClass->section_id,
                    'section_name' => $subjectClass->section_display_name,
                    'student_count' => $studentCount,
                    'is_class_teacher' => false,
                    'subject' => $subjectClass->subject_name,
                ]);
            }
        }

        return $classes;
    }

    /**
     * Get today's timetable schedule for the teacher.
     *
     * @param  \App\Models\User  $teacher
     * @return \Illuminate\Support\Collection
     */
    protected function getTodaySchedule($teacher): \Illuminate\Support\Collection
    {
        $today = Carbon::now()->dayOfWeek;
        $dayNames = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
        $dayName = $dayNames[$today];

        $schedule = DB::table('class_timetables')
            ->where('teacher_id', $teacher->id)
            ->where('day_of_week', $dayName)
            ->join('school_classes', 'class_timetables.class_id', '=', 'school_classes.id')
            ->join('sections', 'class_timetables.section_id', '=', 'sections.id')
            ->join('subjects', 'class_timetables.subject_id', '=', 'subjects.id')
            ->select(
                'class_timetables.*',
                'school_classes.display_name as class_name',
                'sections.display_name as section_name',
                'subjects.name as subject_name'
            )
            ->orderBy('class_timetables.start_time')
            ->get();

        return $schedule->map(function ($item) {
            return [
                'period' => $item->period ?? 'N/A',
                'time' => Carbon::parse($item->start_time)->format('h:i A') . ' - ' . Carbon::parse($item->end_time)->format('h:i A'),
                'class' => $item->class_name . ' - ' . $item->section_name,
                'subject' => $item->subject_name,
                'room' => $item->room_number ?? 'N/A',
            ];
        });
    }

    /**
     * Get attendance summary for teacher's classes.
     *
     * @param  \App\Models\User  $teacher
     * @return array
     */
    protected function getAttendanceSummary($teacher): array
    {
        $today = Carbon::today();
        $classIds = $this->getTeacherClassIds($teacher);
        $sectionIds = $this->getTeacherSectionIds($teacher);

        if (empty($classIds) || empty($sectionIds)) {
            return [
                'total_students' => 0,
                'present_today' => 0,
                'absent_today' => 0,
                'late_today' => 0,
                'attendance_percentage' => '0%',
                'classes_marked' => 0,
                'classes_pending' => 0,
            ];
        }

        $totalStudents = Student::whereIn('class_id', $classIds)
            ->whereIn('section_id', $sectionIds)
            ->active()
            ->count();

        $todayAttendance = Attendance::whereDate('attendance_date', $today)
            ->whereIn('class_id', $classIds)
            ->whereIn('section_id', $sectionIds)
            ->with('attendanceType')
            ->get();

        $presentToday = $todayAttendance->filter(function ($attendance) {
            return $attendance->attendanceType && strtolower($attendance->attendanceType->name) === 'present';
        })->count();

        $absentToday = $todayAttendance->filter(function ($attendance) {
            return $attendance->attendanceType && strtolower($attendance->attendanceType->name) === 'absent';
        })->count();

        $lateToday = $todayAttendance->filter(function ($attendance) {
            return $attendance->attendanceType && strtolower($attendance->attendanceType->name) === 'late';
        })->count();

        $attendancePercentage = $totalStudents > 0 
            ? round(($presentToday / $totalStudents) * 100) . '%' 
            : '0%';

        $uniqueSectionsMarked = $todayAttendance->unique(function ($item) {
            return $item->class_id . '-' . $item->section_id;
        })->count();

        $totalSections = count(array_unique($sectionIds));

        return [
            'total_students' => $totalStudents,
            'present_today' => $presentToday,
            'absent_today' => $absentToday,
            'late_today' => $lateToday,
            'attendance_percentage' => $attendancePercentage,
            'classes_marked' => $uniqueSectionsMarked,
            'classes_pending' => max(0, $totalSections - $uniqueSectionsMarked),
        ];
    }

    /**
     * Get recent activities for the teacher.
     *
     * @param  \App\Models\User  $teacher
     * @return array
     */
    protected function getRecentActivities($teacher): array
    {
        $activities = [];

        $recentAttendance = Attendance::where('marked_by', $teacher->id)
            ->with(['schoolClass', 'section'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->unique(function ($item) {
                return $item->attendance_date->format('Y-m-d') . '-' . $item->class_id . '-' . $item->section_id;
            })
            ->take(3);

        foreach ($recentAttendance as $attendance) {
            $activities[] = [
                'type' => 'attendance',
                'icon' => 'bi-calendar-check',
                'color' => 'success',
                'message' => "Marked attendance for <strong>{$attendance->schoolClass->display_name} - {$attendance->section->display_name}</strong>",
                'time' => $attendance->created_at->diffForHumans(),
                'timestamp' => $attendance->created_at,
            ];
        }

        $classIds = $this->getTeacherClassIds($teacher);
        $sectionIds = $this->getTeacherSectionIds($teacher);

        if (!empty($classIds) && !empty($sectionIds)) {
            $recentMarks = ExamMark::whereIn('class_id', $classIds)
                ->whereIn('section_id', $sectionIds)
                ->with(['examSchedule.exam', 'examSchedule.subject', 'student.user'])
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get()
                ->unique(function ($item) {
                    return $item->exam_schedule_id;
                })
                ->take(3);

            foreach ($recentMarks as $mark) {
                if ($mark->examSchedule && $mark->examSchedule->exam && $mark->examSchedule->subject) {
                    $activities[] = [
                        'type' => 'marks',
                        'icon' => 'bi-journal-text',
                        'color' => 'primary',
                        'message' => "Entered marks for <strong>{$mark->examSchedule->exam->name}</strong> - {$mark->examSchedule->subject->name}",
                        'time' => $mark->created_at->diffForHumans(),
                        'timestamp' => $mark->created_at,
                    ];
                }
            }
        }

        usort($activities, function ($a, $b) {
            return $b['timestamp'] <=> $a['timestamp'];
        });

        return array_slice($activities, 0, 5);
    }

    /**
     * Get upcoming exams for teacher's classes.
     *
     * @param  \App\Models\User  $teacher
     * @return \Illuminate\Support\Collection
     */
    protected function getUpcomingExams($teacher): \Illuminate\Support\Collection
    {
        $classIds = $this->getTeacherClassIds($teacher);
        $sectionIds = $this->getTeacherSectionIds($teacher);

        if (empty($classIds) || empty($sectionIds)) {
            return collect();
        }

        return ExamSchedule::whereIn('class_id', $classIds)
            ->whereIn('section_id', $sectionIds)
            ->where('exam_date', '>=', Carbon::today())
            ->with(['exam', 'subject', 'schoolClass', 'section'])
            ->orderBy('exam_date')
            ->take(5)
            ->get()
            ->map(function ($schedule) {
                return [
                    'exam_name' => $schedule->exam->name ?? 'N/A',
                    'subject' => $schedule->subject->name ?? 'N/A',
                    'class' => ($schedule->schoolClass->display_name ?? '') . ' - ' . ($schedule->section->display_name ?? ''),
                    'date' => $schedule->exam_date->format('d M Y'),
                    'time' => Carbon::parse($schedule->start_time)->format('h:i A'),
                    'days_remaining' => $schedule->exam_date->diffInDays(Carbon::today()),
                ];
            });
    }

    /**
     * Get chart data for visualizations.
     *
     * @param  \App\Models\User  $teacher
     * @return array
     */
    protected function getChartData($teacher): array
    {
        return [
            'attendanceTrend' => $this->getAttendanceTrend($teacher),
            'classPerformance' => $this->getClassPerformance($teacher),
        ];
    }

    /**
     * Get attendance trend for the last 7 days.
     *
     * @param  \App\Models\User  $teacher
     * @return array
     */
    protected function getAttendanceTrend($teacher): array
    {
        $labels = [];
        $presentData = [];
        $absentData = [];

        $classIds = $this->getTeacherClassIds($teacher);
        $sectionIds = $this->getTeacherSectionIds($teacher);

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->format('D');

            if (empty($classIds) || empty($sectionIds)) {
                $presentData[] = 0;
                $absentData[] = 0;
                continue;
            }

            $dayAttendance = Attendance::whereDate('attendance_date', $date)
                ->whereIn('class_id', $classIds)
                ->whereIn('section_id', $sectionIds)
                ->with('attendanceType')
                ->get();

            $present = $dayAttendance->filter(function ($attendance) {
                return $attendance->attendanceType && strtolower($attendance->attendanceType->name) === 'present';
            })->count();

            $absent = $dayAttendance->filter(function ($attendance) {
                return $attendance->attendanceType && strtolower($attendance->attendanceType->name) === 'absent';
            })->count();

            $presentData[] = $present;
            $absentData[] = $absent;
        }

        return [
            'labels' => $labels,
            'present' => $presentData,
            'absent' => $absentData,
        ];
    }

    /**
     * Get class performance data.
     *
     * @param  \App\Models\User  $teacher
     * @return array
     */
    protected function getClassPerformance($teacher): array
    {
        $classIds = $this->getTeacherClassIds($teacher);
        $sectionIds = $this->getTeacherSectionIds($teacher);

        if (empty($classIds) || empty($sectionIds)) {
            return [
                'labels' => [],
                'averages' => [],
            ];
        }

        $classes = Section::whereIn('id', $sectionIds)
            ->with('schoolClass')
            ->get();

        $labels = [];
        $averages = [];

        foreach ($classes as $section) {
            $className = ($section->schoolClass->display_name ?? '') . '-' . ($section->display_name ?? '');
            $labels[] = $className;

            $avgMarks = ExamMark::where('class_id', $section->class_id)
                ->where('section_id', $section->id)
                ->whereNotNull('obtained_marks')
                ->avg('obtained_marks');

            $averages[] = round($avgMarks ?? 0, 1);
        }

        return [
            'labels' => $labels,
            'averages' => $averages,
        ];
    }

    /**
     * Get class IDs for the teacher.
     *
     * @param  \App\Models\User  $teacher
     * @return array
     */
    protected function getTeacherClassIds($teacher): array
    {
        $classTeacherClasses = Section::where('class_teacher_id', $teacher->id)
            ->pluck('class_id')
            ->toArray();

        $subjectClasses = DB::table('class_subjects')
            ->where('teacher_id', $teacher->id)
            ->pluck('class_id')
            ->toArray();

        return array_unique(array_merge($classTeacherClasses, $subjectClasses));
    }

    /**
     * Get section IDs for the teacher.
     *
     * @param  \App\Models\User  $teacher
     * @return array
     */
    protected function getTeacherSectionIds($teacher): array
    {
        $classTeacherSections = Section::where('class_teacher_id', $teacher->id)
            ->pluck('id')
            ->toArray();

        $subjectSections = DB::table('class_subjects')
            ->where('teacher_id', $teacher->id)
            ->pluck('section_id')
            ->toArray();

        return array_unique(array_merge($classTeacherSections, $subjectSections));
    }
}
