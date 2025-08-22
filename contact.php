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

// make sure cleared_notifs exists
if (!isset($_SESSION['cleared_notifs'])) {
    $_SESSION['cleared_notifs'] = [];
}

// fetch all notifications (Approved/Declined)
$sql = "
    SELECT id, 'barangay_clearance' AS src, status FROM barangay_clearance WHERE user_id = ? AND status IN ('Approved','Declined')
    UNION ALL
    SELECT id, 'business_permit', status FROM business_permit WHERE user_id = ? AND status IN ('Approved','Declined')
    UNION ALL
    SELECT id, 'business_permit_renewal', status FROM business_permit_renewal WHERE user_id = ? AND status IN ('Approved','Declined')
    UNION ALL
    SELECT id, 'indigency', status FROM indigency WHERE user_id = ? AND status IN ('Approved','Declined')
    UNION ALL
    SELECT id, 'animal_bite_reports', status FROM animal_bite_reports WHERE user_id = ? AND status IN ('Approved','Declined')
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id,$user_id,$user_id,$user_id,$user_id]);

$rows = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $notifKey = $row['src'].'_'.$row['id'];
    if (!in_array($notifKey, $_SESSION['cleared_notifs'])) {
        $rows[] = $row;
    }
}

// ✅ This is where you set notifCount
$notifCount = count($rows);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Contact | Barangay Sampiruhan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f8;
            font-family: 'Segoe UI', sans-serif;
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

        .section {
            background-color: white;
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .section h5 {
            font-weight: bold;
            margin-bottom: 15px;
        }

        .submit-btn {
            background-color: #227275;
            color: white;
            font-weight: bold;
            padding: 10px 30px;
            border: none;
            border-radius: 8px;
        }

        .submit-btn:hover {
            background-color: #1b5e5d;
        }

        label {
            font-weight: 500;
        }
    </style>
</head>

<body>

    <div class="container my-5">
        <!-- Top Bar -->
        <div class="top-bar d-flex justify-content-between align-items-center px-4 py-2">
            <!-- Left side: Logo -->
            <div class="d-flex align-items-center">
                <a href="dashboard.php"><img src="include/uploads/1752672752_474043763_2836446049861869_694324624727446876_n.jpg" alt="Barangay Logo" width="40" height="40" class="rounded-circle me-2"></a>
                <span class="fw-bold" style="font-size: x-large;">Barangay Sampiruhan</span>
            </div>

            <!-- Right side: Icons and Profile -->
            <div class="d-flex align-items-center">
                <a href="notif.php" title="Notifications" class="position-relative">
                    <i class="fas fa-bell"></i>
                    <?php if ($notifCount > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                    <?php endif; ?>
                </a>
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
        <!-- Contact Information -->
        <div class="section">
            <h5>Barangay Contact Information</h5>
            <div class="row">
                <div class="col-md-12">
                    <p><strong>📍 Address:</strong> Brgy. Sampiruhan The Old Brgy. Hall, 658M+V2W, Sampaguita st. brgy, Calamba, Laguna</p>
                    <p><strong>📞 Emergency Hotline:</strong> Available 24/7</p>
                    <p><strong>📧 Email:</strong> <a href="mailto:barangaysampiruhan@gmail.com">barangaysampiruhan@gmail.com</a></p>
                    <p><strong>🔗 Facebook:</strong> <a href="#">Barangay Sampiruhan FB</a></p>
                </div>
            </div>
        </div>

        <!-- FAQ Section -->
        <div class="section">
            <h5>Frequently Asked Questions (FAQs)</h5>
            <p><strong>What are the office hours of Barangay Sampiruhan?</strong><br>
                Our office is open Monday to Friday from 8:00 AM to 5:00 PM. We are closed on weekends and public holidays.</p>

            <p><strong>How can I request a Barangay Clearance?</strong><br>
                To request a Barangay Permit, visit the Barangay Hall or submit your request via our online form.</p>

            <p><strong>What should I do if I need to report an issue in our Barangay?</strong><br>
                You can report issues via our hotline, email, or directly through the inquiry form on this page.</p>

            <p><strong>How can I get a copy of my Barangay Permit?</strong><br>
                Please provide valid ID and proof of residency when requesting a Barangay Certificate. It can be processed at the Barangay Hall.</p>
        </div>

        <!-- Inquiry Form -->
        <div class="section">
            <h5>Inquiry Form</h5>
            <p>Please fill out the form below to submit your inquiries, requests, or concerns. A member of our team will get back to you as soon as possible.</p>
            <form action="process_inquiry.php" method="POST">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="fullName" class="form-label">Full Name:</label>
                        <input type="text" name="fullName" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email Address:</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="contact" class="form-label">Contact Number:</label>
                        <input type="text" name="contact" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <!-- Empty to balance layout -->
                    </div>
                </div>

                <div class="mb-3">
                    <label for="message" class="form-label">Your Inquiry/Message:</label>
                    <textarea name="message" class="form-control" rows="5" required></textarea>
                </div>

                <div class="text-end">
                    <button type="submit" class="submit-btn">Submit</button>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>