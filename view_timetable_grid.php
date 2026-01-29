<?php
include("../includes/auth.php");
include("../includes/db.php");

if ($_SESSION['user_type'] != 'Admin') {
    header("Location: ../login.php");
    exit;
}

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
<title>View Timetable</title>
<style>
body{font-family:Arial;background:#f4f6f8;}
.box{
    width:95%;
    margin:20px auto;
    background:#fff;
    padding:20px;
    border-radius:8px;
}
select,button{
    padding:6px;
    margin:4px;
}
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
.cell{
    font-size:13px;
}
.cell small{
    display:block;
    color:#555;
}
.badge{
    display:inline-block;
    padding:2px 6px;
    font-size:11px;
    border-radius:4px;
    background:#6c757d;
    color:#fff;
    margin-top:4px;
}
</style>
</head>
<body>

<div class="box">
<h3>Class Timetable (Grid View)</h3>

<!-- FILTER FORM -->
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
<option value="A"   <?= ($batch=='A')?'selected':'' ?>>A</option>
<option value="B"   <?= ($batch=='B')?'selected':'' ?>>B</option>
</select>

<button type="submit">View</button>
</form>

<?php
if ($course && $year && $sem && $batch) {

    /* ✅ BATCH LOGIC FIX */
    if ($batch === 'ALL') {
        $batchCondition = "ct.batch IN ('ALL','A','B')";
    } else {
        $batchCondition = "ct.batch = '$batch'";
    }

    $q = mysqli_query($conn, "
        SELECT 
            ct.day,
            ct.period_number,
            s.subject_name,
            CONCAT(f.first_name, ' ', f.last_name) AS faculty_name,
            ct.room_no,
            ct.batch
        FROM class_timetable ct
        JOIN subjects s ON ct.subject_code = s.subject_code
        JOIN faculty  f ON ct.faculty_id  = f.faculty_id
        WHERE ct.course_code='$course'
          AND ct.academic_year='$year'
          AND ct.semester_number='$sem'
          AND $batchCondition
        ORDER BY ct.day, ct.period_number, ct.batch
    ");

    $data = [];
    while($r=mysqli_fetch_assoc($q)){
        // allow multiple batches in same period
        $data[$r['day']][$r['period_number']][] = $r;
    }
?>

<!-- GRID TABLE -->
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
        } else {
            echo "-";
        }
        ?>
    </td>
    <?php } ?>

</tr>
<?php } ?>
</table>

<?php } ?>

</div>
</body>
</html>
