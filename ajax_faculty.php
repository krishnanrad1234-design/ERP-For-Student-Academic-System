<?php
include("../includes/db.php");

$course = $_GET['course'] ?? '';

echo "<option value=''>-- Select Faculty --</option>";

$q = mysqli_query($conn,"
    SELECT faculty_id, first_name, last_name
    FROM faculty
    WHERE course_code='$course'
      AND status='active'
    ORDER BY first_name
");

while($r=mysqli_fetch_assoc($q)){
    echo "<option value='{$r['faculty_id']}'>
            {$r['first_name']} {$r['last_name']}
          </option>";
}
