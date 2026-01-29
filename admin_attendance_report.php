<?php
include("../includes/auth.php");
include("../includes/db.php");

if ($_SESSION['user_type'] !== 'Admin') {
    header("Location: ../login.php");
    exit;
}

// Get filter values
$course_code = $_GET['course_code'] ?? '';
$semester = $_GET['semester_number'] ?? '';
$academic_year = $_GET['academic_year'] ?? '';
$subject_code = $_GET['subject_code'] ?? '';
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';
$student_id = $_GET['student_id'] ?? '';
$attendance_status = $_GET['attendance_status'] ?? '';
?>

<!DOCTYPE html>
<html>
<head>
<title>Attendance Report</title>
<style>
body{font-family:Arial;background:#f4f6f8;}
.box{width:95%;margin:20px auto;background:#fff;padding:20px;border-radius:8px;}
.filter-box{background:#e9ecef;padding:15px;border-radius:8px;margin-bottom:20px;}
.filter-row{display:flex;gap:10px;margin-bottom:10px;flex-wrap:wrap;}
.filter-row > div{flex:1;min-width:150px;}
.filter-row label{display:block;font-weight:bold;margin-bottom:5px;}
.filter-row input, .filter-row select{width:100%;padding:8px;border:1px solid #ccc;border-radius:4px;}
.filter-row button{background:#198754;color:#fff;padding:10px 20px;border:none;border-radius:4px;cursor:pointer;}
.filter-row button:hover{background:#157347;}
.stats-box{display:flex;gap:15px;margin-bottom:20px;flex-wrap:wrap;}
.stat-card{flex:1;min-width:150px;background:#fff;border-left:4px solid;padding:15px;border-radius:4px;box-shadow:0 2px 4px rgba(0,0,0,0.1);}
.stat-card.present{border-color:#198754;}
.stat-card.absent{border-color:#dc3545;}
.stat-card.onduty{border-color:#ffc107;}
.stat-card.total{border-color:#0d6efd;}
.stat-card h4{margin:0 0 5px 0;font-size:14px;color:#666;}
.stat-card .value{font-size:32px;font-weight:bold;margin:0;}
.stat-card .percentage{font-size:14px;color:#666;margin-top:5px;}
table{width:100%;border-collapse:collapse;margin-top:20px;}
th,td{border:1px solid #ccc;padding:8px;text-align:center;}
th{background:#198754;color:#fff;}
.status-present{color:#198754;font-weight:bold;}
.status-absent{color:#dc3545;font-weight:bold;}
.status-onduty{color:#ffc107;font-weight:bold;}
.export-btn{background:#0d6efd;color:#fff;padding:8px 16px;border:none;border-radius:4px;cursor:pointer;margin-bottom:10px;}
.export-btn:hover{background:#0b5ed7;}
.clear-btn{background:#6c757d;color:#fff;padding:8px 16px;border:none;border-radius:4px;cursor:pointer;margin-left:10px;}
.clear-btn:hover{background:#5c636a;}
</style>
</head>
<body>

<div class="box">
<h3>Attendance Report</h3>

<!-- Filter Section -->
<div class="filter-box">
<form method="get">
<div class="filter-row">
<div>
<label>Course:</label>
<select name="course_code">
<option value="">All Courses</option>
<?php
$courses = mysqli_query($conn, "SELECT course_code, course_name FROM courses ORDER BY course_name");
while($c = mysqli_fetch_assoc($courses)) {
    $selected = ($course_code == $c['course_code']) ? 'selected' : '';
    echo "<option value='{$c['course_code']}' $selected>{$c['course_name']}</option>";
}
?>
</select>
</div>

<div>
<label>Semester:</label>
<select name="semester_number">
<option value="">All Semesters</option>
<?php
for($i = 1; $i <= 6; $i++) {
    $selected = ($semester == $i) ? 'selected' : '';
    echo "<option value='$i' $selected>Semester $i</option>";
}
?>
</select>
</div>

<div>
<label>Academic Year:</label>
<select name="academic_year">
<option value="">All Years</option>
<?php
$years = mysqli_query($conn, "SELECT DISTINCT academic_year FROM academic_periods ORDER BY academic_year DESC");
while($y = mysqli_fetch_assoc($years)) {
    $selected = ($academic_year == $y['academic_year']) ? 'selected' : '';
    echo "<option value='{$y['academic_year']}' $selected>{$y['academic_year']}</option>";
}
?>
</select>
</div>

<div>
<label>Subject:</label>
<select name="subject_code">
<option value="">All Subjects</option>
<?php
$subjects = mysqli_query($conn, "SELECT subject_code, subject_name FROM subjects ORDER BY subject_name");
while($s = mysqli_fetch_assoc($subjects)) {
    $selected = ($subject_code == $s['subject_code']) ? 'selected' : '';
    echo "<option value='{$s['subject_code']}' $selected>{$s['subject_name']}</option>";
}
?>
</select>
</div>
</div>

<div class="filter-row">
<div>
<label>Start Date:</label>
<input type="date" name="start_date" value="<?= $start_date ?>">
</div>

<div>
<label>End Date:</label>
<input type="date" name="end_date" value="<?= $end_date ?>">
</div>

<div>
<label>Student ID:</label>
<input type="text" name="student_id" value="<?= $student_id ?>" placeholder="Enter Student ID">
</div>

<div>
<label>Status:</label>
<select name="attendance_status">
<option value="">All Status</option>
<option value="Present" <?= ($attendance_status == 'Present') ? 'selected' : '' ?>>Present</option>
<option value="Absent" <?= ($attendance_status == 'Absent') ? 'selected' : '' ?>>Absent</option>
<option value="On-Duty" <?= ($attendance_status == 'On-Duty') ? 'selected' : '' ?>>On-Duty</option>
</select>
</div>
</div>

<div class="filter-row">
<button type="submit">Apply Filters</button>
<button type="button" class="clear-btn" onclick="window.location.href='admin_attendance_report.php'">Clear Filters</button>
</div>
</form>
</div>

<?php
// Build WHERE clause based on filters
$where_conditions = [];

if (!empty($course_code)) {
    $where_conditions[] = "a.course_code = '$course_code'";
}
if (!empty($semester)) {
    $where_conditions[] = "a.semester_number = '$semester'";
}
if (!empty($academic_year)) {
    $where_conditions[] = "a.academic_year = '$academic_year'";
}
if (!empty($subject_code)) {
    $where_conditions[] = "a.subject_code = '$subject_code'";
}
if (!empty($start_date)) {
    $where_conditions[] = "a.class_date >= '$start_date'";
}
if (!empty($end_date)) {
    $where_conditions[] = "a.class_date <= '$end_date'";
}
if (!empty($student_id)) {
    $where_conditions[] = "a.student_id LIKE '%$student_id%'";
}
if (!empty($attendance_status)) {
    $where_conditions[] = "a.attendance_status = '$attendance_status'";
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Get statistics
$stats_query = mysqli_query($conn, "
SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN attendance_status = 'Present' THEN 1 ELSE 0 END) as present,
    SUM(CASE WHEN attendance_status = 'Absent' THEN 1 ELSE 0 END) as absent,
    SUM(CASE WHEN attendance_status = 'On-Duty' THEN 1 ELSE 0 END) as onduty
FROM attendance a
$where_clause
");

$stats = mysqli_fetch_assoc($stats_query);
$total = $stats['total'];
$present = $stats['present'];
$absent = $stats['absent'];
$onduty = $stats['onduty'];

$present_pct = $total > 0 ? round(($present / $total) * 100, 1) : 0;
$absent_pct = $total > 0 ? round(($absent / $total) * 100, 1) : 0;
$onduty_pct = $total > 0 ? round(($onduty / $total) * 100, 1) : 0;
?>

<!-- Statistics Cards -->
<div class="stats-box">
<div class="stat-card total">
<h4>Total Records</h4>
<p class="value"><?= $total ?></p>
</div>

<div class="stat-card present">
<h4>Present</h4>
<p class="value"><?= $present ?></p>
<p class="percentage"><?= $present_pct ?>%</p>
</div>

<div class="stat-card absent">
<h4>Absent</h4>
<p class="value"><?= $absent ?></p>
<p class="percentage"><?= $absent_pct ?>%</p>
</div>

<div class="stat-card onduty">
<h4>On-Duty</h4>
<p class="value"><?= $onduty ?></p>
<p class="percentage"><?= $onduty_pct ?>%</p>
</div>
</div>

<!-- Export Button -->
<button class="export-btn" onclick="exportToCSV()">Export to CSV</button>

<!-- Attendance Table -->
<table id="attendance-table">
<tr>
<th>Student ID</th>
<th>Student Name</th>
<th>Course</th>
<th>Semester</th>
<th>Date</th>
<th>Subject</th>
<th>Period</th>
<th>Batch</th>
<th>Status</th>
<th>Marked By</th>
<th>Marked At</th>
</tr>

<?php
$q = mysqli_query($conn, "
SELECT 
    a.student_id,
    s.student_name,
    a.course_code,
    a.semester_number,
    a.class_date,
    a.subject_code,
    sub.subject_name,
    a.period_number,
    a.batch,
    a.attendance_status,
    CONCAT(f.first_name,' ',f.last_name) as faculty,
    a.marked_at
FROM attendance a
LEFT JOIN students s ON a.student_id = s.student_id
LEFT JOIN subjects sub ON a.subject_code = sub.subject_code
LEFT JOIN faculty f ON a.marked_by = f.faculty_id
$where_clause
ORDER BY a.class_date DESC, a.period_number ASC
");

if (mysqli_num_rows($q) > 0) {
    while($r = mysqli_fetch_assoc($q)) {
        $status_class = '';
        if ($r['attendance_status'] == 'Present') $status_class = 'status-present';
        elseif ($r['attendance_status'] == 'Absent') $status_class = 'status-absent';
        elseif ($r['attendance_status'] == 'On-Duty') $status_class = 'status-onduty';
?>
<tr>
<td><?= $r['student_id'] ?></td>
<td><?= $r['student_name'] ?></td>
<td><?= $r['course_code'] ?></td>
<td><?= $r['semester_number'] ?></td>
<td><?= date('d-M-Y', strtotime($r['class_date'])) ?></td>
<td><?= $r['subject_name'] ?> (<?= $r['subject_code'] ?>)</td>
<td><?= $r['period_number'] ?></td>
<td><?= $r['batch'] ?></td>
<td class="<?= $status_class ?>"><?= $r['attendance_status'] ?></td>
<td><?= $r['faculty'] ?></td>
<td><?= date('d-M-Y h:i A', strtotime($r['marked_at'])) ?></td>
</tr>
<?php 
    }
} else {
    echo "<tr><td colspan='11'>No attendance records found</td></tr>";
}
?>
</table>
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
    var filename = 'attendance_report_' + new Date().toISOString().slice(0,10) + '.csv';
    
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