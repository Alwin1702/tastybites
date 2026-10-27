<?php
include 'db.php';

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $image = trim($_POST['image'] ?? '');
    $ingredients = trim($_POST['ingredients'] ?? '');
    $instructions = trim($_POST['instructions'] ?? '');
    $cooking_time = (int) ($_POST['cooking_time'] ?? 0);

    if ($name && $category && $ingredients && $instructions && $cooking_time > 0) {

        $sql = "INSERT INTO recipes (name, category, image, ingredients, instructions, cooking_time)
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param(
                $stmt,
                "sssssi",
                $name,
                $category,
                $image,
                $ingredients,
                $instructions,
                $cooking_time
            );

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                header("Location: index.php");
                exit;
            } else {
                $message = "Error saving recipe: " . mysqli_stmt_error($stmt);
                $messageType = "error";
            }
            mysqli_stmt_close($stmt);
        } else {
            $message = "Database error: " . mysqli_error($conn);
            $messageType = "error";
        }
    } else {
        $message = "Please fill in all required fields with valid details.";
        $messageType = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="app-mode" content="dynamic">
    <title>Add Recipe - TastyBites</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js" defer></script>
</head>
<body>

<header>
    <a href="index.php" class="logo-container">
        <div class="logo-icon">🍳</div>
        <h1>TastyBites</h1>
    </a>

    <nav>
        <a href="index.php">
            <span>🏠</span> Home
        </a>
        <a href="favorites.php">
            <span>❤️</span> Favorites
        </a>
        <a href="add_recipe.php" class="active">
            <span>✨</span> Add Recipe
        </a>
    </nav>
</header>

<div class="form-card">
    <div class="form-header">
        <h2>Share Your Creation 👨‍🍳</h2>
        <p>Add a new dish to the TastyBites culinary collection.</p>
    </div>

    <?php if (!empty($message)) { ?>
        <div style="background:#FEE2E2; border:1px solid #FCA5A5; color:#DC2626; padding:12px 18px; border-radius:10px; margin-bottom:20px; font-weight:600; font-size:14px;">
            ⚠️ <?php echo htmlspecialchars($message); ?>
        </div>
    <?php } ?>

    <form method="POST">
        <div class="form-group">
            <label for="name">Recipe Name *</label>
            <input type="text" id="name" name="name" class="form-control" placeholder="e.g. Creamy Tuscan Garlic Chicken" value="<?php echo htmlspecialchars($name ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label for="category">Category *</label>
            <select id="category" name="category" class="form-control" required>
                <option value="">Select a Category</option>
                <option value="Breakfast" <?php echo (isset($category) && $category === 'Breakfast') ? 'selected' : ''; ?>>🥞 Breakfast</option>
                <option value="Lunch" <?php echo (isset($category) && $category === 'Lunch') ? 'selected' : ''; ?>>🥗 Lunch</option>
                <option value="Dinner" <?php echo (isset($category) && $category === 'Dinner') ? 'selected' : ''; ?>>🍔 Dinner</option>
                <option value="Dessert" <?php echo (isset($category) && $category === 'Dessert') ? 'selected' : ''; ?>>🍰 Dessert</option>
                <option value="Drinks" <?php echo (isset($category) && $category === 'Drinks') ? 'selected' : ''; ?>>🍹 Drinks</option>
            </select>
        </div>

        <div class="form-group">
            <label for="image">Cover Image URL (Optional)</label>
            <input type="url" id="image" name="image" class="form-control" placeholder="https://images.unsplash.com/photo-..." value="<?php echo htmlspecialchars($image ?? ''); ?>" autocomplete="off">
            <div class="image-preview-box" id="imagePreview">
                Live image preview will appear here
            </div>
        </div>

        <div class="form-group">
            <label for="cooking_time">Cooking Time (minutes) *</label>
            <input type="number" id="cooking_time" name="cooking_time" class="form-control" min="1" max="1440" placeholder="e.g. 25" value="<?php echo !empty($cooking_time) ? (int)$cooking_time : ''; ?>" required>
        </div>

        <div class="form-group">
            <label for="ingredients">Ingredients *</label>
            <textarea id="ingredients" name="ingredients" class="form-control" placeholder="List ingredients separated by commas or line breaks (e.g. 200g Chicken breast, 2 cloves Garlic, 1 cup Heavy cream)" required><?php echo htmlspecialchars($ingredients ?? ''); ?></textarea>
        </div>

        <div class="form-group">
            <label for="instructions">Cooking Instructions *</label>
            <textarea id="instructions" name="instructions" class="form-control" style="min-height:140px;" placeholder="Step 1: Heat olive oil in a skillet...&#10;Step 2: Add garlic and sauté until fragrant...&#10;Step 3: Simmer sauce and serve warm." required><?php echo htmlspecialchars($instructions ?? ''); ?></textarea>
        </div>

        <button type="submit" class="submit-btn">
            Publish Recipe 🚀
        </button>
    </form>
</div>

<footer>
    <p>&copy; <?php echo date('Y'); ?> TastyBites &bull; Crafted with passion for food lovers everywhere.</p>
</footer>

</body>
</html>
