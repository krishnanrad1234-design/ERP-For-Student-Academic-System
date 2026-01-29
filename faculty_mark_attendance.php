<?php
include("../includes/auth.php");
include("../includes/db.php");

if ($_SESSION['user_type'] !== 'Faculty') {
    header("Location: ../login.php");
    exit;
}

$faculty_id = $_SESSION['user_id'];
$msg = "";

/* ---------- SAVE ATTENDANCE ---------- */
if (isset($_POST['save_attendance'])) {

    foreach ($_POST['status'] as $student_id => $status) {

        $sql = "
        INSERT INTO attendance
        (student_id, course_code, academic_year, semester_number,
         class_date, period_number, batch, subject_code,
         attendance_status, marked_by)
        VALUES (
            '{$_POST['student_id'][$student_id]}',
            '{$_POST['course_code']}',
            '{$_POST['academic_year']}',
            '{$_POST['semester_number']}',
            '{$_POST['class_date']}',
            '{$_POST['period_number']}',
            '{$_POST['batch']}',
            '{$_POST['subject_code']}',
            '$status',
            '$faculty_id'
        )
        ON DUPLICATE KEY UPDATE
            attendance_status='$status',
            updated_by='$faculty_id',
            updated_at=NOW()
        ";

        mysqli_query($conn, $sql);
    }

    $msg = "Attendance saved successfully";
}

// Get active tab
$active_tab = $_GET['tab'] ?? 'mark';
?>

