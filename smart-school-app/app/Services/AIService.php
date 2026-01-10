<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://openrouter.ai/api/v1';
    protected string $model = 'openai/gpt-4o-mini';

    public function __construct()
    {
        $this->apiKey = config('services.openrouter.api_key', env('OPENROUTER_API_KEY', ''));
    }

    protected function chat(string $systemPrompt, string $userMessage, float $temperature = 0.7): ?string
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'HTTP-Referer' => config('app.url', 'http://localhost'),
                'X-Title' => 'Smart School Management System',
            ])->post($this->baseUrl . '/chat/completions', [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userMessage],
                ],
                'temperature' => $temperature,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['choices'][0]['message']['content'] ?? null;
            }

            Log::error('OpenRouter API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('OpenRouter API exception', [
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    public function predictStudentPerformance(array $studentData): array
    {
        $systemPrompt = "You are an educational data analyst specializing in student performance prediction. Analyze the provided student data and provide a risk assessment. Return your response in JSON format with the following structure:
{
    \"risk_level\": \"low|medium|high\",
    \"confidence_score\": 0-100,
    \"areas_of_concern\": [\"list of specific concerns\"],
    \"strengths\": [\"list of strengths\"],
    \"recommendations\": [\"list of actionable recommendations\"],
    \"predicted_outcome\": \"brief prediction statement\"
}";

        $userMessage = "Analyze this student's performance data and predict their academic risk level:\n\n" . json_encode($studentData, JSON_PRETTY_PRINT);

        $response = $this->chat($systemPrompt, $userMessage, 0.3);
        
        if ($response) {
            $jsonStart = strpos($response, '{');
            $jsonEnd = strrpos($response, '}');
            if ($jsonStart !== false && $jsonEnd !== false) {
                $jsonStr = substr($response, $jsonStart, $jsonEnd - $jsonStart + 1);
                $decoded = json_decode($jsonStr, true);
                if ($decoded) {
                    return $decoded;
                }
            }
        }

        return [
            'risk_level' => 'unknown',
            'confidence_score' => 0,
            'areas_of_concern' => [],
            'strengths' => [],
            'recommendations' => ['Unable to analyze data. Please try again.'],
            'predicted_outcome' => 'Analysis failed',
        ];
    }

    public function generateReportCardComment(array $data): string
    {
        $systemPrompt = "You are an experienced teacher writing personalized report card comments. Write professional, encouraging, and constructive comments that highlight achievements while suggesting areas for improvement. Keep comments between 2-4 sentences. Be specific and avoid generic phrases.";

        $userMessage = "Write a report card comment for:\n" .
            "Student Name: {$data['student_name']}\n" .
            "Subject: {$data['subject']}\n" .
            "Grade: {$data['grade']}\n" .
            "Attendance: {$data['attendance']}%\n" .
            "Strengths: " . implode(', ', $data['strengths'] ?? []) . "\n" .
            "Areas for Improvement: " . implode(', ', $data['weaknesses'] ?? []);

        $response = $this->chat($systemPrompt, $userMessage, 0.7);
        
        return $response ?? "Unable to generate comment. Please try again.";
    }

    public function generateParentMessage(array $data): string
    {
        $templates = [
            'fee_reminder' => "You are a school administrator writing a polite but firm fee reminder to parents. Be professional, include the amount due and deadline, and offer assistance if needed.",
            'meeting' => "You are a school administrator inviting parents to a meeting. Be warm and professional, include the purpose, date, time, and location, and emphasize the importance of attendance.",
            'progress' => "You are a teacher writing a progress update to parents. Be positive and constructive, highlight achievements, mention areas for improvement, and suggest ways parents can help at home.",
        ];

        $systemPrompt = $templates[$data['type']] ?? $templates['progress'];
        
        $userMessage = "Generate a message for:\n" .
            "Student Name: {$data['student_name']}\n" .
            "Parent Name: {$data['parent_name']}\n" .
            "Message Type: {$data['type']}\n" .
            "Additional Details: " . ($data['custom_notes'] ?? 'None');

        if ($data['type'] === 'fee_reminder' && isset($data['amount_due'])) {
            $userMessage .= "\nAmount Due: {$data['amount_due']}\nDue Date: {$data['due_date']}";
        }

        if ($data['type'] === 'meeting' && isset($data['meeting_date'])) {
            $userMessage .= "\nMeeting Date: {$data['meeting_date']}\nMeeting Time: {$data['meeting_time']}\nVenue: {$data['venue']}";
        }

        $response = $this->chat($systemPrompt, $userMessage, 0.7);
        
        return $response ?? "Unable to generate message. Please try again.";
    }

    public function gradeAssignment(array $data): array
    {
        $systemPrompt = "You are an experienced teacher grading student assignments. Evaluate the submission based on the provided rubric and topic. Return your response in JSON format:
{
    \"score\": 0-100,
    \"grade\": \"A+/A/B+/B/C+/C/D/F\",
    \"feedback\": {
        \"strengths\": [\"list of what was done well\"],
        \"improvements\": [\"list of areas to improve\"],
        \"detailed_comments\": \"paragraph with detailed feedback\"
    },
    \"rubric_scores\": {
        \"content\": 0-25,
        \"organization\": 0-25,
        \"language\": 0-25,
        \"creativity\": 0-25
    }
}";

        $userMessage = "Grade this assignment:\n" .
            "Subject: {$data['subject']}\n" .
            "Topic: {$data['topic']}\n" .
            "Rubric: " . ($data['rubric'] ?? 'Standard academic rubric') . "\n\n" .
            "Student Submission:\n{$data['student_submission']}";

        $response = $this->chat($systemPrompt, $userMessage, 0.3);
        
        if ($response) {
            $jsonStart = strpos($response, '{');
            $jsonEnd = strrpos($response, '}');
            if ($jsonStart !== false && $jsonEnd !== false) {
                $jsonStr = substr($response, $jsonStart, $jsonEnd - $jsonStart + 1);
                $decoded = json_decode($jsonStr, true);
                if ($decoded) {
                    return $decoded;
                }
            }
        }

        return [
            'score' => 0,
            'grade' => 'N/A',
            'feedback' => [
                'strengths' => [],
                'improvements' => [],
                'detailed_comments' => 'Unable to grade assignment. Please try again.',
            ],
            'rubric_scores' => [
                'content' => 0,
                'organization' => 0,
                'language' => 0,
                'creativity' => 0,
            ],
        ];
    }

    public function generateStudyPlan(array $data): array
    {
        $systemPrompt = "You are an educational consultant creating personalized study plans. Create a detailed, day-by-day study schedule that is realistic and achievable. Return your response in JSON format:
{
    \"overview\": \"brief overview of the study plan\",
    \"total_hours\": number,
    \"daily_schedule\": [
        {
            \"day\": 1,
            \"date\": \"Day 1\",
            \"subjects\": [
                {
                    \"subject\": \"subject name\",
                    \"topic\": \"specific topic\",
                    \"duration_minutes\": 60,
                    \"resources\": [\"list of recommended resources\"],
                    \"activities\": [\"list of study activities\"]
                }
            ],
            \"breaks\": [\"suggested break activities\"],
            \"total_study_hours\": number
        }
    ],
    \"tips\": [\"general study tips\"],
    \"milestones\": [\"weekly milestones to track progress\"]
}";

        $userMessage = "Create a study plan for:\n" .
            "Target Exam: {$data['target_exam']}\n" .
            "Weak Subjects: " . implode(', ', $data['weak_subjects'] ?? []) . "\n" .
            "Available Hours Per Day: {$data['available_hours']}\n" .
            "Duration: " . ($data['duration_days'] ?? 30) . " days\n" .
            "Current Level: " . ($data['current_level'] ?? 'Intermediate');

        $response = $this->chat($systemPrompt, $userMessage, 0.5);
        
        if ($response) {
            $jsonStart = strpos($response, '{');
            $jsonEnd = strrpos($response, '}');
            if ($jsonStart !== false && $jsonEnd !== false) {
                $jsonStr = substr($response, $jsonStart, $jsonEnd - $jsonStart + 1);
                $decoded = json_decode($jsonStr, true);
                if ($decoded) {
                    return $decoded;
                }
            }
        }

        return [
            'overview' => 'Unable to generate study plan. Please try again.',
            'total_hours' => 0,
            'daily_schedule' => [],
            'tips' => [],
            'milestones' => [],
        ];
    }

    public function generateQuestions(array $data): array
    {
        $systemPrompt = "You are an experienced teacher creating exam questions. Generate high-quality questions with answers and marking schemes. Return your response in JSON format:
{
    \"questions\": [
        {
            \"id\": 1,
            \"type\": \"mcq|short|long\",
            \"question\": \"question text\",
            \"options\": [\"A. option1\", \"B. option2\", \"C. option3\", \"D. option4\"] (for MCQ only),
            \"correct_answer\": \"answer\",
            \"explanation\": \"explanation of the answer\",
            \"marks\": number,
            \"difficulty\": \"easy|medium|hard\"
        }
    ],
    \"total_marks\": number,
    \"suggested_time_minutes\": number
}";

        $questionTypes = implode(', ', $data['question_types'] ?? ['mcq', 'short', 'long']);
        
        $userMessage = "Generate {$data['count']} questions for:\n" .
            "Subject: {$data['subject']}\n" .
            "Topic: {$data['topic']}\n" .
            "Class/Grade: {$data['class']}\n" .
            "Difficulty: {$data['difficulty']}\n" .
            "Question Types: {$questionTypes}";

        $response = $this->chat($systemPrompt, $userMessage, 0.6);
        
        if ($response) {
            $jsonStart = strpos($response, '{');
            $jsonEnd = strrpos($response, '}');
            if ($jsonStart !== false && $jsonEnd !== false) {
                $jsonStr = substr($response, $jsonStart, $jsonEnd - $jsonStart + 1);
                $decoded = json_decode($jsonStr, true);
                if ($decoded) {
                    return $decoded;
                }
            }
        }

        return [
            'questions' => [],
            'total_marks' => 0,
            'suggested_time_minutes' => 0,
        ];
    }

    public function optimizeTimetable(array $data): array
    {
        $systemPrompt = "You are a school scheduling expert. Analyze the provided constraints and suggest an optimal timetable arrangement. Return your response in JSON format:
{
    \"optimized_schedule\": [
        {
            \"day\": \"Monday\",
            \"periods\": [
                {
                    \"period\": 1,
                    \"start_time\": \"08:00\",
                    \"end_time\": \"08:45\",
                    \"subject\": \"subject name\",
                    \"teacher\": \"teacher name\",
                    \"room\": \"room number\"
                }
            ]
        }
    ],
    \"conflicts_resolved\": [\"list of conflicts that were resolved\"],
    \"optimization_notes\": [\"notes about the optimization\"],
    \"suggestions\": [\"additional suggestions for improvement\"]
}";

        $userMessage = "Optimize this timetable:\n" .
            "Teachers: " . json_encode($data['teachers'] ?? []) . "\n" .
            "Subjects: " . json_encode($data['subjects'] ?? []) . "\n" .
            "Rooms: " . json_encode($data['rooms'] ?? []) . "\n" .
            "Constraints: " . json_encode($data['constraints'] ?? []);

        $response = $this->chat($systemPrompt, $userMessage, 0.4);
        
        if ($response) {
            $jsonStart = strpos($response, '{');
            $jsonEnd = strrpos($response, '}');
            if ($jsonStart !== false && $jsonEnd !== false) {
                $jsonStr = substr($response, $jsonStart, $jsonEnd - $jsonStart + 1);
                $decoded = json_decode($jsonStr, true);
                if ($decoded) {
                    return $decoded;
                }
            }
        }

        return [
            'optimized_schedule' => [],
            'conflicts_resolved' => [],
            'optimization_notes' => ['Unable to optimize timetable. Please try again.'],
            'suggestions' => [],
        ];
    }

    public function provideCareerGuidance(array $data): array
    {
        $systemPrompt = "You are a career counselor specializing in student guidance. Provide personalized career advice based on the student's interests, strengths, and aspirations. Return your response in JSON format:
{
    \"recommended_careers\": [
        {
            \"career\": \"career name\",
            \"match_score\": 0-100,
            \"description\": \"brief description\",
            \"required_skills\": [\"list of skills needed\"],
            \"education_path\": \"recommended education path\",
            \"average_salary_range\": \"salary range\",
            \"job_outlook\": \"growth prospects\"
        }
    ],
    \"skill_development\": [
        {
            \"skill\": \"skill name\",
            \"importance\": \"high|medium|low\",
            \"how_to_develop\": \"suggestions\"
        }
    ],
    \"recommended_courses\": [\"list of courses/certifications\"],
    \"next_steps\": [\"immediate action items\"],
    \"resources\": [\"helpful resources and links\"]
}";

        $userMessage = "Provide career guidance for:\n" .
            "Interests: " . implode(', ', $data['interests'] ?? []) . "\n" .
            "Strong Subjects: " . implode(', ', $data['strong_subjects'] ?? []) . "\n" .
            "Aspirations: " . ($data['aspirations'] ?? 'Not specified') . "\n" .
            "Current Grade/Class: " . ($data['current_class'] ?? 'Not specified');

        $response = $this->chat($systemPrompt, $userMessage, 0.6);
        
        if ($response) {
            $jsonStart = strpos($response, '{');
            $jsonEnd = strrpos($response, '}');
            if ($jsonStart !== false && $jsonEnd !== false) {
                $jsonStr = substr($response, $jsonStart, $jsonEnd - $jsonStart + 1);
                $decoded = json_decode($jsonStr, true);
                if ($decoded) {
                    return $decoded;
                }
            }
        }

        return [
            'recommended_careers' => [],
            'skill_development' => [],
            'recommended_courses' => [],
            'next_steps' => ['Unable to provide guidance. Please try again.'],
            'resources' => [],
        ];
    }

    public function generateMeetingSummary(array $data): array
    {
        $systemPrompt = "You are an administrative assistant summarizing parent-teacher meetings. Create a clear, professional summary with key points and action items. Return your response in JSON format:
{
    \"summary\": \"brief executive summary\",
    \"key_points\": [\"list of main discussion points\"],
    \"student_progress\": {
        \"academic\": \"academic progress summary\",
        \"behavioral\": \"behavioral observations\",
        \"social\": \"social development notes\"
    },
    \"action_items\": [
        {
            \"item\": \"action item description\",
            \"responsible_party\": \"teacher|parent|student\",
            \"deadline\": \"suggested deadline\"
        }
    ],
    \"follow_up\": {
        \"next_meeting_suggested\": \"date suggestion\",
        \"topics_for_next_meeting\": [\"topics to discuss\"]
    },
    \"additional_notes\": \"any other relevant information\"
}";

        $userMessage = "Summarize this parent-teacher meeting:\n" .
            "Attendees: " . implode(', ', $data['attendees'] ?? []) . "\n" .
            "Meeting Notes:\n{$data['meeting_notes']}\n" .
            "Action Items Discussed: " . implode(', ', $data['action_items'] ?? []);

        $response = $this->chat($systemPrompt, $userMessage, 0.4);
        
        if ($response) {
            $jsonStart = strpos($response, '{');
            $jsonEnd = strrpos($response, '}');
            if ($jsonStart !== false && $jsonEnd !== false) {
                $jsonStr = substr($response, $jsonStart, $jsonEnd - $jsonStart + 1);
                $decoded = json_decode($jsonStr, true);
                if ($decoded) {
                    return $decoded;
                }
            }
        }

        return [
            'summary' => 'Unable to generate summary. Please try again.',
            'key_points' => [],
            'student_progress' => [
                'academic' => '',
                'behavioral' => '',
                'social' => '',
            ],
            'action_items' => [],
            'follow_up' => [
                'next_meeting_suggested' => '',
                'topics_for_next_meeting' => [],
            ],
            'additional_notes' => '',
        ];
    }

    public function checkCurriculumAlignment(array $data): array
    {
        $systemPrompt = "You are a curriculum specialist familiar with CBSE and ICSE educational standards. Analyze the lesson plan and check its alignment with the specified board's curriculum. Return your response in JSON format:
{
    \"alignment_score\": 0-100,
    \"board\": \"CBSE|ICSE\",
    \"class\": \"class/grade\",
    \"subject\": \"subject name\",
    \"covered_topics\": [
        {
            \"topic\": \"topic name\",
            \"alignment\": \"full|partial|missing\",
            \"curriculum_reference\": \"reference to curriculum document\"
        }
    ],
    \"missing_topics\": [\"topics from curriculum not covered\"],
    \"extra_topics\": [\"topics covered but not in curriculum\"],
    \"learning_objectives_met\": [\"list of objectives met\"],
    \"suggestions\": [\"suggestions for better alignment\"],
    \"overall_assessment\": \"detailed assessment paragraph\"
}";

        $userMessage = "Check curriculum alignment for:\n" .
            "Board: {$data['board']}\n" .
            "Class: {$data['class']}\n" .
            "Subject: {$data['subject']}\n\n" .
            "Lesson Plan:\n{$data['lesson_plan']}";

        $response = $this->chat($systemPrompt, $userMessage, 0.3);
        
        if ($response) {
            $jsonStart = strpos($response, '{');
            $jsonEnd = strrpos($response, '}');
            if ($jsonStart !== false && $jsonEnd !== false) {
                $jsonStr = substr($response, $jsonStart, $jsonEnd - $jsonStart + 1);
                $decoded = json_decode($jsonStr, true);
                if ($decoded) {
                    return $decoded;
                }
            }
        }

        return [
            'alignment_score' => 0,
            'board' => $data['board'] ?? 'Unknown',
            'class' => $data['class'] ?? 'Unknown',
            'subject' => $data['subject'] ?? 'Unknown',
            'covered_topics' => [],
            'missing_topics' => [],
            'extra_topics' => [],
            'learning_objectives_met' => [],
            'suggestions' => ['Unable to check alignment. Please try again.'],
            'overall_assessment' => 'Analysis failed',
        ];
    }
}
