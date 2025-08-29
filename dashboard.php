<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'include/config.php';

$user_id = $_SESSION['user_id'];

// Fetch user name
$stmt = $pdo->prepare("SELECT f_name, l_name, picture FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Check for unread notifications
$notification_stmt = $pdo->prepare("SELECT COUNT(*) as unread_count FROM notification WHERE user_id = ? AND is_read = 0");
$notification_stmt->execute([$user_id]);
$notification_count = $notification_stmt->fetch(PDO::FETCH_ASSOC)['unread_count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Barangay SAMPIRUHAN Dashboard</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
  <style>
    body {
      background-color: #f8f9fa;
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
    .banner-img {
      height: 600px;
      object-fit: cover; /* crops image without distortion */
      width: 100%;
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
    footer {
      background: #34495e;
      color: white;
      padding: 20px 0;
    }
    footer a {
      color: white;
      text-decoration: none;
      margin: 0 10px;
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
      <a href="dashboard.php"><img src="assets/image/sam.png" alt="Barangay Logo" width="50" height="50" class="rounded-circle me-2"></a>
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

  <!-- Banner (full width) -->
  <div class="container-fluid px-0">
    <div class="card border-0">
      <img src="assets/image/hero.jpg" class="card-img-top w-100 banner-img" alt="Barangay Officials">
    </div>
  </div>

  <!-- Services Section -->
  <div class="container my-4">
    <div class="row text-center g-3">
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

  <!-- Contact Info -->
  <div class="container mb-4">
    <div class="card p-3">
      <h5>Barangay Contact Information</h5>
      <p><i class="bi bi-geo-alt-fill text-danger"></i> Address: Brgy. Sampiruhan The Old Brgy. Hall, Sampaguita St. Brgy, Calamba, Laguna</p>
      <p><i class="bi bi-telephone-fill text-danger"></i> Emergency Hotline: Available 24/7</p>
      <p><i class="bi bi-envelope-fill text-primary"></i> Email: <a href="mailto:barangaysampiruhan@gmail.com">barangaysampiruhan@gmail.com</a></p>
      <p><i class="bi bi-facebook text-primary"></i> Facebook: <a href="#">Barangay Sampiruhan FB</a></p>
    </div>
  </div>

  <!-- FAQs -->
  <div class="container mb-4">
    <div class="card p-3">
      <h5>Frequently Asked Questions (FAQs)</h5>
      <p><b>What are the office hours of Barangay Sampiruhan?</b><br>Our office is open Monday to Friday from 8:00 AM to 5:00 PM. We are closed on weekends and public holidays.</p>
      <p><b>How can I request a Barangay Clearance?</b><br>To request a Barangay Permit, visit the Barangay Hall or submit your request via our online form.</p>
      <p><b>What should I do if I need to report an issue in our Barangay?</b><br>You can report issues via our hotline, email, or directly through the inquiry form on this page.</p>
      <p><b>How can I get a copy of my Barangay Permit?</b><br>Please provide valid ID and proof of residency when requesting a Barangay Certificate. It can be processed at the Barangay Hall.</p>
    </div>
  </div>

  <!-- Footer -->
  <footer>
    <div class="container text-center">
      <div class="row">
        <div class="col-md-4">
          <h6>BARANGAY SAMPIRUHAN</h6>
          <p>143 Sampiruan Balot Street<br>Hon. Name D. Sampal<br>+63 902-2391-123</p>
        </div>
        <div class="col-md-4">
          <h6>FOLLOW US</h6>
          <a href="#"><i class="bi bi-facebook"></i></a>
          <a href="#"><i class="bi bi-instagram"></i></a>
          <a href="#"><i class="bi bi-tiktok"></i></a>
        </div>
        <div class="col-md-4">
          <h6>Inquiry Form</h6>
          <form method="POST" action="include/send_inquiry.php">
            <input type="text" name="name" class="form-control mb-2" placeholder="Name" required>
            <input type="email" name="email" class="form-control mb-2" placeholder="Email" required>
            <input type="text" name="contact" class="form-control mb-2" placeholder="Contact" required>
            <textarea name="message" class="form-control mb-2" rows="2" placeholder="Message" required></textarea>
            <button class="btn btn-light btn-sm" type="submit">Submit</button>
          </form>
        </div>
      </div>
      <hr>
      <p class="mb-0">COPY RIGHT © TO LSPU STUDENTS</p>
    </div>
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
