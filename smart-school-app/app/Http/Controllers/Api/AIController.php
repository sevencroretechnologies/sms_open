<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\ExamMark;
use App\Models\Student;
use App\Services\AIService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AIController extends Controller
{
    protected AIService $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function predictPerformance(Request $request): JsonResponse
    {
        $request->validate([
            'student_id' => 'required|integer|exists:students,id',
            'include_attendance' => 'boolean',
            'include_grades' => 'boolean',
        ]);

        $studentId = $request->input('student_id');
        $includeAttendance = $request->boolean('include_attendance', true);
        $includeGrades = $request->boolean('include_grades', true);

        $student = Student::with(['user', 'schoolClass', 'section'])->find($studentId);

        if (!$student) {
            return $this->notFoundResponse('Student not found');
        }

        $studentData = [
            'name' => $student->user->name ?? 'Unknown',
            'class' => $student->schoolClass->name ?? 'Unknown',
            'section' => $student->section->name ?? 'Unknown',
        ];

        if ($includeAttendance) {
            $attendanceStats = Attendance::where('student_id', $studentId)
                ->selectRaw('
                    COUNT(*) as total_days,
                    SUM(CASE WHEN attendance_type_id = 1 THEN 1 ELSE 0 END) as present_days,
                    SUM(CASE WHEN attendance_type_id = 2 THEN 1 ELSE 0 END) as absent_days,
                    SUM(CASE WHEN attendance_type_id = 3 THEN 1 ELSE 0 END) as late_days
                ')
                ->first();

            $studentData['attendance'] = [
                'total_days' => $attendanceStats->total_days ?? 0,
                'present_days' => $attendanceStats->present_days ?? 0,
                'absent_days' => $attendanceStats->absent_days ?? 0,
                'late_days' => $attendanceStats->late_days ?? 0,
                'attendance_percentage' => $attendanceStats->total_days > 0
                    ? round(($attendanceStats->present_days / $attendanceStats->total_days) * 100, 2)
                    : 0,
            ];
        }

        if ($includeGrades) {
            $examMarks = ExamMark::where('student_id', $studentId)
                ->with(['examSchedule.subject', 'examSchedule.exam'])
                ->get();

            $grades = [];
            foreach ($examMarks as $mark) {
                $subjectName = $mark->examSchedule->subject->name ?? 'Unknown';
                $fullMarks = $mark->examSchedule->full_marks ?? 100;
                $percentage = $fullMarks > 0 ? round(($mark->obtained_marks / $fullMarks) * 100, 2) : 0;
                
                $grades[] = [
                    'subject' => $subjectName,
                    'exam' => $mark->examSchedule->exam->name ?? 'Unknown',
                    'obtained_marks' => $mark->obtained_marks,
                    'full_marks' => $fullMarks,
                    'percentage' => $percentage,
                ];
            }

            $studentData['grades'] = $grades;
            $studentData['average_percentage'] = count($grades) > 0
                ? round(array_sum(array_column($grades, 'percentage')) / count($grades), 2)
                : 0;
        }

        $prediction = $this->aiService->predictStudentPerformance($studentData);

        return $this->successResponse([
            'student' => $studentData,
            'prediction' => $prediction,
        ], 'Performance prediction generated successfully');
    }

    public function generateReportCardComment(Request $request): JsonResponse
    {
        $request->validate([
            'student_name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'grade' => 'required|string|max:10',
            'attendance' => 'required|numeric|min:0|max:100',
            'strengths' => 'array',
            'strengths.*' => 'string|max:255',
            'weaknesses' => 'array',
            'weaknesses.*' => 'string|max:255',
        ]);

        $data = $request->only(['student_name', 'subject', 'grade', 'attendance', 'strengths', 'weaknesses']);
        $comment = $this->aiService->generateReportCardComment($data);

        return $this->successResponse([
            'comment' => $comment,
        ], 'Report card comment generated successfully');
    }

    public function generateParentMessage(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'required|in:fee_reminder,meeting,progress',
            'student_name' => 'required|string|max:255',
            'parent_name' => 'required|string|max:255',
            'custom_notes' => 'nullable|string|max:1000',
            'amount_due' => 'required_if:type,fee_reminder|nullable|numeric',
            'due_date' => 'required_if:type,fee_reminder|nullable|date',
            'meeting_date' => 'required_if:type,meeting|nullable|date',
            'meeting_time' => 'required_if:type,meeting|nullable|string',
            'venue' => 'required_if:type,meeting|nullable|string|max:255',
        ]);

        $data = $request->only([
            'type', 'student_name', 'parent_name', 'custom_notes',
            'amount_due', 'due_date', 'meeting_date', 'meeting_time', 'venue'
        ]);

        $message = $this->aiService->generateParentMessage($data);

        return $this->successResponse([
            'message' => $message,
        ], 'Parent message generated successfully');
    }

    public function gradeAssignment(Request $request): JsonResponse
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'topic' => 'required|string|max:255',
            'rubric' => 'nullable|string|max:2000',
            'student_submission' => 'required|string|max:10000',
        ]);

        $data = $request->only(['subject', 'topic', 'rubric', 'student_submission']);
        $result = $this->aiService->gradeAssignment($data);

        return $this->successResponse($result, 'Assignment graded successfully');
    }

    public function generateStudyPlan(Request $request): JsonResponse
    {
        $request->validate([
            'student_id' => 'nullable|integer|exists:students,id',
            'target_exam' => 'required|string|max:255',
            'weak_subjects' => 'required|array|min:1',
            'weak_subjects.*' => 'string|max:255',
            'available_hours' => 'required|numeric|min:1|max:12',
            'duration_days' => 'nullable|integer|min:7|max:90',
            'current_level' => 'nullable|string|in:beginner,intermediate,advanced',
        ]);

        $data = $request->only([
            'target_exam', 'weak_subjects', 'available_hours', 'duration_days', 'current_level'
        ]);

        $studyPlan = $this->aiService->generateStudyPlan($data);

        return $this->successResponse($studyPlan, 'Study plan generated successfully');
    }

    public function generateQuestions(Request $request): JsonResponse
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'topic' => 'required|string|max:255',
            'class' => 'nullable|string|max:50',
            'difficulty' => 'required|in:easy,medium,hard,mixed',
            'count' => 'required|integer|min:1|max:50',
            'question_types' => 'required|array|min:1',
            'question_types.*' => 'in:mcq,short,long',
        ]);

        $data = $request->only(['subject', 'topic', 'class', 'difficulty', 'count', 'question_types']);
        $questions = $this->aiService->generateQuestions($data);

        return $this->successResponse($questions, 'Questions generated successfully');
    }

    public function optimizeTimetable(Request $request): JsonResponse
    {
        $request->validate([
            'teachers' => 'required|array|min:1',
            'teachers.*.name' => 'required|string|max:255',
            'teachers.*.subjects' => 'required|array|min:1',
            'subjects' => 'required|array|min:1',
            'subjects.*.name' => 'required|string|max:255',
            'subjects.*.periods_per_week' => 'required|integer|min:1|max:10',
            'rooms' => 'required|array|min:1',
            'constraints' => 'nullable|array',
        ]);

        $data = $request->only(['teachers', 'subjects', 'rooms', 'constraints']);
        $optimizedTimetable = $this->aiService->optimizeTimetable($data);

        return $this->successResponse($optimizedTimetable, 'Timetable optimized successfully');
    }

    public function provideCareerGuidance(Request $request): JsonResponse
    {
        $request->validate([
            'student_id' => 'nullable|integer|exists:students,id',
            'interests' => 'required|array|min:1',
            'interests.*' => 'string|max:255',
            'strong_subjects' => 'required|array|min:1',
            'strong_subjects.*' => 'string|max:255',
            'aspirations' => 'nullable|string|max:1000',
            'current_class' => 'nullable|string|max:50',
        ]);

        $data = $request->only(['interests', 'strong_subjects', 'aspirations', 'current_class']);

        if ($request->filled('student_id')) {
            $student = Student::with(['schoolClass'])->find($request->input('student_id'));
            if ($student) {
                $data['current_class'] = $student->schoolClass->name ?? $data['current_class'];
            }
        }

        $guidance = $this->aiService->provideCareerGuidance($data);

        return $this->successResponse($guidance, 'Career guidance generated successfully');
    }

    public function generateMeetingSummary(Request $request): JsonResponse
    {
        $request->validate([
            'meeting_notes' => 'required|string|max:10000',
            'attendees' => 'required|array|min:1',
            'attendees.*' => 'string|max:255',
            'action_items' => 'nullable|array',
            'action_items.*' => 'string|max:500',
        ]);

        $data = $request->only(['meeting_notes', 'attendees', 'action_items']);
        $summary = $this->aiService->generateMeetingSummary($data);

        return $this->successResponse($summary, 'Meeting summary generated successfully');
    }

    public function checkCurriculumAlignment(Request $request): JsonResponse
    {
        $request->validate([
            'lesson_plan' => 'required|string|max:10000',
            'board' => 'required|in:CBSE,ICSE',
            'class' => 'required|string|max:50',
            'subject' => 'required|string|max:255',
        ]);

        $data = $request->only(['lesson_plan', 'board', 'class', 'subject']);
        $alignment = $this->aiService->checkCurriculumAlignment($data);

        return $this->successResponse($alignment, 'Curriculum alignment checked successfully');
    }
}
