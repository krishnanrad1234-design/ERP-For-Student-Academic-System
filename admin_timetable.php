<?php
include("../includes/auth.php");
include("../includes/db.php");

if ($_SESSION['user_type'] != 'Admin') {
    header("Location: ../login.php");
    exit;
}

/* ===== FILTER VALUES FOR GRID ===== */
$course  = $_GET['course_code']     ?? '';
$year    = $_GET['academic_year']   ?? '';
$sem     = $_GET['semester_number'] ?? '';
$batch   = $_GET['batch']           ?? '';

$days = ['Monday','Tuesday','Wednesday','Thursday','Friday'];
$periods = range(1,8);
?>
<!DOCTYPE html>
<html>
<head>
<title>Admin Timetable Management</title>
<style>
body{font-family:Arial;background:#f4f6f8;}
.box{
    width:95%;
    margin:20px auto;
    background:#fff;
    padding:20px;
    border-radius:8px;
}
.form-box{
    width:450px;
    margin:20px auto;
    background:#fff;
    padding:20px;
    border-radius:8px;
}
select,input,button{
    width:100%;
    padding:8px;
    margin:6px 0;
}
button{
    background:#007bff;
    color:#fff;
    border:none;
    cursor:pointer;
}
button:hover{background:#0056b3;}
table{
    width:100%;
    border-collapse:collapse;
    margin-top:15px;
}
th,td{
    border:1px solid #ccc;
    padding:8px;
    text-align:center;
    vertical-align:middle;
}
th{
    background:#343a40;
    color:#fff;
}
.cell{font-size:13px;}
.cell small{display:block;color:#555;}
.badge{
    display:inline-block;
    padding:2px 6px;
    font-size:11px;
    border-radius:4px;
    background:#6c757d;
    color:#fff;
    margin-top:4px;
}
hr{border:0;border-top:1px solid #eee;}
</style>
</head>
<body>

<!-- ================= CREATE TIMETABLE FORM ================= -->
<div class="form-box">
<h3>Create Timetable Entry</h3>

<form method="post" action="save_timetable_form.php">

Course:
<select name="course_code" id="course" required onchange="loadSubjects()">
<option value="">-- Select Course --</option>
<?php
$c=mysqli_query($conn,"SELECT course_code, course_name FROM courses");
while($r=mysqli_fetch_assoc($c)){
    echo "<option value='{$r['course_code']}'>{$r['course_name']}</option>";
}
?>
</select>

Academic Year:
<select name="academic_year" required>
<option value="">-- Select Academic Year --</option>
<?php
$ay=mysqli_query($conn,"SELECT DISTINCT academic_year FROM academic_periods");
while($r=mysqli_fetch_assoc($ay)){
    echo "<option value='{$r['academic_year']}'>{$r['academic_year']}</option>";
}
?>
</select>

Semester:
<select name="semester_number" id="semester" required onchange="loadSubjects()">
<option value="">-- Select Semester --</option>
<?php for($i=1;$i<=6;$i++) echo "<option value='$i'>$i</option>"; ?>
</select>

Subject:
<select name="subject_code" id="subject" required>
<option value="">-- Select Subject --</option>
</select>

Class Type:
<select name="class_mode" id="class_mode" required onchange="toggleBatch()">
<option value="">-- Select Type --</option>
<option value="Theory">Theory</option>
<option value="Lab">Lab</option>
</select>

Day:
<select name="day" required>
<option value="">-- Select Day --</option>
<?php foreach($days as $d) echo "<option>$d</option>"; ?>
</select>

Start Period:
<select name="start_period" required>
<?php for($i=1;$i<=8;$i++) echo "<option>$i</option>"; ?>
</select>

End Period:
<select name="end_period" required>
<?php for($i=1;$i<=8;$i++) echo "<option>$i</option>"; ?>
</select>

Batch:
<select name="batch" id="batch" required>
<option value="ALL">ALL</option>
<option value="A">A</option>
<option value="B">B</option>
</select>

Room Number:
<input type="text" name="room_no" required>

<button type="submit">Save Timetable</button>
</form>
</div>

<!-- ================= VIEW TIMETABLE GRID ================= -->
<div class="box">
<h3>Class Timetable (Grid View)</h3>

<form method="get">
Course:
<select name="course_code" required>
<option value="">Course</option>
<?php
$c=mysqli_query($conn,"SELECT course_code, course_name FROM courses");
while($r=mysqli_fetch_assoc($c)){
    $sel = ($course==$r['course_code'])?'selected':'';
    echo "<option value='{$r['course_code']}' $sel>{$r['course_name']}</option>";
}
?>
</select>

Academic Year:
<select name="academic_year" required>
<option value="">Year</option>
<?php
$ay=mysqli_query($conn,"SELECT DISTINCT academic_year FROM academic_periods");
while($r=mysqli_fetch_assoc($ay)){
    $sel = ($year==$r['academic_year'])?'selected':'';
    echo "<option value='{$r['academic_year']}' $sel>{$r['academic_year']}</option>";
}
?>
</select>

Semester:
<select name="semester_number" required>
<option value="">Semester</option>
<?php
for($i=1;$i<=6;$i++){
    $sel = ($sem==$i)?'selected':'';
    echo "<option value='$i' $sel>$i</option>";
}
?>
</select>

Batch:
<select name="batch" required>
<option value="">Batch</option>
<option value="ALL" <?= ($batch=='ALL')?'selected':'' ?>>ALL</option>
<option value="A" <?= ($batch=='A')?'selected':'' ?>>A</option>
<option value="B" <?= ($batch=='B')?'selected':'' ?>>B</option>
</select>

<button type="submit">View</button>
</form>

<?php
if ($course && $year && $sem && $batch) {

    $batchCondition = ($batch==='ALL')
        ? "ct.batch IN ('ALL','A','B')"
        : "ct.batch='$batch'";

    $q=mysqli_query($conn,"
        SELECT ct.day, ct.period_number, s.subject_name,
               CONCAT(f.first_name,' ',f.last_name) faculty_name,
               ct.room_no, ct.batch
        FROM class_timetable ct
        JOIN subjects s ON ct.subject_code=s.subject_code
        JOIN faculty f ON ct.faculty_id=f.faculty_id
        WHERE ct.course_code='$course'
          AND ct.academic_year='$year'
          AND ct.semester_number='$sem'
          AND $batchCondition
        ORDER BY ct.day, ct.period_number
    ");

    $data=[];
    while($r=mysqli_fetch_assoc($q)){
        $data[$r['day']][$r['period_number']][]=$r;
    }
?>
<table>
<tr>
<th>Day / Period</th>
<?php foreach($periods as $p) echo "<th>P$p</th>"; ?>
</tr>

<?php foreach($days as $d){ ?>
<tr>
<th><?= $d ?></th>
<?php foreach($periods as $p){ ?>
<td class="cell">
<?php
if(isset($data[$d][$p])){
    foreach($data[$d][$p] as $r){
        echo "<strong>{$r['subject_name']}</strong>";
        echo "<small>{$r['faculty_name']}</small>";
        echo "<small>Room: {$r['room_no']}</small>";
        echo "<span class='badge'>Batch {$r['batch']}</span><hr>";
    }
}else echo "-";
?>
</td>
<?php } ?>
</tr>
<?php } ?>
</table>
<?php } ?>
</div>

<script>
function loadSubjects(){
    let c=document.getElementById("course").value;
    let s=document.getElementById("semester").value;
    if(!c||!s)return;
    fetch("ajax_subjects.php?course="+c+"&sem="+s)
    .then(r=>r.text())
    .then(d=>document.getElementById("subject").innerHTML=d);
}
function toggleBatch(){
    let b=document.getElementById("batch");
    let m=document.getElementById("class_mode").value;
    b.disabled=(m==="Theory");
    if(m==="Theory") b.value="ALL";
}
</script>

</body>
</html>
