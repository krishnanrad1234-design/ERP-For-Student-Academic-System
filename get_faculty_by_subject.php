<?php
include("../includes/db.php");

header("Content-Type: application/json");

$subject  = $_GET['subject_code'] ?? '';
$year     = $_GET['academic_year'] ?? '';
$semester = $_GET['semester_number'] ?? '';

$response = [];

if ($subject && $year && $semester) {

    $sql = "
        SELECT f.faculty_id, f.faculty_name
        FROM faculty_subjects fs
        JOIN faculty f ON fs.faculty_id = f.faculty_id
        WHERE TRIM(fs.subject_code) = TRIM(?)
          AND TRIM(fs.academic_year) = TRIM(?)
          AND fs.semester_number = ?
          AND fs.status = 'Active'
        LIMIT 1
    ";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssi", $subject, $year, $semester);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $response[] = $row;
    }
}

echo json_encode($response);
exit;
