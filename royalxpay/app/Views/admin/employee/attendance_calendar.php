<?php
// Get number of days in month
$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
// Get first day of month (0=Sunday, 6=Saturday)
$firstDay = date('w', strtotime("$year-$month-01"));

$statusClasses = [
    'present' => 'status-present',
    'half day' => 'status-halfday',
    'absent' => 'status-absent',
    'casual leave' => 'status-leave',
    'sick leave' => 'status-leave',
    'work from home' => 'status-wfh',
    'on duty / official visit' => 'status-duty',
    'paid leave' => 'status-leave',
    'unpaid leave' => 'status-leave',
    'compensatory off' => 'status-leave',
    'holiday / weekend' => 'status-holiday'
];

// Create attendance map for quick lookup
$attendanceMap = [];

foreach ($attendanceData as $record) {
    $day = date('j', strtotime($record['attendance_date']));
    $attendanceMap[$day] = $record;
}
?>

<table class="attendance-calendar table-bordered">
    <thead>
        <tr>
            <th>Sun</th>
            <th>Mon</th>
            <th>Tue</th>
            <th>Wed</th>
            <th>Thu</th>
            <th>Fri</th>
            <th>Sat</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <?php
            // Fill in blank cells for days before the first day of month
            for ($i = 0; $i < $firstDay; $i++) {
                echo '<td>&nbsp;</td>';
            }

            $dayCount = 1;
            while ($dayCount <= $daysInMonth) {
                // Start new row at beginning of week
                if (($dayCount + $firstDay - 1) % 7 == 0 && $dayCount != 1) {
                    echo '</tr><tr>';
                }

                $attendance = $attendanceMap[$dayCount] ?? null;
                $status = $attendance ? strtolower($attendance['status']) : null;
                $statusClass = $status ? ($statusClasses[$status] ?? '') : '';

                echo '<td class="' . $statusClass . '">';
                echo '<span class="day-number">' . $dayCount . '</span>';

                if ($attendance) {
                    // Status mapping for display text
                    $statusDisplayMap = [
                        'present' => 'Present',
                        'half day' => 'Half Day',
                        'absent' => 'Absent',
                        'casual leave' => 'CL',
                        'sick leave' => 'SL',
                        'work from home' => 'WFH',
                        'on duty / official visit' => 'Duty',
                        'paid leave' => 'PL',
                        'unpaid leave' => 'UL',
                        'compensatory off' => 'CO',
                        'holiday / weekend' => 'Holiday'
                    ];

                    $statusLower = strtolower($attendance['status']);
                    $displayStatus = $statusDisplayMap[$statusLower] ?? ucfirst($attendance['status']);

                    echo '<span class="attendance-status">' . $displayStatus . '</span>';

                    if ($attendance['remarks']) {
                        echo '<i class="fas fa-info-circle" title="' . esc($attendance['remarks']) . '"></i>';
                    }
                }

                echo '</td>';

                $dayCount++;
            }

            // Fill in remaining cells with blanks
            while (($dayCount + $firstDay - 1) % 7 != 0) {
                echo '<td>&nbsp;</td>';
                $dayCount++;
            }
            ?>
        </tr>
    </tbody>
</table>