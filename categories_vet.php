<?php
// categories.php
$categories = [
    [
        'name' => 'Mammals',
        'img'  => 'assets/mammal.jpg',
        'link' => 'categories_detail_vet.php?category=Mammals'
    ],
    [
        'name' => 'Birds',
        'img'  => 'assets/bird.jpg',
        'link' => 'categories_detail_vet.php?category=Birds'
    ],
    [
        'name' => 'Reptiles',
        'img'  => 'assets/reptiles.jpg',
        'link' => 'categories_detail_vet.php?category=Reptiles'
    ],
    [
        'name' => 'Domesticated Animals',
        'img'  => 'assets/frog.jpg',
        'link' => 'categories_detail_vet.php?category=Domesticated'
    ],
    [
        'name' => 'Aquatic',
        'img'  => 'assets/aquatic.jpg',
        'link' => 'categories_detail_vet.php?category=Aquatic'
    ],
];

// Get the current page's filename
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Animal Categories - Zoo Portal</title>
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
        .categories-container {
            max-width: 900px;
            margin: 40px auto;
            background: #fff;
            padding: 32px 24px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            margin-left: 220px;
        }
        .categories-list {
            display: flex;
            flex-wrap: wrap;
            gap: 35px;
            margin-top: 15px;
            justify-content: center;
        }
        .category-box {
            background: #eaf6f6;
            padding: 14px 18px;
            border-radius: 7px;
            font-size: 1.1em;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            width: 200px;
            text-align: center;
            transition: box-shadow .2s, transform .2s;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            display: block;
        }
        .category-box:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.13);
            transform: translateY(-4px) scale(1.03);
        }
        .category-box img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 8px;
            border: 2px solid #009688;
            background: #fff;
        }
        .category-title {
            font-weight: bold;
            margin-bottom: 6px;
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
            .categories-container {
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
<div class="categories-container">
    <h2>Categories of Animals in the Zoo</h2>
    <div class="categories-list">
        <?php foreach($categories as $category): ?>
            <a class="category-box" href="<?php echo htmlspecialchars($category['link']); ?>">
                <img src="<?php echo htmlspecialchars($category['img']); ?>"
                     alt="<?php echo htmlspecialchars($category['name']); ?> photo"
                     onerror="this.src='assets/default-category.jpg'">
                <div class="category-title"><?php echo htmlspecialchars($category['name']); ?></div>
            </a>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>