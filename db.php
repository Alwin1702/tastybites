<?php
$servername = getenv('DB_HOST') ?: "localhost";
$username   = getenv('DB_USER') ?: "root";
$password   = getenv('DB_PASS') !== false ? getenv('DB_PASS') : "";
$dbname     = getenv('DB_NAME') ?: "tastybites";
$port       = getenv('DB_PORT') ? (int)getenv('DB_PORT') : 3306;

// Disable mysqli default exception throwing in PHP 8.1+ for custom graceful handling
mysqli_report(MYSQLI_REPORT_OFF);

$conn = @mysqli_connect(
    $servername,
    $username,
    $password,
    $dbname,
    $port
);

if (!$conn) {
    // If database tastybites doesn't exist yet, attempt to connect to MySQL server and create it
    $initConn = @mysqli_connect($servername, $username, $password, "", $port);
    if ($initConn) {
        @mysqli_query($initConn, "CREATE DATABASE IF NOT EXISTS `$dbname`");
        @mysqli_close($initConn);
        $conn = @mysqli_connect($servername, $username, $password, $dbname, $port);
    }
}

if (!$conn) {
    $errorMsg = mysqli_connect_error() ?: "Unable to establish MySQL connection.";
    http_response_code(500);
    die("<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Database Notice - TastyBites</title>
    <link rel='stylesheet' href='style.css'>
</head>
<body style='display:flex; align-items:center; justify-content:center; min-height:100vh; padding:20px; background:#FBFBFE;'>
    <div style='background:white; padding:40px; border-radius:20px; box-shadow:0 10px 30px rgba(0,0,0,0.08); max-width:540px; text-align:center; border:1px solid #E2E8F0;'>
        <div style='font-size:52px; margin-bottom:16px;'>🔌</div>
        <h2 style='font-family:sans-serif; color:#023047; margin-bottom:12px;'>Database Connection Error</h2>
        <p style='color:#64748B; font-size:15px; line-height:1.6; margin-bottom:24px;'>
            Could not connect to MySQL (<code>" . htmlspecialchars($errorMsg) . "</code>).<br>
            Please make sure <strong>MySQL</strong> is started in your <strong>XAMPP Control Panel</strong>.
        </p>
        <div style='display:flex; gap:12px; justify-content:center; flex-wrap:wrap;'>
            <a href='index.html' style='display:inline-block; background:#FF5A36; color:white; padding:12px 24px; border-radius:12px; text-decoration:none; font-weight:700;'>View Offline Demo &rarr;</a>
            <button onclick='window.location.reload()' style='background:#F1F5F9; border:none; color:#1E293B; padding:12px 24px; border-radius:12px; font-weight:700; cursor:pointer;'>Retry Connection</button>
        </div>
    </div>
</body>
</html>");
}

mysqli_set_charset($conn, "utf8mb4");

// Auto-initialize tables if they don't exist yet
@mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `recipes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(150) NOT NULL,
    `category` VARCHAR(50) NOT NULL,
    `image` VARCHAR(500),
    `ingredients` TEXT NOT NULL,
    `instructions` TEXT NOT NULL,
    `cooking_time` INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

@mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `favorites` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `recipe_id` INT NOT NULL,
    FOREIGN KEY (`recipe_id`) REFERENCES `recipes`(`id`) ON DELETE CASCADE,
    UNIQUE (`recipe_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Check if recipes table is empty, seed standard recipes if empty
$cntCheck = @mysqli_query($conn, "SELECT COUNT(*) AS total FROM `recipes`");
if ($cntCheck) {
    $rowCnt = @mysqli_fetch_assoc($cntCheck);
    if ($rowCnt && (int)$rowCnt['total'] === 0) {
        @mysqli_query($conn, "INSERT INTO `recipes` (`name`, `category`, `image`, `ingredients`, `instructions`, `cooking_time`) VALUES
        ('Chocolate Cake', 'Dessert', 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=600', 'Flour, Sugar, Cocoa Powder, Eggs, Milk, Butter', 'Mix all ingredients. Pour into a cake tin. Bake at 180°C for 30 minutes.', 40),
        ('Vegetable Salad', 'Breakfast', 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=600', 'Lettuce, Tomato, Cucumber, Carrot, Olive Oil, Salt', 'Wash and chop the vegetables. Mix them in a bowl. Add olive oil and salt.', 10),
        ('Pasta', 'Lunch', 'https://images.unsplash.com/photo-1473093295043-cdd812d0e601?w=600', 'Pasta, Tomato, Onion, Garlic, Oil, Salt', 'Boil pasta. Sauté onion and garlic. Add tomato sauce and mix with pasta.', 25),
        ('Pancakes', 'Breakfast', 'https://images.unsplash.com/photo-1528207776546-365bb710ee93?w=600', 'Flour, Milk, Egg, Sugar, Baking Powder', 'Mix all ingredients. Pour batter into a heated pan. Cook both sides until golden.', 15),
        ('Chicken Burger', 'Dinner', 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=600', 'Burger Bun, Chicken Patty, Lettuce, Tomato, Cheese, Sauce', 'Cook the chicken patty thoroughly. Place it inside the bun with vegetables and sauce.', 30),
        ('Fruit Smoothie', 'Drinks', 'https://images.unsplash.com/photo-1502741224143-90386d7f8c82?w=600', 'Banana, Strawberry, Milk, Honey', 'Add all ingredients to a blender. Blend until smooth and serve chilled.', 5)");
    }
}
?>
