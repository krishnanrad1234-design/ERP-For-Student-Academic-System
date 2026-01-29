<?php
include("../includes/auth.php");
include("../includes/db.php");

if ($_SESSION['user_type'] != 'Admin') {
    header("Location: ../login.php");
    exit;
}

$msg = "";
$msg_type = "";

/* ---------- COMMON VARIABLES ---------- */
$course_code     = $_REQUEST['course_code']     ?? '';
$semester_number = $_REQUEST['semester_number'] ?? '';
$academic_year   = $_REQUEST['academic_year']   ?? '';

/* ---------- SAVE BATCHES ---------- */
if (isset($_POST['save_batches'])) {

    foreach ($_POST['batch'] as $student_id => $batch) {

        $check = mysqli_query($conn, "
            SELECT 1 FROM student_batches
            WHERE student_id='$student_id'
              AND course_code='$course_code'
              AND academic_year='$academic_year'
              AND semester_number='$semester_number'
        ");

        if (mysqli_num_rows($check) > 0) {

            mysqli_query($conn, "
                UPDATE student_batches
                SET batch='$batch'
                WHERE student_id='$student_id'
                  AND course_code='$course_code'
                  AND academic_year='$academic_year'
                  AND semester_number='$semester_number'
            ");

        } else {

            mysqli_query($conn, "
                INSERT INTO student_batches
                (student_id, course_code, academic_year, semester_number, batch)
                VALUES
                ('$student_id','$course_code','$academic_year','$semester_number','$batch')
            ");
        }
    }

    $msg = "Batch assignment saved successfully.";
    $msg_type = "success";
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Assign & View Student Batches</title>

<style>
body{
    font-family: Arial, Helvetica, sans-serif;
    background:#f4f6f8;
}

.container{
    width:90%;
    margin:30px auto;
    background:#fff;
    padding:20px;
    border-radius:8px;
}

h3{
    text-align:center;
    margin-bottom:15px;
}

select,button{
    padding:6px 10px;
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

th{
    background:#343a40;
    color:#fff;
}

.success{
    background:#d1e7dd;
    color:#0f5132;
    padding:8px;
    margin-bottom:10px;
    border-radius:4px;
}

/* ===== PRINT STRICT MODE ===== */
@media print {

    body * {
        visibility: hidden !important;
    }

    .print-area,
    .print-area * {
        visibility: visible !important;
    }

    .print-area {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
    }

    .no-print {
        display: none !important;
    }

    table {
        font-size:12px;
    }

    th {
        background:#000 !important;
        color:#fff !important;
        -webkit-print-color-adjust: exact;
    }
}
</style>

<script>
function printBatchList(){
    window.print();
}
</script>

</head>
<body>

<div class="container">

<h3>Assign & View Student Batches</h3>

<?php if($msg){ ?>
<div class="<?= $msg_type ?>"><?= $msg ?></div>
<?php } ?>

<!-- ================= FILTER ================= -->
<form method="post" class="no-print">

    <select name="course_code" required>
        <option value="">Course</option>
        <?php
        $c=mysqli_query($conn,"SELECT course_code,course_name FROM courses");
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
        $ay=mysqli_query($conn,"SELECT DISTINCT academic_year FROM academic_periods");
        while($r=mysqli_fetch_assoc($ay)){
            $sel = ($academic_year==$r['academic_year'])?"selected":"";
            echo "<option $sel>{$r['academic_year']}</option>";
        }
        ?>
    </select>

    <button name="load">Load Students</button>
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
    ORDER BY s.student_name
");
?>

<!-- ================= ASSIGN SECTION ================= -->
<div class="no-print">

<h3>Assign Student Batch</h3>

<form method="post">
<input type="hidden" name="course_code" value="<?= $course_code ?>">
<input type="hidden" name="semester_number" value="<?= $semester_number ?>">
<input type="hidden" name="academic_year" value="<?= $academic_year ?>">

<table>
<tr>
    <th>Student ID</th>
    <th>Student Name</th>
    <th>Batch</th>
</tr>

<?php while($r=mysqli_fetch_assoc($students)){ ?>
<tr>
    <td><?= $r['student_id'] ?></td>
    <td><?= $r['student_name'] ?></td>
    <td>
        <select name="batch[<?= $r['student_id'] ?>]" required>
            <option value="">Select</option>
            <option value="A" <?= ($r['batch']=='A')?'selected':'' ?>>A</option>
            <option value="B" <?= ($r['batch']=='B')?'selected':'' ?>>B</option>
        </select>
    </td>
</tr>
<?php } ?>
</table>

<br>
<button name="save_batches">Save Batch Assignment</button>
</form>
</div>

<!-- ================= VIEW / PRINT SECTION ================= -->
<div class="print-area">

<h3>Student Batch List</h3>

<button onclick="printBatchList()" class="no-print">🖨 Print</button>

<table>
<tr>
    <th>Student ID</th>
    <th>Student Name</th>
    <th>Batch</th>
</tr>

<?php
mysqli_data_seek($students, 0);

while($r=mysqli_fetch_assoc($students)){
?>
<tr>
    <td><?= $r['student_id'] ?></td>
    <td><?= $r['student_name'] ?></td>
    <td><?= $r['batch'] ?: '-' ?></td>
</tr>
<?php } ?>
</table>

</div>

<?php } ?>

</div>

</body>
</html>
