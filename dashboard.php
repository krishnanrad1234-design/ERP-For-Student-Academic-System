<?php
include("../includes/auth.php");

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'Faculty') {
    header("Location: ../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Faculty Dashboard</title>

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
    background:#1e7e34;
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
    background:#28a745;
    color:#ffffff;
    text-decoration:none;
    text-align:center;
    border-radius:5px;
    font-size:14px;
}

.sidebar a:hover{
    background:#218838;
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

    <!-- LEFT FACULTY DASHBOARD -->
    <div class="sidebar">
        <h3>Faculty Dashboard</h3>
        <p>ID: <?= htmlspecialchars($_SESSION['user_id']) ?></p>

        <a href="my_subjects.php" target="contentFrame">My Subjects</a>
        <a href="view_timetable.php" target="contentFrame">My Timetable</a>
           <li>
    <a href="../attendance/faculty_mark_attendance.php" target="contentFrame">
        <i class="fa-solid fa-clipboard-check"></i>
        Student Attendance 
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
