<?php
require 'db.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$user_id = $_SESSION['user_id'];
$res = $conn->query("SELECT * FROM users WHERE id=$user_id");
$user = $res->fetch_assoc();
// Dummy categories for demo
$categories = ['Mammals', 'Birds', 'Reptiles', 'Domesticated Animals', 'Aquatic'];
// Handle feeding log input
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_feeding_log'])) {
    $animal_category = $conn->real_escape_string($_POST['animal_category']);
    $animal_name = $conn->real_escape_string($_POST['animal_name']);
    $food_type = $conn->real_escape_string($_POST['food_type']);
    $quantity = $conn->real_escape_string($_POST['quantity']);
    $feeding_time = $conn->real_escape_string($_POST['feeding_time']);
    $notes = $conn->real_escape_string($_POST['notes']);

    $sql = "INSERT INTO feeding_logs (user_id, animal_category, animal_name, food_type, quantity, feeding_time, notes)
             VALUES ($user_id, '$animal_category', '$animal_name', '$food_type', '$quantity', '$feeding_time', '$notes')";
    if ($conn->query($sql)) {
        $msg = "Feeding log added successfully!";
    } else {
        $msg = "Error: " . $conn->error;
    }
}
// Fetch feeding logs with search functionality
$search_query = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_term = $conn->real_escape_string($_GET['search']);
    $search_query = " AND (animal_category LIKE '%$search_term%' OR animal_name LIKE '%$search_term%' OR food_type LIKE '%$search_term%')";
}
$log_result = $conn->query("SELECT * FROM feeding_logs WHERE user_id = $user_id $search_query ORDER BY feeding_time DESC");

