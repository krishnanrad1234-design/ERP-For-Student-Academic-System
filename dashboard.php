<?php
include("../includes/auth.php");

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'Admin') {
    header("Location: ../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>

<!-- ===== FONT AWESOME (REAL ERP ICONS) ===== -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
/* ===== RESET ===== */
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family: Arial, Helvetica, sans-serif;
}

body{
    background:#f4f6f8;
}

/* ===== LAYOUT ===== */
.wrapper{
    display:flex;
    height:100vh;
}

/* ===== SIDEBAR ===== */
.sidebar{
    width:260px;
    background:linear-gradient(180deg, #0b1d4d, #0a1435);
    color:#fff;
}

/* ===== SIDEBAR HEADER ===== */
.sidebar-header{
    display:flex;
    align-items:center;
    gap:12px;
    padding:18px 20px;
    border-bottom:1px solid rgba(255,255,255,0.2);
}

.sidebar-header .avatar{
    width:38px;
    height:38px;
    border-radius:50%;
    background:#2F6FB2;
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:bold;
    font-size:16px;
}

.sidebar-header h3{
    font-size:16px;
}

/* ===== MENU ===== */
.menu{
    list-style:none;
    padding-top:10px;
}

.menu li{
    padding:12px 20px;
}

.menu li a{
    display:flex;
    align-items:center;
    gap:14px;
    color:#e0e6ff;
    text-decoration:none;
    font-size:14px;
}

.menu li i{
    width:18px;
    text-align:center;
    font-size:15px;
}

.menu li:hover{
    background:rgba(255,255,255,0.08);
}

/* ===== LOGOUT ===== */
.menu li.logout{
    margin-top:20px;
    border-top:1px solid rgba(255,255,255,0.2);
}

.menu li.logout a{
    color:#ffb3b3;
}

/* ===== CONTENT ===== */
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

    <!-- ===== SIDEBAR ===== -->
    <div class="sidebar">

        <!-- HEADER -->
        <div class="sidebar-header">
            <div class="avatar">
                <i class="fa-solid fa-user-shield"></i>
            </div>
            <h3>Admin</h3>
        </div>

        <!-- MENU (ONLY YOUR LINKS) -->
        <ul class="menu">

            <li>
                <a href="subject_management.php" target="contentFrame">
                    <i class="fa-solid fa-book"></i>
                    Subject Management
                </a>
            </li>

            
            <li>
                <a href="assign_and_report_subjects.php" target="contentFrame">
                    <i class="fa-solid fa-chalkboard-user"></i>
                    Faculty Subject Assignment
                </a>
            </li>

              <li>
                <a href="assign_and_view_student_batches.php" target="contentFrame">
                    <i class="fa-solid fa-people-group"></i>
                    Student Batch Management
                </a>
            </li>

            <li>
                <a href="admin_timetable.php" target="contentFrame">
                    <i class="fa-solid fa-calendar-plus"></i>
                    Timetable Management
                </a>
            </li>

            <li>
                <a href="admin_date_timetable.php" target="contentFrame">
                    <i class="fa-solid fa-table"></i>
                    Date-wise Timetable
                </a>
            </li>

            <li>
                <a href="admin_attendance_report.php" target="contentFrame">
                    <i class="fa-solid fa-layer-group"></i>
                    Assign Batch
                </a>
            </li>

            

            <li>
                <a href="view_student_batches.php" target="contentFrame">
                    <i class="fa-solid fa-users"></i>
                    View Student Batch List
                </a>
            </li>

            <li>
                <a href="create_timetable_form.php" target="contentFrame">
                    <i class="fa-solid fa-file-lines"></i>
                    Create Timetable
                </a>
            </li>

            <li>
    <a href="../attendance/admin_attendance_report.php" target="contentFrame">
        <i class="fa-solid fa-clipboard-check"></i>
        Attendance Report
    </a>
</li>


            <li class="logout">
                <a href="../logout.php">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    Logout
                </a>
            </li>

        </ul>
    </div>

    <!-- ===== RIGHT CONTENT ===== -->
    <div class="main-content">
        <iframe name="contentFrame"></iframe>
    </div>

</div>

</body>
</html>