<!DOCTYPE html>
<html>
<head>
<title>Faculty Attendance</title>
<style>
body{font-family:Arial;background:#f4f6f8;}
.box{width:90%;margin:20px auto;background:#fff;padding:20px;}
table{width:100%;border-collapse:collapse;}
th,td{border:1px solid #ccc;padding:8px;text-align:center;}
th{background:#343a40;color:#fff;}
.form-group{margin-bottom:15px;}
.period-section{margin-bottom:30px;border:2px solid #007bff;padding:15px;border-radius:8px;}
.period-section h4{background:#007bff;color:#fff;padding:10px;margin:-15px -15px 15px -15px;}

/* Tab Styles */
.tabs{display:flex;gap:10px;margin-bottom:20px;border-bottom:2px solid #dee2e6;}
.tab{padding:12px 24px;background:#f8f9fa;border:none;cursor:pointer;border-radius:8px 8px 0 0;font-size:16px;transition:all 0.3s;}
.tab:hover{background:#e9ecef;}
.tab.active{background:#007bff;color:#fff;font-weight:bold;}

/* Report Styles */
.filter-box{background:#e9ecef;padding:15px;border-radius:8px;margin-bottom:20px;}
.filter-row{display:flex;gap:10px;margin-bottom:10px;flex-wrap:wrap;}
.filter-row > div{flex:1;min-width:150px;}
.filter-row label{display:block;font-weight:bold;margin-bottom:5px;}
.filter-row input, .filter-row select{width:100%;padding:8px;border:1px solid #ccc;border-radius:4px;}
.filter-row button{background:#007bff;color:#fff;padding:10px 20px;border:none;border-radius:4px;cursor:pointer;}
.filter-row button:hover{background:#0056b3;}

.stats-box{display:flex;gap:15px;margin-bottom:20px;flex-wrap:wrap;}
.stat-card{flex:1;min-width:150px;background:#fff;border-left:4px solid;padding:15px;border-radius:4px;box-shadow:0 2px 4px rgba(0,0,0,0.1);}
.stat-card.present{border-color:#28a745;}
.stat-card.absent{border-color:#dc3545;}
.stat-card.onduty{border-color:#ffc107;}
.stat-card.total{border-color:#007bff;}
.stat-card h4{margin:0 0 5px 0;font-size:14px;color:#666;}
.stat-card .value{font-size:32px;font-weight:bold;margin:0;}
.stat-card .percentage{font-size:14px;color:#666;margin-top:5px;}

.status-present{color:#28a745;font-weight:bold;}
.status-absent{color:#dc3545;font-weight:bold;}
.status-onduty{color:#ffc107;font-weight:bold;}

.export-btn{background:#28a745;color:#fff;padding:8px 16px;border:none;border-radius:4px;cursor:pointer;margin-bottom:10px;}
.export-btn:hover{background:#218838;}

.clear-btn{background:#6c757d;color:#fff;padding:8px 16px;border:none;border-radius:4px;cursor:pointer;margin-left:10px;}
.clear-btn:hover{background:#5c636a;}
</style>
<script>
function loadTimetable() {
    var courseCode = document.querySelector('select[name="course_code"]').value;
    var semester = document.querySelector('select[name="semester_number"]').value;
    var classDate = document.querySelector('input[name="class_date"]').value;
    
    if(courseCode && semester && classDate) {
        window.location.href = '?tab=mark&course_code=' + courseCode + '&semester_number=' + semester + '&class_date=' + classDate;
    }
}
</script>
</head>
<body>

<div class="box">
<h3>Faculty Attendance Management</h3>

<!-- Tabs -->
<div class="tabs">
    <button class="tab <?= $active_tab == 'mark' ? 'active' : '' ?>" onclick="window.location.href='?tab=mark'">Mark Attendance</button>
    <button class="tab <?= $active_tab == 'view' ? 'active' : '' ?>" onclick="window.location.href='?tab=view'">View Attendance</button>
</div>

<?php if($active_tab == 'mark'): ?>
<!-- =============== MARK ATTENDANCE TAB =============== -->

<?php if($msg) echo "<p style='color:green'>$msg</p>"; ?>

<div class="form-group">
Date: <input type="date" name="class_date" value="<?= isset($_GET['class_date']) ? $_GET['class_date'] : date('Y-m-d') ?>" onchange="loadTimetable()" required>
</div>

<div class="form-group">
Course:
<select name="course_code" onchange="loadTimetable()" required>
<option value="">Select Course</option>
<?php
$c=mysqli_query($conn,"SELECT DISTINCT course_code FROM faculty WHERE faculty_id='$faculty_id'");
while($r=mysqli_fetch_assoc($c)){
    $selected = (isset($_GET['course_code']) && $_GET['course_code']==$r['course_code']) ? 'selected' : '';
    echo "<option $selected>{$r['course_code']}</option>";
}
?>
</select>
</div>

<div class="form-group">
Semester:
<select name="semester_number" onchange="loadTimetable()" required>
<option value="">Select Semester</option>
<?php 
for($i=1;$i<=6;$i++) {
    $selected = (isset($_GET['semester_number']) && $_GET['semester_number']==$i) ? 'selected' : '';
    echo "<option value='$i' $selected>$i</option>";
}
?>
</select>
</div>

<div class="form-group">
Academic Year:
<select name="academic_year" required>
<?php
$ay=mysqli_query($conn,"SELECT academic_year FROM academic_periods ORDER BY academic_year DESC LIMIT 1");
while($r=mysqli_fetch_assoc($ay)) echo "<option selected>{$r['academic_year']}</option>";
?>
</select>
</div>

<hr>

<?php
// Only show timetable if course, semester, and date are selected
if(isset($_GET['course_code']) && isset($_GET['semester_number']) && isset($_GET['class_date'])) {
    $course_code = mysqli_real_escape_string($conn, $_GET['course_code']);
    $semester = mysqli_real_escape_string($conn, $_GET['semester_number']);
    $class_date = mysqli_real_escape_string($conn, $_GET['class_date']);
    
    // Get day of week from the selected date
    $day_of_week = date('l', strtotime($class_date)); // Monday, Tuesday, etc.
    
    // Get academic year
    $ay_result = mysqli_query($conn,"SELECT academic_year FROM academic_periods ORDER BY academic_year DESC LIMIT 1");
    $ay_row = mysqli_fetch_assoc($ay_result);
    $academic_year = $ay_row['academic_year'];
    
    // Query to get timetable for this faculty on this day
    $timetable_query = mysqli_query($conn,"
    SELECT ct.period_number, ct.subject_code, ct.batch, ct.room_no,
           s.subject_name, CONCAT(f.first_name,' ',f.last_name) as faculty_name
    FROM class_timetable ct
    LEFT JOIN subjects s ON ct.subject_code = s.subject_code
    LEFT JOIN faculty f ON ct.faculty_id = f.faculty_id
    WHERE ct.course_code = '$course_code'
    AND ct.semester_number = '$semester'
    AND ct.faculty_id = '$faculty_id'
    AND ct.day = '$day_of_week'
    AND ct.academic_year = '$academic_year'
    ORDER BY ct.period_number
    ");
    
    $timetable_count = mysqli_num_rows($timetable_query);
    
    if($timetable_count > 0) {
        while($tt = mysqli_fetch_assoc($timetable_query)) {
            $period_number = $tt['period_number'];
            $subject_code = $tt['subject_code'];
            $batch = $tt['batch'];
            $subject_name = $tt['subject_name'];
            $room_no = $tt['room_no'];
?>

<div class="period-section">
<h4>Period <?= $period_number ?> - <?= $subject_name ?> (<?= $subject_code ?>) - Batch: <?= $batch ?> - Room: <?= $room_no ?></h4>

<form method="post" action="?tab=mark&course_code=<?= $course_code ?>&semester_number=<?= $semester ?>&class_date=<?= $class_date ?>">
<input type="hidden" name="course_code" value="<?= $course_code ?>">
<input type="hidden" name="semester_number" value="<?= $semester ?>">
<input type="hidden" name="class_date" value="<?= $class_date ?>">
<input type="hidden" name="academic_year" value="<?= $academic_year ?>">
<input type="hidden" name="period_number" value="<?= $period_number ?>">
<input type="hidden" name="subject_code" value="<?= $subject_code ?>">
<input type="hidden" name="batch" value="<?= $batch ?>">

<?php
            // Get all students for this course only
            $students = mysqli_query($conn,"
            SELECT s.student_id, s.student_name
            FROM students s
            WHERE s.course_code = '$course_code'
            ORDER BY s.student_id
            ");
            
            $student_count = mysqli_num_rows($students);
            
            if($student_count > 0) {
?>

<p><strong>Total Students: <?= $student_count ?></strong></p>

<table>
<tr>
<th>Student ID</th>
<th>Name</th>
<th>Status</th>
</tr>

<?php
while($s=mysqli_fetch_assoc($students)){
?>
<tr>
<td><?= $s['student_id'] ?></td>
<td><?= $s['student_name'] ?></td>
<td>
<input type="hidden" name="student_id[<?= $s['student_id'] ?>]" value="<?= $s['student_id'] ?>">
<select name="status[<?= $s['student_id'] ?>]">
<option value="Present" selected>Present</option>
<option value="Absent">Absent</option>
<option value="On-Duty">On-Duty</option>
</select>
</td>
</tr>
<?php } ?>
</table>

<br>
<button type="submit" name="save_attendance">Save Attendance for Period <?= $period_number ?></button>

<?php 
            } else {
                echo "<p style='color:red'>No students found for this course.</p>";
            }
?>

</form>
</div>

<?php 
        }
    } else {
        echo "<p style='color:orange'>No classes scheduled for <strong>$day_of_week</strong> in the timetable.</p>";
        echo "<p style='color:gray'>Faculty: $faculty_id | Course: $course_code | Semester: $semester | Date: $class_date ($day_of_week)</p>";
    }
} else {
    echo "<p style='color:blue'>Please select Course, Semester, and Date to load timetable.</p>";
}
?>

<?php else: ?>
<!-- =============== VIEW ATTENDANCE TAB =============== -->

<?php
// Get filter values
$view_course = $_GET['view_course'] ?? '';
$view_semester = $_GET['view_semester'] ?? '';
$view_subject = $_GET['view_subject'] ?? '';
$view_start_date = $_GET['view_start_date'] ?? '';
$view_end_date = $_GET['view_end_date'] ?? '';
$view_student = $_GET['view_student'] ?? '';
$view_status = $_GET['view_status'] ?? '';
?>

<!-- Filter Section -->
<div class="filter-box">
<form method="get">
<input type="hidden" name="tab" value="view">

<div class="filter-row">
<div>
<label>Course:</label>
<select name="view_course">
<option value="">All Courses</option>
<?php
$courses = mysqli_query($conn, "SELECT DISTINCT course_code FROM faculty WHERE faculty_id='$faculty_id'");
while($c = mysqli_fetch_assoc($courses)) {
    $selected = ($view_course == $c['course_code']) ? 'selected' : '';
    echo "<option value='{$c['course_code']}' $selected>{$c['course_code']}</option>";
}
?>
</select>
</div>

<div>
<label>Semester:</label>
<select name="view_semester">
<option value="">All Semesters</option>
<?php
for($i = 1; $i <= 6; $i++) {
    $selected = ($view_semester == $i) ? 'selected' : '';
    echo "<option value='$i' $selected>Semester $i</option>";
}
?>
</select>
</div>

<div>
<label>Subject:</label>
<select name="view_subject">
<option value="">All Subjects</option>
<?php
$subjects = mysqli_query($conn, "
    SELECT DISTINCT s.subject_code, s.subject_name 
    FROM subjects s
    JOIN class_timetable ct ON s.subject_code = ct.subject_code
    WHERE ct.faculty_id = '$faculty_id'
    ORDER BY s.subject_name
");
while($s = mysqli_fetch_assoc($subjects)) {
    $selected = ($view_subject == $s['subject_code']) ? 'selected' : '';
    echo "<option value='{$s['subject_code']}' $selected>{$s['subject_name']}</option>";
}
?>
</select>
</div>
</div>

<div class="filter-row">
<div>
<label>Start Date:</label>
<input type="date" name="view_start_date" value="<?= $view_start_date ?>">
</div>

<div>
<label>End Date:</label>
<input type="date" name="view_end_date" value="<?= $view_end_date ?>">
</div>

<div>
<label>Student ID:</label>
<input type="text" name="view_student" value="<?= $view_student ?>" placeholder="Search Student">
</div>

<div>
<label>Status:</label>
<select name="view_status">
<option value="">All Status</option>
<option value="Present" <?= ($view_status == 'Present') ? 'selected' : '' ?>>Present</option>
<option value="Absent" <?= ($view_status == 'Absent') ? 'selected' : '' ?>>Absent</option>
<option value="On-Duty" <?= ($view_status == 'On-Duty') ? 'selected' : '' ?>>On-Duty</option>
</select>
</div>
</div>

<div class="filter-row">
<button type="submit">Apply Filters</button>
<button type="button" class="clear-btn" onclick="window.location.href='?tab=view'">Clear Filters</button>
</div>
</form>
</div>

<?php
// Build WHERE clause
$where_conditions = ["a.marked_by = '$faculty_id'"];

if (!empty($view_course)) {
    $where_conditions[] = "a.course_code = '$view_course'";
}
if (!empty($view_semester)) {
    $where_conditions[] = "a.semester_number = '$view_semester'";
}
if (!empty($view_subject)) {
    $where_conditions[] = "a.subject_code = '$view_subject'";
}
if (!empty($view_start_date)) {
    $where_conditions[] = "a.class_date >= '$view_start_date'";
}
if (!empty($view_end_date)) {
    $where_conditions[] = "a.class_date <= '$view_end_date'";
}
if (!empty($view_student)) {
    $where_conditions[] = "a.student_id LIKE '%$view_student%'";
}
if (!empty($view_status)) {
    $where_conditions[] = "a.attendance_status = '$view_status'";
}

$where_clause = 'WHERE ' . implode(' AND ', $where_conditions);

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
    a.marked_at
FROM attendance a
LEFT JOIN students s ON a.student_id = s.student_id
LEFT JOIN subjects sub ON a.subject_code = sub.subject_code
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
<td><?= date('d-M-Y h:i A', strtotime($r['marked_at'])) ?></td>
</tr>
<?php 
    }
} else {
    echo "<tr><td colspan='10'>No attendance records found</td></tr>";
}
?>
</table>

<?php endif; ?>

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