<?php
include("../includes/auth.php");
include("../includes/db.php");

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'Admin') {
    header("Location: ../login.php");
    exit;
}

$subject_code = $_GET['subject_code'] ?? '';

$subject = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT * FROM subjects WHERE subject_code='$subject_code'"
));

if (!$subject) {
    echo "Invalid Subject";
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Edit Subject</title>

<style>
body{font-family:Arial;background:#f4f6f8;}
.box{
    width:650px;
    margin:40px auto;
    background:#fff;
    padding:20px;
    border-radius:6px;
}
h3{text-align:center;color:#0B4F6C;}
label{font-size:14px;}
input,select{
    width:100%;
    padding:8px;
    margin:6px 0 12px;
    border:1px solid #ccc;
}
button{
    padding:10px;
    background:#2F6FB2;
    color:#fff;
    border:none;
    cursor:pointer;
}
button:hover{background:#1E4F91;}
</style>
</head>

<body>

<div class="box">
<h3>Edit Subject</h3>

<form method="post" action="update_subject.php">


<label>Subject Code *</label>
<input
    type="text"
    name="subject_code"
    maxlength="10"
    value="<?= $subject['subject_code'] ?>"
    inputmode="numeric"
    oninput="this.value=this.value.replace(/[^0-9]/g,'')"
    required
>

<input type="hidden" name="old_subject_code"
value="<?= $subject['subject_code'] ?>">


<label>Subject Name *</label>
<input
    type="text"
    name="subject_name"
    maxlength="40"
    value="<?= $subject['subject_name'] ?>"
    oninput="this.value=this.value.replace(/[^A-Za-z ]/g,'')"
    required
>

<label>L</label>
<input type="number" name="L" value="<?= $subject['L'] ?>">

<label>T</label>
<input type="number" name="T" value="<?= $subject['T'] ?>">

<label>P</label>
<input type="number" name="P" value="<?= $subject['P'] ?>">

<label>Periods</label>
<input type="number" name="periods" value="<?= $subject['periods'] ?>">

<label>Credits</label>
<input type="number" name="credits" value="<?= $subject['credits'] ?>">

<label>Subject Type</label>
<select name="subject_type">
<option <?= $subject['subject_type']=='Theory'?'selected':'' ?>>Theory</option>
<option <?= $subject['subject_type']=='Practicum'?'selected':'' ?>>Practicum</option>
<option <?= $subject['subject_type']=='Practical/Lab'?'selected':'' ?>>Practical/Lab</option>
<option <?= $subject['subject_type']=='Elective'?'selected':'' ?>>Elective</option>
<option <?= $subject['subject_type']=='Project/Internship'?'selected':'' ?>>Project/Internship</option>
</select>

<label>End Exam</label>
<select name="end_exam">
<option <?= $subject['end_exam']=='Theory'?'selected':'' ?>>Theory</option>
<option <?= $subject['end_exam']=='Practical'?'selected':'' ?>>Practical</option>
<option <?= $subject['end_exam']=='Project'?'selected':'' ?>>Project</option>
<option <?= $subject['end_exam']=='NA'?'selected':'' ?>>NA</option>
</select>

<button type="submit">Update Subject</button>

</form>
</div>

</body>
</html>
