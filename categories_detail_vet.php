<?php
// category_detail.php
// Displays details for a selected animal category.

$categories = [
    'Mammals'    => ['img' => 'assets/mammal.jpg', 'desc' => 'Warm-blooded vertebrates with hair or fur. Examples include lions, elephants, and monkeys.'],
    'Birds'      => ['img' => 'assets/bird.jpg', 'desc' => 'Feathered vertebrates that lay eggs. Examples include eagles, parrots, and ducks.'],
    'Reptiles'   => ['img' => 'assets/reptile.jpg', 'desc' => 'Cold-blooded vertebrates with scales. Examples include snakes, lizards, and turtles.'],
    'Domesticated Animals' => ['img' => 'assets/frog.jpg', 'desc' => 'Animals that have been tamed and kept by humans. Examples include dogs, cats, and farm animals.'],
    'Aquatic'    => ['img' => 'assets/aquatic.jpg', 'desc' => 'Animals that live in water environments. Examples include fish, crabs, and dolphins.'],
];

// Get category from query string

$category = isset($_GET['category']) ? $_GET['category'] : '';

if (!array_key_exists($category, $categories)) {
    // Invalid or missing category
    header('Location: categories_vet.php');
    exit;
}

$cat_info = $categories[$category];

// Get the current page's filename
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($category); ?> Details - Zoo Portal</title>
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
        .detail-container {
            max-width: 500px;
            margin: 50px auto;
            background: #fff;
            padding: 32px 24px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            text-align: center;
            margin-left: 220px;
        }
        .category-img {
            width: 180px;
            height: 180px;
            object-fit: cover;
            border-radius: 12px;
            border: 2px solid #009688;
            margin-bottom: 20px;
            background: #fff;
        }
        .category-title {
            font-size: 2em;
            font-weight: bold;
            margin-bottom: 12px;
        }
        .category-desc {
            color: #444;
            font-size: 1.14em;
            margin-bottom: 22px;
        }
        .back-link {
            display: inline-block;
            margin-top: 18px;
            color: #009688;
            text-decoration: none;
            font-weight: bold;
            transition: color .15s;
        }
        .back-link:hover {
            color: #00675b;
            text-decoration: underline;
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
            .detail-container {
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
    <a href="veterinarian_dashboard.php" class="<?php echo ($current_page == 'veterinarian_dashboard.php') ? 'active' : ''; ?>">Dashboard</a>
    <a href="categories_vet.php" class="<?php echo ($current_page == 'categories_vet.php') ? 'active' : ''; ?>">Categories of Animals</a>
    <a href="profile_vet.php" class="<?php echo ($current_page == 'profile_vet.php') ? 'active' : ''; ?>">User Profile</a>
    <a href="logout.php" class="<?php echo ($current_page == 'logout.php') ? 'active' : ''; ?>">Logout</a>
</nav>
<div class="detail-container">
    <img src="<?php echo htmlspecialchars($cat_info['img']); ?>"
         alt="<?php echo htmlspecialchars($category); ?> photo"
         class="category-img"
         onerror="this.src='assets/default-category.jpg'">
    <div class="category-title"><?php echo htmlspecialchars($category); ?></div>
    <div class="category-desc"><?php echo htmlspecialchars($cat_info['desc']); ?></div>
    <a href="categories_vet.php" class="back-link">&larr; Back to Categories</a>
</div>
</body>
</html>