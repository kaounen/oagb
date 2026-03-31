<?php
// admin/includes/auth_check.php
// Already includes session_start via connect.php which is required by db.php

if (!isset($_SESSION['admin_id'])) {
    header("Location: " . ADMIN_PATH . "/login.php");
    exit;
}

// User details for quick access
$admin_id = $_SESSION['admin_id'];
$admin_username = $_SESSION['admin_username'];
$admin_name = $_SESSION['admin_name'];
$admin_role = $_SESSION['admin_role'];
?>
