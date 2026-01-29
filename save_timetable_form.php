<?php
include("../includes/auth.php");
include("../includes/db.php");

if ($_SESSION['user_type'] !== 'Admin') {
    header("Location: ../login.php");
    exit;
}

/* =====================
   FETCH & VALIDATE INPUT
===================== */
$course_code     = $_POST['course_code']     ?? '';
$academic_year   = $_POST['academic_year']   ?? '';
$semester_number = $_POST['semester_number'] ?? '';
$subject_code    = $_POST['subject_code']    ?? '';
$class_mode      = $_POST['class_mode']      ?? '';
$day             = $_POST['day']             ?? '';
$start_period    = (int)($_POST['start_period'] ?? 0);
$end_period      = (int)($_POST['end_period'] ?? 0);
$batch           = $_POST['batch']           ?? 'ALL';
$room_no         = trim($_POST['room_no'] ?? '');

/* BASIC VALIDATION */
if (
    !$course_code || !$academic_year || !$semester_number ||
    !$subject_code || !$class_mode || !$day ||
    !$start_period || !$end_period || !$room_no
) {
    die("❌ Missing required fields");
}

if ($start_period > $end_period) {
    die("❌ Start period cannot be greater than end period");
}

/* =====================
   GET FACULTY HANDLING SUBJECT
===================== */
$fq = mysqli_query($conn, "
    SELECT faculty_id
    FROM faculty_subjects
    WHERE subject_code='$subject_code'
      AND academic_year='$academic_year'
      AND semester_number='$semester_number'
      AND status='Active'
    LIMIT 1
");

if (mysqli_num_rows($fq) == 0) {
    die("❌ No faculty assigned to this subject");
}

$f = mysqli_fetch_assoc($fq);
$faculty_id = $f['faculty_id'];

/* =====================
   PERIOD LOOP (IMPORTANT)
===================== */
for ($p = $start_period; $p <= $end_period; $p++) {

    /* ===== CLASH CHECK ===== */
    $clash = mysqli_query($conn, "
        SELECT 1
        FROM class_timetable
        WHERE course_code='$course_code'
          AND academic_year='$academic_year'
          AND semester_number='$semester_number'
          AND day='$day'
          AND period_number='$p'
          AND (
              batch='$batch'
              OR batch='ALL'
              OR '$batch'='ALL'
          )
    ");

    if (mysqli_num_rows($clash) > 0) {
        die("❌ Clash detected on $day Period $p");
    }

    /* ===== INSERT TIMETABLE ===== */
    mysqli_query($conn, "
        INSERT INTO class_timetable
        (
            course_code,
            academic_year,
            semester_number,
            subject_code,
            faculty_id,
            day,
            period_number,
            class_mode,
            batch,
            room_no
        )
        VALUES
        (
            '$course_code',
            '$academic_year',
            '$semester_number',
            '$subject_code',
            '$faculty_id',
            '$day',
            '$p',
            '$class_mode',
            '$batch',
            '$room_no'
        )
    ") or die(mysqli_error($conn));
}

/* =====================
   SUCCESS REDIRECT
===================== */
header("Location: admin_timetable.php?success=1");
exit;
