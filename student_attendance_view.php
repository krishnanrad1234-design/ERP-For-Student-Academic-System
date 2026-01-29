<?php
include("../includes/auth.php");
include("../includes/db.php");

if ($_SESSION['user_type'] !== 'Student') {
    header("Location: ../login.php");
    exit;
}

$student_id = $_SESSION['user_id'];

// Get filter values
$subject_filter = $_GET['subject'] ?? '';
$semester_filter = $_GET['semester'] ?? '';
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';
$status_filter = $_GET['status'] ?? '';

// Get student info
$student_info = mysqli_query($conn, "
    SELECT student_name, course_code 
    FROM students 
    WHERE student_id = '$student_id'
");
$student = mysqli_fetch_assoc($student_info);
?>

<!DOCTYPE html>
<html>
<head>
<title>My Attendance</title>
<style>
body{font-family:Arial;background:#f4f6f8;margin:0;padding:20px;}
.container{max-width:1200px;margin:0 auto;}
.box{background:#fff;padding:25px;border-radius:8px;box-shadow:0 2px 4px rgba(0,0,0,0.1);margin-bottom:20px;}
.header{background:linear-gradient(135deg, #667eea 0%, #764ba2 100%);color:#fff;padding:20px;border-radius:8px;margin-bottom:20px;}
.header h2{margin:0 0 5px 0;}
.header p{margin:0;opacity:0.9;}

/* Stats Cards */
.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:15px;margin-bottom:20px;}
.stat-card{background:#fff;padding:20px;border-radius:8px;box-shadow:0 2px 4px rgba(0,0,0,0.1);border-left:4px solid;}
.stat-card.total{border-color:#0d6efd;}
.stat-card.present{border-color:#28a745;}
.stat-card.absent{border-color:#dc3545;}
.stat-card.onduty{border-color:#ffc107;}
.stat-card.percentage{border-color:#6f42c1;}
.stat-card h4{margin:0 0 10px 0;font-size:14px;color:#666;text-transform:uppercase;}
.stat-card .value{font-size:36px;font-weight:bold;margin:0;}
.stat-card.total .value{color:#0d6efd;}
.stat-card.present .value{color:#28a745;}
.stat-card.absent .value{color:#dc3545;}
.stat-card.onduty .value{color:#ffc107;}
.stat-card.percentage .value{color:#6f42c1;}

/* Filter Section */
.filter-box{background:#f8f9fa;padding:20px;border-radius:8px;margin-bottom:20px;}
.filter-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:15px;margin-bottom:15px;}
.filter-group label{display:block;font-weight:600;margin-bottom:5px;color:#495057;}
.filter-group select,.filter-group input{width:100%;padding:10px;border:1px solid #ced4da;border-radius:4px;font-size:14px;}
.filter-buttons{display:flex;gap:10px;}
.btn{padding:10px 20px;border:none;border-radius:4px;cursor:pointer;font-size:14px;font-weight:600;transition:all 0.3s;}
.btn-primary{background:#0d6efd;color:#fff;}
.btn-primary:hover{background:#0b5ed7;}
.btn-secondary{background:#6c757d;color:#fff;}
.btn-secondary:hover{background:#5c636a;}
.btn-success{background:#28a745;color:#fff;}
.btn-success:hover{background:#218838;}

/* Subject-wise breakdown */
.subject-breakdown{margin-bottom:20px;}
.subject-card{background:#fff;padding:15px;border-radius:8px;margin-bottom:10px;border-left:4px solid #0d6efd;}
.subject-card h4{margin:0 0 10px 0;color:#495057;}
.subject-stats{display:flex;gap:20px;flex-wrap:wrap;}
.subject-stat{flex:1;min-width:100px;}
.subject-stat span{display:block;font-size:12px;color:#6c757d;margin-bottom:5px;}
.subject-stat strong{font-size:20px;}
.progress-bar{background:#e9ecef;height:8px;border-radius:4px;overflow:hidden;margin-top:10px;}
.progress-fill{height:100%;background:#28a745;transition:width 0.3s;}

/* Table */
table{width:100%;border-collapse:collapse;background:#fff;}
th,td{padding:12px;text-align:left;border-bottom:1px solid #dee2e6;}
th{background:#f8f9fa;font-weight:600;color:#495057;text-transform:uppercase;font-size:13px;}
tr:hover{background:#f8f9fa;}
.status-badge{padding:4px 12px;border-radius:12px;font-size:12px;font-weight:600;display:inline-block;}
.status-present{background:#d4edda;color:#155724;}
.status-absent{background:#f8d7da;color:#721c24;}
.status-onduty{background:#fff3cd;color:#856404;}

.no-data{text-align:center;padding:40px;color:#6c757d;}
</style>
</head>
<body>

<div class="container">
<!-- Header -->
<div class="header">
<h2>My Attendance Record</h2>
<p>Student: <?= $student['student_name'] ?> (<?= $student_id ?>) | Course: <?= $student['course_code'] ?></p>
</div>

<?php
// Build WHERE clause
$where_conditions = ["student_id = '$student_id'"];

if (!empty($subject_filter)) {
    $where_conditions[] = "subject_code = '$subject_filter'";
}
if (!empty($semester_filter)) {
    $where_conditions[] = "semester_number = '$semester_filter'";
}
if (!empty($start_date)) {
    $where_conditions[] = "class_date >= '$start_date'";
}
if (!empty($end_date)) {
    $where_conditions[] = "class_date <= '$end_date'";
}
if (!empty($status_filter)) {
    $where_conditions[] = "attendance_status = '$status_filter'";
}

$where_clause = 'WHERE ' . implode(' AND ', $where_conditions);

// Get overall statistics
$stats_query = mysqli_query($conn, "
    SELECT 
        COUNT(*) AS total,
        SUM(CASE WHEN attendance_status = 'Present' THEN 1 ELSE 0 END) AS present,
        SUM(CASE WHEN attendance_status = 'Absent' THEN 1 ELSE 0 END) AS absent,
        SUM(CASE WHEN attendance_status = 'On-Duty' THEN 1 ELSE 0 END) AS onduty
    FROM attendance
    $where_clause
");

$stats = mysqli_fetch_assoc($stats_query);
$total = $stats['total'];
$present = $stats['present'];
$absent = $stats['absent'];
$onduty = $stats['onduty'];
$percentage = $total > 0 ? round(($present / $total) * 100, 2) : 0;
?>

<!-- Statistics Cards -->
<div class="stats-grid">
<div class="stat-card total">
<h4>Total Classes</h4>
<p class="value"><?= $total ?></p>
</div>

<div class="stat-card present">
<h4>Present</h4>
<p class="value"><?= $present ?></p>
</div>

<div class="stat-card absent">
<h4>Absent</h4>
<p class="value"><?= $absent ?></p>
</div>

<div class="stat-card onduty">
<h4>On-Duty</h4>
<p class="value"><?= $onduty ?></p>
</div>

<div class="stat-card percentage">
<h4>Attendance %</h4>
<p class="value"><?= $percentage ?>%</p>
</div>
</div>

<!-- Filter Section -->
<div class="box">
<h3 style="margin-top:0;">Filters</h3>
<form method="get">
<div class="filter-grid">
<div class="filter-group">
<label>Subject:</label>
<select name="subject">
<option value="">All Subjects</option>
<?php
$subjects = mysqli_query($conn, "
    SELECT DISTINCT a.subject_code, s.subject_name 
    FROM attendance a
    LEFT JOIN subjects s ON a.subject_code = s.subject_code
    WHERE a.student_id = '$student_id'
    ORDER BY s.subject_name
");
while($sub = mysqli_fetch_assoc($subjects)) {
    $selected = ($subject_filter == $sub['subject_code']) ? 'selected' : '';
    echo "<option value='{$sub['subject_code']}' $selected>{$sub['subject_name']}</option>";
}
?>
</select>
</div>

<div class="filter-group">
<label>Semester:</label>
<select name="semester">
<option value="">All Semesters</option>
<?php
for($i = 1; $i <= 6; $i++) {
    $selected = ($semester_filter == $i) ? 'selected' : '';
    echo "<option value='$i' $selected>Semester $i</option>";
}
?>
</select>
</div>

<div class="filter-group">
<label>Start Date:</label>
<input type="date" name="start_date" value="<?= $start_date ?>">
</div>

<div class="filter-group">
<label>End Date:</label>
<input type="date" name="end_date" value="<?= $end_date ?>">
</div>

<div class="filter-group">
<label>Status:</label>
<select name="status">
<option value="">All Status</option>
<option value="Present" <?= ($status_filter == 'Present') ? 'selected' : '' ?>>Present</option>
<option value="Absent" <?= ($status_filter == 'Absent') ? 'selected' : '' ?>>Absent</option>
<option value="On-Duty" <?= ($status_filter == 'On-Duty') ? 'selected' : '' ?>>On-Duty</option>
</select>
</div>
</div>

<div class="filter-buttons">
<button type="submit" class="btn btn-primary">Apply Filters</button>
<button type="button" class="btn btn-secondary" onclick="window.location.href='student_attendance.php'">Clear Filters</button>
<button type="button" class="btn btn-success" onclick="exportToCSV()">Export CSV</button>
</div>
</form>
</div>

<!-- Subject-wise Breakdown -->
<div class="box">
<h3 style="margin-top:0;">Subject-wise Attendance</h3>
<div class="subject-breakdown">
<?php
$subject_stats = mysqli_query($conn, "
    SELECT 
        a.subject_code,
        s.subject_name,
        a.semester_number,
        COUNT(*) as total_classes,
        SUM(CASE WHEN a.attendance_status = 'Present' THEN 1 ELSE 0 END) as present_count,
        SUM(CASE WHEN a.attendance_status = 'Absent' THEN 1 ELSE 0 END) as absent_count,
        SUM(CASE WHEN a.attendance_status = 'On-Duty' THEN 1 ELSE 0 END) as onduty_count
    FROM attendance a
    LEFT JOIN subjects s ON a.subject_code = s.subject_code
    WHERE a.student_id = '$student_id'
    GROUP BY a.subject_code, a.semester_number
    ORDER BY a.semester_number DESC, s.subject_name
");

if(mysqli_num_rows($subject_stats) > 0) {
    while($sub = mysqli_fetch_assoc($subject_stats)) {
        $sub_percentage = round(($sub['present_count'] / $sub['total_classes']) * 100, 2);
        $bar_color = $sub_percentage >= 75 ? '#28a745' : ($sub_percentage >= 50 ? '#ffc107' : '#dc3545');
?>
<div class="subject-card">
<h4><?= $sub['subject_name'] ?> (<?= $sub['subject_code'] ?>) - Semester <?= $sub['semester_number'] ?></h4>
<div class="subject-stats">
<div class="subject-stat">
<span>Total Classes</span>
<strong><?= $sub['total_classes'] ?></strong>
</div>
<div class="subject-stat">
<span>Present</span>
<strong style="color:#28a745;"><?= $sub['present_count'] ?></strong>
</div>
<div class="subject-stat">
<span>Absent</span>
<strong style="color:#dc3545;"><?= $sub['absent_count'] ?></strong>
</div>
<div class="subject-stat">
<span>On-Duty</span>
<strong style="color:#ffc107;"><?= $sub['onduty_count'] ?></strong>
</div>
<div class="subject-stat">
<span>Percentage</span>
<strong style="color:<?= $bar_color ?>;"><?= $sub_percentage ?>%</strong>
</div>
</div>
<div class="progress-bar">
<div class="progress-fill" style="width:<?= $sub_percentage ?>%;background:<?= $bar_color ?>;"></div>
</div>
</div>
<?php 
    }
} else {
    echo "<p class='no-data'>No attendance records found</p>";
}
?>
</div>
</div>

<!-- Detailed Attendance Table -->
<div class="box">
<h3 style="margin-top:0;">Detailed Attendance Records</h3>
<table id="attendance-table">
<thead>
<tr>
<th>Date</th>
<th>Day</th>
<th>Subject</th>
<th>Semester</th>
<th>Period</th>
<th>Batch</th>
<th>Status</th>
<th>Marked By</th>
</tr>
</thead>
<tbody>
<?php
$attendance_query = mysqli_query($conn, "
    SELECT 
        a.class_date,
        a.subject_code,
        s.subject_name,
        a.semester_number,
        a.period_number,
        a.batch,
        a.attendance_status,
        CONCAT(f.first_name, ' ', f.last_name) as faculty_name
    FROM attendance a
    LEFT JOIN subjects s ON a.subject_code = s.subject_code
    LEFT JOIN faculty f ON a.marked_by = f.faculty_id
    $where_clause
    ORDER BY a.class_date DESC, a.period_number ASC
");

if(mysqli_num_rows($attendance_query) > 0) {
    while($row = mysqli_fetch_assoc($attendance_query)) {
        $status_class = '';
        if ($row['attendance_status'] == 'Present') $status_class = 'status-present';
        elseif ($row['attendance_status'] == 'Absent') $status_class = 'status-absent';
        elseif ($row['attendance_status'] == 'On-Duty') $status_class = 'status-onduty';
        
        $day = date('l', strtotime($row['class_date']));
?>
<tr>
<td><?= date('d-M-Y', strtotime($row['class_date'])) ?></td>
<td><?= $day ?></td>
<td><?= $row['subject_name'] ?> (<?= $row['subject_code'] ?>)</td>
<td><?= $row['semester_number'] ?></td>
<td><?= $row['period_number'] ?></td>
<td><?= $row['batch'] ?></td>
<td><span class="status-badge <?= $status_class ?>"><?= $row['attendance_status'] ?></span></td>
<td><?= $row['faculty_name'] ?></td>
</tr>
<?php 
    }
} else {
    echo "<tr><td colspan='8' class='no-data'>No attendance records found</td></tr>";
}
?>
</tbody>
</table>
</div>

</div>

<script>
function exportToCSV() {
    var table = document.getElementById('attendance-table');
    var rows = table.querySelectorAll('tr');
    var csv = [];
    
    for (var i = 0; i < rows.length; i++) {
        var row = [], cols = rows[i].querySelectorAll('td, th');
        
        for (var j = 0; j < cols.length; j++) {
            var data = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, '').replace(/(\s\s)/gm, ' ');
            data = data.replace(/"/g, '""');
            row.push('"' + data + '"');
        }
        
        csv.push(row.join(','));
    }
    
    var csv_string = csv.join('\n');
    var filename = 'my_attendance_<?= $student_id ?>_' + new Date().toISOString().slice(0,10) + '.csv';
    
    var link = document.createElement('a');
    link.style.display = 'none';
    link.setAttribute('target', '_blank');
    link.setAttribute('href', 'data:text/csv;charset=utf-8,' + encodeURIComponent(csv_string));
    link.setAttribute('download', filename);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>

</body>
</html>