<?php
session_start();
include("includes/db.php");

$reference_id = $_POST['reference_id'];
$password     = $_POST['password'];

$query = "
SELECT reference_id, user_type 
FROM users 
WHERE reference_id='$reference_id'
AND password='$password'
AND is_active='Yes'
";

$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 1) {

    $row = mysqli_fetch_assoc($result);

    $_SESSION['user_id']   = $row['reference_id'];
    $_SESSION['user_type'] = $row['user_type'];

    if ($row['user_type'] == 'Student') {
        header("Location: student/dashboard.php");
    }
    elseif ($row['user_type'] == 'Faculty') {
        header("Location: faculty/dashboard.php");
    }
    elseif ($row['user_type'] == 'Admin') {
        header("Location: admin/dashboard.php");
    }

} else {
    header("Location: login.php?error=1");
}
?>
