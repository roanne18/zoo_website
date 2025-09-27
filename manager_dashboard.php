<?php
require 'db.php';
// Start a session if it hasn't been started in db.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in AND has the 'manager' role
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Check the user's role to ensure only managers can access this page
$stmt = $conn->prepare("SELECT username, role FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();
$stmt->close();

if ($user['role'] !== 'Manager') {
    header('Location: manager_dashboard.php'); // Redirect to a different dashboard
    exit;
}

$msg = '';
$edit_animal = null;

// --- Handle Form Submissions ---

// Handle adding or updating an animal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['add_animal']) || isset($_POST['update_animal']))) {
    $name = $conn->real_escape_string($_POST['name']);
    $category = $conn->real_escape_string($_POST['category']);
    $status = $conn->real_escape_string($_POST['status']);
    $notes = $conn->real_escape_string($_POST['notes']);
    
    // Validate inputs
    if (empty($name) || empty($category)) {
        $msg = "Error: Animal name and category are required.";
    } else {
        if (isset($_POST['add_animal'])) {
            // Add a new animal
            $stmt = $conn->prepare("INSERT INTO animals (name, category, status, notes) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $category, $status, $notes);
            if ($stmt->execute()) {
                $msg = "Animal added successfully! ✅";
            } else {
                $msg = "Error adding animal: " . $stmt->error;
            }
        } elseif (isset($_POST['update_animal'])) {
            // Update an existing animal
            $animal_id = $conn->real_escape_string($_POST['animal_id']);
            $stmt = $conn->prepare("UPDATE animals SET name = ?, category = ?, status = ?, notes = ? WHERE id = ?");
            $stmt->bind_param("ssssi", $name, $category, $status, $notes, $animal_id);
            if ($stmt->execute()) {
                $msg = "Animal updated successfully! ✅";
            } else {
                $msg = "Error updating animal: " . $stmt->error;
            }
        }
        $stmt->close();
    }
}

// Handle deleting an animal
if (isset($_GET['delete'])) {
    $animal_id = $conn->real_escape_string($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM animals WHERE id = ?");
    $stmt->bind_param("i", $animal_id);
    if ($stmt->execute()) {
        $msg = "Animal deleted successfully! 🗑️";
    } else {
        $msg = "Error deleting animal: " . $stmt->error;
    }
    $stmt->close();
    header('Location: manager_dashboard.php'); // Redirect to prevent re-deletion on refresh
    exit;
}

// Handle pre-filling form for editing
if (isset($_GET['edit'])) {
    $animal_id = $conn->real_escape_string($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM animals WHERE id = ?");
    $stmt->bind_param("i", $animal_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $edit_animal = $result->fetch_assoc();
    }
    $stmt->close();
}

// Fetch all animals from the database
$result = $conn->query("SELECT * FROM animals ORDER BY date_added DESC");
$animals = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $animals[] = $row;
    }
}

// Dummy categories (can be fetched from a table if you create one)
$categories = ['Mammals', 'Birds', 'Reptiles', 'Domesticated Animals', 'Aquatic'];

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manager Dashboard - Zoo Portal</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        body { margin: 0; padding: 0; font-family: sans-serif; background: #e8f5e9; }
        .sidebar-nav { position: fixed; top: 80px; left: 0; width: 250px; height: calc(100% - 80px); background: #388e3c; padding-top: 30px; display: flex; flex-direction: column; gap: 18px; z-index: 999; }
        .sidebar-nav a { color: #fff; text-decoration: none; font-weight: bold; padding: 15px 24px; border-radius: 2px; margin: 0 16px; transition: background 0.2s; }
        .sidebar-nav a:hover, .sidebar-nav a.active { background: #1b5e20; }
        .container { margin-left: 270px; margin-top: 40px; max-width: 1500px; padding: 0 20px; }
        @media (max-width: 700px) { .sidebar-nav { position: static; width: 100%; height: auto; flex-direction: row; padding: 0; top: 0; } .container { margin-left: 0; padding: 0 10px; } }
        .animal-table { width: 100%; background: #fff; border-collapse: collapse; margin-top: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .animal-table th, .animal-table td { border: 1px solid #ddd; padding: 12px; }
        .animal-table th { background: #4caf50; color: #fff; }
        .action-buttons a { margin-right: 5px; text-decoration: none; padding: 5px 10px; border-radius: 4px; }
        .edit-btn { background-color: #ff9800; color: #fff; }
        .delete-btn { background-color: #f44336; color: #fff; }
        .add-form { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin-bottom: 30px; }
        .status-alive { color: #4CAF50; font-weight: bold; }
        .status-deceased { color: #F44336; font-weight: bold; }
        .header-content { display: flex; align-items: center; justify-content: center; gap: 20px; padding: 20px; background: #4caf50; color: #fff; text-align: center; }
        .header-logo { height: 60px; }
        form input, form select, form textarea, form button { width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        form button { background: #00703c; color: #fff; border: none; cursor: pointer; }
        form button:hover { background: #005f33; }
    </style>
</head>
<body>
<header>
    <div class="header-content">
        <img src="assets/logo.png" alt="Zoo Logo" class="header-logo">
        <h1>Manager Dashboard — Albay Park & Wildlife</h1>
    </div>
</header>
<nav class="sidebar-nav">
    <a href="manager_dashboard.php" class="<?php echo ($current_page == 'manager_dashboard.php') ? 'active' : ''; ?>">Dashboard</a>
    <a href="categories_manager.php" class="<?php echo ($current_page == 'categories_manager.php') ? 'active' : ''; ?>">View Categories</a>
    <a href="profile_man.php" class="<?php echo ($current_page == 'profile_man.php' || $current_page == 'update_profile_man.php') ? 'active' : ''; ?>">User Profile</a>
    <a href="logout.php" class="<?php echo ($current_page == 'logout.php') ? 'active' : ''; ?>">Logout</a>
</nav>
<div class="container">
    <h2>Welcome, <?php echo htmlspecialchars($user['username']); ?> (Manager)</h2>
    <p>This is the manager's dashboard. You have the authority to add new animals, edit their details, or mark them as deceased.</p>
    <hr>
    
    <h3><?php echo $edit_animal ? 'Edit Animal' : 'Add New Animal'; ?></h3>
    <div class="add-form">
        <?php if ($msg) echo "<p style='color: green;'>$msg</p>"; ?>
        <form method="post">
            <input type="hidden" name="animal_id" value="<?php echo htmlspecialchars($edit_animal['id'] ?? ''); ?>">
            
            <label for="name">Animal Name</label>
            <input type="text" name="name" id="name" placeholder="Animal Name" value="<?php echo htmlspecialchars($edit_animal['name'] ?? ''); ?>" required>
            
            <label for="category">Animal Category</label>
            <select name="category" id="category" required>
                <?php foreach($categories as $cat): ?>
                    <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo ($edit_animal['category'] ?? '') === $cat ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <label for="status">Status</label>
            <select name="status" id="status" required>
                <option value="alive" <?php echo ($edit_animal['status'] ?? '') === 'alive' ? 'selected' : ''; ?>>Alive</option>
                <option value="deceased" <?php echo ($edit_animal['status'] ?? '') === 'deceased' ? 'selected' : ''; ?>>Deceased</option>
            </select>
            
            <label for="notes">Notes</label>
            <textarea name="notes" id="notes" placeholder="Notes (optional)"><?php echo htmlspecialchars($edit_animal['notes'] ?? ''); ?></textarea>
            
            <button type="submit" name="<?php echo $edit_animal ? 'update_animal' : 'add_animal'; ?>">
                <?php echo $edit_animal ? 'Update Animal' : 'Add Animal'; ?>
            </button>
        </form>
    </div>
    <hr>
    
    <h3>All Animals List</h3>
    <table class="animal-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Category</th>
                <th>Status</th>
                <th>Date Added</th>
                <th>Notes</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($animals)): ?>
                <tr>
                    <td colspan="7" style="text-align: center;">No animals found.</td>
                </tr>
            <?php else: ?>
                <?php foreach($animals as $animal): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($animal['id']); ?></td>
                        <td><?php echo htmlspecialchars($animal['name']); ?></td>
                        <td><?php echo htmlspecialchars($animal['category']); ?></td>
                        <td>
                            <span class="status-<?php echo htmlspecialchars($animal['status']); ?>">
                                <?php echo htmlspecialchars(ucfirst($animal['status'])); ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($animal['date_added']); ?></td>
                        <td><?php echo htmlspecialchars($animal['notes']); ?></td>
                        <td class="action-buttons">
                            <a href="manager_dashboard.php?edit=<?php echo $animal['id']; ?>" class="edit-btn">Edit</a>
                            <a href="manager_dashboard.php?delete=<?php echo $animal['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this animal?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>