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
    'Animal Bite Report' => 'animal_bite_reports'
];

$rows = [];

foreach ($permits as $label => $table) {
    $sql = "SELECT id, status, gcash_ref_no, comment, created_at FROM $table WHERE user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $rows[] = [
            'type' => $label,
            'status' => ucfirst($row['status']),
            'comment' => $row['comment'],
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
        }
        .service-card:hover {
        background: #eaf7f8;
        text-decoration: none;
        color: inherit;
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
                            <li><a class="dropdown-item" href="barangay_permit.php">Request</a></li>
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
                    <!-- My Request -->
                    <div class="col-md-3">
                        <a href="my_requests.php" class="service-card">
                        <i class="bi bi-journal-text" style="font-size:2rem;"></i>
                        <h6 class="mt-2">My Request</h6>
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
                    <div class="col-md-3">Comments </div>
                </div>

                <?php if (count($rows) > 0): ?>
                    <?php foreach ($rows as $row): ?>
                        <div class="row row-entry text-center">
                            <div class="col-md-3"><?php echo htmlspecialchars($row['type']); ?></div>
                            <div class="col-md-2"><?php echo htmlspecialchars($row['status']); ?></div>
                            <div class="col-md-2"><?php echo htmlspecialchars($row['date']); ?></div>
                            <div class="col-md-3"><?php echo htmlspecialchars($row['comment']); ?></div>
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