<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'include/config.php';

$user_id = $_SESSION['user_id'];

// Fetch user profile data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Animal Bite Investigation Report</title>
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

        .form-section {
            background-color: #d1e7e7;
            border-radius: 10px;
            padding: 30px;
            margin-top: 30px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        .form-title {
            background-color: #258B8C;
            color: white;
            padding: 12px;
            text-align: center;
            font-size: 1.3rem;
            border-radius: 6px;
            margin-bottom: 25px;
        }

        .form-label {
            font-weight: bold;
        }

        .form-control,
        .form-select {
            border-radius: 6px;
        }

        .btn-submit {
            width: 100%;
            background-color: #c2c2c2;
            font-weight: bold;
            border-radius: 20px;
        }

        .btn-submit:hover {
            background-color: #b0b0b0;
        }

        .alert {
            border-radius: 6px;
            margin-bottom: 20px;
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
      <div class="container form-container">
            <div class="form-title">Animal Bite Investigation Report</div>

            <form action="include/submit_animal_bite.php" method="POST" enctype="multipart/form-data" class="form-section">
                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($_SESSION['error_message']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['error_message']); ?>
                <?php endif; ?>

                <!-- I. Victim's Profile -->
                <div class="form-section-title">I. Victim’s Profile</div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Last Name</label>
                        <input type="text" name="last_name" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">First Name</label>
                        <input type="text" name="first_name" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Middle Name</label>
                        <input type="text" name="middle_name" class="form-control">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="dob" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Age</label>
                        <input type="number" name="age" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Gender</label>
                        <select name="gender" class="form-select">
                            <option disabled selected>Select</option>
                            <option>Male</option>
                            <option>Female</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Contact</label>
                        <input type="text" name="contact" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Guardian (for Minor)</label>
                        <input type="text" name="guardian" class="form-control">
                    </div>
                </div>

                <!-- II. Animal Bite History -->
                <div class="form-section-title">II. Animal Bite History</div>

                <div class="mb-3">
                    <label class="form-label">Lokasyon kung saan nakagat (bahay/kalsada etc.)</label>
                    <input type="text" name="bite_location" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Parte ng katawan na nakagat</label>
                    <input type="text" name="body_part" class="form-control">
                </div>

                <div class="row mb-3">
                    <label class="form-label">Hinugasan?</label>
                    <div class="col-md-2">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="washed" value="Oo" id="washed_yes">
                            <label class="form-check-label" for="washed_yes">OO</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="washed" value="Hindi" id="washed_no">
                            <label class="form-check-label" for="washed_no">HINDI</label>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Petsa ng Pagkagat</label>
                    <input type="date" name="bite_date" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Uri at Deskripsyon ng hayop na kumagat</label>
                    <input type="text" name="animal_description" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Kulay</label>
                    <input type="text" name="color" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Palatandaan</label>
                    <input type="text" name="marks" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Kalagayan ng Hayop</label><br>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="condition[]" value="Nakakulong" id="caged">
                        <label class="form-check-label" for="caged">Nakakulong</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="condition[]" value="Nakatali" id="tied">
                        <label class="form-check-label" for="tied">Nakatali</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="condition[]" value="Bakuran" id="yard">
                        <label class="form-check-label" for="yard">Alpas sa loob ng bakuran</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="condition[]" value="Gala" id="stray">
                        <label class="form-check-label" for="stray">Gala</label>
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="form-label">Rehistrado at Bakunado?</label>
                    <div class="col-md-2">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="registered" value="Oo" id="reg_yes">
                            <label class="form-check-label" for="reg_yes">OO</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="registered" value="Hindi" id="reg_no">
                            <label class="form-check-label" for="reg_no">HINDI</label>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="form-label">May kasamang ibang hayop?</label>
                    <div class="col-md-2">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="other_animals" value="Meron" id="with_other">
                            <label class="form-check-label" for="with_other">MERON</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="other_animals" value="Wala" id="no_other">
                            <label class="form-check-label" for="no_other">WALA</label>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Kalagayan ng aso bago nakagat?</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="dog_condition" value="Malusog" id="healthy">
                        <label class="form-check-label" for="healthy">MALUSOG</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="dog_condition" value="Bagong panganak" id="newborn">
                        <label class="form-check-label" for="newborn">BAGONG PANANGAK</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="dog_condition" value="May sakit" id="sick">
                        <label class="form-check-label" for="sick">MAY SAKIT</label>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Pangalan ng may-ari ng hayop na nakagat</label>
                    <input type="text" name="owner_name" class="form-control">
                </div>

                <div class="mb-4">
                    <label class="form-label">Select Payment Method</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_method" id="cash" value="Cash" checked>
                        <label class="form-check-label" for="cash">
                            <i class="fas fa-money-bill-wave"></i> Cash (Pay at Barangay Hall)
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_method" id="online" value="Online">
                        <label class="form-check-label" for="online">
                            <i class="fas fa-credit-card"></i> Pay Online
                        </label>
                    </div>
                </div>


                <button type="submit" class="btn btn-submit">Done</button>


            </form>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const gcashRadio = document.getElementById('gcash');
                const gcashModal = new bootstrap.Modal(document.getElementById('gcashModal'));
                const confirmBtn = document.getElementById('confirmGcashPayment');
                const form = document.querySelector('form');

                // Show modal when GCash is selected
                gcashRadio.addEventListener('change', function() {
                    if (this.checked) {
                        gcashModal.show();
                    }
                });

                // Handle confirm button
                confirmBtn.addEventListener('click', function() {
                    const refNo = document.getElementById('gcashRefNo').value;
                    if (!refNo) {
                        alert('Please enter your GCash reference number');
                        return;
                    }
                    gcashModal.hide();
                });

                // Validate form submission
                form.addEventListener('submit', function(e) {
                    if (gcashRadio.checked) {
                        const refNo = document.getElementById('gcashRefNo').value;
                        if (!refNo) {
                            e.preventDefault();
                            alert('Please complete your GCash payment details');
                            gcashModal.show();
                        }
                    }
                });
            });
        </script>
</body>

</html>