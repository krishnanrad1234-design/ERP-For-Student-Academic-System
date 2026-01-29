<?php
include("../includes/db.php");

header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=subject_assignment_report.csv");

$output = fopen("php://output", "w");

fputcsv($output, [
    "Course","Semester","Subject Code","Subject Name",
    "Faculty Name","Academic Year","Faculty Source"
]);

$where = [];

if (!empty($_GET['course_code']))
    $where[] = "s.course_code='".$_GET['course_code']."'";
if (!empty($_GET['semester_number']))
    $where[] = "fs.semester_number='".$_GET['semester_number']."'";
if (!empty($_GET['academic_year']))
    $where[] = "fs.academic_year='".$_GET['academic_year']."'";
if (!empty($_GET['faculty_id']))
    $where[] = "fs.faculty_id='".$_GET['faculty_id']."'";

$condition = $where ? "WHERE ".implode(" AND ",$where) : "";

$result = mysqli_query($conn,"
SELECT
 s.course_code,
 fs.semester_number,
 s.subject_code,
 s.subject_name,
 CONCAT(f.first_name,' ',f.last_name) AS faculty_name,
 fs.academic_year,
 fs.faculty_source
FROM faculty_subjects fs
JOIN subjects s ON fs.subject_code=s.subject_code
JOIN faculty f ON fs.faculty_id=f.faculty_id
$condition
ORDER BY fs.academic_year DESC, fs.semester_number
");

while($row=mysqli_fetch_assoc($result)){
    fputcsv($output,$row);
}

fclose($output);
exit;
