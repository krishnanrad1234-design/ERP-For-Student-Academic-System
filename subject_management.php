<?php
include("../includes/auth.php");
include("../includes/db.php");

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'Admin') {
    header("Location: ../login.php");
    exit;
}

/* ---------- ADD SUBJECT DROPDOWNS ---------- */
$courses_add = mysqli_query($conn, "SELECT course_code, course_name FROM courses");
$sem_add = mysqli_query($conn, "SELECT DISTINCT semester_number FROM academic_periods ORDER BY semester_number");

/* ---------- FILTER CONDITIONS ---------- */
$where = [];

if (!empty($_GET['course_code']))
    $where[] = "s.course_code='{$_GET['course_code']}'";
if (!empty($_GET['semester_number']))
    $where[] = "s.semester_number='{$_GET['semester_number']}'";
if (!empty($_GET['regulation']))
    $where[] = "s.regulation='{$_GET['regulation']}'";
if (!empty($_GET['subject_type']))
    $where[] = "s.subject_type='{$_GET['subject_type']}'";
if (!empty($_GET['end_exam']))
    $where[] = "s.end_exam='{$_GET['end_exam']}'";
if (!empty($_GET['elective_type']))
    $where[] = "s.elective_type='{$_GET['elective_type']}'";

$condition = $where ? "WHERE " . implode(" AND ", $where) : "";

