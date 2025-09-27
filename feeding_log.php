<?php
require 'db.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
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
$categories = ['Mammals', 'Birds', 'Reptiles', 'Amphibians', 'Insects', 'Aquatic'];

// Get the current page's filename
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Feeding Log - Zoo Portal</title>
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
        input, select, textarea, button, label {
            font-size: 1em;
        }
        input[type="text"], 
        input[type="datetime-local"], 
        select, textarea {
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
    <a href="caretaker_dashboard.php" class="<?php echo ($current_page == 'caretaker_dashboard.php') ? 'active' : ''; ?>">Dashboard</a>
    <a href="categories_caretaker.php" class="<?php echo ($current_page == 'categories_caretaker.php') ? 'active' : ''; ?>">Categories of Animals</a>
    <a href="feeding_log.php" class="<?php echo ($current_page == 'feeding_log.php') ? 'active' : ''; ?>">Add Feeding Log</a>
    <a href="feeding_logs_list.php" class="<?php echo ($current_page == 'feeding_logs_list.php') ? 'active' : ''; ?>">View Feeding Logs</a>
    <a href="profile_caretaker.php" class="<?php echo ($current_page == 'profile_caretaker.php') ? 'active' : ''; ?>">User Profile</a>
    <a href="logout.php" class="<?php echo ($current_page == 'logout.php') ? 'active' : ''; ?>">Logout</a>
</nav>
<div class="container">
    <h2>Add Feeding Log</h2>
    <?php if ($msg) echo "<p class='error'>$msg</p>"; ?>
    <form method="post">
        <label for="animal_category">Animal Category</label>
        <select name="animal_category" id="animal_category" required>
            <?php foreach($categories as $cat): ?>
                <option value="<?php echo htmlspecialchars($cat); ?>"><?php echo htmlspecialchars($cat); ?></option>
            <?php endforeach; ?>
        </select>
        <label for="animal_name">Animal Name</label>
        <input type="text" name="animal_name" id="animal_name" placeholder="Animal Name" required>
        <label for="food_type">Food Type</label>
        <input type="text" name="food_type" id="food_type" placeholder="Food Type" required>
        <label for="quantity">Quantity</label>
        <input type="text" name="quantity" id="quantity" placeholder="Quantity (e.g. 2kg, 5 pieces)" required>
        <label for="feeding_time">Feeding Time</label>
        <input type="datetime-local" name="feeding_time" id="feeding_time" required>
        <label for="notes">Notes</label>
        <textarea name="notes" id="notes" placeholder="Notes (optional)"></textarea>
        <button type="submit">Add Log</button>
    </form>
</div>
</body>
</html>