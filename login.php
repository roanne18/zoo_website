<?php
require 'db.php';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Use a prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res && $row = $res->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            
            // Redirect based on the user's role
            switch ($row['role']) {
                case 'Caretaker':
                    header('Location: caretaker_dashboard.php');
                    break;
                case 'Manager':
                    header('Location: manager_dashboard.php');
                    break;
                case 'Veterinarian':
                    header('Location: veterinarian_dashboard.php');
                    break;
                default:
                    // Default redirection if role is unknown
                    header('Location: dashboard.php');
                    break;
            }
            exit;
        } else {
            $msg = 'Incorrect password.';
        }
    } else {
        $msg = 'User not found.';
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - Zoo Portal</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header style="background:#009688;color:#fff;padding:20px;text-align:center;">
    <h1>Animal Feeding Monitoring System — Albay Park & Wildlife</h1>
</header>
<div class="container">
    <h2>Login</h2>
    <?php if ($msg) echo "<p class='error'>$msg</p>"; ?>
    <form method="post">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="register.php">Register here</a></p>
</div>
</body>
</html>