<?php
include("../includes/auth.php");

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'Student') {
    header("Location: ../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Dashboard</title>

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family: Arial, Helvetica, sans-serif;
}

body{
    background:#f4f6f8;
}

/* MAIN LAYOUT */
.wrapper{
    display:flex;
    height:100vh;
}

/* LEFT SIDEBAR */
.sidebar{
    width:260px;
    background:#0056b3;
    padding:20px;
}

.sidebar h3{
    color:#ffffff;
    text-align:center;
    margin-bottom:10px;
}

.sidebar p{
    color:#ffffff;
    text-align:center;
    font-size:14px;
    margin-bottom:20px;
}

.sidebar a{
    display:block;
    padding:12px;
    margin-bottom:12px;
    background:#007bff;
    color:#ffffff;
    text-decoration:none;
    text-align:center;
    border-radius:5px;
    font-size:14px;
}

.sidebar a:hover{
    background:#004a99;
}

/* RIGHT CONTENT */
.main-content{
    flex:1;
    background:#ffffff;
}

.main-content iframe{
    width:100%;
    height:100%;
    border:none;
}
</style>

</head>
<body>

<div class="wrapper">

    <!-- LEFT STUDENT DASHBOARD -->
    <div class="sidebar">
        <h3>Student Dashboard</h3>
        <p>ID: <?= htmlspecialchars($_SESSION['user_id']) ?></p>

        <a href="my_subjects.php" target="contentFrame">My Subjects</a>
        <a href="view_timetable.php" target="contentFrame">My Timetable</a>
           <li>
    <a href="../attendance/student_attendance_view.php" target="contentFrame">
        <i class="fa-solid fa-clipboard-check"></i>
        Attendance Report
    </a>
</li>
        <a href="../logout.php">Logout</a>
    </div>

    <!-- RIGHT SIDE CONTENT -->
    <div class="main-content">
        <iframe name="contentFrame"></iframe>
    </div>

</div>

</body>
</html>
