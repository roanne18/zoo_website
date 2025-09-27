<?php
// Landing page: redirect to login or dashboard if logged in
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: caretaker_dashboard.php');
    exit;
}
header('Location: login.php');
exit;
?>