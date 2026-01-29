<?php
include("../includes/auth.php");
include("../includes/db.php");

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'Admin') {
    header("Location: ../login.php");
    exit;
}

$old_code = $_POST['old_subject_code'];
$new_code = $_POST['subject_code'];

/* Prevent duplicate subject code */
$check = mysqli_query($conn,
    "SELECT subject_code FROM subjects 
     WHERE subject_code='$new_code' 
     AND subject_code!='$old_code'"
);

if (mysqli_num_rows($check) > 0) {
    die("❌ Subject Code already exists");
}

mysqli_query($conn, "
UPDATE subjects SET
    subject_code = '$new_code',
    subject_name = '{$_POST['subject_name']}',
    L = '{$_POST['L']}',
    T = '{$_POST['T']}',
    P = '{$_POST['P']}',
    periods = '{$_POST['periods']}',
    credits = '{$_POST['credits']}',
    subject_type = '{$_POST['subject_type']}',
    end_exam = '{$_POST['end_exam']}'
WHERE subject_code = '$old_code'
");

header("Location: subject_management.php?success=1");
