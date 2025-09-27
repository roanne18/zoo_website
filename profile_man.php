<?php
require 'db.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$user_id = $_SESSION['user_id'];
$res = $conn->query("SELECT * FROM users WHERE id=$user_id");
$user = $res->fetch_assoc();
$pic = $user['profile_pic'] ? 'uploads/'.$user['profile_pic'] : 'assets/default-user.png';

// Get the current page's filename
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>User Profile - Zoo Portal</title>
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
        .profile-pic {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 60px;
            border: 2px solid #009688;
            background: #fff;
            margin-bottom: 15px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        .profile-info {
            background: #f9f9f9;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            font-size: 1.1em;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
        }
        form {
            display: flex;
            justify-content: center;
        }
        button {
            background: #009688;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 12px 24px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s;
        }
        button:hover {
            background: #00796b;
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
    <a href="profile_man.php" class="<?php echo ($current_page == 'profile_man.php') ? 'active' : ''; ?>">User Profile</a>
    <a href="logout.php" class="<?php echo ($current_page == 'logout.php') ? 'active' : ''; ?>">Logout</a>
</nav>
<div class="container">
    <h2>User Profile</h2>
    <img src="<?php echo $pic; ?>" alt="Profile Picture" class="profile-pic">
    <div class="profile-info">
        <b>Username:</b> <?php echo htmlspecialchars($user['username']); ?><br>
        <b>Full Name:</b> <?php echo htmlspecialchars($user['fullname']); ?><br>
        <b>Age:</b> <?php echo htmlspecialchars($user['age']); ?><br>
        <b>Position:</b> <?php echo htmlspecialchars($user['position']); ?><br>
        <?php if ($user['position']=='Veterinarian'): ?>
            <b>License Number:</b> <?php echo htmlspecialchars($user['license_number']); ?><br>
        <?php endif; ?>
    </div>
    <form action="update_profile_man.php" method="get">
        <button type="submit">Update Profile</button>
    </form>
</div>
</body>
</html>