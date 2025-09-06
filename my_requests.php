<?php
session_start();
include 'include/config.php';
// 2️⃣ Now $pdo is available for delete logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_request'])) {
    $delete_table = $_POST['table'];
    $delete_id = intval($_POST['id']);

    // Only allow deletion from known tables
    $allowed_tables = [
        'barangay_clearance',
        'business_permit',
        'business_permit_renewal',
        'indigency',
        'animal_bite_reports'
    ];

    if (in_array($delete_table, $allowed_tables)) {
        $stmt = $pdo->prepare("DELETE FROM $delete_table WHERE id = ? AND user_id = ?");
        $stmt->execute([$delete_id, $_SESSION['user_id']]);
    }

    // Redirect to avoid form resubmission
    header("Location: my_requests.php");
    exit();
}


$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT f_name, l_name, m_name, birthday, marriage_status, gender, address, contact, email, picture FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Mark notifications as read when user visits this page
$update_stmt = $pdo->prepare("
    UPDATE notification 
    SET is_read = 1 
    WHERE user_id = ? AND recipient_type = 'user'
");
$update_stmt->execute([$user_id]);

$permits = [
    'Barangay Clearance' => 'barangay_clearance',
    'Business Permit' => 'business_permit',
    'Business Permit Renewal' => 'business_permit_renewal',
    'Indigency' => 'indigency',
    'Animal Bite Report' => 'animal_bite_reports'
];

$rows = [];

foreach ($permits as $label => $table) {
    $sql = "SELECT id, status, comment, created_at, pdf_path FROM $table WHERE user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $rows[] = [
            'type' => $label,
            'status' => ucfirst($row['status']),
            'comment' => $row['comment'],
            'date' => date('d M Y', strtotime($row['created_at'])),
            'table' => $table,
            'pdf_path' => $row['pdf_path'],
            'id' => $row['id']
        ];
    }
}

// ✅ Sort applications by status (Approved → Pending → Rejected → Others)
$status_priority = [
    'Approved' => 1,
    'Pending'  => 2,
    'Rejected' => 3,
    'Other'    => 4
];

usort($rows, function ($a, $b) use ($status_priority) {
    $a_status = $status_priority[$a['status']] ?? $status_priority['Other'];
    $b_status = $status_priority[$b['status']] ?? $status_priority['Other'];

    if ($a_status === $b_status) {
        return strtotime($b['date']) - strtotime($a['date']); // Newest first
    }
    return $a_status - $b_status;
});

