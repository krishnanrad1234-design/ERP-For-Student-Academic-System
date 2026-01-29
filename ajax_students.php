<?php
include("../includes/db.php");

$course = $_GET['course'];
$sem    = $_GET['sem'];

$q = mysqli_query($conn, "
    SELECT student_id, student_name
    FROM students
    WHERE course_code='$course'
    AND current_semester='$sem'
");

while($r=mysqli_fetch_assoc($q)){
    echo "
        <label>
            <input type='checkbox' name='students[]' value='{$r['student_id']}'>
            {$r['student_id']} - {$r['student_name']}
        </label><br>
    ";
}
