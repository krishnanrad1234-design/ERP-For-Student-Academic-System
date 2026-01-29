<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("../includes/auth.php");
include("../includes/db.php");

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'Admin') {
    header("Location: ../login.php");
    exit;
}

/* ---------- COLLECT DATA ---------- */
$subject_code   = $_POST['subject_code'];
$course_code    = $_POST['course_code'];
$semester       = $_POST['semester_number'];
$subject_name   = $_POST['subject_name'];
$L              = $_POST['L'] ?? 0;
$T              = $_POST['T'] ?? 0;
$P              = $_POST['P'] ?? 0;
$periods        = $_POST['periods'] ?? 0;
$credits        = $_POST['credits'] ?? 0;
$regulation     = $_POST['regulation'];
$subject_type   = $_POST['subject_type'];
$end_exam       = $_POST['end_exam'];
$elective_type  = $_POST['elective_type'];

/* ---------- DUPLICATE CHECK ---------- */
$check = mysqli_query(
    $conn,
    "SELECT subject_code FROM subjects WHERE subject_code='$subject_code'"
);

if (mysqli_num_rows($check) > 0) {
    echo "<script>
        alert('❌ Subject Code already exists');
        window.location='subject_management.php';
    </script>";
    exit;
}

/* ---------- INSERT SUBJECT ---------- */
$sql = "
INSERT INTO subjects (
    subject_code,
    course_code,
    semester_number,
    subject_name,
    L, T, P,
    periods,
    credits,
    regulation,
    subject_type,
    end_exam,
    elective_type
) VALUES (
    '$subject_code',
    '$course_code',
    '$semester',
    '$subject_name',
    '$L', '$T', '$P',
    '$periods',
    '$credits',
    '$regulation',
    '$subject_type',
    '$end_exam',
    '$elective_type'
)";

if (!mysqli_query($conn, $sql)) {
    die("❌ Database Error: " . mysqli_error($conn));
}

/* ---------- SUCCESS ---------- */
header("Location: subject_management.php?success=1");
