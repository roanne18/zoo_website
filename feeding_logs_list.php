<?php
require 'db.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT * FROM feeding_logs WHERE user_id = $user_id ORDER BY feeding_time DESC");

// Get the current page's filename
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Feeding Logs - Zoo Portal</title>
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
            max-width: 900px;
        }
        table {
            width: 100%;
            background: #fff;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            padding: 10px 7px;
            border: 1px solid #eee;
            text-align: left;
        }
        th {
            background: #eaf6f6;
            color: #009688;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        .print-btn {
            background: #009688;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 10px 24px;
            font-weight: bold;
            cursor: pointer;
            margin-bottom: 18px;
            margin-left: 0;
            transition: background 0.2s;
        }
        .print-btn:hover {
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
        /* Print styles */
        @media print {
            body * {
                visibility: hidden;
            }
            .container, .container * {
                visibility: visible;
            }
            .container {
                position: absolute;
                left: 0;
                top: 0;
                width: 100% !important;
                margin: 0 !important;
            }
            .sidebar-nav, header, .print-btn {
                display: none !important;
            }
        }
    </style>
    <script>
        function printLogs() {
            window.print();
        }
    </script>
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
    <h2>My Feeding Logs</h2>
    <button class="print-btn" onclick="printLogs()">Print Logs</button>
    <table>
        <tr>
            <th>Date/Time</th>
            <th>Animal Category</th>
            <th>Animal Name</th>
            <th>Food Type</th>
            <th>Quantity</th>
            <th>Notes</th>
        </tr>
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['feeding_time']); ?></td>
                <td><?php echo htmlspecialchars($row['animal_category']); ?></td>
                <td><?php echo htmlspecialchars($row['animal_name']); ?></td>
                <td><?php echo htmlspecialchars($row['food_type']); ?></td>
                <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                <td><?php echo htmlspecialchars($row['notes']); ?></td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" style="text-align: center;">No feeding logs found.</td>
            </tr>
        <?php endif; ?>
    </table>
</div>
</body>
</html>