// Check for unread notifications
$notification_stmt = $pdo->prepare("
    SELECT COUNT(*) as unread_count 
    FROM notification 
    WHERE user_id = ? 
      AND is_read = 0
      AND recipient_type = 'user'
");
$notification_stmt->execute([$user_id]);
$notification_count = $notification_stmt->fetch(PDO::FETCH_ASSOC)['unread_count'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Application</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #eaf4f4;
            font-family: Arial, sans-serif;
        }

        .navbar {
            background-color: #20a4b9;
        }

        .navbar-brand {
            font-weight: bold;
            color: white !important;
            display: flex;
            align-items: center;
        }

        .navbar-brand i {
            margin-right: 8px;
        }

        .navbar .dropdown-menu {
            min-width: 150px;
        }

        .profile-icon {
            font-size: 1.5rem;
            cursor: pointer;
            color: white;
        }

        .header-bar {
            background-color: #258B8C;
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

        .service-card {
            background: #fff;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            transition: 0.3s;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            display: block;
            position: relative;
        }

        .service-card:hover {
            background: #eaf7f8;
            text-decoration: none;
            color: inherit;
        }

        /* Notification badge styles */
        .notification-badge {
            position: absolute;
            top: 5px;
            right: 5px;
            background-color: #dc3545;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg px-3">
        <a class="navbar-brand" href="#">
            <a href="dashboard.php"><img src="include/uploads/1752672752_474043763_2836446049861869_694324624727446876_n.jpg" alt="Barangay Logo" width="50" height="50" class="rounded-circle me-2"></a>
            <h3>BARANGAY SAMPIRUHAN</h3>
        </a>
        <div class="ms-auto dropdown">
            <img src="include/<?php echo htmlspecialchars($user['picture']); ?>" 
                alt="Profile" 
                width="40" 
                height="40" 
                class="rounded-circle dropdown-toggle" 
                data-bs-toggle="dropdown" 
                style="cursor:pointer; object-fit:cover;">
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="applicant_profile.php">Profile</a></li>
                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <!-- Services Section -->
    <div class="container my-2">
        <div class="row text-center g-6">

            <!-- Business Permit with Dropdown -->
            <div class="col-md-3">
                <div class="dropdown service-card">
                    <i class="bi bi-file-earmark-text" style="font-size:2rem;"></i>
                    <h6 class="mt-2 dropdown-toggle" data-bs-toggle="dropdown">Business Permit</h6>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="barangay_permit.php">New</a></li>
                        <li><a class="dropdown-item" href="renew_permit.php">Renew</a></li>
                    </ul>
                </div>
            </div>
            <!-- Clearance -->
            <div class="col-md-2">
                <a href="clearance.php" class="service-card">
                    <i class="bi bi-file-text" style="font-size:2rem;"></i>
                    <h6 class="mt-2">Clearance</h6>
                </a>
            </div>
            <!-- Indigency -->
            <div class="col-md-2">
                <a href="indigency.php" class="service-card">
                    <i class="bi bi-people" style="font-size:2rem;"></i>
                    <h6 class="mt-2">Indigency</h6>
                </a>
            </div>
            <!-- Bite Report -->
            <div class="col-md-2">
                <a href="animal_bite.php" class="service-card">
                    <i class="bi bi-flag" style="font-size:2rem;"></i>
                    <h6 class="mt-2">Bite Report</h6>
                </a>
            </div>
            <!-- My Request with Notification Badge -->
            <div class="col-md-3">
                <a href="my_requests.php" class="service-card position-relative">
                    <i class="bi bi-journal-text" style="font-size:2rem;"></i>
                    <h6 class="mt-2">My Request</h6>
                    <?php if ($notification_count > 0): ?>
                        <span class="notification-badge"><?php echo $notification_count; ?></span>
                    <?php endif; ?>
                </a>
            </div>

        </div>
    </div>

    <div class="container my-5">
        <div class="container my-4">
            <div class="header-bar">My Application</div>

            <div class="container-box mt-3">
                <div class="row row-header text-center">
                    <div class="col-md-3">Application Type</div>
                    <div class="col-md-2">Status</div>
                    <div class="col-md-2">Date</div>
                    <div class="col-md-2">File</div>
                   <div class="col-md-2">Comments</div>
                    <div class="col-md-1">Action</div>
                    
                </div>

                <?php if (count($rows) > 0): ?>
                    <?php foreach ($rows as $row): ?>
                        <div class="row row-entry text-center">
                            <div class="col-md-3"><?php echo htmlspecialchars($row['type']); ?></div>
                            <div class="col-md-2">
                                <?php
                                $statusClass = match($row['status']) {
                                    'Approved' => 'success',
                                    'Pending'  => 'warning',
                                    'Rejected' => 'danger',
                                    default    => 'secondary'
                                };
                                ?>
                                <span class="badge bg-<?php echo $statusClass; ?> px-3 py-2">
                                    <?php echo htmlspecialchars($row['status']); ?>
                                </span>
                            </div>
                            <div class="col-md-2"><?php echo htmlspecialchars($row['date']); ?></div>
                            <div class="col-md-2">
                                <?php if (!empty($row['pdf_path'])): ?>
                                    <a href="<?php echo htmlspecialchars($row['pdf_path']); ?>" target="_blank">View File</a>
                                <?php else: ?>
                                    No File
                                <?php endif; ?>
                            </div>
                            <div class="col-md-2"><?php echo htmlspecialchars($row['comment']); ?></div>
                            <div class="col-md-1">
                                <form method="POST" onsubmit="return confirm('Are you sure you want to delete this request?');">
                                    <input type="hidden" name="table" value="<?php echo htmlspecialchars($row['table']); ?>">
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                    <button type="submit" name="delete_request" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
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
