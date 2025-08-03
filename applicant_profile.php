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
    <style>
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
    </style>
</head>

<body>
    <div class="container mt-5">
        <!-- Top Bar -->
        <div class="top-bar d-flex justify-content-between align-items-center px-4 py-2">
            <!-- Left side: Logo -->
            <div class="d-flex align-items-center">
                <a href="dashboard.php"><img src="include/uploads/1752672752_474043763_2836446049861869_694324624727446876_n.jpg" alt="Barangay Logo" width="40" height="40" class="rounded-circle me-2"></a>
                <span class="fw-bold" style="font-size: x-large;">Barangay Sampiruhan</span>
            </div>

            <!-- Right side: Icons and Profile -->
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
    </div>
    <div class="container mt-5">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-4">
                <div class="profile-sidebar">
                    <img src="assets/image/user-placeholder.png" class="rounded-circle mb-3" alt="Profile Picture">
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