<?php
require 'db.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$msg = '';
$user_id = $_SESSION['user_id'];
$res = $conn->query("SELECT * FROM users WHERE id=$user_id");
$user = $res->fetch_assoc();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = $conn->real_escape_string($_POST['fullname']);
    $age = intval($_POST['age']);
    $license = ($user['position'] == "Veterinarian") ? $conn->real_escape_string($_POST['license_number']) : '';
    // Handle profile picture upload
    $picname = $user['profile_pic'];
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['size'] > 0) {
        $target_dir = "uploads/";
        $ext = pathinfo($_FILES["profile_pic"]["name"], PATHINFO_EXTENSION);
        $picname = "user" . $user_id . "." . $ext;
        move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_dir . $picname);
    }
    if ($user['position'] == "Veterinarian") {
        $sql = "UPDATE users SET fullname='$fullname', age=$age, license_number='$license', profile_pic='$picname' WHERE id=$user_id";
    } else {
        $sql = "UPDATE users SET fullname='$fullname', age=$age, profile_pic='$picname' WHERE id=$user_id";
    }
    if ($conn->query($sql)) {
        $msg = "Profile updated!";
        header("Refresh:1; url=profile.php");
    } else {
        $msg = "Update failed: " . $conn->error;
    }
}

// Get the current page's filename
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Update Profile - Zoo Portal</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        .sidebar-nav {
            position: fixed;
            top: 80px;
            left: 0;
            width: 200px;
            height: calc(100% - 80px);
            background: #009688;
            padding-top: 30px;
            display: flex;
            flex-direction: column;
            gap: 18px;
            z-index: 999;
        }
        .sidebar-nav a {
            color: #fff;
            text-decoration: none;
            font-weight: bold;
            padding: 12px 24px;
            border-radius: 4px;
            margin: 0 16px;
            transition: background 0.2s;
        }
        .sidebar-nav a:hover, .sidebar-nav a.active {
            background: #00796b;
        }
        .container {
            margin-left: 220px;
            margin-top: 40px;
            max-width: 600px;
        }
        form {
            background: #f9f9f9;
            border-radius: 8px;
            padding: 24px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
            display: flex;
            flex-direction: column;
            gap: 14px;
        }
        input, button, label {
            font-size: 1em;
        }
        input[type="text"], 
        input[type="number"], 
        input[type="file"] {
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        button {
            background: #009688;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 12px 24px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 12px;
            transition: background 0.2s;
        }
        button:hover {
            background: #00796b;
        }
        .error {
            color: #c00;
            font-weight: bold;
            margin-bottom: 16px;
        }
        @media (max-width: 700px) {
            .sidebar-nav {
                position: static;
                width: 100%;
                height: auto;
                flex-direction: row;
                padding: 0;
                top: 0;
            }
            .container {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
<header style="background:#009688;color:#fff;padding:20px;text-align:center;">
    <h1>Animal Feeding Monitoring System — Albay Park & Wildlife</h1>
</header>
<nav class="sidebar-nav">
    <a href="manager_dashboard.php" class="<?php echo ($current_page == 'manager_dashboard.php') ? 'active' : ''; ?>">Dashboard</a>
    <a href="categories_manager.php" class="<?php echo ($current_page == 'categories_manager.php') ? 'active' : ''; ?>">Categories of Animals</a>
    <a href="profile_man.php" class="<?php echo ($current_page == 'profile_man.php' || $current_page == 'update_profile.php') ? 'active' : ''; ?>">User Profile</a>
    <a href="logout.php" class="<?php echo ($current_page == 'logout.php') ? 'active' : ''; ?>">Logout</a>
</nav>
<div class="container">
    <h2>Update Profile</h2>
    <?php if ($msg) echo "<p class='error'>$msg</p>"; ?>
    <form method="post" enctype="multipart/form-data">
        <label for="fullname">Full Name:</label>
        <input type="text" name="fullname" id="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>" required>
        <label for="age">Age:</label>
        <input type="number" name="age" id="age" value="<?php echo htmlspecialchars($user['age']); ?>" min="18" required>
        <?php if ($user['position']=='Veterinarian'): ?>
            <label for="license_number">License Number:</label>
            <input type="text" name="license_number" id="license_number" value="<?php echo htmlspecialchars($user['license_number']); ?>" placeholder="License Number" required>
        <?php endif; ?>
        <label for="profile_pic">Profile Picture:</label>
        <input type="file" name="profile_pic" id="profile_pic" accept="image/*">
        <button type="submit">Save Changes</button>
    </form>
</div>
</body>
</html>