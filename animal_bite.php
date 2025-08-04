<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'include/config.php';

$user_id = $_SESSION['user_id'];

// Fetch user profile data
$stmt = $pdo->prepare("SELECT f_name, l_name, m_name, birthday, marriage_status, gender, address, contact, email, picture FROM users WHERE id = ?");
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
    <style>
        body {
            background-color: #eaf4f4;
            font-family: Arial, sans-serif;
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

        .form-container {
            background-color: #d1e7e7;
            padding: 30px;
            margin: 30px auto;
            border-radius: 10px;
            max-width: 900px;
        }

        .form-title {
            background-color: #0e5f5f;
            color: white;
            padding: 15px;
            text-align: center;
            font-size: 1.4rem;
            margin-bottom: 30px;
            border-radius: 6px;
        }

        .form-section-title {
            font-weight: bold;
            margin-bottom: 15px;
            font-size: 1.1rem;
        }

        .form-check-inline {
            margin-right: 20px;
        }

        .btn-submit {
            width: 100%;
            background-color: #c2c2c2;
            font-weight: bold;
            border-radius: 20px;
            padding: 10px 40px;
        }

        .alert {
            border-radius: 6px;
            margin-bottom: 20px;
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
                        <input class="form-check-input" type="radio" name="payment_method" id="gcash" value="GCash">
                        <label class="form-check-label" for="gcash">
                            <img src="assets/image/gcash.png" alt="GCash" style="height: 24px;"> GCash (Online Payment)
                        </label>
                    </div>

                    <div class="modal fade" id="gcashModal" tabindex="-1" aria-labelledby="gcashModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="gcashModalLabel">GCash Payment</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-center">
                                    <p>Please scan the QR code below or send payment to:</p>
                                    <img src="assets/image/gcash_qr.png" alt="GCash QR Code" class="img-fluid mb-3" style="max-width: 250px;">
                                    <p><strong>Barangay Sampiruhan Official GCash</strong><br>0912-345-6789</p>

                                    <div class="mb-3">
                                        <label for="gcashRefNo" class="form-label">GCash Reference Number</label>
                                        <input type="text" class="form-control" id="gcashRefNo" name="gcash_ref_no" placeholder="Enter reference number">
                                    </div>

                                    <div class="mb-3">
                                        <label for="paymentProof" class="form-label">Upload Payment Screenshot</label>
                                        <input type="file" class="form-control" id="paymentProof" name="payment_proof" accept="image/*,.pdf">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-primary" id="confirmGcashPayment">Confirm Payment</button>
                                </div>
                            </div>
                        </div>
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