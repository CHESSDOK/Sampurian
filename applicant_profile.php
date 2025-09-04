<?php
session_start();
require_once 'include/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $f_name = $_POST['f_name'];
    $l_name = $_POST['l_name'];
    $dob = $_POST['dob'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];

    // Optional: hash password (uncomment if passwords are stored hashed)
    // $password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("UPDATE users SET f_name = ?, l_name = ?, birthday = ?, email = ?, contact = ? WHERE id = ?");
    $stmt->execute([$f_name, $l_name, $dob, $email, $contact, $user_id]);

    $success_message = "Profile updated successfully!";
}

// Fetch user details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Profile</title>
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

        .profile-sidebar {
            background-color: #5c9a9e;
            color: white;
            padding: 30px 20px;
            height: 100%;
            text-align: center;
            border-radius: 10px;
        }

        .profile-sidebar img {
            width: 100px;
            height: 100px;
            object-fit: cover;
        }

        .form-control {
            border-radius: 10px;
        }

        .btn-logout {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: bold;
        }

        .btn-logout:hover {
            background-color: #c82333;
        }

        .form-section {
            padding: 30px;
            background-color: #f9f9f9;
            border-radius: 15px;
            box-shadow: 0px 2px 6px rgba(0, 0, 0, 0.1);
        }

        h4 {
            font-weight: bold;
        }

        .success-msg {
            color: green;
            font-weight: bold;
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
                    <!-- My Request -->
                    <div class="col-md-3">
                        <a href="my_requests.php" class="service-card">
                        <i class="bi bi-journal-text" style="font-size:2rem;"></i>
                        <h6 class="mt-2">My Request</h6>
                        </a>
                    </div>

                </div>
            </div>
    <div class="container mt-5">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-4">
                <div class="profile-sidebar">
                    <img src="include/<?php echo htmlspecialchars($user['picture']); ?>" class="rounded-circle mb-3" alt="Profile Picture">
                    <h5 class="fw-bold"><?php echo htmlspecialchars($user['f_name'] . ' ' . $user['l_name']); ?></h5>
                    <p><?php echo htmlspecialchars($user['email']); ?></p>
                    <p><?php echo htmlspecialchars($user['contact']); ?></p>
                    <a href="logout.php" class="btn btn-logout mt-3">Sign out</a>
                </div>
            </div>

            <!-- Profile Form -->
            <div class="col-md-8">
                <div class="form-section">
                    <h4>Personal Information</h4>
                    <p>Manage your personal information and password.</p>

                    <?php if (isset($success_message)): ?>
                        <p class="success-msg"><?php echo $success_message; ?></p>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label for="f_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="f_name" name="f_name" required value="<?php echo htmlspecialchars($user['f_name']); ?>">
                        </div>

                        <div class="mb-3">
                            <label for="l_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="l_name" name="l_name" required value="<?php echo htmlspecialchars($user['l_name']); ?>">
                        </div>

                        <div class="mb-3">
                            <label for="dob" class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" id="dob" name="dob" required value="<?php echo htmlspecialchars($user['birthday']); ?>">
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" required value="<?php echo htmlspecialchars($user['email']); ?>">
                        </div>

                        <div class="mb-3">
                            <label for="contact" class="form-label">Contact Number</label>
                            <input type="text" class="form-control" id="contact" name="contact" required value="<?php echo htmlspecialchars($user['contact']); ?>">
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Update Profile</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>