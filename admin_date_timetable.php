<?php
include("../includes/auth.php");
include("../includes/db.php");

if ($_SESSION['user_type'] != 'Admin') {
    header("Location: ../login.php");
    exit;
}

/* =======================
   HANDLE SAVE (POST)
======================= */
$saveSuccess = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_date'])) {

    $date       = $_POST['date'] ?? '';
    $is_working = $_POST['is_working'] ?? '';
    $follow_day = $_POST['follow_day'] ?? '';
    $reason     = $_POST['reason'] ?? '';

    if ($date && $is_working) {

        $date = date('Y-m-d', strtotime($date));
        $follow_day = $follow_day ?: NULL;
        $reason     = $reason ?: NULL;

        if ($is_working === 'No') {
            $follow_day = NULL;
        }

        mysqli_query($conn,"
            INSERT INTO date_timetable_mapping
            (date, is_working, follow_day, reason)
            VALUES
            (
                '$date',
                '$is_working',
                " . ($follow_day ? "'$follow_day'" : "NULL") . ",
                " . ($reason ? "'$reason'" : "NULL") . "
            )
            ON DUPLICATE KEY UPDATE
                is_working = VALUES(is_working),
                follow_day = VALUES(follow_day),
                reason     = VALUES(reason)
        ");

        $saveSuccess = true;
    }
}

/* =======================
   INPUTS FOR VIEW (GET)
======================= */
$date   = $_GET['date'] ?? '';
$course = $_GET['course_code'] ?? '';
$year   = $_GET['academic_year'] ?? '';
$sem    = $_GET['semester_number'] ?? '';
$batch  = $_GET['batch'] ?? '';

if ($date) {
    $date = date('Y-m-d', strtotime($date));
}

$submitted = ($date && $course && $year && $sem && $batch);
?>
<!DOCTYPE html>
<html>
<head>
<title>Admin Date Timetable</title>
<style>
body{font-family:Arial;background:#f4f6f8;}
.box{
    width:800px;
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
.row{
    border-bottom:1px solid #eee;
    padding:8px 0;
}
.badge{
    background:#6c757d;
    color:#fff;
    padding:2px 6px;
    border-radius:4px;
    font-size:12px;
}
.success{color:green;}
.error{color:red;}
.info{color:#0d6efd;}
hr{border:0;border-top:1px solid #ddd;margin:20px 0;}
</style>
</head>
<body>

<!-- =======================
     ASSIGN DATE SECTION
======================= -->
<div class="box">
<h3>🗓 Assign Date Timetable</h3>

<?php if ($saveSuccess) { ?>
<p class="success">✅ Date timetable saved successfully</p>
<?php } ?>

<form method="post">

Date:
<input type="date" name="date" required>

Working Day?
<select name="is_working" required>
    <option value="Yes">Yes</option>
    <option value="No">No</option>
</select>

Follow Which Weekday Timetable (optional):
<select name="follow_day">
    <option value="">-- Actual Day --</option>
    <option>Monday</option>
    <option>Tuesday</option>
    <option>Wednesday</option>
    <option>Thursday</option>
    <option>Friday</option>
</select>

Reason (optional):
<input type="text" name="reason">

<button type="submit" name="save_date">Save Date Mapping</button>
</form>
</div>

<!-- =======================
     VIEW TIMETABLE SECTION
======================= -->
<div class="box">
<h3>📅 View Timetable by Date</h3>

<form method="get">

Date:
<input type="date" name="date" value="<?= htmlspecialchars($date) ?>" required>

Course:
<select name="course_code" required>
<option value="">-- Course --</option>
<?php
$c=mysqli_query($conn,"SELECT course_code,course_name FROM courses");
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
$ay=mysqli_query($conn,"SELECT DISTINCT academic_year FROM academic_periods");
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
if ($submitted) {

    $actualDay = date('l', strtotime($date));

    $q=mysqli_query($conn,"
        SELECT is_working, follow_day
        FROM date_timetable_mapping
        WHERE date='$date'
    ");

    if(mysqli_num_rows($q)){
        $r=mysqli_fetch_assoc($q);

        if($r['is_working']=='No'){
            echo "<p class='error'>❌ Holiday – No classes</p>";
            exit;
        }

        $dayToUse = $r['follow_day'] ?: $actualDay;

        echo "<p class='info'>Actual Day: <b>$actualDay</b></p>";
        echo "<p class='success'>Following <b>$dayToUse</b> timetable</p>";

    } else {
        $dayToUse = $actualDay;
        echo "<p class='info'>No override set. Following <b>$actualDay</b> timetable</p>";
    }

    $batchCond = ($batch=='ALL')
        ? "ct.batch IN ('ALL','A','B')"
        : "ct.batch='$batch'";

    $tt=mysqli_query($conn,"
        SELECT ct.period_number,
               s.subject_name,
               CONCAT(f.first_name,' ',f.last_name) faculty,
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

    if(mysqli_num_rows($tt)==0){
        echo "<p class='error'>No timetable found.</p>";
    } else {
        while($row=mysqli_fetch_assoc($tt)){
            echo "<div class='row'>";
            echo "<b>Period {$row['period_number']}</b><br>";
            echo "{$row['subject_name']}<br>";
            echo "{$row['faculty']}<br>";
            echo "Room {$row['room_no']} ";
            echo "<span class='badge'>Batch {$row['batch']}</span>";
            echo "</div>";
        }
    }
}
?>

</div>
</body>
</html>
