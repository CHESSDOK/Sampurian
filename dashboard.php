<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'include/config.php';

$user_id = $_SESSION['user_id'];

// Fetch user name
$stmt = $pdo->prepare("SELECT f_name, l_name, picture FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dashboard | Barangay Sampiruhan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #fff;
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
            /* Adjust width as needed */
            padding: 10px 0;
        }

        .custom-dropdown .dropdown-item {
            width: 100%;
            /* Force full width */
            padding: 10px 20px;
            text-align: center;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-left: 0px;
        }

        .welcome-banner {
            background-color: #227275;
            color: white;
            padding: 30px 20px;
            border-radius: 15px;
            margin: 20px;
            display: flex;
            align-items: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border: 2px solid #1e90ff;
        }

        .btn-teal {
            background-color: #2f7e79;
            color: white;
            border-radius: 10px;
            border: none;
        }

        .btn-teal:hover {
            background-color: #256661;
        }

        .bg-teal {
            background-color: #2f7e79;
        }

        h2 {
            font-family: 'Georgia', serif;
        }

        @media screen and (max-width: 768px) {
            .welcome-banner {
                flex-direction: column;
                text-align: center;
            }

            .welcome-banner img {
                margin-bottom: 15px;
            }
        }
    </style>
</head>

<body>

    <!-- Top Bar -->
    <div class="container text-center mt-4">
        <div class="top-bar">
            <a href="notifications.php" title="Notifications"><i class="fas fa-bell"></i></a>
            <a href="contact.php" title="Contact Us"><i class="fas fa-phone"></i></a>

            <div class="dropdown">
                <a href="#" class="d-flex align-items-center" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <?php if (!empty($user['picture'])): ?>
                        <img src="include/<?php echo htmlspecialchars($user['picture']); ?>" alt="Profile Image" class="rounded-circle">
                    <?php else: ?>
                        <img src="assets/image/user-placeholder.png" alt="Profile Picture" class="rounded-circle">
                    <?php endif; ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end text-center mt-2 custom-dropdown" aria-labelledby="profileDropdown">
                    <li><strong><?php echo htmlspecialchars($user['f_name']); ?></strong></li>
                    <li><a class="dropdown-item" href="applicant_profile.php">Profile</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Welcome Banner -->
    <div class="container text-center mt-4">
        <div class="row justify-content-center mb-4">
            <div class="col-md-12 p-4 bg-teal text-white rounded">
                <div class="row">
                    <div class="col-md-4 d-flex align-items-center justify-content-center">
                        <img src="img/sampiruhanlogo.png" class="img-fluid" style="width: 100px;">
                    </div>
                    <div class="col-md-4 d-flex align-items-center justify-content-center">
                        <h2>Welcome to<br>Barangay Sampiruhan<br>E-Permit System</h2>
                    </div>
                    <div class="col-md-4"></div>
                </div>
            </div>
        </div>

        <!-- Dashboard Buttons -->
        <div class="row g-4">
            <div class="col-md-4">
                <a href="barangay_permit.php" class="btn btn-teal w-100 py-3 fw-bold">BARANGAY PERMIT</a>
            </div>
            <div class="col-md-4">
                <a href="renew_permit.php" class="btn btn-teal w-100 py-3 fw-bold">RENEW BARANGAY PERMIT</a>
            </div>
            <div class="col-md-4">
                <a href="clearance.php" class="btn btn-teal w-100 py-3 fw-bold">BARANGAY CLEARANCE</a>
            </div>
            <div class="col-md-4">
                <a href="indigency.php" class="btn btn-teal w-100 py-3 fw-bold">BARANGAY INDIGENCY</a>
            </div>
            <div class="col-md-4">
                <a href="animal_bite.php" class="btn btn-teal w-100 py-3 fw-bold">ANIMAL BITE</a>
            </div>
            <div class="col-md-4">
                <a href="my_requests.php" class="btn btn-teal w-100 py-3 fw-bold">MY REQUEST</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>