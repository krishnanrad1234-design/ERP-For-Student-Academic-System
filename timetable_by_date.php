<?php
include("../includes/auth.php");
include("../includes/db.php");

/* =======================
   INPUTS
======================= */
$date   = $_GET['date'] ?? '';
$course = $_GET['course_code'] ?? '';
$year   = $_GET['academic_year'] ?? '';
$sem    = $_GET['semester_number'] ?? '';
$batch  = $_GET['batch'] ?? '';

$submitted = ($date || $course || $year || $sem || $batch);

/* Normalize date */
if ($date) {
    $date = date('Y-m-d', strtotime($date));
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Timetable by Date</title>
<style>
body{font-family:Arial;background:#f4f6f8;}
.box{width:750px;margin:20px auto;background:#fff;padding:20px;border-radius:8px;}
select,input,button{width:100%;padding:8px;margin:6px 0;}
.row{border-bottom:1px solid #eee;padding:8px 0;}
.badge{background:#6c757d;color:#fff;padding:2px 6px;border-radius:4px;font-size:12px;}
.error{color:red;}
.info{color:#0d6efd;}
.success{color:green;}
</style>
</head>
<body>

<div class="box">
<h3>📅 View Timetable by Date</h3>

<!-- =======================
     FILTER FORM
======================= -->
<form method="get">

Date:
<input type="date" name="date" value="<?= htmlspecialchars($date) ?>" required>

Course:
<select name="course_code" required>
<option value="">-- Course --</option>
<?php
$c = mysqli_query($conn,"SELECT course_code,course_name FROM courses");
while($r=mysqli_fetch_assoc($c)){
    $sel = ($course==$r['course_code'])?'selected':'';
    echo "<option value='{$r['course_code']}' $sel>{$r['course_name']}</option>";
}
?>
</select>

Academic Year:
<select name="academic_year" required>
<option value="">-- Year --</option>
<?php
$ay = mysqli_query($conn,"SELECT DISTINCT academic_year FROM academic_periods");
while($r=mysqli_fetch_assoc($ay)){
    $sel = ($year==$r['academic_year'])?'selected':'';
    echo "<option value='{$r['academic_year']}' $sel>{$r['academic_year']}</option>";
}
?>
</select>

Semester:
<select name="semester_number" required>
<option value="">-- Semester --</option>
<?php
for($i=1;$i<=6;$i++){
    $sel = ($sem==$i)?'selected':'';
    echo "<option value='$i' $sel>$i</option>";
}
?>
</select>

Batch:
<select name="batch" required>
<option value="">-- Batch --</option>
<option value="ALL" <?=($batch=='ALL')?'selected':''?>>ALL</option>
<option value="A" <?=($batch=='A')?'selected':''?>>A</option>
<option value="B" <?=($batch=='B')?'selected':''?>>B</option>
</select>

<button type="submit">View Timetable</button>
</form>

<hr>

<?php
/* =======================
   PROCESS AFTER SUBMIT
======================= */
if ($submitted) {

    /* Validation */
    $missing=[];
    if(!$date)   $missing[]="Date";
    if(!$course) $missing[]="Course";
    if(!$year)   $missing[]="Academic Year";
    if(!$sem)    $missing[]="Semester";
    if(!$batch)  $missing[]="Batch";

    if($missing){
        echo "<p class='error'>Missing: ".implode(', ',$missing)."</p>";
        exit;
    }

    /* Actual day */
    $actualDay = date('l', strtotime($date));

    /* =======================
       DATE OVERRIDE CHECK
    ======================= */
    $overrideMsg = "No override set. Following actual day timetable.";

    $q = mysqli_query($conn,"
        SELECT is_working, follow_day
        FROM date_timetable_mapping
        WHERE date='$date'
    ");

    if(mysqli_num_rows($q)){
        $r = mysqli_fetch_assoc($q);

        if($r['is_working'] === 'No'){
            echo "<h3 class='error'>❌ Holiday (No classes)</h3>";
            exit;
        }

        if($r['follow_day']){
            $dayToUse = $r['follow_day'];
            $overrideMsg = "Override applied: Following <b>{$r['follow_day']}</b> timetable.";
        } else {
            $dayToUse = $actualDay;
            $overrideMsg = "Marked working. Following actual day timetable.";
        }

    } else {
        $dayToUse = $actualDay;
    }

    echo "<p class='info'>📌 Actual Day: <b>$actualDay</b></p>";
    echo "<p class='success'>$overrideMsg</p>";

    /* =======================
       BATCH CONDITION
    ======================= */
    $batchCond = ($batch=='ALL')
        ? "ct.batch IN ('ALL','A','B')"
        : "ct.batch='$batch'";

    /* =======================
       FETCH TIMETABLE
    ======================= */
    $tt = mysqli_query($conn,"
        SELECT
            ct.period_number,
            s.subject_name,
            CONCAT(f.first_name,' ',f.last_name) AS faculty,
            ct.room_no,
            ct.batch
        FROM class_timetable ct
        JOIN subjects s ON ct.subject_code=s.subject_code
        JOIN faculty f ON ct.faculty_id=f.faculty_id
        WHERE ct.course_code='$course'
          AND ct.academic_year='$year'
          AND ct.semester_number='$sem'
          AND ct.day='$dayToUse'
          AND $batchCond
        ORDER BY ct.period_number, ct.batch
    ");

    echo "<h3>📘 Timetable for <b>$dayToUse</b></h3>";

    if(mysqli_num_rows($tt)==0){
        echo "<p class='error'>No timetable found for this day.</p>";
    } else {
        while($row=mysqli_fetch_assoc($tt)){
            echo "<div class='row'>";
            echo "<b>Period {$row['period_number']}</b><br>";
            echo "{$row['subject_name']}<br>";
            echo "{$row['faculty']}<br>";
            echo "Room: {$row['room_no']} ";
            echo "<span class='badge'>Batch {$row['batch']}</span>";
            echo "</div>";
        }
    }
}
?>

</div>
</body>
</html>
