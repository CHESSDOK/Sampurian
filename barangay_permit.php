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
    <title>Barangay Business Permit</title>
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

        .form-section {
            background-color: #d1e7e7;
            border-radius: 10px;
            padding: 30px;
            margin-top: 30px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        .form-title {
            background-color: #0e5f5f;
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

        <!-- Business Permit Form -->
        <form action="include/submit_business_permit.php" method="POST" enctype="multipart/form-data" class="form-section">
            <div class="form-title">Barangay Business Permit</div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Last Name</label>
                    <input type="text" class="form-control" name="last_name" required value="<?= htmlspecialchars($user['l_name']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">First Name</label>
                    <input type="text" class="form-control" name="first_name" required value="<?= htmlspecialchars($user['f_name']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Middle Name</label>
                    <input type="text" class="form-control" name="middle_name" value="<?= htmlspecialchars($user['m_name']) ?>">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" class="form-control" name="dob" required value="<?= htmlspecialchars($user['birthday']) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <input type="text" class="form-control" name="status" value="<?= htmlspecialchars($user['marriage_status']) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Gender</label>
                    <select class="form-select" name="gender" required>
                        <option disabled>Select</option>
                        <option value="Male" <?= $user['gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
                        <option value="Female" <?= $user['gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <label class="form-label">Address</label>
                <div class="col-md-6 mb-2">
                    <input type="text" class="form-control" name="house_no" placeholder="House / Building / Block & Lot No." value="<?= htmlspecialchars($user['address']) ?>">
                </div>
                <div class="col-md-6 mb-2">
                    <input type="text" class="form-control" name="street" placeholder="Street / Subdivision / Village" value="<?= htmlspecialchars($user['address']) ?>">
                </div>
                <div class="col-12">
                    <input type="text" class="form-control" value="Sampiruhan, Calamba City Laguna" readonly>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Mobile No.</label>
                    <input type="text" class="form-control" name="mobile" required value="<?= htmlspecialchars($user['contact']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email Address</label>
                    <input type="email" class="form-control" name="email" required value="<?= htmlspecialchars($user['email']) ?>">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Name/Kind of Establishment</label>
                    <input type="text" class="form-control" name="establishment_name" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nature of Business</label>
                    <input type="text" class="form-control" name="business_nature" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Required Documents</label>
                <ul class="list-unstyled">
                    <li>Certificate of Business Registration (DTI or SEC)
                        <input type="file" class="form-control mt-1" name="doc_business_reg" required>
                    </li>
                    <li class="mt-2">Community Tax Certificate (Cedula)
                        <input type="file" class="form-control mt-1" name="doc_cedula" required>
                    </li>
                    <li class="mt-2">Other specific requirements
                        <input type="file" class="form-control mt-1" name="doc_others">
                    </li>
                </ul>
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
            </div>

            <button type="submit" class="btn btn-submit">Done</button>
        </form>
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
                            <input type="text" class="form-control" id="gcashRefNo" name="gcash_ref_no" placeholder="Enter reference number" required>
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