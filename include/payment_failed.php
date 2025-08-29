<?php
session_start();

$permit_id = $_GET['permit_id'] ?? null;
$_SESSION['error_message'] = "Payment failed for request ID: $permit_id. Please try again.";
header("Location: ../indigency.php");
exit();
