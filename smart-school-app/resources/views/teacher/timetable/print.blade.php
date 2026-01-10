<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timetable - {{ $teacher->name ?? 'Teacher' }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { text-align: center; margin-bottom: 5px; }
        .subtitle { text-align: center; color: #666; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f5f5f5; }
        .time { font-weight: bold; color: #4f46e5; }
        .subject { font-weight: bold; }
        .class-info { color: #666; font-size: 0.9em; }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <h1>Weekly Timetable</h1>
    <p class="subtitle">{{ $teacher->name ?? 'Teacher' }} | Generated: {{ now()->format('d M Y') }}</p>
    
    <table>
        <thead>
            <tr>
                <th style="width: 100px;">Day</th>
                <th>Schedule</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dayNames as $day)
                <tr>
                    <td style="text-transform: capitalize; font-weight: bold;">{{ $day }}</td>
                    <td>
                        @if(isset($timetable[$day]) && $timetable[$day]->count() > 0)
                            @foreach($timetable[$day] as $slot)
                                <div style="margin-bottom: 8px; padding: 5px; background: #f9f9f9; border-radius: 4px;">
                                    <span class="time">
                                        {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }} - 
                                        {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}
                                    </span>
                                    <span class="subject">{{ $slot->subject->name ?? 'N/A' }}</span>
                                    <span class="class-info">
                                        ({{ $slot->schoolClass->display_name ?? '' }} - {{ $slot->section->display_name ?? '' }})
                                    </span>
                                </div>
                            @endforeach
                        @else
                            <span style="color: #999;">No classes</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer;">Print</button>
    </div>
</body>
</html>
