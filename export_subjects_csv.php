<?php
include("../includes/db.php");

/* ===== CSV HEADERS ===== */
header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=subjects.csv");
header("Pragma: no-cache");
header("Expires: 0");

/* ===== OPEN OUTPUT ===== */
$output = fopen("php://output", "w");

/* ===== CSV COLUMN HEADERS ===== */
fputcsv($output, [
    "Code",
    "Subject Name",
    "Semester",
    "L",
    "T",
    "P",
    "Periods",
    "Credits",
    "Subject Type",
    "End Exam"
]);

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

/* ===== WRITE DATA ROWS ===== */
while ($r = mysqli_fetch_assoc($result)) {
    fputcsv($output, [
        $r['subject_code'],
        $r['subject_name'],
        $r['semester_number'],
        $r['L'],
        $r['T'],
        $r['P'],
        $r['periods'],
        $r['credits'],
        $r['subject_type'],
        $r['end_exam']
    ]);
}

/* ===== CLOSE OUTPUT ===== */
fclose($output);
exit;
