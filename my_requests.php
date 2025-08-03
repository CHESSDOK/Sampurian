<?php
session_start();
include 'include/config.php';

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT f_name, l_name, m_name, birthday, marriage_status, gender, address, contact, email, picture FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$permits = [
    'New Barangay Permit' => 'barangay_clearance',
    'Business Permit' => 'business_permit',
    'Business Permit Renewal' => 'business_permit_renewal',
    'Indigency' => 'indigency',
    'Animal Bite Report' => 'animal_bite_investigation_report'
];

$rows = [];

foreach ($permits as $label => $table) {
    $sql = "SELECT id, status, gcash_ref_no, created_at FROM $table WHERE user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $rows[] = [
            'type' => $label,
            'status' => ucfirst($row['status']),
            'payment' => $row['gcash_ref_no'] ? 'Paid' : 'Unpaid',
            'date' => date('d M Y', strtotime($row['created_at'])),
            'table' => $table,
            'id' => $row['id']
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Application</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #d1e6e2;
        }

        .top-bar {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            padding: 10px 20px;
            border-bottom: 2px solid #ccc;
        }

        .top-bar a,
        .top-bar i {
            font-size: 20px;
            margin-left: 20px;
            color: black;
            text-decoration: none;
        }

        .dropdown img {
            width: 40px;
            height: 40px;
            object-fit: cover;
        }

        .custom-dropdown {
            width: 220px;
            padding: 10px 0;
        }

        .custom-dropdown .dropdown-item {
            width: 100%;
            padding: 10px 20px;
            text-align: center;
        }

        .header-bar {
            background-color: #11695d;
            color: white;
            padding: 10px;
            font-size: 20px;
            font-weight: 600;
            text-align: center;
            border-radius: 5px;
        }

        .row-header {
            background-color: #f8f9f9;
            padding: 10px 0;
            border-bottom: 2px solid #ccc;
            font-weight: 600;
        }

        .row-entry {
            background-color: white;
            padding: 12px 0;
            border-bottom: 1px solid #ccc;
            align-items: center;
        }

        .btn-outline-dark,
        .btn-success {
            border-radius: 12px;
            padding: 2px 12px;
        }

        .container-box {
            background-color: white;
            border-radius: 8px;
            padding: 0 10px;
            box-shadow: 0 0 4px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <div class="container my-5">
        <!-- Top Bar -->
        <div class="top-bar d-flex justify-content-between align-items-center px-4 py-2">
            <div class="d-flex align-items-center">
                <a href="dashboard.php"><img src="include/uploads/1752672752_474043763_2836446049861869_694324624727446876_n.jpg" alt="Barangay Logo" width="40" height="40" class="rounded-circle me-2"></a>
                <span class="fw-bold" style="font-size: x-large;">Barangay Sampiruhan</span>
            </div>
            <div class="d-flex align-items-center">
                <a href="notifications.php" title="Notifications"><i class="fas fa-bell"></i></a>
                <a href="contact.php" title="Contact Us"><i class="fas fa-phone"></i></a>

                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php if (!empty($user['picture'])): ?>
                            <img src="include/<?php echo htmlspecialchars($user['picture']); ?>" alt="Profile Image" class="rounded-circle" width="40" height="40">
                        <?php else: ?>
                            <img src="assets/image/user-placeholder.png" alt="Profile Picture" class="rounded-circle" width="40" height="40">
                        <?php endif; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end text-center mt-2 custom-dropdown" aria-labelledby="profileDropdown">
                        <li><strong><?= htmlspecialchars($user['f_name']) ?></strong></li>
                        <li><a class="dropdown-item" href="applicant_profile.php">Profile</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="container my-4">
            <div class="header-bar">My Application</div>

            <div class="container-box mt-3">
                <div class="row row-header text-center">
                    <div class="col-md-3">Application Type</div>
                    <div class="col-md-2">Status</div>
                    <div class="col-md-2">Payment</div>
                    <div class="col-md-2">Date</div>
                    <div class="col-md-1">Details</div>
                    <div class="col-md-2">Download File</div>
                </div>

                <?php if (count($rows) > 0): ?>
                    <?php foreach ($rows as $row): ?>
                        <div class="row row-entry text-center">
                            <div class="col-md-3"><?php echo htmlspecialchars($row['type']); ?></div>
                            <div class="col-md-2"><?php echo htmlspecialchars($row['status']); ?></div>
                            <div class="col-md-2"><?php echo htmlspecialchars($row['payment']); ?></div>
                            <div class="col-md-2"><?php echo htmlspecialchars($row['date']); ?></div>
                            <div class="col-md-1">
                                <a href="view_details.php?table=<?php echo urlencode($row['table']); ?>&id=<?php echo $row['id']; ?>" class="btn btn-outline-dark btn-sm">View</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="row row-entry text-center">
                        <div class="col-12">No permit applications found.</div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>