<?php
include("../includes/auth.php");
include("../includes/db.php");

/* ===== ALLOW ONLY STUDENT ===== */
if ($_SESSION['user_type'] !== 'Student') {
    header("Location: ../login.php");
    exit;
}

$student_id = $_SESSION['user_id'];

/* ===== STUDENT DETAILS ===== */
$s = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT course_code, current_semester
    FROM students
    WHERE student_id='$student_id'
"));

$course = $s['course_code'];
$sem    = $s['current_semester'];

$date = $_GET['date'] ?? '';
$year = $_GET['academic_year'] ?? '';

if ($date) {
    $date = date('Y-m-d', strtotime($date));
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Student Timetable</title>

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
<h3>📘 My Class Timetable</h3>

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

<button type="submit">View</button>
</form>

<hr>

<?php
if ($date && $year) {

    $actualDay = date('l', strtotime($date));

    $map = mysqli_query($conn,"
        SELECT is_working, follow_day
        FROM date_timetable_mapping
        WHERE date='$date'
    ");

    if(mysqli_num_rows($map)){
        $m=mysqli_fetch_assoc($map);

        if($m['is_working']=='No'){
            echo "<p class='error'>❌ Holiday – No classes</p>";
            exit;
        }

        $dayToUse = $m['follow_day'] ?: $actualDay;
    } else {
        $dayToUse = $actualDay;
    }

    echo "<p class='info'>📅 Following <b>$dayToUse</b> timetable</p>";

    /* ===== STUDENT BATCH ===== */
    $b = mysqli_fetch_assoc(mysqli_query($conn,"
        SELECT batch
        FROM student_batches
        WHERE student_id='$student_id'
          AND academic_year='$year'
          AND semester_number='$sem'
    "));
    $batch = $b['batch'] ?? 'ALL';

    /* ===== STUDENT TIMETABLE ===== */
    $tt = mysqli_query($conn,"
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
          AND (ct.batch='ALL' OR ct.batch='$batch')
        ORDER BY ct.period_number
    ");

    if(mysqli_num_rows($tt)==0){
        echo "<p class='error'>No classes found.</p>";
    }

    while($r=mysqli_fetch_assoc($tt)){
        echo "<div class='row'>";
        echo "<b>Period {$r['period_number']}</b><br>";
        echo "{$r['subject_name']}<br>";
        echo "{$r['faculty']}<br>";
        echo "Room {$r['room_no']} ";
        echo "<span class='badge'>Batch {$r['batch']}</span>";
        echo "</div>";
    }
}
?>

</div>
</body>
</html>