/* ---------- SUBJECT DATA ---------- */
$result = mysqli_query($conn, "
SELECT 
    s.subject_code,
    s.subject_name,
    s.semester_number,
    s.L, s.T, s.P,
    s.periods,
    s.credits,
    s.subject_type,
    s.end_exam
FROM subjects s
$condition
ORDER BY s.subject_code ASC
");

/* ---------- FILTER DROPDOWNS ---------- */
$courses_f = mysqli_query($conn, "SELECT course_code, course_name FROM courses");
$sem_f = mysqli_query($conn, "SELECT DISTINCT semester_number FROM academic_periods ORDER BY semester_number");
$regs = mysqli_query($conn, "SELECT DISTINCT regulation FROM subjects");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Subject Management</title>

<style>
/* ===== ERP COLOR SYSTEM ===== */
:root{
    --primary:#2F6FB2;
    --primary-hover:#1E4F91;
    --accent:#0B4F6C;

    --success:#28A745;
    --danger:#DC3545;
    --warning:#FFC107;

    --text-dark:#333333;
    --text-muted:#666666;

    --bg-page:#F4F6F8;
    --bg-card:#FFFFFF;
    --bg-hover:#EAF2FB;

    --border:#CCCCCC;
}

/* ===== GLOBAL ===== */
body{
    margin:0;
    font-family:Arial, Helvetica, sans-serif;
    background:var(--bg-page);
}

/* ===== CARD / BOX ===== */
.box{
    width:98%;
    margin:20px auto;
    background:var(--bg-card);
    padding:20px;
    border-radius:6px;
}

/* ===== HEADINGS ===== */
h3{
    text-align:center;
    color:var(--accent);
    margin-bottom:15px;
}

/* ===== FORM ===== */
label{
    font-size:14px;
    color:var(--text-dark);
}
input, select{
    width:100%;
    padding:8px;
    margin:6px 0 12px;
    border:1px solid var(--border);
    border-radius:4px;
}
input:focus, select:focus{
    outline:none;
    border-color:var(--primary);
}

/* ===== BUTTONS ===== */
button{
    padding:10px;
    background:var(--primary);
    color:#fff;
    border:none;
    border-radius:4px;
    cursor:pointer;
}
button:hover{
    background:var(--primary-hover);
}

/* ===== FILTER BAR ===== */
.filter-form select{
    width:auto;
    margin-right:6px;
}
.filter-form button{
    padding:7px 14px;
}

/* ===== TABLE ===== */
table{
    width:100%;
    border-collapse:collapse;
    font-size:14px;
}
th, td{
    border:1px solid var(--border);
    padding:6px;
    text-align:center;
}
th{
    background:var(--primary);
    color:#fff;
}
tr:hover{
    background:var(--bg-hover);
}
/* ===== PRINT SETTINGS ===== */
@media print {

    body * {
        visibility: hidden;
    }

    .print-area, .print-area * {
        visibility: visible;
    }

    .print-area {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }

    /* Hide Action column */
    th:last-child,
    td:last-child {
        display: none;
    }

    /* Hide filters & buttons */
    form,
    button,
    a {
        display: none !important;
    }

    table {
        font-size: 12px;
    }

    th {
        background: #ddd !important;
        color: #000 !important;
    }
}

</style>
</head>

<body>

<?php if (isset($_GET['success'])) { ?>
<script>alert("✅ Subject saved successfully");</script>
<?php } ?>

<!-- ================= ADD SUBJECT ================= -->
<div class="box">
<h3>Add Subject</h3>

<form method="post" action="save_subject.php">

<label>Subject Code *</label>
<input
    type="text"
    name="subject_code"
    maxlength="10"
    inputmode="numeric"
    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
    required
>

<label>Course *</label>
<select name="course_code" required>
<option value="">-- Select Course --</option>
<?php while($c=mysqli_fetch_assoc($courses_add)){ ?>
<option value="<?= $c['course_code'] ?>">
<?= $c['course_code'] ?> - <?= $c['course_name'] ?>
</option>
<?php } ?>
</select>

<label>Semester *</label>
<select name="semester_number" required>
<option value="">-- Select Semester --</option>
<?php while($s=mysqli_fetch_assoc($sem_add)){ ?>
<option value="<?= $s['semester_number'] ?>">Semester <?= $s['semester_number'] ?></option>
<?php } ?>
</select>

<label>Subject Name *</label>
<input
    type="text"
    name="subject_name"
    maxlength="40"
    oninput="this.value = this.value.replace(/[^A-Za-z ]/g, '')"
    required
>



<label>L - T - P</label>
<input type="number" name="L" placeholder="L">
<input type="number" name="T" placeholder="T">
<input type="number" name="P" placeholder="P">

<label>Periods</label>
<input type="number" name="periods">

<label>Credits</label>
<input type="number" name="credits">

<label>Regulation *</label>
<input type="text" name="regulation" required>

<label>Subject Type *</label>
<select name="subject_type" required>
<option>Theory</option>
<option>Practicum</option>
<option>Practical/Lab</option>
<option>Elective</option>
<option>Project/Internship</option>
<option>Advanced Skill Certification</option>
<option>Integrated Learning Experience</option>
</select>

<label>End Exam</label>
<select name="end_exam">
<option>Theory</option>
<option>Practical</option>
<option>Project</option>
<option>NA</option>
</select>

<label>Elective Type</label>
<select name="elective_type">
<option>None</option>
<option>Elective 1</option>
<option>Elective 2</option>
<option>Elective 3 (Pathway)</option>
<option>Elective 4 (Specialisation)</option>
</select>

<button type="submit">Save Subject</button>
</form>
</div>

<!-- ================= VIEW SUBJECTS ================= -->
<div class="box print-area">
<h3>View Subjects</h3>

<form method="get" class="filter-form">
<select name="course_code">
<option value="">Course</option>
<?php while($c=mysqli_fetch_assoc($courses_f)){ ?>
<option value="<?= $c['course_code'] ?>" <?= ($_GET['course_code'] ?? '')==$c['course_code']?'selected':'' ?>>
<?= $c['course_name'] ?>
</option>
<?php } ?>
</select>

<select name="semester_number">
<option value="">Semester</option>
<?php while($s=mysqli_fetch_assoc($sem_f)){ ?>
<option value="<?= $s['semester_number'] ?>" <?= ($_GET['semester_number'] ?? '')==$s['semester_number']?'selected':'' ?>>
Sem <?= $s['semester_number'] ?>
</option>
<?php } ?>
</select>

<select name="regulation">
<option value="">Regulation</option>
<?php while($r=mysqli_fetch_assoc($regs)){ ?>
<option <?= ($_GET['regulation'] ?? '')==$r['regulation']?'selected':'' ?>>
<?= $r['regulation'] ?>
</option>
<?php } ?>
</select>

<select name="subject_type">
<option value="">Subject Type</option>
<option>Theory</option>
<option>Practicum</option>
<option>Practical/Lab</option>
<option>Elective</option>
<option>Project/Internship</option>
<option>Advanced Skill Certification</option>
<option>Integrated Learning Experience</option>
</select>

<select name="end_exam">
<option value="">End Exam</option>
<option>Theory</option>
<option>Practical</option>
<option>Project</option>
<option>NA</option>
</select>

<select name="elective_type">
<option value="">Elective Type</option>
<option>None</option>
<option>Elective 1</option>
<option>Elective 2</option>
<option>Elective 3 (Pathway)</option>
<option>Elective 4 (Specialisation)</option>
</select>

<button type="submit">Filter</button>
</form>

<br>
<div style="margin-bottom:10px;">

    <!-- PRINT -->
    <button type="button" onclick="window.print()">Print</button>

    <!-- PDF -->
    <a href="export_subjects_pdf.php?<?= http_build_query($_GET) ?>"
       style="margin-left:6px;">
        <button type="button">PDF</button>
    </a>

    <!-- EXCEL -->
    <a href="export_subjects_excel.php?<?= http_build_query($_GET) ?>"
       style="margin-left:6px;">
        <button type="button">Excel</button>
    </a>

    <!-- CSV -->
    <a href="export_subjects_csv.php?<?= http_build_query($_GET) ?>"
       style="margin-left:6px;">
        <button type="button">CSV</button>
    </a>

</div>

<table>
<tr>
<th>Subject Code</th>
<th>Subject Name</th>
<th>Semester</th>
<th>L</th>
<th>T</th>
<th>P</th>
<th>Periods</th>
<th>Credits</th>
<th>Subject Type</th>
<th>End Exam</th>
<th>Action</th>

</tr>

<?php while($row=mysqli_fetch_assoc($result)){ ?>
<tr>
<td><?= $row['subject_code'] ?></td>
<td><?= $row['subject_name'] ?></td>
<td><?= $row['semester_number'] ?></td>
<td><?= $row['L'] ?></td>
<td><?= $row['T'] ?></td>
<td><?= $row['P'] ?></td>
<td><?= $row['periods'] ?></td>
<td><?= $row['credits'] ?></td>
<td><?= $row['subject_type'] ?></td>
<td><?= $row['end_exam'] ?></td>
    <td>
    <a href="edit_subject.php?subject_code=<?= $row['subject_code'] ?>"
       style="color:#2F6FB2;font-weight:bold;text-decoration:none;">
        Edit
    </a>
    |
    <a href="delete_subject.php?subject_code=<?= $row['subject_code'] ?>"
       onclick="return confirm('Are you sure you want to delete this subject?')"
       style="color:#DC3545;font-weight:bold;text-decoration:none;">
        Delete
    </a>
</td>

</td>
</tr>
<?php } ?>
</table>
</div>

</body>
</html>
