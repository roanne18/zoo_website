<?php
require 'db.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();
$stmt->close();

// Optional: check for 'Veterinarian' role if your users table has one
if ($user['role'] !== 'Veterinarian') {
    header('Location: veterinarian_dashboard.php'); // Redirect to a different dashboard
    exit;
}

$msg = '';

// Handle treatment log submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_treatment'])) {
    $animal_id = $conn->real_escape_string($_POST['animal_id']);
    $medicine_id = $conn->real_escape_string($_POST['medicine_id']);
    $notes = $conn->real_escape_string($_POST['notes']);

    $stmt = $conn->prepare("INSERT INTO treatments (animal_id, medicine_id, notes) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $animal_id, $medicine_id, $notes);

    if ($stmt->execute()) {
        $msg = "Treatment log added successfully! ✅";
    } else {
        $msg = "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch all animals to be displayed
$animals_result = $conn->query("SELECT id, name, category, status FROM animals WHERE status = 'alive' ORDER BY name ASC");

// Fetch all available medicines for the form
$medicines_result = $conn->query("SELECT id, name FROM medicines ORDER BY name ASC");

// Fetch all treatment logs
$treatments_result = $conn->query("SELECT t.treatment_date, a.name AS animal_name, a.category, m.name AS medicine_name, t.notes
                                   FROM treatments t
                                   JOIN animals a ON t.animal_id = a.id
                                   JOIN medicines m ON t.medicine_id = m.id
                                   ORDER BY t.treatment_date DESC");

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Veterinarian Dashboard</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        body { margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #e8f5e9; }
        .sidebar-nav { position: fixed; top: 80px; left: 0; width: 250px; height: calc(100% - 80px); background: #2e7d32; padding-top: 30px; display: flex; flex-direction: column; gap: 18px; z-index: 999; }
        .sidebar-nav a { color: #fff; text-decoration: none; font-weight: bold; padding: 15px 24px; border-radius: 2px; margin: 0 16px; transition: background 0.2s; }
        .sidebar-nav a:hover, .sidebar-nav a.active { background: #1b5e20; }
        .container { margin-left: 270px; margin-top: 40px; max-width: 1500px; padding: 0 20px; }
        @media (max-width: 700px) { .sidebar-nav { position: static; width: 100%; height: auto; flex-direction: row; padding: 0; top: 0; } .container { margin-left: 0; padding: 0 10px; } }
        .section { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin-bottom: 30px; }
        .animal-list, .treatments-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .animal-list th, .animal-list td, .treatments-table th, .treatments-table td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        .animal-list th, .treatments-table th { background: #4caf50; color: #fff; }
        .header-content { display: flex; align-items: center; justify-content: center; gap: 20px; padding: 20px; background: #4caf50; color: #fff; text-align: center; }
        .header-logo { height: 60px; }
        .treat-btn { background-color: #4CAF50; color: white; border: none; padding: 8px 12px; border-radius: 4px; cursor: pointer; text-decoration: none; }
        .treat-btn:hover { background-color: #388E3C; }
        .animal-card { background: #f9fbe7; padding: 15px; margin-bottom: 10px; border-radius: 5px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .animal-card h4 { margin: 0 0 10px 0; }
    </style>
</head>
<body>
<header>
    <div class="header-content">
        <img src="assets/logo.png" alt="Zoo Logo" class="header-logo">
        <h1>Veterinarian Dashboard — Albay Park & Wildlife</h1>
    </div>
</header>
<nav class="sidebar-nav">
    <a href="veterinarian_dashboard.php" class="<?php echo ($current_page == 'veterinarian_dashboard.php') ? 'active' : ''; ?>">Dashboard</a>
    <a href="categories_vet.php" class="<?php echo ($current_page == 'categories_vet.php') ? 'active' : ''; ?>">Categories of Animals</a>
    <a href="profile_vet.php" class="<?php echo ($current_page == 'profile_vet.php' || $current_page == 'update_profile_vet.php') ? 'active' : ''; ?>">User Profile</a>
    <a href="logout.php" class="<?php echo ($current_page == 'logout.php') ? 'active' : ''; ?>">Logout</a>
</nav>
<div class="container">
    <h2>Welcome, Dr. <?php echo htmlspecialchars($user['fullname']); ?></h2>
    <p>This is your veterinary dashboard. You can add treatment records for animals and manage their health.</p>
    <hr>
    
    <div class="section">
        <h3>Log Animal Treatment 💉</h3>
        <?php if ($msg) echo "<p style='color: green;'>$msg</p>"; ?>
        <form method="post">
            <label for="animal_id">Select Animal</label>
            <select name="animal_id" id="animal_id" required>
                <?php while ($animal = $animals_result->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($animal['id']); ?>">
                        <?php echo htmlspecialchars($animal['name']) . " (" . htmlspecialchars($animal['category']) . ")"; ?>
                    </option>
                <?php endwhile; ?>
            </select>
            
            <label for="medicine_id">Select Medicine</label>
            <select name="medicine_id" id="medicine_id" required>
                <?php while ($medicine = $medicines_result->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($medicine['id']); ?>">
                        <?php echo htmlspecialchars($medicine['name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
            
            <label for="notes">Notes</label>
            <textarea name="notes" id="notes" placeholder="e.g., Dosage, symptoms, and observations"></textarea>
            
            <button type="submit" name="add_treatment">Add Treatment Log</button>
        </form>
    </div>
    <hr>
    
    <div class="section">
        <h3>Recent Treatment History 🩺</h3>
        <table class="treatments-table">
            <thead>
                <tr>
                    <th>Date/Time</th>
                    <th>Animal Name</th>
                    <th>Category</th>
                    <th>Medicine</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($treatments_result->num_rows > 0): ?>
                    <?php while($row = $treatments_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['treatment_date']); ?></td>
                            <td><?php echo htmlspecialchars($row['animal_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['category']); ?></td>
                            <td><?php echo htmlspecialchars($row['medicine_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['notes']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center;">No treatment logs found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>