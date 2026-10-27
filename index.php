<?php
include 'db.php';

$sql = "SELECT recipes.*, (CASE WHEN favorites.recipe_id IS NOT NULL THEN 1 ELSE 0 END) AS is_favorite 
        FROM recipes 
        LEFT JOIN favorites ON recipes.id = favorites.recipe_id 
        ORDER BY recipes.id DESC";
$result = mysqli_query($conn, $sql);
$recipeCount = $result ? mysqli_num_rows($result) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="app-mode" content="dynamic">
    <title>TastyBites - Discover & Cook Delicious Recipes</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js" defer></script>
</head>
<body>

<!-- ==========================================================================
     Animated Spiral Intro Window
     ========================================================================== -->
<div id="introOverlay" class="intro-overlay">
    <button id="skipIntroBtn" class="intro-skip-btn">Skip Intro &rarr;</button>

    <div class="intro-spiral-container">
        <!-- 8 Dishes Circling and Scattering all over the screen -->
        <div class="spiral-dish dish-1">
            <span class="dish-emoji">🍰</span>
        </div>
        <div class="spiral-dish dish-2">
            <span class="dish-emoji">🍔</span>
        </div>
        <div class="spiral-dish dish-3">
            <span class="dish-emoji">🥗</span>
        </div>
        <div class="spiral-dish dish-4">
            <span class="dish-emoji">🍝</span>
        </div>
        <div class="spiral-dish dish-5">
            <span class="dish-emoji">🥞</span>
        </div>
        <div class="spiral-dish dish-6">
            <span class="dish-emoji">🍹</span>
        </div>
        <div class="spiral-dish dish-7">
            <span class="dish-emoji">🍕</span>
        </div>
        <div class="spiral-dish dish-8">
            <span class="dish-emoji">🍣</span>
        </div>

        <!-- Shockwave Energy Rings -->
        <div class="intro-shockwave ring-1"></div>
        <div class="intro-shockwave ring-2"></div>

        <!-- Center Brand Reveal -->
        <div class="intro-brand-box">
            <div class="intro-logo-badge">🍳</div>
            <h1 class="intro-title">Tasty Bites</h1>
            <p class="intro-tagline">Discover &bull; Cook &bull; Savor</p>
            <div class="intro-sparkles">✨ ✨ ✨</div>
        </div>
    </div>
</div>

<header>
    <a href="index.php" class="logo-container">
        <div class="logo-icon">🍳</div>
        <h1>TastyBites</h1>
    </a>

    <nav>
        <a href="index.php" class="active">
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

<section class="hero">
    <div class="hero-tag">
        <span>🔥</span> Hand-Picked Culinary Delights
    </div>
    
    <h2>Find Your Next <span>Favorite Meal</span></h2>
    <p>Explore chef-crafted recipes, easy-to-cook comfort food, and vibrant drinks tailored for food lovers.</p>

    <div class="search-wrapper">
        <span class="search-icon">🔍</span>
        <input
            type="text"
            id="searchInput"
            placeholder="Search recipes, ingredients, or meals..."
            autocomplete="off"
        >
    </div>

    <!-- Hidden select for form compatibility -->
    <select id="categoryFilter" style="display:none;">
        <option value="all">All</option>
        <option value="Breakfast">Breakfast</option>
        <option value="Lunch">Lunch</option>
        <option value="Dinner">Dinner</option>
        <option value="Dessert">Dessert</option>
        <option value="Drinks">Drinks</option>
    </select>

    <!-- Animated Category Filter Pills -->
    <div class="category-pills">
        <button type="button" class="category-pill active" data-cat="all">✨ All Recipes</button>
        <button type="button" class="category-pill" data-cat="Breakfast">🥞 Breakfast</button>
        <button type="button" class="category-pill" data-cat="Lunch">🥗 Lunch</button>
        <button type="button" class="category-pill" data-cat="Dinner">🍔 Dinner</button>
        <button type="button" class="category-pill" data-cat="Dessert">🍰 Dessert</button>
        <button type="button" class="category-pill" data-cat="Drinks">🍹 Drinks</button>
    </div>
</section>

<section class="container">
    <div class="section-header">
        <div>
            <h2>Explore Recipes</h2>
        </div>
        <div class="recipe-count-badge" id="recipeCountBadge">
            <?php echo (int) $recipeCount; ?> Recipes
        </div>
    </div>

    <div class="recipe-grid">

        <?php if ($result && mysqli_num_rows($result) > 0) { ?>
            <?php while ($row = mysqli_fetch_assoc($result)) { 
                $isFav = (int) $row['is_favorite'] === 1;
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
                                <input type="hidden" name="action" value="<?php echo $isFav ? 'remove' : 'add'; ?>">
                                <input type="hidden" name="redirect" value="index.php">
                                <button type="submit" 
                                        class="favorite-icon-btn <?php echo $isFav ? 'is-active' : ''; ?>"
                                        title="<?php echo $isFav ? 'Remove from favorites' : 'Add to favorites'; ?>">
                                    <?php echo $isFav ? '❤️' : '🤍'; ?>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            <?php } ?>
        <?php } ?>

    </div>

    <div id="noResults" style="display:none;">
        <span>🔍</span>
        <h3>No matching recipes found</h3>
        <p>Try searching for different ingredients or select a different category pill above.</p>
    </div>
</section>

<footer>
    <p>&copy; <?php echo date('Y'); ?> TastyBites &bull; Crafted with passion for food lovers everywhere.</p>
</footer>

</body>
</html>
