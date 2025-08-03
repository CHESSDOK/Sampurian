<!-- registration.html -->
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
            <form action="include/process_register.php" method="POST" enctype="multipart/form-data">
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
                        <td colspan="2"><input type="email" name="email" placeholder="Email address *" required></td>
                    </tr>
                    <tr>
                        <td colspan="2"><input type="text" name="contact" placeholder="Mobile no. *" required></td>
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
            <div class="footer">
                Already have an Account? <a href="index.php">Login</a>
            </div>
        </div>
    </div>
</body>

</html>