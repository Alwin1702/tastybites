<?php
include 'db.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$recipe = null;
if ($id > 0) {
    $stmt = mysqli_prepare(
        $conn,
        "SELECT recipes.*, (CASE WHEN favorites.recipe_id IS NOT NULL THEN 1 ELSE 0 END) AS is_favorite 
         FROM recipes 
         LEFT JOIN favorites ON recipes.id = favorites.recipe_id 
         WHERE recipes.id = ?"
    );

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($result) {
            $recipe = mysqli_fetch_assoc($result);
        }
        mysqli_stmt_close($stmt);
    }
}

if (!$recipe) {
    echo "<div style='text-align:center; padding:80px; font-family:sans-serif;'>
            <h2>Recipe not found</h2>
            <p><a href='index.php' style='color:#FF5A36;'>Return to Home</a></p>
          </div>";
    exit;
}

$isFav = (int) $recipe['is_favorite'] === 1;

// Parse ingredients (comma or newline separated)
$ingredients = array_filter(array_map('trim', explode(',', $recipe['ingredients'])));
if (count($ingredients) <= 1) {
    $alt = array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $recipe['ingredients'])));
    if (!empty($alt)) $ingredients = $alt;
}

// Parse instructions into distinct steps
$rawSteps = preg_split('/(?<=[.!?])\s+|\r\n|\r|\n/', $recipe['instructions']);
$steps = array_values(array_filter(array_map('trim', $rawSteps)));
if (empty($steps)) {
    $steps = [$recipe['instructions']];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="app-mode" content="dynamic">
    <title><?php echo htmlspecialchars($recipe['name']); ?> - TastyBites</title>
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
        <a href="add_recipe.php">
            <span>✨</span> Add Recipe
        </a>
    </nav>
</header>

<div class="recipe-details-wrapper">
    <div class="recipe-hero-card">
        <div class="recipe-hero-image">
            <?php if (!empty($recipe['image'])) { ?>
                <img
                    src="<?php echo htmlspecialchars($recipe['image']); ?>"
                    alt="<?php echo htmlspecialchars($recipe['name']); ?>"
                >
            <?php } ?>

            <div class="recipe-hero-overlay">
                <h2><?php echo htmlspecialchars($recipe['name']); ?></h2>
                <div class="recipe-quick-specs">
                    <span class="spec-pill">
                        🏷️ <?php echo htmlspecialchars($recipe['category']); ?>
                    </span>
                    <span class="spec-pill">
                        ⏱️ <?php echo (int) $recipe['cooking_time']; ?> minutes
                    </span>
                </div>
            </div>
        </div>

        <div class="recipe-body-content">
            <div class="recipe-grid-layout">
                <!-- Ingredients with interactive checklist -->
                <div>
                    <h3 class="recipe-section-title">
                        <span>🥗</span> Ingredients
                    </h3>
                    <p style="font-size:12px; color:var(--text-light); margin-bottom:12px;">
                        Tip: Click an item to check it off while cooking!
                    </p>

                    <ul class="ingredient-list">
                        <?php foreach ($ingredients as $ingredient) { ?>
                            <li class="ingredient-item">
                                <input type="checkbox" class="ingredient-checkbox">
                                <span><?php echo htmlspecialchars($ingredient); ?></span>
                            </li>
                        <?php } ?>
                    </ul>
                </div>

                <!-- Instructions with step cards -->
                <div>
                    <h3 class="recipe-section-title">
                        <span>👩‍🍳</span> Cooking Steps
                    </h3>

                    <div class="instruction-steps">
                        <?php foreach ($steps as $index => $step) { ?>
                            <div class="step-card">
                                <div class="step-number"><?php echo $index + 1; ?></div>
                                <div class="step-text"><?php echo htmlspecialchars($step); ?></div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <div class="recipe-detail-actions">
                <a href="index.php" class="back-link">
                    &larr; Back to all recipes
                </a>

                <form action="favorite_action.php" method="POST" class="favorite-form" style="margin:0;">
                    <input type="hidden" name="recipe_id" value="<?php echo (int) $recipe['id']; ?>">
                    <input type="hidden" name="action" value="<?php echo $isFav ? 'remove' : 'add'; ?>">
                    <input type="hidden" name="redirect" value="recipe.php?id=<?php echo (int) $recipe['id']; ?>">
                    <button type="submit" 
                            class="view-btn recipe-detail-fav-btn <?php echo $isFav ? 'is-active' : ''; ?>"
                            style="background: <?php echo $isFav ? '#64748B' : 'linear-gradient(135deg, #FF5A36, #E44825)'; ?>;">
                        <?php echo $isFav ? '💔 Remove from Favorites' : '❤️ Save to Favorites'; ?>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<footer>
    <p>&copy; <?php echo date('Y'); ?> TastyBites &bull; Crafted with passion for food lovers everywhere.</p>
</footer>

</body>
</html>
