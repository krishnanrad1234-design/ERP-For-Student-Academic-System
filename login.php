<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>College Login</title>

<style>
body{
    margin:0;
    font-family:Tahoma, Arial, sans-serif;
    background:#E6F0FA;
}

/* ===== HEADER (UNCHANGED) ===== */
.college-header{
    display:flex;
    align-items:center;
    padding:20px 40px;
    background:#F2F2F2;
    border-bottom:1px solid #ccc;
}
.college-header img{
    width:120px;
    margin-right:30px;
}
.college-text h1{
    margin:0;
    font-size:32px;
    color:#0B4F6C;
}
.college-text p{
    margin:4px 0;
    font-size:14px;
    color:#333;
}

/* ===== LOGIN PANEL ===== */
.login-panel{
    width:420px;
    margin:80px auto;
    background:#EAF2FB;
    border:1px solid #B5CDE8;
    border-radius:6px;
    padding:20px;
}
.login-panel h2{
    margin:0 0 10px;
    font-size:18px;
    color:#1E4F91;
    border-bottom:1px solid #B5CDE8;
    padding-bottom:6px;
}
label{
    font-size:14px;
}
input{
    width:100%;
    padding:8px;
    margin:5px 0 12px;
    border:1px solid #999;
    border-radius:18px;
}
.login-btn{
    padding:6px 20px;
    background:#2F6FB2;
    color:#fff;
    border:none;
    border-radius:4px;
    cursor:pointer;
}

/* ===== MODAL OVERLAY ===== */
.modal-overlay{
    position:fixed;
    inset:0;
    background:rgba(0,0,0,0.3);
    display:none;
    align-items:center;
    justify-content:center;
}

/* ===== ERP DIALOG ===== */
.erp-dialog{
    width:380px;
    background:#DCE7F3;
    border:1px solid #8AAED1;
    box-shadow:0 5px 15px rgba(0,0,0,0.4);
}

/* TITLE BAR */
.erp-title{
    background:#C7DBF0;
    padding:6px 10px;
    font-weight:bold;
    color:#1E4F91;
    display:flex;
    justify-content:space-between;
    align-items:center;
}
.erp-close{
    cursor:pointer;
    font-weight:bold;
}

/* BODY */
.erp-body{
    display:flex;
    align-items:center;
    gap:15px;
    padding:20px;
    font-size:14px;
}
.erp-icon{
    width:40px;
    height:40px;
    border-radius:50%;
    background:#2F6FB2;
    color:white;
    font-size:26px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:bold;
}

/* FOOTER */
.erp-footer{
    text-align:center;
    padding:10px;
}
.erp-footer button{
    padding:4px 30px;
    border:1px solid #888;
    background:#F5F5F5;
    cursor:pointer;
}
</style>
</head>

<body>

<!-- ===== HEADER ===== -->
<div class="college-header">
    <img src="assets/images/NLPTC.jpg">
    <div class="college-text">
        <h1>NANJIAH LINGAMMAL POLYTECHNIC COLLEGE</h1>
        <p>Approved by AICTE and NBA, New Delhi and Recognized by Govt of Tamil Nadu</p>
        <p><b>Sponsored by TAMILNADU KALVI KAPPU ARAKATTALAI.</b></p>
        <p>Sirumugai Road, Chikkadasam Palayam, Mettupalayam,<br>Coimbatore – 641 301</p>
    </div>
</div>

<!-- ===== LOGIN PANEL ===== -->
<div class="login-panel">
    <h2>Student & Faculty Login Panel</h2>

    <form method="post" action="login_process.php">
        <label>Enter Login Code:</label>
        <input type="text" name="reference_id" required>

        <label>Enter Password:</label>
        <input type="password" name="password" required>

        <button class="login-btn">Login</button>
    </form>
</div>

<!-- ===== ERP POPUP ===== -->
<div class="modal-overlay" id="erpModal">
    <div class="erp-dialog">
        <div class="erp-title">
            Information
            <span class="erp-close" onclick="closeModal()">✖</span>
        </div>

        <div class="erp-body">
            <div class="erp-icon">i</div>
            <div>Login not attempted.</div>
        </div>

        <div class="erp-footer">
            <button onclick="closeModal()">OK</button>
        </div>
    </div>
</div>

<script>
function closeModal(){
    document.getElementById("erpModal").style.display="none";
}
<?php if(isset($_GET['error'])){ ?>
    document.getElementById("erpModal").style.display="flex";
<?php } ?>
</script>

</body>
</html>
