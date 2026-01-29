<?php
include("../includes/db.php");

$subject_code = $_GET['subject_code'];

$q = mysqli_query($conn,"
SELECT fs.faculty_id, f.first_name
FROM faculty_subjects fs
JOIN faculty f ON fs.faculty_id = f.faculty_id
WHERE fs.subject_code = '$subject_code'
AND fs.status = 'Active'
LIMIT 1
");

if(mysqli_num_rows($q) > 0){
    $row = mysqli_fetch_assoc($q);
    echo "<option value='{$row['faculty_id']}'>
            {$row['faculty_id']} - {$row['first_name']}
          </option>";
} else {
    echo "<option value=''>No faculty assigned</option>";
}
