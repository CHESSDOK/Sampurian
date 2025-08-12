<?php
// auth.php — include at the top of all admin pages
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}
