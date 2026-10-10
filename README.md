# Financial Transaction System Financial Transaction System

![Financial Transaction System Transaction System](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=for-the-badge&logo=mysql)

Financial Transaction System is a centralized **Financial Transaction System** designed to securely handle and approve user-initiated transactions.

The system provides a secure API for "Citizens" to submit financial transaction requests, and a beautiful Apple-inspired UI dashboard for Administrators to review, accept, or reject them.

## 🌟 Key Features

### 1. API-First Citizen Experience
- **Citizens:** Submit transactions through a secure REST API (perfect for testing via Postman or integrating into mobile apps).
- **Transaction Details:** Captures essential details including sender name, phone number, and transaction amount.
- **Unique Reference Number:** Every transaction is assigned a unique, trackable reference number upon creation.

### 2. Administrator Dashboard
- **Admin Control:** Administrators log into a secure UI dashboard to monitor all incoming transactions.
- **Approval Workflow:** Admins can easily "Accept" or "Reject" pending transactions.
- **Transaction Ledger:** Clear view of transaction statuses (Pending, Accepted, Rejected) with an immutable history.

### 3. Beautiful UI/UX (Admin Panel)
- **Apple-Inspired Aesthetics:** Uses glassmorphism, responsive collapsible sidebars, custom SVG iconography, and deep dark-mode ready color palettes.
- **Custom Pagination:** Clean, modern pagination controls built from scratch without relying on heavy external CSS frameworks.

## 🚀 Tech Stack

- **Backend:** Laravel 11 (PHP 8.2+)
- **Frontend (Admin):** Laravel Blade, Vite, Vanilla CSS (Custom Design System)
- **API (Citizen):** RESTful JSON API
- **Database:** MySQL
- **Auth & Roles:** Laravel Breeze, Spatie Laravel-Permission

## 🛠️ Installation & Setup

Follow these steps to set up the project locally:

1. **Clone the repository:**
   ```bash
   git clone https://github.com/Siraj-T/Financial Transaction System-Transaction-System.git
   cd Financial Transaction System-Transaction-System
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
   This will build the database schema and populate it with the default admin role and test users.
   ```bash
   php artisan migrate:fresh --seed
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

If you ran the seeder (`php artisan migrate:fresh --seed`), the following account is available for testing the Admin Dashboard. *The password is `password`.*

| Role       | Email                   | Password |
|------------|-------------------------|----------|
| **Admin**  | `admin@Financial Transaction System.local`   | password |

## 📡 API Testing (Postman)

To submit a transaction as a Citizen, use Postman:

**Endpoint:** `POST http://localhost:8000/api/transactions`
**Headers:** `Accept: application/json`
**Body (JSON):**
```json
{
    "sender_name": "John Doe",
    "sender_phone": "+1234567890",
    "amount": 150.00
}
```

## 🛡️ Security

If you discover any security-related issues, please do not use the issue tracker. Instead, contact the repository owner directly.

---
*Designed and built for modern financial transaction management.*
