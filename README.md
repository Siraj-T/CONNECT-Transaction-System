# CONNECT Transaction System

![CONNECT Transaction System](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=for-the-badge&logo=mysql)

CONNECT is a comprehensive **ISP Voucher & Reseller Management System** designed to handle the generation, distribution, and redemption of internet data vouchers. 

The system features an Apple-inspired UI design language (translucency, fluid animations, and modern typography) and provides a secure, role-based ecosystem for Administrators, Resellers, and end Customers.

## 🌟 Key Features

### 1. Role-Based Access Control (RBAC)
- **Administrator:** Full system control. Can generate bulk vouchers, create/manage voucher plans (data limits, duration, pricing), manage users, and view the global transaction ledger.
- **Reseller:** B2B partners who purchase vouchers in bulk using a wallet ledger system at a commission discount, and manage their own local inventory to sell to end-users.
- **Customer:** End-users who can register, log in, and securely redeem purchased 12-digit PIN codes to their accounts.

### 2. Secure Financial Ledger
- **Wallet System:** Resellers have wallets that track their balance.
- **Immutable Transactions:** Every voucher purchase and redemption is logged in an immutable, append-only transaction ledger to ensure exact financial tracking.
- **Race-Condition Protection:** Database row-locking (`lockForUpdate`) ensures that high-concurrency voucher purchases do not result in stock overselling.

### 3. Beautiful UI/UX
- **Apple-Inspired Aesthetics:** Uses glassmorphism, responsive collapsible sidebars, custom SVG iconography, and deep dark-mode ready color palettes.
- **Custom Pagination:** Clean, modern pagination controls built from scratch without relying on heavy external CSS frameworks.

## 🚀 Tech Stack

- **Backend:** Laravel 11 (PHP 8.2+)
- **Frontend:** Laravel Blade, Vite, Vanilla CSS (Custom Design System)
- **Database:** MySQL
- **Auth & Roles:** Laravel Breeze, Spatie Laravel-Permission

## 🛠️ Installation & Setup

Follow these steps to set up the project locally:

1. **Clone the repository:**
   ```bash
   git clone https://github.com/Siraj-T/CONNECT-Transaction-System.git
   cd CONNECT-Transaction-System
   ```

2. **Install PHP dependencies:**
   ```bash
   composer install
   ```

3. **Install NPM dependencies:**
   ```bash
   npm install
   ```

4. **Environment Setup:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Edit the `.env` file to include your local database credentials.*

5. **Database Migration & Seeding:**
   This will build the database schema and populate it with the default roles, a voucher plan, and test users.
   ```bash
   php artisan migrate --seed
   ```

6. **Compile Frontend Assets:**
   ```bash
   npm run build
   ```

7. **Run the Development Server:**
   ```bash
   php artisan serve
   ```

## 🔑 Default Test Accounts

If you ran the seeder (`php artisan migrate --seed`), the following accounts are available for testing. *The password for all test accounts is `password`.*

| Role       | Email                   | Password |
|------------|-------------------------|----------|
| **Admin**  | `admin@connect.local`   | password |
| **Reseller**| `reseller@connect.local`| password |
| **Customer**| `customer@connect.local`| password |

## 🛡️ Security

If you discover any security-related issues, please do not use the issue tracker. Instead, contact the repository owner directly. Transactions and Vouchers are designed as immutable ledgers; manual deletion or editing of financial history is restricted by design to prevent fraud.

---
*Designed and built for modern ISP voucher management.*
