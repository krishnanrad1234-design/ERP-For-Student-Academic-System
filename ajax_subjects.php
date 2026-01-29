<?php
include("../includes/db.php");

$course = $_GET['course'];
$sem    = $_GET['sem'];

$q = mysqli_query($conn,"
    SELECT subject_code, subject_name
    FROM subjects
    WHERE course_code='$course'
      AND semester_number='$sem'
");

echo "<option value=''>-- Select Subject --</option>";
while($r=mysqli_fetch_assoc($q)){
    echo "<option value='{$r['subject_code']}'>
            {$r['subject_name']}
          </option>";
}
