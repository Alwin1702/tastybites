<?php
include 'db.php';

$sql = "SELECT recipes.*
        FROM recipes
        INNER JOIN favorites
        ON recipes.id = favorites.recipe_id
        ORDER BY favorites.id DESC";

$result = mysqli_query($conn, $sql);
$favCount = $result ? mysqli_num_rows($result) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="app-mode" content="dynamic">
    <title>My Favorite Recipes - TastyBites</title>
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
        <a href="favorites.php" class="active">
            <span>❤️</span> Favorites
        </a>
        <a href="add_recipe.php">
            <span>✨</span> Add Recipe
        </a>
    </nav>
</header>

<div class="container">
    <div class="section-header" style="margin-top: 25px;">
        <div>
            <h2>My Saved Favorites ❤️</h2>
            <p style="color:var(--text-muted); font-size:14px; margin-top:4px;">
                Your curated personal cookbook of saved recipes.
            </p>
        </div>
        <div class="recipe-count-badge">
            <?php echo (int) $favCount; ?> <?php echo $favCount === 1 ? 'Favorite' : 'Favorites'; ?>
        </div>
    </div>

    <div class="recipe-grid" style="<?php echo $favCount === 0 ? 'display:none;' : ''; ?>">
        <?php if ($result && $favCount > 0) { 
            while ($row = mysqli_fetch_assoc($result)) { 
                $cat = htmlspecialchars($row['category']);
        ?>
            <div class="recipe-card"
                 data-id="<?php echo (int) $row['id']; ?>"
                 data-name="<?php echo htmlspecialchars(strtolower($row['name'])); ?>"
                 data-category="<?php echo $cat; ?>"
                 data-ingredients="<?php echo htmlspecialchars(strtolower($row['ingredients'] ?? '')); ?>">

                <div class="card-media">
                    <span class="card-badge" data-cat="<?php echo $cat; ?>">
                        <?php echo $cat; ?>
                    </span>

                    <?php if (!empty($row['image'])) { ?>
                        <img
                            src="<?php echo htmlspecialchars($row['image']); ?>"
                            alt="<?php echo htmlspecialchars($row['name']); ?>"
                            loading="lazy"
                        >
                    <?php } else { ?>
                        <div style="height:100%; display:flex; align-items:center; justify-content:center; background:#fed7aa; font-size:48px;">
                            🍲
                        </div>
                    <?php } ?>
                </div>

                <div class="card-content">
                    <h3>
                        <?php echo htmlspecialchars($row['name']); ?>
                    </h3>

                    <div class="recipe-meta">
                        <span class="meta-item">
                            ⏱️ <?php echo (int) $row['cooking_time']; ?> mins
                        </span>
                        <span class="meta-item">
                            🍽️ <?php echo $cat; ?>
                        </span>
                    </div>

                    <div class="card-actions">
                        <a class="view-btn" href="recipe.php?id=<?php echo (int) $row['id']; ?>">
                            View Recipe &rarr;
                        </a>

                        <form action="favorite_action.php" method="POST" class="favorite-form">
                            <input type="hidden" name="recipe_id" value="<?php echo (int) $row['id']; ?>">
                            <input type="hidden" name="action" value="remove">
                            <input type="hidden" name="redirect" value="favorites.php">
                            <button type="submit" 
                                    class="favorite-icon-btn remove-mode"
                                    title="Remove from favorites">
                                ❌
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php } } ?>
    </div>

    <div class="empty-favorites-box" style="<?php echo $favCount > 0 ? 'display:none;' : ''; ?>">
        <span class="empty-heart-icon">💔</span>
        <h3 style="font-family:'Outfit', sans-serif; font-size:22px; margin-bottom:8px; color:var(--secondary);">
            No favorite recipes yet!
        </h3>
        <p style="color:var(--text-muted); font-size:14px; margin-bottom:24px;">
            You haven't saved any recipes to your favorites. Explore our delicious collection and click the heart icon to save recipes you love!
        </p>
        <a href="index.php" class="view-btn" style="display:inline-flex; width:auto; padding:12px 28px;">
            Browse Recipes &rarr;
        </a>
    </div>
</div>

<footer>
    <p>&copy; <?php echo date('Y'); ?> TastyBites &bull; Crafted with passion for food lovers everywhere.</p>
</footer>

</body>
</html>
