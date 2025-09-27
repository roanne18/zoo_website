<?php
require 'db.php';
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $fullname = $conn->real_escape_string($_POST['fullname']);
    $age = intval($_POST['age']);
    $position = $conn->real_escape_string($_POST['position']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];
    $license = ($position == "Veterinarian") ? $conn->real_escape_string($_POST['license_number']) : '';
    
    // Set the role based on the selected position
    $role = $position;

    // Basic validation
    if ($password !== $confirm) {
        $msg = "Password and confirmation do not match.";
    } else {
        $sql = "SELECT id FROM users WHERE username='$username'";
        $res = $conn->query($sql);
        if ($res->num_rows > 0) {
            $msg = "Username already exists.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            // Insert user with the new 'role' column
            $stmt = $conn->prepare("INSERT INTO users (username, fullname, age, position, role, password, license_number) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssissss", $username, $fullname, $age, $position, $role, $hash, $license);
            
            if ($stmt->execute()) {
                $msg = "Registration successful! <a href='login.php'>Login here</a>";
            } else {
                $msg = "Registration failed: " . $conn->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register - Zoo Portal</title>
    <link rel="stylesheet" href="assets/style.css">
    <script src="assets/script.js"></script>
</head>
<body>
<header style="background:#009688;color:#fff;padding:20px;text-align:center;">
    <h1>Animal Feeding Monitoring System — Albay Park & Wildlife</h1>
</header>
<div class="container">
    <h2>Register</h2>
    <?php if ($msg) echo "<p class='error'>$msg</p>"; ?>
    <form method="post" id="regForm">
        <input type="text" name="username" placeholder="Username" required>
        <input type="text" name="fullname" placeholder="Full Name" required>
        <input type="number" name="age" placeholder="Age" min="18" required>
        <select name="position" id="position" required onchange="showLicense()">
            <option value="">Select Position</option>
            <option value="Veterinarian">Veterinarian</option>
            <option value="Manager">Manager</option>
            <option value="Caretaker">Caretaker</option>
        </select>
        <div id="licenseDiv" style="display:none;">
            <input type="text" name="license_number" placeholder="License Number">
        </div>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        <button type="submit">Register</button>
    </form>
    <p>Already have an account? <a href="login.php">Login here</a></p>
</div>
</body>
</html>