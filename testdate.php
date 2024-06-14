<?php

// Get today's date
$today = date('d');

// Handle the month's first day
if ($today == 1) {
    // Get the last day of the previous month
    $yesterday = date('t', strtotime('last day of last month'));
} else {
    // Subtract one day from today
    $yesterday = $today - 1;
}

echo "Yesterday's date: " . $yesterday;


?>