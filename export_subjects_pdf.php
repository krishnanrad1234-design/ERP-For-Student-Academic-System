<?php
include("../includes/db.php");
error_reporting(E_ALL);
ini_set('display_errors', 1);

/* ===== LOAD TCPDF ===== */
require_once("../tcpdf/tcpdf/tcpdf.php");

/* ===== FILTER CONDITIONS ===== */
$where = [];

foreach (['course_code','semester_number','regulation','subject_type','end_exam','elective_type'] as $f) {
    if (!empty($_GET[$f])) {
        $where[] = "$f='".mysqli_real_escape_string($conn, $_GET[$f])."'";
    }
}

$condition = $where ? "WHERE ".implode(" AND ", $where) : "";

/* ===== FETCH DATA ===== */
$result = mysqli_query($conn, "
    SELECT subject_code, subject_name, semester_number,
           L, T, P, periods, credits, subject_type, end_exam
    FROM subjects
    $condition
    ORDER BY CAST(subject_code AS UNSIGNED)
");

/* ===== CREATE PDF ===== */
$pdf = new TCPDF('P','mm','A4',true,'UTF-8',false);
$pdf->SetMargins(10,12,10);
$pdf->SetAutoPageBreak(true,15);
$pdf->AddPage();

/* ===== HEADER ===== */
$pdf->SetFont('helvetica','B',13);
$pdf->Cell(0,8,"NANJIAH LINGAMMAL POLYTECHNIC COLLEGE",0,1,'C');

$pdf->SetFont('helvetica','',10);
$pdf->Cell(0,6,"Approved by AICTE & Recognized by Govt. of Tamil Nadu",0,1,'C');
$pdf->Cell(0,6,"Coimbatore – 641 301",0,1,'C');

$pdf->Ln(4);

$pdf->SetFont('helvetica','B',12);
$pdf->Cell(0,8,"SUBJECT LIST",0,1,'C');
$pdf->Ln(3);

/* ===== TABLE STYLE ===== */
$pdf->SetFont('helvetica','',9);

$html = '
<table border="1" cellpadding="6" cellspacing="0" width="100%">
<tr style="background-color:#E6EEF8;font-weight:bold;">
    <th width="10%" align="center">Subject Code</th>
    <th width="27%" align="center">Subject Name</th>
    <th width="10%"  align="center">Semester</th>
    <th width="4%"  align="center">L</th>
    <th width="4%"  align="center">T</th>
    <th width="4%"  align="center">P</th>
    <th width="9%"  align="center">Periods</th>
    <th width="8%"  align="center">Credits</th>
    <th width="14%" align="center">Subject Type</th>
    <th width="10%" align="center">End Exam</th>
</tr>
';

if (mysqli_num_rows($result) == 0) {

    $html .= '
    <tr>
        <td colspan="10" align="center"><b>No records found</b></td>
    </tr>';

} else {

    while ($r = mysqli_fetch_assoc($result)) {
        $html .= '
        <tr>
            <td align="center">'.$r['subject_code'].'</td>
            <td align="left">'.$r['subject_name'].'</td>
            <td align="center">'.$r['semester_number'].'</td>
            <td align="center">'.$r['L'].'</td>
            <td align="center">'.$r['T'].'</td>
            <td align="center">'.$r['P'].'</td>
            <td align="center">'.$r['periods'].'</td>
            <td align="center">'.$r['credits'].'</td>
            <td align="left">'.$r['subject_type'].'</td>
            <td align="center">'.$r['end_exam'].'</td>
        </tr>';
    }
}

$html .= '</table>';

$pdf->writeHTML($html,true,false,true,false,'');

/* ===== FOOTER ===== */
$pdf->Ln(5);
$pdf->SetFont('helvetica','',9);
$pdf->Cell(0,6,"Generated on: ".date("d-m-Y h:i A"),0,1,'R');

/* ===== DOWNLOAD ===== */
$pdf->Output("Subject_List.pdf","D");
