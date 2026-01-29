<?php
include("../includes/db.php");

/* ===== EXCEL HEADERS ===== */
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=subjects.xls");

/* ===== FILTER CONDITIONS ===== */
$where = [];

if (!empty($_GET['course_code']))
    $where[] = "course_code='".mysqli_real_escape_string($conn, $_GET['course_code'])."'";

if (!empty($_GET['semester_number']))
    $where[] = "semester_number='".mysqli_real_escape_string($conn, $_GET['semester_number'])."'";

if (!empty($_GET['regulation']))
    $where[] = "regulation='".mysqli_real_escape_string($conn, $_GET['regulation'])."'";

if (!empty($_GET['subject_type']))
    $where[] = "subject_type='".mysqli_real_escape_string($conn, $_GET['subject_type'])."'";

if (!empty($_GET['end_exam']))
    $where[] = "end_exam='".mysqli_real_escape_string($conn, $_GET['end_exam'])."'";

if (!empty($_GET['elective_type']))
    $where[] = "elective_type='".mysqli_real_escape_string($conn, $_GET['elective_type'])."'";

$condition = $where ? "WHERE ".implode(" AND ", $where) : "";

/* ===== FETCH FILTERED DATA ===== */
$result = mysqli_query($conn, "
SELECT subject_code, subject_name, semester_number,
       L, T, P, periods, credits, subject_type, end_exam
FROM subjects
$condition
ORDER BY CAST(subject_code AS UNSIGNED)
");

/* ===== EXCEL HEADER ROW ===== */
echo "Code\tSubject Name\tSemester\tL\tT\tP\tPeriods\tCredits\tType\tEnd Exam\n";

/* ===== DATA ROWS ===== */
while ($r = mysqli_fetch_assoc($result)) {
    echo
        $r['subject_code']."\t".
        $r['subject_name']."\t".
        $r['semester_number']."\t".
        $r['L']."\t".
        $r['T']."\t".
        $r['P']."\t".
        $r['periods']."\t".
        $r['credits']."\t".
        $r['subject_type']."\t".
        $r['end_exam']."\n";
}
