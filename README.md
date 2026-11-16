<<<<<<< HEAD
# 💊 PharmaLink — Online Pharmacy & Delivery Portal

[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat-square&logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=flat-square&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![JavaScript](https://img.shields.io/badge/JavaScript-ES6+-F7DF1E?style=flat-square&logo=javascript&logoColor=black)](https://developer.mozilla.org/en-US/docs/Web/JavaScript)
[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg?style=flat-square)](LICENSE)

**PharmaLink** is a full-stack web application designed for digital pharmacy management and direct-to-door medication delivery. It provides a customer-facing storefront for browsing categorized pharmaceuticals, user authentication, delivery dispatch scheduling, and a dedicated administrative dashboard for managing medicine inventory in real time.

---

## 🌟 Key Features

### 👤 Customer Experience
- **Authentication System:** Secure registration and login workflows with user session management.
- **Categorized Product Catalog:** Browse pharmaceuticals across curated categories (Kids, Women, Men, Animal Care, and Common Healthcare).
- **Delivery Registration:** Online dispatch booking with address, contact, and fulfillment preference tracking.
- **Payment Gateway Simulation:** Client-side card validation (Luhn algorithm checksum, expiry, and CVV checks) connected to order processing.
- **User Dashboard:** Dedicated account portal showing personal information and order history.
- **Contact & Support:** Interactive inquiry submission system for customer assistance.

### 🛡️ Administrative Inventory Control
- **Real-Time Inventory Table:** Searchable, sortable, and paginated medicine catalog powered by jQuery DataTables.
- **Full CRUD Support:** Add, edit, and delete pharmaceutical stock with image reference and dosage details.
- **Session Flash Messaging:** User feedback for inventory transactions.

---

## 🏗️ Project Architecture

PharmaLink follows a clean, modular structure that separates the public web interface, business logic, administration dashboard, and database configuration:

```
pharmalink/
│
├── .env.example             # Template for local environment variables
├── .gitignore               # Ignored files (.env, OS artifacts, logs)
├── README.md                # Project documentation & setup guide
│
├── database/
│   └── schema.sql           # Canonical database schema with seed data
│
├── docs/                    # Technical architecture & engineering guides
│   ├── ANALYSIS.md          # Architectural audit and system analysis
│   ├── HERD_SETUP.md        # Local development setup with Laravel Herd
│   ├── ISSUES.md            # Technical issue registry and fixes
│   └── ROADMAP.md           # Engineering modernization roadmap
│
└── public/                  # Web Document Root (served by web server)
    ├── index.php            # Root entrypoint & session router
    ├── favicon.ico          # Optimized platform favicon
    │
    ├── assets/              # Static styling, scripting, and media
    │   ├── css/             # Stylesheets & CSS design tokens (tokens.css)
    │   ├── js/              # Modular JavaScript (auth, CSRF, validation)
    │   └── images/          # Platform branding & pharmaceutical imagery
    │
    ├── pages/               # Customer storefront views
    │   ├── auth.html        # Login & registration portal
    │   ├── home.html        # Main storefront & seasonal medicines
    │   ├── categories.html  # Category navigator
    │   ├── delivery.html    # Order delivery dispatch form
    │   ├── payment.html     # Checkout & card payment form
    │   ├── contact.html     # Customer support inquiries
    │   ├── about.html       # Company mission & information
    │   ├── search.html      # Product search interface
    │   ├── forgot-password.html # Password recovery help
    │   └── shop/            # Category-specific medicine listings
    │       ├── kids.html
    │       ├── womens.html
    │       ├── mens.html
    │       ├── animal.html
    │       └── common.html
    │
    ├── src/                 # Server-side business logic & request handlers
    │   ├── config/          # Centralized PDO connection, CSRF & auth guards
    │   ├── auth/            # Login, registration, and session termination
    │   ├── user/            # Profile display & order history
    │   ├── delivery/        # Delivery registration handler
    │   ├── payment/         # Payment submission processor
    │   ├── contact/         # Support form inquiry handler
    │   └── shop/            # Category listing data providers
    │
    └── admin/               # Administrative inventory management (CRUD)
        ├── index.php        # Real-time DataTables inventory management
        ├── add.php          # Add medicine endpoint
        ├── edit.php         # Update medicine endpoint
        ├── delete.php       # Delete medicine endpoint
        ├── add-modal.php    # Creation modal template
        ├── edit-delete-modal.php # Edit & delete confirmation modals
        ├── db.php           # Admin database connector
        └── vendor/          # Production vendor libraries (Bootstrap, jQuery, DataTables)
```

---

## 🚀 Getting Started

### Prerequisites
- **Web Server:** Apache (via [XAMPP](https://www.apachefriends.org/), [WampServer](https://www.wampserver.com/), or standalone Apache)
- **PHP:** 8.1 or higher
- **Database:** MySQL / MariaDB

### Installation

1. **Clone the repository:**
   ```bash
   git clone https://github.com/muaath-im/pharmaLink.git
   cd pharmalink
   ```

2. **Configure Environment:**
   Copy the `.env.example` file to `.env`:
   ```bash
   cp .env.example .env
   ```
   Open `.env` and set your database connection parameters:
   ```ini
   DB_HOST=localhost
   DB_NAME=pharmalink
   DB_USER=root
   DB_PASS=your_password
   ```

3. **Import Database Schema:**
   - Open **phpMyAdmin** (or your MySQL CLI tool).
   - Create a database named `pharmalink` (or the name set in `.env`).
   - Import `database/schema.sql`:
     ```bash
     mysql -u root -p pharmalink < database/schema.sql
     ```

4. **Launch Application:**
   - **Using XAMPP/WAMP:** Place the project folder into your `htdocs` or `www` directory and visit:
     ```
     http://localhost/pharmalink/public/
     ```
   - **Using PHP Built-in Server:**
     ```bash
     php -S localhost:8000 -t public
     ```
     Then open `http://localhost:8000` in your browser.

5. **Access the Admin Panel:**
   Navigate to:
   ```
   http://pharmalink.test/admin/
   ```
   *(Or `http://localhost:8000/admin/` if using the PHP built-in server)*

---

## 🔒 Security & Engineering Best Practices

- **Separation of Concerns:** Client views (`public/`), server-side operations (`src/`), and administration tools (`admin/`) are strictly decoupled.
- **Environment Isolation:** Sensitive database credentials reside in `.env`, preventing credentials from leaking into source control.
- **Prepared Statements:** Backend queries use parameterized PDO / MySQLi prepared statements to mitigate SQL injection vulnerabilities.
- **Scrubbed Seed Data:** The repository schema contains zero personally identifiable information (PII) or plain credentials.

---

## 🗺️ Roadmap & Upcoming Improvements

- [ ] UI/UX redesign applying Human-Computer Interaction (HCI) heuristics.
- [ ] Cart management system with real-time stock balance verification.
- [ ] Industry-standard password hashing using `password_hash()` (Argon2id/Bcrypt).
- [ ] Integration with third-party payment gateways (Stripe / PayHere).
- [ ] Automated email notifications for order status and tracking.

---

## 📄 License

This project is licensed under the [MIT License](LICENSE).
=======
# pharmaLink
Online Pharmacy Portal
>>>>>>> d85e555c5300f85cf2d0450235ff8945a52c53f5
