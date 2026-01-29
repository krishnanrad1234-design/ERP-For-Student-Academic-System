<?php
include("../includes/auth.php");
include("../includes/db.php");

/* ===== ALLOW ONLY FACULTY ===== */
if ($_SESSION['user_type'] !== 'Faculty') {
    header("Location: ../login.php");
    exit;
}

$faculty_id = $_SESSION['user_id'];

$date   = $_GET['date'] ?? '';
$year   = $_GET['academic_year'] ?? '';
$sem    = $_GET['semester_number'] ?? '';

if ($date) {
    $date = date('Y-m-d', strtotime($date));
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Faculty Timetable</title>

<style>
body{font-family:Arial;background:#f4f6f8;}
.box{
    width:600px;
    margin:25px auto;
    background:#fff;
    padding:20px;
    border-radius:8px;
}
.row{
    border-bottom:1px solid #ddd;
    padding:10px 0;
}
.badge{
    background:#6c757d;
    color:#fff;
    padding:2px 6px;
    border-radius:4px;
    font-size:12px;
}
.error{color:red;}
.info{color:#0d6efd;}
</style>
</head>

<body>

<div class="box">
<h3>📘 My Timetable</h3>

<form method="get">
Date:
<input type="date" name="date" required>

Academic Year:
<select name="academic_year" required>
<?php
$ay = mysqli_query($conn,"SELECT DISTINCT academic_year FROM academic_periods");
while($r=mysqli_fetch_assoc($ay)){
    echo "<option>{$r['academic_year']}</option>";
}
?>
</select>

Semester:
<select name="semester_number" required>
<?php for($i=1;$i<=6;$i++) echo "<option>$i</option>"; ?>
</select>

<button type="submit">View</button>
</form>

<hr>

<?php
if ($date && $year && $sem) {

    /* ===== DATE MAPPING ===== */
    $actualDay = date('l', strtotime($date));

    $map = mysqli_query($conn,"
        SELECT is_working, follow_day
        FROM date_timetable_mapping
        WHERE date='$date'
    ");

    if(mysqli_num_rows($map)){
        $m = mysqli_fetch_assoc($map);

        if($m['is_working']=='No'){
            echo "<p class='error'>❌ Holiday – No classes</p>";
            exit;
        }

        $dayToUse = $m['follow_day'] ?: $actualDay;
    } else {
        $dayToUse = $actualDay;
    }

    echo "<p class='info'>📅 Following <b>$dayToUse</b> timetable</p>";

    /* ===== FACULTY-ONLY TIMETABLE ===== */
    $tt = mysqli_query($conn,"
        SELECT ct.period_number,
               s.subject_name,
               ct.room_no,
               ct.batch
        FROM class_timetable ct
        JOIN subjects s ON ct.subject_code=s.subject_code
        WHERE ct.faculty_id='$faculty_id'
          AND ct.academic_year='$year'
          AND ct.semester_number='$sem'
          AND ct.day='$dayToUse'
        ORDER BY ct.period_number
    ");

    if(mysqli_num_rows($tt)==0){
        echo "<p class='error'>No classes assigned.</p>";
    }

    while($r=mysqli_fetch_assoc($tt)){
        echo "<div class='row'>";
        echo "<b>Period {$r['period_number']}</b><br>";
        echo "{$r['subject_name']}<br>";
        echo "Room {$r['room_no']} ";
        echo "<span class='badge'>Batch {$r['batch']}</span>";
        echo "</div>";
    }
}
?>

</div>
</body>
</html>