// Get the current page's filename
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Animal Feeding Monitoring System</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        body { margin: 0; padding: 0; font-family: sans-serif; background:#009688; }
        .sidebar-nav { position: fixed; top: 80px; left: 0; width: 250px; height: calc(100% - 80px); background: #009688; padding-top: 30px; display: flex; flex-direction: column; gap: 18px; z-index: 999; }
        .sidebar-nav a { color: #fff; text-decoration: none; font-weight: bold; padding: 15px 24px; border-radius: 2px; margin: 0 16px; transition: background 0.2s; }
        .sidebar-nav a:hover, .sidebar-nav a.active { background: #00796b; }
        .container { margin-left: 270px; margin-top: 40px; max-width: 1500px; padding: 0 20px; }
        @media (max-width: 700px) { .sidebar-nav { position: static; width: 100%; height: auto; flex-direction: row; padding: 0; top: 0; } .container { margin-left: 0; padding: 0 10px; } }
        .feeding-logs-table { width: 100%; background: #fff; border-collapse: collapse; margin-top: 20px; }
        .feeding-logs-table th, .feeding-logs-table td { border: 1px solid #ddd; padding: 8px 10px; }
        .feeding-logs-table th { background: #009688; color: #fff; }
        .feeding-log-form { background: #f9f9f9; border-radius: 8px; padding: 18px; margin-bottom: 30px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); }
        .categories { display: flex; flex-wrap: wrap; gap: 15px; margin-bottom: 20px; }
        .category-card { background: #eaf6f6; border-radius: 8px; padding: 15px; text-align: center; box-shadow: 0 1px 4px rgba(0,0,0,0.08); flex: 1 1 150px; }
        .category-card h4 { margin-top: 0; }
        .category-card img { width: 100px; height: 100px; object-fit: contain; margin-bottom: 10px; }
        h2, h3 { margin-top: 0; }
        .error { color: #c00; font-weight: bold; margin-bottom: 16px; }
        form input, form select, form textarea, form button { width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        form button { background: #00703c; color: #fff; border: none; cursor: pointer; }
        form button:hover { background: #005f33; }
        .header-content { display: flex; align-items: center; justify-content: center; gap: 20px; padding: 20px; }
        .header-logo { height: 60px; }
        @media (max-width: 700px) { .header-content { flex-direction: column; text-align: center; } }
        .search-container { margin-bottom: 20px; }
        .search-container input { width: calc(100% - 100px); display: inline-block; }
        .search-container button { width: 90px; display: inline-block; margin-left: 5px; }
    </style>
</head>
<body>
<header style="background:#009688;position:sticky;color:#fff;text-align:center;">
    <div class="header-content">
        <img src="assets/logo.png" alt="Zoo Logo" class="header-logo">
        <h1>Animal Feeding Monitoring System — Albay Park & Wildlife</h1>
    </div>
</header>
<nav class="sidebar-nav">
    <a href="caretaker_dashboard.php" class="<?php echo ($current_page == 'caretaker_dashboard.php') ? 'active' : ''; ?>">Dashboard</a>
    <a href="categories_caretaker.php" class="<?php echo ($current_page == 'categories_caretaker.php') ? 'active' : ''; ?>">Categories of Animals</a>
    <a href="feeding_log.php" class="<?php echo ($current_page == 'feeding_log.php') ? 'active' : ''; ?>">Add Feeding Log</a>
    <a href="feeding_logs_list.php" class="<?php echo ($current_page == 'feeding_logs_list.php') ? 'active' : ''; ?>">View Feeding Logs</a>
    <a href="profile_caretaker.php" class="<?php echo ($current_page == 'profile_caretaker.php' || $current_page == 'update_profile_care.php') ? 'active' : ''; ?>">User Profile</a>
    <a href="logout.php" class="<?php echo ($current_page == 'logout.php') ? 'active' : ''; ?>">Logout</a>
</nav>
<div class="container">
    <h2>Welcome, <?php echo htmlspecialchars($user['username']); ?></h2>
    <p>This dashboard provides access to your account, animal categories in the zoo, and navigation to your profile and logout. Here, you can explore zoo animal categories, log animal feedings, and manage your profile as a zoo staff member.</p>
    <hr>
    <h3>Categories of Animals in the Zoo:</h3>
    <div class="categories">
        <div class="category-card">
            <h4>Mammals</h4>
            <img src="assets/mammal.jpg" alt="Mammals">
        </div>
        <div class="category-card">
            <h4>Birds</h4>
            <img src="assets/bird.jpg" alt="Birds">
        </div>
        <div class="category-card">
            <h4>Reptiles</h4>
            <img src="assets/reptiles.jpg" alt="Reptiles">
        </div>
        <div class="category-card">
            <h4>Domesticated Animals</h4>
            <img src="assets/frog.jpg" alt="Domesticated">
        </div>
        <div class="category-card">
            <h4>Aquatic</h4>
            <img src="assets/aquatic.jpg" alt="Aquatic">
        </div>
    </div>
    <hr>
    <h3>Add Feeding Log</h3>
    <div class="feeding-log-form">
        <?php if ($msg) echo "<p class='error'>$msg</p>"; ?>
        <form method="post">
            <label>Animal Category</label>
            <select name="animal_category" required>
                <?php foreach($categories as $cat): ?>
                    <option value="<?php echo htmlspecialchars($cat); ?>"><?php echo htmlspecialchars($cat); ?></option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="animal_name" placeholder="Animal Name" required>
            <input type="text" name="food_type" placeholder="Food Type" required>
            <input type="text" name="quantity" placeholder="Quantity (e.g. 2kg, 5 pieces)" required>
            <input type="datetime-local" name="feeding_time" required>
            <textarea name="notes" placeholder="Notes (optional)"></textarea>
            <button type="submit" name="add_feeding_log">Add Log</button>
        </form>
    </div>
    <hr>
    <h3>Feeding Logs List</h3>
    <div class="search-container">
        <form method="get" action="">
            <input type="text" name="search" placeholder="Search logs..." value="<?php echo htmlspecialchars(isset($_GET['search']) ? $_GET['search'] : ''); ?>">
            <button type="submit">Search</button>
        </form>
    </div>
    <table class="feeding-logs-table">
        <tr>
            <th>Date/Time</th>
            <th>Animal Category</th>
            <th>Animal Name</th>
            <th>Food Type</th>
            <th>Quantity</th>
            <th>Notes</th>
        </tr>
        <?php if ($log_result->num_rows == 0): ?>
        <tr>
            <td colspan="6" style="text-align:center;">No feeding logs found.</td>
        </tr>
        <?php else: ?>
        <?php while($row = $log_result->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['feeding_time']); ?></td>
            <td><?php echo htmlspecialchars($row['animal_category']); ?></td>
            <td><?php echo htmlspecialchars($row['animal_name']); ?></td>
            <td><?php echo htmlspecialchars($row['food_type']); ?></td>
            <td><?php echo htmlspecialchars($row['quantity']); ?></td>
            <td><?php echo htmlspecialchars($row['notes']); ?></td>
        </tr>
        <?php endwhile; ?>
        <?php endif; ?>
    </table>
</div>
</body>
</html>