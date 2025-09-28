# Neptune Swap  

Neptune Swap is a responsive online marketplace built with **PHP, HTML, CSS, and JavaScript** for trading used electronics.  
Users can **list and edit items, negotiate prices up to 30%, view real-time statuses (available, reserved, sold), and chat directly** — all powered by a secure **MySQL backend**.  

---

## 📂 Project Structure

```tree
CTS/
│
├── public/             # Static files (HTML, CSS, JS, media)
│   ├── index.php       # Main entry point
│   ├── style/          # Stylesheets
│   ├── script/         # Client-side scripts
│   ├── media/          # Images, icons, etc.
│   ├── ionicons/       # Icon pack
│   └── ...             # Other PHP/HTML files
│
├── src/                # Backend PHP scripts
│   ├── db.php          # Database connection
│   ├── shop.php        # Shop logic
│   ├── inventory.php   # Inventory management
│   └── ...             # Other PHP files
│
└── marketplace.sql     # Database schema and seed data
```

## 🚀 Installation & Setup

### 1. Requirements
Before running the project, install:

- [XAMPP](https://www.apachefriends.org/) or [MAMP](https://www.mamp.info/) (for PHP + Apache + MySQL)  
- PHP 8+  
- MySQL 5.7+ or MariaDB  

---

### 2. Clone the Repository
```bash
git clone https://github.com/MaksymSyrotiuk/neptune-swap-shop.git
cd neptune-swap-shop
```

### 3. Set Up the Database

Start **MySQL** from XAMPP/MAMP.  

Create a new database:

```sql
CREATE DATABASE marketplace;
SOURCE database/marketplace.sql;
```

### 4. Configure Database Connection

Edit `src/modules/db.php` and update with your local credentials:

```php
<?php
    $host = "localhost"; // Host name
    $dbname = "marketplace"; // Database name
    $username = "root";  // DB login
    $password = "";      // Password

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Database connection error: " . $e->getMessage());
    }
?>
```
### 5. Run the Project

Move the project into your web server root:

- **XAMPP** → `htdocs/neptune-swap-shop`  
- **MAMP** → `htdocs/neptune-swap-shop`  

Start **Apache** and **MySQL** in XAMPP/MAMP.  

Open in your browser: 

http://localhost/neptune-swap-shop/public
