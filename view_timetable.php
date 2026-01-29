<?php
include("../includes/auth.php");
include("../includes/db.php");

if($_SESSION['user_type']!='Admin'){header("Location:../login.php");exit;}

$where="";
if(isset($_GET['course_code'])){
$where="WHERE ct.course_code='{$_GET['course_code']}'
AND ct.semester_number='{$_GET['semester_number']}'
AND ct.academic_year='{$_GET['academic_year']}'";
}
?>
<!DOCTYPE html>
<html>
<head>
<title>View Timetable</title>
<style>
table{width:100%;border-collapse:collapse}
th,td{border:1px solid #ccc;padding:6px;text-align:center}
</style>
</head>
<body>

<form method="get">
<select name="course_code">
<?php
$c=mysqli_query($conn,"SELECT course_code,course_name FROM courses");
while($r=mysqli_fetch_assoc($c))
echo "<option value='{$r['course_code']}'>{$r['course_name']}</option>";
?>
</select>

<select name="semester_number">
<?php for($i=1;$i<=6;$i++) echo "<option>$i</option>"; ?>
</select>

<select name="academic_year">
<?php
$ay=mysqli_query($conn,"SELECT DISTINCT academic_year FROM academic_periods");
while($r=mysqli_fetch_assoc($ay)) echo "<option>{$r['academic_year']}</option>";
?>
</select>
<button>View</button>
</form>

<table>
<tr>
<th>Date</th><th>Day</th><th>Period</th>
<th>Subject</th><th>Faculty</th><th>Room</th>
</tr>

<?php
$q=mysqli_query($conn,"
SELECT ct.*,s.subject_name,
CONCAT(f.first_name,' ',f.last_name) faculty
FROM class_timetable ct
JOIN subjects s ON s.subject_code=ct.subject_code
JOIN faculty f ON f.faculty_id=ct.faculty_id
$where ORDER BY class_date,period_number
");
while($r=mysqli_fetch_assoc($q)){
echo "<tr>
<td>{$r['class_date']}</td>
<td>{$r['day']}</td>
<td>{$r['period_number']}</td>
<td>{$r['subject_name']}</td>
<td>{$r['faculty']}</td>
<td>{$r['room_no']}</td>
</tr>";
}
?>
</table>
</body>
</html>
