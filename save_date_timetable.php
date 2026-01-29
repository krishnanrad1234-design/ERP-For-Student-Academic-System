<?php
include("../includes/auth.php");
include("../includes/db.php");

if ($_SESSION['user_type'] != 'Admin') {
    die("Unauthorized access");
}

$date       = $_POST['date'] ?? '';
$is_working = $_POST['is_working'] ?? '';
$follow_day = $_POST['follow_day'] ?? '';
$reason     = $_POST['reason'] ?? '';

if (!$date || !$is_working) {
    die("Missing required fields");
}

/* 🔒 NORMALIZE DATE (CRITICAL FIX) */
$date = date('Y-m-d', strtotime($date));

/* RULES */
$follow_day = $follow_day ?: NULL;
$reason     = $reason ?: NULL;

if ($is_working === 'No') {
    $follow_day = NULL;
}

/* SAVE / UPDATE */
$sql = "
INSERT INTO date_timetable_mapping
(date, is_working, follow_day, reason)
VALUES
(
    '$date',
    '$is_working',
    " . ($follow_day ? "'$follow_day'" : "NULL") . ",
    " . ($reason ? "'$reason'" : "NULL") . "
)
ON DUPLICATE KEY UPDATE
    is_working = VALUES(is_working),
    follow_day = VALUES(follow_day),
    reason     = VALUES(reason)
";

if (!mysqli_query($conn, $sql)) {
    die("DB Error: " . mysqli_error($conn));
}

header("Location: admin_assign_date_timetable.php?success=1");
exit;
