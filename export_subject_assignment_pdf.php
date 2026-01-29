<?php
include("../includes/db.php");
require_once("../tcpdf/tcpdf/tcpdf.php");

/* ================= FILTER CONDITIONS ================= */
$where = [];

if (!empty($_GET['course_code'])) {
    $course = mysqli_real_escape_string($conn, $_GET['course_code']);
    $where[] = "s.course_code = '$course'";
}

if (!empty($_GET['semester_number'])) {
    $sem = mysqli_real_escape_string($conn, $_GET['semester_number']);
    $where[] = "fs.semester_number = '$sem'";
}

if (!empty($_GET['academic_year'])) {
    $year = mysqli_real_escape_string($conn, $_GET['academic_year']);
    $where[] = "fs.academic_year = '$year'";
}

if (!empty($_GET['faculty_id'])) {
    $faculty = mysqli_real_escape_string($conn, $_GET['faculty_id']);
    $where[] = "fs.faculty_id = '$faculty'";
}

$condition = $where ? "WHERE " . implode(" AND ", $where) : "";

/* ================= DATA QUERY ================= */
$sql = "
SELECT
    s.course_code,
    fs.semester_number,
    s.subject_code,
    s.subject_name,
    CONCAT(f.first_name,' ',f.last_name) AS faculty_name,
    fs.academic_year,
    fs.faculty_source
FROM faculty_subjects fs
JOIN subjects s ON fs.subject_code = s.subject_code
JOIN faculty f  ON fs.faculty_id = f.faculty_id
$condition
ORDER BY fs.academic_year DESC, fs.semester_number, s.subject_code
";

$result = mysqli_query($conn, $sql);

/* ================= CREATE PDF ================= */
$pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(true, 15);
$pdf->AddPage();

/* ================= HEADER ================= */
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 8, "NANJIAH LINGAMMAL POLYTECHNIC COLLEGE", 0, 1, 'C');

$pdf->SetFont('helvetica', '', 11);
$pdf->Cell(0, 6, "SUBJECT ASSIGNMENT REPORT", 0, 1, 'C');
$pdf->Ln(5);

/* ================= TABLE ================= */
$pdf->SetFont('helvetica', '', 9);

$html = "
<table border='1' cellpadding='6' cellspacing='0' width='100%'>
<tr style='background-color:#f0f0f0;font-weight:bold;'>
    <th width='10%'>Course</th>
    <th width='8%'>Semester</th>
    <th width='12%'>Subject Code</th>
    <th width='28%'>Subject Name</th>
    <th width='20%'>Faculty Name</th>
    <th width='12%'>Academic Year</th>
    <th width='10%'>Source</th>
</tr>
";

if (mysqli_num_rows($result) == 0) {
    $html .= "
    <tr>
        <td colspan='7' align='center'>No records found</td>
    </tr>";
} else {
    while ($row = mysqli_fetch_assoc($result)) {
        $html .= "
        <tr>
            <td>{$row['course_code']}</td>
            <td align='center'>{$row['semester_number']}</td>
            <td>{$row['subject_code']}</td>
            <td>{$row['subject_name']}</td>
            <td>{$row['faculty_name']}</td>
            <td align='center'>{$row['academic_year']}</td>
            <td>{$row['faculty_source']}</td>
        </tr>";
    }
}

$html .= "</table>";

$pdf->writeHTML($html, true, false, true, false, '');

/* ================= FOOTER ================= */
$pdf->Ln(6);
$pdf->SetFont('helvetica', '', 9);
$pdf->Cell(0, 6, "Generated on: " . date("d-m-Y h:i A"), 0, 1, 'R');

/* ================= DOWNLOAD ================= */
$pdf->Output("Subject_Assignment_Report.pdf", "D");
exit;
