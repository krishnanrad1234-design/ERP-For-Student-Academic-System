    <?php
include("../includes/auth.php");
include("../includes/db.php");

if ($_SESSION['user_type'] != 'Admin') {
    header("Location: ../login.php");
    exit;
}

$course_code     = $_GET['course_code']     ?? '';
$semester_number = $_GET['semester_number'] ?? '';
$academic_year   = $_GET['academic_year']   ?? '';
?>
<!DOCTYPE html>
<html>
<head>
<title>View Student Batch List</title>

<style>
body{font-family:Arial;background:#f4f6f8;}
.container{
    width:90%;
    margin:30px auto;
    background:#fff;
    padding:20px;
    border-radius:8px;
}
h3{text-align:center;}

select,button{
    padding:6px;
    margin:5px;
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
}
th{background:#343a40;color:#fff;}
</style>
</head>

<body>

<div class="container">
<h3>Student Batch List</h3>

<!-- FILTER FORM -->
<form method="get">

    <select name="course_code" required>
        <option value="">Course</option>
        <?php
        $c = mysqli_query($conn,"SELECT course_code, course_name FROM courses");
        while($r=mysqli_fetch_assoc($c)){
            $sel = ($course_code==$r['course_code'])?"selected":"";
            echo "<option value='{$r['course_code']}' $sel>{$r['course_name']}</option>";
        }
        ?>
    </select>

    <select name="semester_number" required>
        <option value="">Semester</option>
        <?php
        for($i=1;$i<=6;$i++){
            $sel = ($semester_number==$i)?"selected":"";
            echo "<option value='$i' $sel>Semester $i</option>";
        }
        ?>
    </select>

    <select name="academic_year" required>
        <option value="">Academic Year</option>
        <?php
        $ay = mysqli_query($conn,"SELECT DISTINCT academic_year FROM academic_periods");
        while($r=mysqli_fetch_assoc($ay)){
            $sel = ($academic_year==$r['academic_year'])?"selected":"";
            echo "<option value='{$r['academic_year']}' $sel>{$r['academic_year']}</option>";
        }
        ?>
    </select>

    <button type="submit">View</button>
</form>

<?php
if ($course_code && $semester_number && $academic_year) {

$students = mysqli_query($conn, "
    SELECT 
        s.student_id,
        s.student_name,
        sb.batch
    FROM students s
    LEFT JOIN student_batches sb
        ON sb.student_id = s.student_id
       AND sb.course_code = '$course_code'
       AND sb.academic_year = '$academic_year'
       AND sb.semester_number = '$semester_number'
    WHERE s.course_code='$course_code'
      AND s.current_semester='$semester_number'
    ORDER BY sb.batch, s.student_name
");
?>

<table>
<tr>
    <th>Student ID</th>
    <th>Student Name</th>
    <th>Batch</th>
</tr>

<?php
if(mysqli_num_rows($students) == 0){
    echo "<tr><td colspan='3'>No students found</td></tr>";
}

while($r=mysqli_fetch_assoc($students)){
?>
<tr>
    <td><?= $r['student_id'] ?></td>
    <td><?= $r['student_name'] ?></td>
    <td><?= $r['batch'] ?: '-' ?></td>
</tr>
<?php } ?>
</table>

<?php } ?>

</div>
</body>
</html>
