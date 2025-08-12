<?php
// admin_header.php
include 'auth.php';
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?= htmlspecialchars($page_title ?? 'Admin Panel') ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<style>
:root{--accent:#0d6efd}
body{background:#f5f7fb}
.sidebar{width:240px;position:fixed;top:0;bottom:0;background:var(--accent);color:#fff;padding:22px}
.sidebar a{color:#fff;display:block;padding:10px;border-radius:6px;text-decoration:none;margin-bottom:6px}
.sidebar a:hover{background:rgba(255,255,255,0.08)}
.content{margin-left:260px;padding:24px}
.card-stat{border-radius:12px;box-shadow:0 6px 18px rgba(13,110,253,0.06)}
.small-muted{font-size:0.85rem;color:#6c757d}
.table-wrap{overflow-x:auto}
</style>
</head>
<body>
<div class="sidebar">
    <h4 class="mb-3">Barangay Admin</h4>
    <div class="mb-3 small-muted">Hello, <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></div>
    <a href="admin_dashboard.php"><i class="fa fa-home me-2"></i> Dashboard</a>
    <a href="admin_business_permit.php"><i class="fa fa-briefcase me-2"></i> Business Permits</a>
    <a href="admin_business_permit_renewal.php"><i class="fa fa-rotate-right me-2"></i> Permit Renewals</a>
    <a href="admin_barangay_clearance.php"><i class="fa fa-id-card me-2"></i> Clearances</a>
    <a href="admin_indigency.php"><i class="fa fa-hand-holding-heart me-2"></i> Indigencies</a>
    <a href="admin_animal_bite_report.php"><i class="fa fa-paw me-2"></i> Animal Bite Reports</a>
    <div style="position:absolute;bottom:24px;left:22px;right:22px">
        <a href="logout.php" class="btn btn-outline-light w-100"><i class="fa fa-sign-out-alt me-2"></i> Logout</a>
    </div>
</div>
<div class="content">
<h2><?= htmlspecialchars($page_title ?? '') ?></h2>
<hr>
