<?php
include("../includes/auth.php");
include("../includes/db.php");

/* ===============================
   READ INPUTS (SAFE)
================================ */
$date   = $_GET['date'] ?? '';
$course = $_GET['course_code'] ?? '';
$year   = $_GET['academic_year'] ?? '';
$sem    = $_GET['semester_number'] ?? '';
$batch  = $_GET['batch'] ?? '';

/* ===============================
   BASIC VALIDATION
================================ */
$errors = [];

if ($date   === '') $errors[] = "Date";
if ($course === '') $errors[] = "Course";
if ($year   === '') $errors[] = "Academic Year";
if ($sem    === '') $errors[] = "Semester";
if ($batch  === '') $errors[] = "Batch";

if ($errors) {
    echo "<h3 style='color:red'>Missing: " . implode(', ', $errors) . "</h3>";
    exit;
}

/* ===============================
   FIND ACTUAL DAY FROM DATE
================================ */
$actualDay = date('l', strtotime($date)); // Monday, Tuesday, Saturday...

/* ===============================
   CHECK DATE OVERRIDE TABLE
================================ */
$dayToUse = null;

$q = mysqli_query($conn,"
    SELECT is_working, follow_day
    FROM date_timetable_mapping
    WHERE date = '$date'
");

if (mysqli_num_rows($q) > 0) {

    $r = mysqli_fetch_assoc($q);

    if ($r['is_working'] === 'No') {
        echo "<h3 style='color:red'>❌ Holiday</h3>";
        exit;
    }

    if ($actualDay === 'Saturday' && $r['follow_day'] !== null) {
        $dayToUse = $r['follow_day'];     // Saturday follows selected weekday
    } else {
        $dayToUse = $actualDay;           // Normal weekday
    }

} else {
    // No special rule
    if (in_array($actualDay, ['Saturday','Sunday'])) {
        echo "<h3 style='color:red'>❌ Holiday</h3>";
        exit;
    }
    $dayToUse = $actualDay;
}

/* ===============================
   BATCH CONDITION
================================ */
$batchCondition = ($batch === 'ALL')
    ? "ct.batch IN ('ALL','A','B')"
    : "ct.batch = '$batch'";

/* ===============================
   FETCH TIMETABLE
================================ */
$tt = mysqli_query($conn,"
    SELECT
        ct.period_number,
        s.subject_name,
        CONCAT(f.first_name,' ',f.last_name) AS faculty,
        ct.room_no,
        ct.batch
    FROM class_timetable ct
    JOIN subjects s ON ct.subject_code = s.subject_code
    JOIN faculty  f ON ct.faculty_id  = f.faculty_id
    WHERE ct.course_code     = '$course'
      AND ct.academic_year   = '$year'
      AND ct.semester_number = '$sem'
      AND ct.day             = '$dayToUse'
      AND $batchCondition
    ORDER BY ct.period_number, ct.batch
");
?>
<!DOCTYPE html>
<html>
<head>
<title>View Timetable by Date</title>
<style>
body{font-family:Arial;background:#f4f6f8;}
.box{
    width:600px;
    margin:30px auto;
    background:#fff;
    padding:20px;
    border-radius:8px;
}
.row{
    padding:8px;
    border-bottom:1px solid #eee;
}
.badge{
    background:#6c757d;
    color:#fff;
    padding:2px 6px;
    border-radius:4px;
    font-size:12px;
}
</style>
</head>
<body>

<div class="box">
<h3>
📅 <?= htmlspecialchars($date) ?>  
→ Following <b><?= htmlspecialchars($dayToUse) ?></b> Timetable
</h3>

<?php
if (mysqli_num_rows($tt) == 0) {
    echo "<p>No timetable found.</p>";
} else {
    while ($r = mysqli_fetch_assoc($tt)) {
        echo "<div class='row'>";
        echo "<b>Period {$r['period_number']}</b><br>";
        echo "{$r['subject_name']}<br>";
        echo "{$r['faculty']}<br>";
        echo "Room: {$r['room_no']} ";
        echo "<span class='badge'>Batch {$r['batch']}</span>";
        echo "</div>";
    }
}
?>

</div>
</body>
</html>
