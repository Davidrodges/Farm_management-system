# DAVINA AND RODGERS FARM MANAGEMENT SYSTEM

A comprehensive Weekly Management Record system designed for **Davina and Rodgers Solution LTD**. This web-based application tracks flock status, egg production, expenses, and performance metrics.

## 🚀 Features
- **Weekly Record Keeping**: 13 detailed sections (Flock, Eggs, Feed, Water, Health, Biosecurity, Labour, Expenses, Sales, Inventory, Assets, Notes).
- **Automated Performance Analysis**: Instantly calculates Mortality %, Production %, Feed Conversion, and Net Profit.
- **Dashboards**: Visual summary of weekly performance.
- **Data Export**: Download all records to CSV for Excel analysis.
- **Print Reports**: Printer-friendly view for physical filing.
- **Secure Access**: User authentication with password recovery (email-based/simulated).
- **Zero-Config Database**: Uses SQLite for instant setup.

## 🛠️ System Requirements
- **Server**: Apache (via XAMPP, WAMP, or standalone).
- **PHP**: Version 7.4 or higher.
- **Database**: SQLite3 (Enabled in `php.ini`).

## 📥 Installation
1. **Copy Files**: Place the `farm_system` folder into your web server's document root (e.g., `C:\Apache24\htdocs\` or `C:\xampp\htdocs\`).
2. **Enable SQLite**:
   - Open your `php.ini` file.
   - Uncomment `extension=sqlite3` and `extension=pdo_sqlite`.
   - Restart Apache.
3. **Launch**: Open your browser and visit:
   ```
   http://localhost/farm_system/
   ```

## 🔐 Default Credentials
- **Username**: `admin`
- **Password**: `admin123`

## 📂 Project Structure
- `config/` - Database connection.
- `assets/` - CSS and Javascript.
- `includes/` - Header, Footer, Auth Checks.
- `records/` - Main record CRUD logic (Create, View, Export).
- `migrations/` - Database schema updates.
- `farm.db` - The SQLite database file (created automatically on first run).

## 📄 License
Internal software for Davina and Rodgers Solutions LTD.
