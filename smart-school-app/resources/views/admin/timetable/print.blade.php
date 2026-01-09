<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timetable - Print</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #333;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .break-row {
            background-color: #fff3cd;
        }
        .subject {
            font-weight: bold;
        }
        .teacher {
            font-size: 12px;
            color: #666;
        }
        @media print {
            body {
                margin: 0;
                padding: 15px;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer;">Print Timetable</button>
        <button onclick="window.close()" style="padding: 10px 20px; cursor: pointer; margin-left: 10px;">Close</button>
    </div>

    <div class="header">
        <h1>Smart School</h1>
        <p>Weekly Timetable</p>
        <p>Class: {{ $timetable->class->name ?? 'N/A' }} | Section: {{ $timetable->section->name ?? 'N/A' }}</p>
        <p>Academic Session: {{ $timetable->academicSession->name ?? 'N/A' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 100px;">Time</th>
                <th>Monday</th>
                <th>Tuesday</th>
                <th>Wednesday</th>
                <th>Thursday</th>
                <th>Friday</th>
                <th>Saturday</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>08:00 - 08:45</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>08:45 - 09:30</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>09:30 - 10:15</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr class="break-row">
                <td>10:15 - 10:30</td>
                <td colspan="6">Break</td>
            </tr>
            <tr>
                <td>10:30 - 11:15</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>11:15 - 12:00</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr class="break-row">
                <td>12:00 - 12:45</td>
                <td colspan="6">Lunch Break</td>
            </tr>
            <tr>
                <td>12:45 - 01:30</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>01:30 - 02:15</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top: 30px; text-align: center; font-size: 12px; color: #666;">
        Generated on {{ date('F d, Y') }}
    </div>
</body>
</html>
