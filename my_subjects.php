<?php
include("../includes/auth.php");
include("../includes/db.php");

if ($_SESSION['user_type'] !== 'Student') {
    header("Location: ../login.php");
    exit;
}

$student_id = $_SESSION['user_id']; // ✅ FIXED
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
<h3>📘 My Subjects & Faculty</h3>

<?php
/* GET STUDENT COURSE & SEM */
$student_q = mysqli_query($conn,"
    SELECT course_code, current_semester
    FROM students
    WHERE student_id = '$student_id'
");

if(mysqli_num_rows($student_q) == 0){
    echo "<p>Student record not found.</p>";
    exit;
}

$student = mysqli_fetch_assoc($student_q);
?>

<table>
<tr>
    <th>Subject Name</th>
    <th>Subject Type</th>
    <th>Faculty</th>
</tr>

<?php
$q = mysqli_query($conn,"
    SELECT 
        s.subject_name,
        s.subject_type,
        CONCAT(f.first_name,' ',f.last_name) AS faculty_name
    FROM subjects s
    JOIN faculty_subjects fs 
        ON fs.subject_code = s.subject_code
    JOIN faculty f 
        ON f.faculty_id = fs.faculty_id
    WHERE s.course_code = '{$student['course_code']}'
      AND s.semester_number = '{$student['current_semester']}'
      AND fs.academic_year = (
            SELECT academic_year 
            FROM academic_periods 
            WHERE semester_number = '{$student['current_semester']}'
            LIMIT 1
      )
    ORDER BY s.subject_name
");

if(mysqli_num_rows($q)==0){
    echo "<tr><td colspan='3'>No subjects assigned yet</td></tr>";
}

while($r=mysqli_fetch_assoc($q)){
    echo "<tr>
        <td>{$r['subject_name']}</td>
        <td>{$r['subject_type']}</td>
        <td>{$r['faculty_name']}</td>
    </tr>";
}
?>
</table>

<a href="dashboard.php" class="back">⬅ Back to Dashboard</a>
</div>
</body>
</html>
