# 🍳 TastyBites — Full-Stack Recipe Web Application

TastyBites is a culinary web application built with **PHP 8**, **MySQL**, **Vanilla CSS3**, and **Modern JavaScript (ES6+)**. It includes dynamic database-driven recipe exploration, real-time search and category filtering, dynamic interactive bookmarks (Favorites), step-by-step checklist preparation, and recipe publishing with live image previews.

---

## 📌 Critical Insight: Why Clicking Buttons Displayed Source Code

If clicking buttons previously displayed raw PHP code or downloaded files, this was caused by one of three execution mismatches:

1. **Opening files via `file:///` protocol (Direct Double-Clicking)**:
   - When opening `index.php` or `index.html` by double-clicking it in Windows File Explorer, the address bar starts with `file:///C:/Users/...`.
   - Web browsers cannot execute PHP scripts directly. Without a running web server (like Apache in XAMPP), the browser treats `.php` files as raw text or prompts a file download.
   - **Fix**: The full-stack application must be accessed through a web server at `http://localhost/Web/index.php` or `http://localhost:8000/index.php`.

2. **GitHub Repository Link vs Deployed Website**:
   - The link `https://github.com/Alwin1702/tastybites` is a **source code repository**, not a running web server. Clicking files inside GitHub opens GitHub's code viewer to display source code.

3. **GitHub Pages Limitation**:
   - **GitHub Pages ONLY hosts static frontend assets (HTML, CSS, JS, images). It DOES NOT execute PHP or run MySQL databases.**
   - If a project attempts to call `.php` files on GitHub Pages, the browser either downloads the PHP file or displays a 404/plain-text response.
   - **Dual Architecture in TastyBites**:
     - `index.php` (Full-Stack Mode): Full PHP & MySQL dynamic engine with server-side queries, prepared statements, and relational database persistence.
     - `index.html` (Static / GitHub Pages Mode): Interactive client-side preview with modals and `localStorage` persistence, safe for static hosting on GitHub Pages.

---

## 📁 Project Structure

```
Web/
├── .htaccess              # Apache configuration (DirectoryIndex index.php index.html)
├── .gitignore             # Git ignore file protecting environment variables, logs, and secrets
├── db.php                 # MySQLi database connection with environment variable overrides
├── create_database.php    # Automated DB initializer and recipe seeder script
├── schema.sql             # SQL database schema and initial seed data for manual import
├── index.php              # Full-stack homepage with dynamic MySQL recipe catalog & filters
├── recipe.php             # Full-stack detailed recipe page with interactive checklist & steps
├── favorites.php          # Full-stack user favorites cookbook backed by MySQL
├── favorite_action.php    # Backend handler for adding/removing favorites (AJAX + Form POST)
├── add_recipe.php         # Full-stack recipe creation form with prepared statement insertion
├── index.html             # Standalone client-side interactive preview (for GitHub Pages / offline)
├── script.js              # Client-side search, category filter, intro animation, & AJAX handlers
├── style.css              # Custom Vanilla CSS design system, typography, & animations
└── README.md              # Project documentation and setup guide
```

---

## 🛠️ Local Setup Guide (XAMPP on Windows)

### 1. Web Root Location
Make sure the project files are located inside your XAMPP `htdocs` directory:
- **Location**: `C:\xampp\htdocs\Web` *(or linked via junction)*

### 2. Start Services
1. Open the **XAMPP Control Panel**.
2. Click **Start** next to **Apache**.
3. Click **Start** next to **MySQL**.

### 3. Initialize the Database
The database `tastybites` and initial recipes can be created using any of the following methods:

- **Method A (Automatic Browser Setup)**:
  Open in your browser:
  ```
  http://localhost/Web/create_database.php
  ```
- **Method B (phpMyAdmin)**:
  1. Open `http://localhost/phpmyadmin/`.
  2. Click **Import**.
  3. Choose the [schema.sql](file:///c:/Users/OWNER/Documents/Web/schema.sql) file from this repository and click **Import**.
- **Method C (Auto-Self Healing in `db.php`)**:
  `db.php` will automatically create the `tastybites` database, `recipes`, and `favorites` tables and seed default recipes upon first connection if they do not exist.

### 4. Open the Application
Navigate to the local URL in your browser:
```
http://localhost/Web/index.php
```
*(Or simply `http://localhost/Web/`)*

---

## 💻 Alternative: Running via PHP Built-in Server

If Apache is not being used, you can start PHP's built-in server directly from this folder (ensure MySQL is running in XAMPP for database features):

```powershell
# Open terminal in project directory
& "C:\xampp\php\php.exe" -S localhost:8000
```

Then visit:
```
http://localhost:8000/index.php
```

---

## 🌐 Deployment Instructions

### 1. Static Preview Deployment (GitHub Pages)
- Repository: `https://github.com/Alwin1702/tastybites`
- GitHub Pages automatically serves [index.html](file:///c:/Users/OWNER/Documents/Web/index.html) as the static entry point.
- The static preview utilizes modals and browser `localStorage` to showcase the interface, recipe details, live search, and recipe creation without requiring a PHP server.
- **To enable GitHub Pages**:
  1. Go to your repository on GitHub: `Settings` > `Pages`.
  2. Under **Build and deployment** > **Branch**, select `main` and root `/`.
  3. Click **Save**. Your site will be published at `https://<your-username>.github.io/tastybites/`.

### 2. Full-Stack PHP & MySQL Hosting Deployment
To deploy the real backend with PHP and MySQL, choose any PHP-compatible cloud host:
- **Free / Low-Cost Options**: InfinityFree, 000webhost, Railway, Render (with Docker / Web Service), Clever Cloud, or shared cPanel hosting.
- **Environment Variables**: Configure the following environment variables in your hosting provider's dashboard:
  - `DB_HOST`: Hostname of your MySQL instance
  - `DB_USER`: Database username
  - `DB_PASS`: Database password
  - `DB_NAME`: Database name (e.g. `tastybites`)
  - `DB_PORT`: MySQL port (default `3306`)
- `db.php` will automatically read these variables via `getenv()` while gracefully falling back to local XAMPP defaults if omitted.

---

## 🔒 Security Best Practices Implemented

- **Prepared Statements**: All database operations (`INSERT`, `SELECT`, `DELETE`) utilize parameter-bound prepared statements (`mysqli_prepare` / `mysqli_stmt_bind_param`) to eliminate SQL Injection risks.
- **XSS Mitigation**: User inputs and database outputs are escaped using `htmlspecialchars()` before rendering into HTML.
- **Secret Protection**: `.gitignore` is configured to exclude all `.env`, `.env.*`, `config.local.php`, and database backup dumps to prevent leaking credentials to version control.
- **Non-destructive Operation**: Fallback error screens guide users when MySQL is unreachable without exposing raw database credentials.

---

## 📋 Git Workflow Commands

To push your fixes to GitHub:

```bash
# 1. Check changed and untracked files
git status

# 2. Stage all modifications and newly created files
git add .

# 3. Commit with a descriptive message
git commit -m "Fix PHP server execution, synchronize favorite handlers, and prepare GitHub deployment"

# 4. Push updates to GitHub main branch
git push origin main
```
