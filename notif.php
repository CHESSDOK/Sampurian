<?php
session_start();
include 'include/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// ✅ Handle "Clear All"
if (isset($_POST['clear_notifications'])) {
    $tables = [
        'barangay_clearance',
        'business_permit',
        'business_permit_renewal',
        'indigency',
        'animal_bite_reports'
    ];

    foreach ($tables as $tbl) {
        $stmt = $pdo->prepare("UPDATE $tbl SET seen = 1 WHERE user_id = ?");
        $stmt->execute([$user_id]);
    }

    header("Location: notif.php"); // refresh
    exit;
}

// ✅ Fetch notifications (only unseen)
$sql = "
SELECT 'New Barangay Permit' AS type, id, status, comment, created_at 
FROM barangay_clearance WHERE user_id = :uid AND seen = 0
UNION ALL
SELECT 'Business Permit' AS type, id, status, comment, created_at 
FROM business_permit WHERE user_id = :uid AND seen = 0
UNION ALL
SELECT 'Business Permit Renewal' AS type, id, status, comment, created_at 
FROM business_permit_renewal WHERE user_id = :uid AND seen = 0
UNION ALL
SELECT 'Indigency' AS type, id, status, comment, created_at 
FROM indigency WHERE user_id = :uid AND seen = 0
UNION ALL
SELECT 'Animal Bite Report' AS type, id, status, comment, created_at 
FROM animal_bite_reports WHERE user_id = :uid AND seen = 0
ORDER BY created_at DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute(['uid' => $user_id]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ✅ Notification dot count
$notifCount = count($rows);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Notifications</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #d1e6e2; }
        .header-bar { background-color: #11695d; color: white; padding: 10px; font-size: 20px; font-weight: 600; text-align: center; border-radius: 5px; }
        .row-header { background-color: #f8f9f9; padding: 10px 0; border-bottom: 2px solid #ccc; font-weight: 600; }
        .row-entry { background-color: white; padding: 12px 0; border-bottom: 1px solid #ccc; align-items: center; }
        .container-box { background-color: white; border-radius: 8px; padding: 0 10px; box-shadow: 0 0 4px rgba(0,0,0,0.1); }
        .btn-outline-danger { border-radius: 12px; padding: 2px 12px; }
    </style>
</head>
<body>
<div class="container my-5">
    <!-- Top Bar -->
    <div class="top-bar d-flex justify-content-between align-items-center px-4 py-2 border-bottom">
        <div class="d-flex align-items-center">
            <a href="dashboard.php">
                <img src="include/uploads/1752672752_474043763_2836446049861869_694324624727446876_n.jpg" alt="Barangay Logo" width="40" height="40" class="rounded-circle me-2">
            </a>
            <span class="fw-bold" style="font-size: x-large;">Barangay Sampiruhan</span>
        </div>
        <div class="d-flex align-items-center">
            <a href="notif.php" title="Notifications" class="position-relative me-3">
                <i class="fas fa-bell"></i>
                <?php if ($notifCount > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                <?php endif; ?>
            </a>
            <a href="contact.php" title="Contact Us"><i class="fas fa-phone"></i></a>
        </div>
    </div>

    <!-- Notifications Header -->
    <div class="container my-4">
        <div class="header-bar">My Notifications</div>

        <!-- Clear Button -->
        <form method="post" class="text-end my-2">
            <?php if ($notifCount > 0): ?>
                <button type="submit" name="clear_notifications" class="btn btn-sm btn-outline-danger">
                    Clear All
                </button>
            <?php endif; ?>
        </form>

        <!-- Notifications List -->
        <div class="container-box mt-3">
            <div class="row row-header text-center">
                <div class="col-md-3">Application Type</div>
                <div class="col-md-2">Status</div>
                <div class="col-md-2">Date</div>
                <div class="col-md-3">Comments</div>
            </div>

            <?php if ($rows): ?>
                <?php foreach ($rows as $row): ?>
                    <div class="row row-entry text-center">
                        <div class="col-md-3"><?= htmlspecialchars($row['type']); ?></div>
                        <div class="col-md-2">
                            <?php if ($row['status'] === 'Approved'): ?>
                                <span class="text-success fw-bold">Approved</span>
                            <?php elseif ($row['status'] === 'Declined'): ?>
                                <span class="text-danger fw-bold">Declined</span>
                            <?php else: ?>
                                <span class="text-warning fw-bold">Pending</span>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-2"><?= date('d M Y', strtotime($row['created_at'])); ?></div>
                        <div class="col-md-3"><?= htmlspecialchars($row['comment']); ?></div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="row row-entry text-center">
                    <div class="col-12">No new notifications.</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
