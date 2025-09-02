<!-- registration.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Create Account</title>
    <link rel="stylesheet" href="assets/css/register.css">
</head>

<body>
    <div class="form-wrapper">
        <div class="form-container">
            <h2>Create Account</h2>
            <form action="include/process_register.php" method="POST" enctype="multipart/form-data" id="registrationForm">
                <table>
                    <tr>
                        <td class="td-gap-right">
                            <input type="text" name="f_name" placeholder="First Name *" required>
                        </td>
                        <td>
                            <input type="text" name="l_name" placeholder="Last Name *" required>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"><input type="text" name="m_name" placeholder="Middle Name (optional)"></td>
                    </tr>
                    <tr>
                        <td colspan="2"><input type="email" name="email" id="email" placeholder="Email address *" required></td>
                    </tr>
                    <tr>
                        <td colspan="2"><input type="text" name="contact" id="contact" placeholder="Mobile no. *" 
                            pattern="^(09\d{9}|(\+639)\d{9})$" 
                            title="Enter a valid PH number (09XXXXXXXXX or +639XXXXXXXXX)" 
                            required>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"><input type="date" name="birthday" required></td>
                    </tr>
                    <tr>
                        <td>
                            <select name="gender" required>
                                <option value="">Gender *</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </td>
                        <td>
                            <select name="marriage_status">
                                <option value="">Marital Status</option>
                                <option value="Single">Single</option>
                                <option value="Married">Married</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"><input type="text" name="address" placeholder="Address"></td>
                    </tr>
                    <tr>
                        <td colspan="2"><input type="password" name="user_password" placeholder="Password *" required></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <label>Upload Picture:</label>
                            <input type="file" name="picture" accept="image/*">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <label>
                                <input type="checkbox" required> I agree to the <a href="#">Terms of Service and Policy</a>.
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <button type="submit" class="submit-btn">Create Account</button>
                        </td>
                    </tr>
                </table>
            </form>
            
            <!-- OTP Verification Section (Initially Hidden) -->
            <div id="otpSection" style="display: none;">
                <h3>Verify Your Email</h3>
                <p>We've sent a 6-digit OTP to your email. It will expire in 5 minutes.</p>
                <form id="otpForm">
                    <input type="text" id="otp" name="otp" placeholder="Enter OTP" maxlength="6" required>
                    <button type="button" id="verifyOtpBtn" class="submit-btn">Verify OTP</button>
                    <button type="button" id="resendOtpBtn" class="resend-btn">Resend OTP</button>
                </form>
                <div id="otpMessage"></div>
            </div>
            
            <div class="footer">
                Already have an Account? <a href="index.php">Login</a>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Submit form via AJAX
            const formData = new FormData(this);
            
            fetch('include/process_register.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Hide registration form, show OTP section
                    document.getElementById('registrationForm').style.display = 'none';
                    document.getElementById('otpSection').style.display = 'block';
                    document.getElementById('otpMessage').innerHTML = '<p style="color: green;">' + data.message + '</p>';
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });

        document.getElementById('verifyOtpBtn').addEventListener('click', function() {
            const otp = document.getElementById('otp').value;
            const email = document.getElementById('email').value;
            
            if (otp.length !== 6) {
                document.getElementById('otpMessage').innerHTML = '<p style="color: red;">Please enter a valid 6-digit OTP.</p>';
                return;
            }
            
            fetch('verify_otp.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'email=' + encodeURIComponent(email) + '&otp=' + encodeURIComponent(otp)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('otpMessage').innerHTML = '<p style="color: green;">' + data.message + '</p>';
                    setTimeout(function() {
                        window.location.href = 'index.php';
                    }, 2000);
                } else {
                    document.getElementById('otpMessage').innerHTML = '<p style="color: red;">' + data.message + '</p>';
                }
            });
        });

        document.getElementById('resendOtpBtn').addEventListener('click', function() {
            const email = document.getElementById('email').value;
            
            fetch('include/resend_otp.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'email=' + encodeURIComponent(email)
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('otpMessage').innerHTML = '<p style="color: green;">' + data.message + '</p>';
            });
        });

        document.getElementById('contact').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9+]/g, ''); // allow only numbers and +
            if (this.value.startsWith('+') && !this.value.startsWith('+63')) {
                this.value = '+63';
            }
            if (this.value.startsWith('0') && this.value.length > 11) {
                this.value = this.value.slice(0, 11);
            }
        });
    </script>
</body>

</html>