{{-- Timetable Print View --}}
{{-- Print-friendly timetable layout --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timetable - {{ $class->name ?? 'Class' }} {{ $section ? '- Section ' . $section->name : '' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }
        .header h2 {
            font-size: 18px;
            font-weight: normal;
            color: #666;
        }
        .header p {
            color: #888;
            margin-top: 5px;
        }
        .info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #333;
            padding: 8px;
            text-align: center;
            vertical-align: middle;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .time-cell {
            background-color: #f8f8f8;
            font-weight: bold;
            width: 100px;
        }
        .subject-name {
            font-weight: bold;
            color: #333;
        }
        .teacher-name {
            font-size: 10px;
            color: #666;
        }
        .room {
            font-size: 9px;
            color: #888;
        }
        .footer {
            text-align: center;
            font-size: 10px;
            color: #888;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
        }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 14px; cursor: pointer;">
            Print Timetable
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; font-size: 14px; cursor: pointer; margin-left: 10px;">
            Close
        </button>
    </div>

    <div class="header">
        <h1>{{ config('app.name', 'Smart School') }}</h1>
        <h2>Class Timetable</h2>
        <p>{{ $class->name ?? 'Class' }} {{ $section ? '- Section ' . $section->name : '' }}</p>
    </div>

    <div class="info">
        <span>Academic Session: {{ $academicSession->name ?? 'Current Session' }}</span>
        <span>Generated: {{ now()->format('d M Y, h:i A') }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th>Time</th>
                <th>Monday</th>
                <th>Tuesday</th>
                <th>Wednesday</th>
                <th>Thursday</th>
                <th>Friday</th>
                <th>Saturday</th>
            </tr>
        </thead>
        <tbody>
            @forelse($periods ?? [] as $period)
                <tr>
                    <td class="time-cell">
                        <div>Period {{ $loop->iteration }}</div>
                        <div style="font-size: 10px; font-weight: normal;">{{ $period->start_time }} - {{ $period->end_time }}</div>
                    </td>
                    @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'] as $day)
                        <td>
                            @php
                                $slot = $timetable[$period->id][$day] ?? null;
                            @endphp
                            @if($slot)
                                <div class="subject-name">{{ $slot->subject->name ?? '-' }}</div>
                                <div class="teacher-name">{{ $slot->teacher->user->name ?? $slot->teacher->name ?? '-' }}</div>
                                @if($slot->room)
                                    <div class="room">Room: {{ $slot->room }}</div>
                                @endif
                            @else
                                -
                            @endif
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="padding: 30px; text-align: center; color: #888;">
                        No timetable defined for this class
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>This timetable is subject to change. Please check with the administration for any updates.</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name', 'Smart School') }}. All rights reserved.</p>
    </div>
</body>
</html>
