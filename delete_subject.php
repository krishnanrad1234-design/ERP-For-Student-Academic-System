<?php
include("../includes/auth.php");
include("../includes/db.php");

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'Admin') {
    header("Location: ../login.php");
    exit;
}

$subject_code = $_GET['subject_code'] ?? '';

if ($subject_code == '') {
    die("Invalid Request");
}

/* 🔒 CHECK DEPENDENCY (faculty_subjects) */
$check = mysqli_query(
    $conn,
    "SELECT 1 FROM faculty_subjects WHERE subject_code='$subject_code' LIMIT 1"
);

if (mysqli_num_rows($check) > 0) {
    echo "
    <script>
        alert('❌ Cannot delete subject. It is already assigned to faculty.');
        window.location='subject_management.php';
    </script>
    ";
    exit;
}

/* ✅ SAFE DELETE */
mysqli_query(
    $conn,
    "DELETE FROM subjects WHERE subject_code='$subject_code'"
);

echo "
<script>
    alert('✅ Subject deleted successfully');
    window.location='subject_management.php';
</script>
";
