<?php
include("../includes/auth.php");
include("../includes/db.php");

if ($_SESSION['user_type'] !== 'Faculty') {
    header("Location: ../login.php");
    exit;
}

$faculty_id = $_SESSION['user_id']; // ✅ FIXED
?>
<!DOCTYPE html>
<html>
<head>
<title>My Subjects</title>
<style>
body{font-family:Arial;background:#f4f6f8;}
.box{width:90%;margin:30px auto;background:#fff;padding:20px;border-radius:6px;}
table{width:100%;border-collapse:collapse;}
th,td{border:1px solid #ccc;padding:10px;text-align:left;}
th{background:#343a40;color:#fff;}
.back{display:inline-block;margin-top:15px;padding:8px 14px;background:#6c757d;color:#fff;text-decoration:none;border-radius:4px;}
</style>
</head>

<body>
<div class="box">
<h3>📘 My Assigned Subjects</h3>

<table>
<tr>
    <th>Subject Name</th>
    <th>Subject Type</th>
    <th>Course</th>
    <th>Semester</th>
    <th>Academic Year</th>
</tr>

<?php
$q = mysqli_query($conn,"
    SELECT 
        s.subject_name,
        s.subject_type,
        c.course_name,
        fs.semester_number,
        fs.academic_year
    FROM faculty_subjects fs
    JOIN subjects s ON s.subject_code = fs.subject_code
    JOIN courses c ON c.course_code = s.course_code
    WHERE fs.faculty_id = '$faculty_id'
    ORDER BY fs.academic_year DESC, fs.semester_number
");

if(mysqli_num_rows($q)==0){
    echo "<tr><td colspan='5'>No subjects assigned</td></tr>";
}

while($r=mysqli_fetch_assoc($q)){
    echo "<tr>
        <td>{$r['subject_name']}</td>
        <td>{$r['subject_type']}</td>
        <td>{$r['course_name']}</td>
        <td>{$r['semester_number']}</td>
        <td>{$r['academic_year']}</td>
    </tr>";
}
?>
</table>


</div>
</body>
</html>
