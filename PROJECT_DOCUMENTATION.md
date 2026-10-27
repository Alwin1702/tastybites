# TastyBites – Complete Project Explanation & Technical Documentation

---

## 📌 Table of Contents
1. [Project Overview & Key Highlights](#1-project-overview--key-highlights)
2. [System Architecture & Technology Stack](#2-system-architecture--technology-stack)
3. [Database Architecture & Data Models](#3-database-architecture--data-models)
4. [File-by-File Code Breakdown](#4-file-by-file-code-breakdown)
   - [4.1 Database Connection: `db.php`](#41-database-connection-dbphp)
   - [4.2 Database Initialization: `create_database.php` & `schema.sql`](#42-database-initialization-create_databasephp--schemasql)
   - [4.3 Homepage & Discovery: `index.php`](#43-homepage--discovery-indexphp)
   - [4.4 Detailed Recipe View: `recipe.php`](#44-detailed-recipe-view-recipephp)
   - [4.5 Saved Favorites: `favorites.php`](#45-saved-favorites-favoritesphp)
   - [4.6 Favorite Toggle Action: `favorite_action.php`](#46-favorite-toggle-action-favorite_actionphp)
   - [4.7 Recipe Submission: `add_recipe.php`](#47-recipe-submission-add_recipephp)
   - [4.8 Client Logic & Interactivity: `script.js`](#48-client-logic--interactivity-scriptjs)
   - [4.9 Design System & Motion Graphics: `style.css`](#49-design-system--motion-graphics-stylecss)
5. [Cinematic Intro Animation Physics](#5-cinematic-intro-animation-physics)
6. [Security & Web Best Practices](#6-security--web-best-practices)
7. [Deployment & Local Execution Guide](#7-deployment--local-execution-guide)
8. [Frequently Asked Viva / Project Defense Questions](#8-frequently-asked-viva--project-defense-questions)

---

## 1. Project Overview & Key Highlights

**TastyBites** is a full-stack culinary web application engineered with modern web standards (PHP 8+, MySQL/MariaDB, Vanilla CSS3, and ES6+ JavaScript). It bridges dynamic server-side database rendering with client-side micro-interactions and animations.

### 🌟 Core Capabilities
- **Cinematic Scatter & Circle Intro Experience**: Fullscreen intro featuring 8 independent dish plates that orbit, scatter across all screen corners, and implode into a golden brand reveal.
- **Dynamic Instant Search & Filter**: Instant client-side search by title or ingredients combined with interactive category pills (Breakfast, Lunch, Dinner, Dessert, Drinks).
- **Interactive Recipe Preparation**: Interactive recipe detail page with a click-to-complete ingredient checklist and timeline-numbered cooking cards.
- **Relational Favorites Engine**: Ability to bookmark and remove favorites with prepared statement queries and dynamic page redirects.
- **Recipe Submission with Live Preview**: Form featuring client-side URL live image rendering and server-side prepared validation.

---

## 2. System Architecture & Technology Stack

The application employs the classic **3-Tier Web Architecture**:

```
+-------------------------------------------------------------+
|                    PRESENTATION TIER                        |
|   HTML5 Semantic Structure + Vanilla CSS3 Design System     |
|   ES6+ JavaScript (DOM Manipulation, Live Filtering, Intro) |
+-------------------------------------------------------------+
                              ↕ (HTTP/HTTPS Requests)
+-------------------------------------------------------------+
|                     APPLICATION TIER                        |
|   PHP 8.2+ Backend Engine                                   |
|   Prepared Statements (mysqli), Input Sanitization, Routing |
+-------------------------------------------------------------+
                              ↕ (SQL Queries over TCP/Socket)
+-------------------------------------------------------------+
|                       DATA TIER                             |
|   MySQL / MariaDB Database (`tastybites`)                   |
|   Relational Tables: `recipes` <---(1:N)---> `favorites`   |
+-------------------------------------------------------------+
```

### 🛠️ Technology Stack Breakdown
| Component | Technology | Rationale |
|---|---|---|
| **Backend** | PHP 8.2+ (CLI & Apache Module) | Server-side execution, native database integration via MySQLi |
| **Database** | MySQL / MariaDB (XAMPP) | Relational storage with Foreign Key constraints & cascade rules |
| **Styling** | Vanilla CSS3 | Custom design system, CSS Grid/Flexbox, 3D CSS transforms & keyframes |
| **Client Script** | Vanilla JavaScript (ES6+) | Lightweight, zero-dependency DOM manipulation and timers |
| **Typography** | Google Fonts (`Outfit` & `Plus Jakarta Sans`) | Modern, legible, and premium typographic hierarchy |

---

## 3. Database Architecture & Data Models

The database name is `tastybites`. It comprises two normalized relational tables:

```
  +-------------------------+             +-------------------------+
  |         recipes         |             |        favorites        |
  +-------------------------+             +-------------------------+
  | PK  id (INT, AI)        |<---+        | PK  id (INT, AI)        |
  |     name (VARCHAR 150)  |    |        | FK  recipe_id (INT)     |---+ UNIQUE
  |     category (VARCHAR 50|    +--------+-------------------------+
  |     image (VARCHAR 500) |
  |     ingredients (TEXT)  |
  |     instructions (TEXT) |
  |     cooking_time (INT)  |
  +-------------------------+
```

### Table Definitions

#### 1. `recipes` Table
Stores all dish information published to the platform.
```sql
CREATE TABLE IF NOT EXISTS `recipes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(150) NOT NULL,
    `category` VARCHAR(50) NOT NULL,
    `image` VARCHAR(500),
    `ingredients` TEXT NOT NULL,
    `instructions` TEXT NOT NULL,
    `cooking_time` INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```
- **`id`**: Unique integer auto-incrementing identifier.
- **`name`**: Title of the recipe (e.g., 'Chocolate Cake').
- **`category`**: One of 'Breakfast', 'Lunch', 'Dinner', 'Dessert', 'Drinks'.
- **`image`**: URL pointing to a high-resolution food image.
- **`ingredients`**: Comma or newline-separated raw ingredient list.
- **`instructions`**: Step-by-step cooking procedures.
- **`cooking_time`**: Estimated preparation and cooking time in minutes.

#### 2. `favorites` Table
Maintains bookmark references between recipes and users.
```sql
CREATE TABLE IF NOT EXISTS `favorites` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `recipe_id` INT NOT NULL,
    FOREIGN KEY (`recipe_id`) REFERENCES `recipes`(`id`) ON DELETE CASCADE,
    UNIQUE (`recipe_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```
- **`UNIQUE (recipe_id)`**: Prevents the same recipe from being inserted twice into the favorites list.
- **`ON DELETE CASCADE`**: If a recipe is deleted from the `recipes` table, its corresponding record in `favorites` is automatically removed by the database engine.

---

## 4. File-by-File Code Breakdown

---

### 4.1 Database Connection: `db.php`
- **Path**: [`db.php`](file:///c:/Users/OWNER/Documents/Web/db.php)
- **Role**: Establishes a singleton MySQLi connection shared across all backend handlers.

#### Code Logic:
```php
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tastybites";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");
?>
```
- **`mysqli_connect()`**: Connects to the local MySQL server.
- **Error Handling**: Invokes `die()` with the connection error if MySQL is unreachable.
- **`mysqli_set_charset($conn, "utf8mb4")`**: Ensures full UTF-8 encoding support, allowing multi-lingual characters, special culinary punctuation, and emojis.

---

### 4.2 Database Initialization: `create_database.php` & `schema.sql`
- **Paths**: [`create_database.php`](file:///c:/Users/OWNER/Documents/Web/create_database.php) | [`schema.sql`](file:///c:/Users/OWNER/Documents/Web/schema.sql)
- **Role**: Automatically provision database `tastybites`, create tables, and populate seed recipes for first-time setup.

#### Key Mechanics:
1. Connects to MySQL without specifying a database to run `CREATE DATABASE IF NOT EXISTS tastybites`.
2. Connects directly to `tastybites` to issue DDL (`CREATE TABLE IF NOT EXISTS`).
3. Executes seed DML (`INSERT INTO recipes ...`) providing 6 starter dishes covering all categories (Breakfast, Lunch, Dinner, Dessert, Drinks).

---

### 4.3 Homepage & Discovery: `index.php`
- **Path**: [`index.php`](file:///c:/Users/OWNER/Documents/Web/index.php)
- **Role**: The main user-facing portal featuring the animated intro, hero banner, live search, category pills, and recipe card grid.

#### Backend Logic:
```php
$sql = "SELECT recipes.*, (CASE WHEN favorites.recipe_id IS NOT NULL THEN 1 ELSE 0 END) AS is_favorite 
        FROM recipes 
        LEFT JOIN favorites ON recipes.id = favorites.recipe_id 
        ORDER BY recipes.id DESC";
$result = mysqli_query($conn, $sql);
$recipeCount = $result ? mysqli_num_rows($result) : 0;
```
- **`LEFT JOIN favorites`**: Combines recipe data with the favorites table in a single optimized query.
- **`is_favorite` Flag**: If `favorites.recipe_id` is found, returns `1`, otherwise `0`. This controls whether the card renders an active red heart (`❤️`) or an inactive heart (`🤍`).

#### Frontend Structure:
1. **Intro Window (`#introOverlay`)**: Houses 8 dishes (`.dish-1` to `.dish-8`), dual shockwave rings, and the `.intro-brand-box`.
2. **Hero Section**: Includes the search bar (`#searchInput`) and category pills (`.category-pill`) with click event listeners.
3. **Recipe Card**:
   - `data-name`: Contains lowercased recipe name for real-time text matching.
   - `data-category`: Contains category name for pill matching.
   - `.card-media`: Image container with hover sweep shine effect.
   - Favorite Form: Direct POST form passing `recipe_id` and toggle action (`add`/`remove`).

---

### 4.4 Detailed Recipe View: `recipe.php`
- **Path**: [`recipe.php`](file:///c:/Users/OWNER/Documents/Web/recipe.php)
- **Role**: Displays complete culinary instructions, ingredients, cooking time, and image banner for a selected dish.

#### Backend Logic:
```php
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$stmt = mysqli_prepare(
    $conn,
    "SELECT recipes.*, (CASE WHEN favorites.recipe_id IS NOT NULL THEN 1 ELSE 0 END) AS is_favorite 
     FROM recipes 
     LEFT JOIN favorites ON recipes.id = favorites.recipe_id 
     WHERE recipes.id = ?"
);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
```
- **Prepared Statement (`mysqli_prepare`)**: Completely isolates SQL code from user parameters (`$id`), preventing SQL injection attacks.
- **Type-Casting (`(int) $_GET['id']`)**: Guarantees `$id` is strictly an integer.

#### Data Parsing Algorithms:
1. **Ingredient Splitting**:
   ```php
   $ingredients = array_filter(array_map('trim', explode(',', $recipe['ingredients'])));
   if (count($ingredients) <= 1) {
       $ingredients = array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $recipe['ingredients'])));
   }
   ```
   Splits ingredients by commas or newlines, trimming extra whitespace and stripping empty entries.
2. **Step-by-Step Instruction Parsing**:
   ```php
   $rawSteps = preg_split('/(?<=[.!?])\s+|\r\n|\r|\n/', $recipe['instructions']);
   $steps = array_values(array_filter(array_map('trim', $rawSteps)));
   ```
   Uses a regular expression lookbehind `(?<=[.!?])\s+` to intelligently break raw paragraph instructions into distinct, numbered cooking steps.

---

### 4.5 Saved Favorites: `favorites.php`
- **Path**: [`favorites.php`](file:///c:/Users/OWNER/Documents/Web/favorites.php)
- **Role**: Renders the authenticated collection of dishes favorited by the user.

#### Backend Query:
```php
$sql = "SELECT recipes.*
        FROM recipes
        INNER JOIN favorites
        ON recipes.id = favorites.recipe_id
        ORDER BY favorites.id DESC";
```
- **`INNER JOIN favorites`**: Only returns recipes that exist in the `favorites` table.
- **Empty State**: If `$favCount === 0`, displays an animated pulsing broken heart (`💔`) and a direct link button back to `index.php`.

---

### 4.6 Favorite Toggle Action: `favorite_action.php`
- **Path**: [`favorite_action.php`](file:///c:/Users/OWNER/Documents/Web/favorite_action.php)
- **Role**: Pure backend POST controller that processes bookmark additions and removals.

#### Code Logic:
```php
$recipe_id = isset($_POST['recipe_id']) ? (int) $_POST['recipe_id'] : 0;
$action = $_POST['action'] ?? '';
$redirect = !empty($_POST['redirect']) ? $_POST['redirect'] : (!empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'favorites.php');

if ($recipe_id > 0 && $action == "add") {
    $stmt = mysqli_prepare($conn, "INSERT IGNORE INTO favorites (recipe_id) VALUES (?)");
    mysqli_stmt_bind_param($stmt, "i", $recipe_id);
    mysqli_stmt_execute($stmt);
    header("Location: " . $redirect);
    exit;
} elseif ($recipe_id > 0 && $action == "remove") {
    $stmt = mysqli_prepare($conn, "DELETE FROM favorites WHERE recipe_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $recipe_id);
    mysqli_stmt_execute($stmt);
    header("Location: " . $redirect);
    exit;
}
```
- **`INSERT IGNORE`**: Gracefully ignores duplicate inserts if the recipe was already favorited.
- **Dynamic Referrer (`$_SERVER['HTTP_REFERER']`)**: Returns the user seamlessly to the exact page they clicked the button from (Homepage, Recipe Details, or Favorites list).

---

### 4.7 Recipe Submission: `add_recipe.php`
- **Path**: [`add_recipe.php`](file:///c:/Users/OWNER/Documents/Web/add_recipe.php)
- **Role**: Form page enabling users to publish new recipes to the MySQL database.

#### Security & Validation Logic:
- Sanitizes all POST fields with `trim()`.
- Validates that `$name`, `$category`, `$ingredients`, `$instructions`, and `$cooking_time > 0` are present.
- Executes an `INSERT INTO recipes` query using prepared statements (`"sssssi"`).
- Automatically redirects to `index.php` upon successful insertion.

---

### 4.8 Client Logic & Interactivity: `script.js`
- **Path**: [`script.js`](file:///c:/Users/OWNER/Documents/Web/script.js)
- **Role**: Client-side execution handling intro timing, real-time filtering, live image preview, and checklist toggles.

#### Modular Breakdown:
1. **Intro Controller**:
   - Manages `#introOverlay` lifecycle with `setTimeout(completeIntro, 5800)`.
   - Binds `#skipIntroBtn` to immediately dismiss the intro overlay.
2. **Real-Time Search & Category Filter**:
   - Listens to `input` on `#searchInput` and `click` on `.category-pill`.
   - Evaluates each card's `dataset.name` and `dataset.category`.
   - Applies an inline staggered entrance animation (`cardEntrance 0.4s ... ${visibleCount * 0.05}s`) to filtered results.
3. **Interactive Ingredient Checklist**:
   - Toggles `.checked` class and checkbox state when clicking anywhere on an `.ingredient-item`.
4. **Live Image Preview**:
   - Listens to typing on `#image` input and updates `#imagePreview` in real-time, handling fallback error states.

---

### 4.9 Design System & Motion Graphics: `style.css`
- **Path**: [`style.css`](file:///c:/Users/OWNER/Documents/Web/style.css)
- **Role**: Centralized design system, typography tokens, glassmorphism filters, and CSS keyframe animations.

#### Key CSS Features:
- **Design Tokens**: Standardized CSS variables (`--primary: #FF5A36`, `--secondary: #023047`, `--radius-lg: 20px`, `--shadow-hover`, etc.).
- **Ambient Glowing Blobs**: `body::before` and `body::after` render radial gradient orbs animated via `@keyframes ambientFloat`.
- **Card Hover Elevation**: `transform: translateY(-8px)` combined with a sweep shine overlay (`transform: skewX(-25deg)`).
- **Responsive Breakpoints**: Media queries tailored for mobile devices (`@media (max-width: 640px)`).

---

## 5. Cinematic Intro Animation Physics

The intro window completely avoids repetitive track or single-rope motion by implementing **8 distinct 3D orbital trajectories**:

```
           [Dish 5: Upper-Center (-5vw, -38vh)]
                    ↑
  [Dish 1: Top-Left]         [Dish 3: Top-Right]
   (-38vw, -28vh)             (+35vw, -30vh)
          ↖                        ↗
[Dish 7: Far-Left]    (CENTER)     [Dish 8: Far-Right]
 (-42vw, +5vh)       Singularity    (+42vw, -6vh)
          ↙                        ↘
 [Dish 4: Bottom-Left]       [Dish 2: Bottom-Right]
  (-36vw, +30vh)              (+38vw, +28vh)
                    ↓
           [Dish 6: Lower-Center (+6vw, +36vh)]
```

### Keyframe Timeline:
1. **0.0s – 1.8s (Independent Circling)**: Dishes enter from screen edges at different initial rotation angles and orbit radii.
2. **1.8s – 3.2s (Full-Screen Scatter)**: Dishes break away to distinct screen quadrants, hovering with gentle rotational wobble.
3. **3.2s – 3.7s (Vortex Convergence)**: All 8 dishes accelerate back toward `(0, 0)`, collapsing to `scale(0)`.
4. **3.5s – 3.7s (Golden Shockwave)**: `.intro-shockwave` rings expand from `scale(0.2)` to `scale(5.5)`.
5. **3.6s – 5.5s (Brand Reveal)**: `.intro-brand-box` pops up with 3D scale and golden text shimmer.
6. **5.8s (Homepage Fade-In)**: Intro overlay fades to `opacity: 0` and is removed from the layout.

---

## 6. Security & Web Best Practices

1. **SQL Injection Prevention**:
   - Used `mysqli_prepare()`, `mysqli_stmt_bind_param()`, and `mysqli_stmt_execute()` on all dynamic queries (`recipe.php`, `add_recipe.php`, `favorite_action.php`).
2. **Cross-Site Scripting (XSS) Mitigation**:
   - Every piece of dynamic text echoed to the DOM is wrapped in `htmlspecialchars($var, ENT_QUOTES, 'UTF-8')`.
3. **Strict Type-Casting**:
   - URL IDs and form IDs are explicitly cast using `(int)` to guarantee numerical values.
4. **Database Cascade Integrity**:
   - Foreign keys enforce data consistency with `ON DELETE CASCADE`.

---

## 7. Deployment & Local Execution Guide

### Prerequisites
- [XAMPP](https://www.apachefriends.org/) (with Apache & MySQL enabled)

### Execution Steps
1. **Launch XAMPP Control Panel**:
   - Start **Apache** (Port 80/443).
   - Start **MySQL** (Port 3306).
2. **Project Placement**:
   - The project is directory-junctioned to `C:\xampp\htdocs\Web` from `c:\Users\OWNER\Documents\Web`.
3. **Open the Website**:
   - Open your web browser and navigate to:
     ```
     http://localhost/Web/index.php
     ```
4. **Database Reset (If ever needed)**:
   - Access `http://localhost/Web/create_database.php` in your browser to automatically recreate tables and re-seed sample recipes.

---

## 8. Frequently Asked Viva / Project Defense Questions

### Q1: What is the purpose of `mysqli_prepare` and parameterized queries?
> **Answer**: `mysqli_prepare` separates the SQL statement structure from the actual user-supplied data. The database compiles the SQL statement first, and parameter values are sent separately via `mysqli_stmt_bind_param`. This ensures user input is never interpreted as executable SQL commands, completely neutralizing SQL Injection attacks.

### Q2: Why did we use an `INNER JOIN` in `favorites.php` but a `LEFT JOIN` in `index.php`?
> **Answer**:
> - In [`index.php`](file:///c:/Users/OWNER/Documents/Web/index.php), we want to show **all** recipes, regardless of whether they are favorited or not. A `LEFT JOIN` returns all recipe records, and outputs `is_favorite = 1` if a match exists in the `favorites` table, or `0` otherwise.
> - In [`favorites.php`](file:///c:/Users/OWNER/Documents/Web/favorites.php), we only want to display recipes that the user has explicitly saved. An `INNER JOIN` strictly returns rows where `recipes.id` matches an existing `favorites.recipe_id`.

### Q3: How does the client-side search and category filtering work without page reloads?
> **Answer**: In [`script.js`](file:///c:/Users/OWNER/Documents/Web/script.js), every recipe card has `data-name` and `data-category` attributes rendered by PHP. On every keystroke or pill click, JavaScript iterates through `.recipe-card` elements, verifies matches using `.includes()`, toggles `display: flex` or `none`, and dynamically triggers staggered CSS keyframe animations.

### Q4: Why is `htmlspecialchars()` used throughout the PHP code?
> **Answer**: It converts special HTML characters (like `<`, `>`, `&`, `"`) into HTML entities (`&lt;`, `&gt;`). If a malicious user submits JavaScript code inside a recipe title, `htmlspecialchars()` ensures the browser renders the code as plain text rather than executing it as an XSS payload.

### Q5: What is the significance of `UNIQUE(recipe_id)` in the `favorites` table?
> **Answer**: It places a database-level integrity constraint preventing duplicate entries for the same recipe in the favorites list. Coupled with `INSERT IGNORE`, any duplicate addition attempts are safely discarded by MySQL without generating fatal errors.
