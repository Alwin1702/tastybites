<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tastybites";

$conn = mysqli_connect($servername, $username, $password);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$sql = "CREATE DATABASE IF NOT EXISTS $dbname";

if (mysqli_query($conn, $sql)) {
    echo "Database created successfully or already exists.<br>";
} else {
    die("Error creating database: " . mysqli_error($conn));
}

mysqli_close($conn);

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$sql = "CREATE TABLE IF NOT EXISTS recipes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    category VARCHAR(50) NOT NULL,
    image VARCHAR(500),
    ingredients TEXT NOT NULL,
    instructions TEXT NOT NULL,
    cooking_time INT NOT NULL
)";

if (mysqli_query($conn, $sql)) {
    echo "Recipes table created successfully.<br>";
} else {
    die("Error creating recipes table: " . mysqli_error($conn));
}

$sql = "CREATE TABLE IF NOT EXISTS favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    recipe_id INT NOT NULL,
    FOREIGN KEY (recipe_id) REFERENCES recipes(id)
    ON DELETE CASCADE,
    UNIQUE (recipe_id)
)";

if (mysqli_query($conn, $sql)) {
    echo "Favorites table created successfully.<br>";
} else {
    die("Error creating favorites table: " . mysqli_error($conn));
}

$sql = "INSERT INTO recipes
(name, category, image, ingredients, instructions, cooking_time)
VALUES
(
    'Chocolate Cake',
    'Dessert',
    'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=600',
    'Flour, Sugar, Cocoa Powder, Eggs, Milk, Butter',
    'Mix all ingredients. Pour into a cake tin. Bake at 180°C for 30 minutes.',
    40
),
(
    'Vegetable Salad',
    'Breakfast',
    'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=600',
    'Lettuce, Tomato, Cucumber, Carrot, Olive Oil, Salt',
    'Wash and chop the vegetables. Mix them in a bowl. Add olive oil and salt.',
    10
),
(
    'Pasta',
    'Lunch',
    'https://images.unsplash.com/photo-1473093295043-cdd812d0e601?w=600',
    'Pasta, Tomato, Onion, Garlic, Oil, Salt',
    'Boil pasta. Sauté onion and garlic. Add tomato sauce and mix with pasta.',
    25
),
(
    'Pancakes',
    'Breakfast',
    'https://images.unsplash.com/photo-1528207776546-365bb710ee93?w=600',
    'Flour, Milk, Egg, Sugar, Baking Powder',
    'Mix all ingredients. Pour batter into a heated pan. Cook both sides until golden.',
    15
),
(
    'Chicken Burger',
    'Dinner',
    'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=600',
    'Burger Bun, Chicken Patty, Lettuce, Tomato, Cheese, Sauce',
    'Cook the chicken patty thoroughly. Place it inside the bun with vegetables and sauce.',
    30
),
(
    'Fruit Smoothie',
    'Drinks',
    'https://images.unsplash.com/photo-1502741224143-90386d7f8c82?w=600',
    'Banana, Strawberry, Milk, Honey',
    'Add all ingredients to a blender. Blend until smooth and serve chilled.',
    5
)";

if (mysqli_query($conn, $sql)) {
    echo "Recipe data inserted successfully.<br>";
} else {
    echo "Error inserting recipes: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
