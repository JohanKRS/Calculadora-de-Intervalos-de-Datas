<?php
function parseDate($date) {
    $parts = explode('-', $date);
    return checkdate($parts[1], $parts[2], $parts[0]) ? DateTimeImmutable::createFromFormat('Y-m-d', $date) : false;
}

function calculateDateIntervals($start_dates, $end_dates) {
    $date_ranges = [];

    for ($i = 0; $i < count($start_dates); $i++) {
        $start_date = parseDate($start_dates[$i]);
        $end_date = parseDate($end_dates[$i]);

        if (!$start_date || !$end_date || $start_date > $end_date) {
            continue;
        }

        $date_ranges[] = ['start' => $start_date, 'end' => $end_date];
    }

    usort($date_ranges, function ($a, $b) {
        return $a['start'] <=> $b['start'];
    });

    $total_days = 0;
    $prev_start = null;
    $prev_end = null;

    foreach ($date_ranges as $range) {
        if ($prev_start === null || $prev_end === null) {
            $prev_start = $range['start'];
            $prev_end = $range['end'];
        } else {
            if ($range['start'] <= $prev_end) {
                if ($range['end'] > $prev_end) {
                    $prev_end = $range['end'];
                }
            } else {
                $total_days += $prev_end->diff($prev_start)->days + 1;
                $prev_start = $range['start'];
                $prev_end = $range['end'];
            }
        }
    }

    if ($prev_start !== null && $prev_end !== null) {
        $total_days += $prev_end->diff($prev_start)->days + 1;
    }

    return $total_days;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $start_dates = $_POST['start_date'] ?? [];
    $end_dates = $_POST['end_date'] ?? [];

    if (count($start_dates) !== count($end_dates)) {
        http_response_code(400);
        exit('Invalid input');
    }

    echo calculateDateIntervals($start_dates, $end_dates);
}
