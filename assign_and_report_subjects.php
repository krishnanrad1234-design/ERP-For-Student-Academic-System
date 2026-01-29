<?php
include("../includes/auth.php");
include("../includes/db.php");

if ($_SESSION['user_type'] !== 'Admin') {
    header("Location: ../login.php");
    exit;
}

/* ================= ASSIGN SUBJECT ================= */
$success = false;
$error = "";

if (isset($_POST['assign'])) {

    if (
        empty($_POST['course_code']) ||
        empty($_POST['semester_number']) ||
        empty($_POST['subject_code']) ||
        empty($_POST['academic_year']) ||
        empty($_POST['faculty_source']) ||
        empty($_POST['faculty_id'])
    ) {
        $error = "Please select all fields.";
    } else {

        // 🔒 One Subject → One Faculty per Sem & Year
        $check = mysqli_query($conn, "
            SELECT faculty_id
            FROM faculty_subjects
            WHERE subject_code='{$_POST['subject_code']}'
              AND academic_year='{$_POST['academic_year']}'
              AND semester_number='{$_POST['semester_number']}'
        ");

        if (mysqli_num_rows($check) > 0) {
            $row = mysqli_fetch_assoc($check);
            $error = "Already assigned to Faculty ID: {$row['faculty_id']}";
        } else {

            mysqli_query($conn, "
                INSERT INTO faculty_subjects
                (faculty_id, subject_code, academic_year, semester_number, faculty_source)
                VALUES (
                    '{$_POST['faculty_id']}',
                    '{$_POST['subject_code']}',
                    '{$_POST['academic_year']}',
                    '{$_POST['semester_number']}',
                    '{$_POST['faculty_source']}'
                )
            ");

            $success = true;
        }
    }
}

/* ================= REPORT FILTER ================= */
$where = [];

if (!empty($_GET['course_code']))
    $where[] = "s.course_code='{$_GET['course_code']}'";

if (!empty($_GET['semester_number']))
    $where[] = "fs.semester_number='{$_GET['semester_number']}'";

if (!empty($_GET['academic_year']))
    $where[] = "fs.academic_year='{$_GET['academic_year']}'";

if (!empty($_GET['faculty_id']))
    $where[] = "fs.faculty_id='{$_GET['faculty_id']}'";

$condition = $where ? "WHERE " . implode(" AND ", $where) : "";

/* ================= REPORT DATA ================= */
$report = mysqli_query($conn, "
SELECT
    s.course_code,
    fs.semester_number,
    s.subject_code,
    s.subject_name,
    CONCAT(f.first_name,' ',f.last_name) AS faculty_name,
    fs.academic_year,
    fs.faculty_source
FROM faculty_subjects fs
JOIN subjects s ON fs.subject_code = s.subject_code
JOIN faculty  f ON fs.faculty_id = f.faculty_id
$condition
ORDER BY fs.academic_year DESC, fs.semester_number, s.subject_code
");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Assign & Report Subjects</title>

<style>
body{font-family:Arial;background:#f4f6f8;}
.box{
    width:95%;
    margin:20px auto;
    background:#fff;
    padding:20px;
    border-radius:6px;
}
h3{color:#0B4F6C;text-align:center;margin-bottom:15px;}

select,button{
    padding:8px;
    margin:5px;
}
button{
    background:#2F6FB2;
    color:#fff;
    border:none;
    cursor:pointer;
}
button:hover{background:#1E4F91;}

.success{color:green;text-align:center;}
.error{color:red;text-align:center;}

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
th{background:#2F6FB2;color:#fff;}
td.left{text-align:left;}
.export-btn{
    padding:8px 12px;
    text-decoration:none;
    color:#fff;
    border-radius:4px;
    font-size:14px;
}
.pdf{background:#0d6efd;}
.excel{background:#0d6efd;}
.csv{background:#0d6efd;}


@media print {

    /* Hide assign section completely */
    #assign-section {
        display: none !important;
    }

    /* Hide filter buttons */
    form, button {
        display: none !important;
    }

    /* Make report full width */
    #report-section {
        margin: 0;
        width: 100%;
    }

    body {
        background: #fff;
    }
}

</style>

<script>
function loadSubjects(){
    let c=document.getElementById("course_code").value;
    let s=document.getElementById("semester_number").value;
    if(c && s){
        fetch("ajax_subjects.php?course="+c+"&sem="+s)
        .then(r=>r.text())
        .then(d=>subject_code.innerHTML=d);
    }
}

function loadFaculty(source){
    let c=document.getElementById("course_code").value;
    if(source==="Parent Course"){
        other_course.style.display="none";
        fetch("ajax_faculty.php?course="+c)
        .then(r=>r.text())
        .then(d=>faculty_id.innerHTML=d);
    }else{
        other_course.style.display="inline";
    }
}

function loadOtherFaculty(){
    let c=document.getElementById("other_course").value;
    fetch("ajax_faculty.php?course="+c)
    .then(r=>r.text())
    .then(d=>faculty_id.innerHTML=d);
}
</script>

</head>

<body>

<!-- ================= ASSIGN ================= -->
<div class="box" id="assign-section">
<h3>Assign Subject to Faculty</h3>

<?php if($success) echo "<p class='success'>✅ Assigned successfully</p>"; ?>
<?php if($error) echo "<p class='error'>❌ $error</p>"; ?>

<form method="post">

<select name="course_code" id="course_code" onchange="loadSubjects();loadFaculty('Parent Course')" required>
<option value="">Course</option>
<?php
$c=mysqli_query($conn,"SELECT course_code,course_name FROM courses");
while($r=mysqli_fetch_assoc($c))
echo "<option value='{$r['course_code']}'>{$r['course_name']}</option>";
?>
</select>

<select name="semester_number" id="semester_number" onchange="loadSubjects()" required>
<option value="">Semester</option>
<?php for($i=1;$i<=6;$i++) echo "<option>$i</option>"; ?>
</select>

<select name="subject_code" id="subject_code" required>
<option value="">Subject</option>
</select>

<select name="academic_year" required>
<option value="">Academic Year</option>
<?php
$ay=mysqli_query($conn,"SELECT DISTINCT academic_year FROM academic_periods ORDER BY academic_year DESC");
while($r=mysqli_fetch_assoc($ay))
echo "<option>{$r['academic_year']}</option>";
?>
</select>

<select name="faculty_source" onchange="loadFaculty(this.value)" required>
<option value="Parent Course">Parent Course</option>
<option value="Other Course">Other Course</option>
</select>

<select id="other_course" onchange="loadOtherFaculty()" style="display:none">
<option value="">Other Course</option>
<?php
$c=mysqli_query($conn,"SELECT course_code,course_name FROM courses");
while($r=mysqli_fetch_assoc($c))
echo "<option value='{$r['course_code']}'>{$r['course_name']}</option>";
?>
</select>

<select name="faculty_id" id="faculty_id" required>
<option value="">Faculty</option>
</select>

<br><br>
<button name="assign">Assign Subject</button>
</form>
</div>

<!-- ================= REPORT ================= -->
<div class="box" id="report-section">
<h3>Subject Assignment Report</h3>


<form method="get">

<select name="course_code">
<option value="">Course</option>
<?php
$c=mysqli_query($conn,"SELECT course_code,course_name FROM courses");
while($r=mysqli_fetch_assoc($c))
echo "<option value='{$r['course_code']}'>{$r['course_name']}</option>";
?>
</select>

<select name="semester_number">
<option value="">Semester</option>
<?php for($i=1;$i<=6;$i++) echo "<option>$i</option>"; ?>
</select>

<select name="academic_year">
<option value="">Academic Year</option>
<?php
$ay=mysqli_query($conn,"SELECT DISTINCT academic_year FROM academic_periods");
while($r=mysqli_fetch_assoc($ay))
echo "<option>{$r['academic_year']}</option>";
?>
</select>

<select name="faculty_id">
<option value="">Faculty</option>
<?php
$f=mysqli_query($conn,"SELECT faculty_id,first_name,last_name FROM faculty");
while($r=mysqli_fetch_assoc($f))
echo "<option value='{$r['faculty_id']}'>{$r['first_name']} {$r['last_name']}</option>";
?>
</select>

<button type="submit">Filter</button></br>



<a href="export_subject_assignment_pdf.php?<?= http_build_query($_GET) ?>"
   class="export-btn pdf">PDF</a>

<a href="export_subject_assignment_excel.php?<?= http_build_query($_GET) ?>"
   class="export-btn excel">Excel</a>

<a href="export_subject_assignment_csv.php?<?= http_build_query($_GET) ?>"
   class="export-btn csv">CSV</a>

<button type="button" onclick="window.print()" class="print-btn">🖨 Print</button>

</form>

<table>
<tr>
<th>Course</th>
<th>Sem</th>
<th>Subject Code</th>
<th>Subject Name</th>
<th>Faculty</th>
<th>Academic Year</th>
<th>Source</th>
</tr>

<?php
if(mysqli_num_rows($report)==0){
echo "<tr><td colspan='7'>No records found</td></tr>";
}
while($r=mysqli_fetch_assoc($report)){
?>
<tr>
<td><?= $r['course_code'] ?></td>
<td><?= $r['semester_number'] ?></td>
<td><?= $r['subject_code'] ?></td>
<td class="left"><?= $r['subject_name'] ?></td>
<td class="left"><?= $r['faculty_name'] ?></td>
<td><?= $r['academic_year'] ?></td>
<td><?= $r['faculty_source'] ?></td>
</tr>
<?php } ?>
</table>

</div>

</body>
</html>
