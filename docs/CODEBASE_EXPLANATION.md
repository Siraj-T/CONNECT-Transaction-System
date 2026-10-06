# CONNECT Transaction System - Codebase Explanation

This document provides a detailed, file-by-file and line-by-line breakdown of the custom logic built for the CONNECT Transaction System.

---

## 1. Database & Schema

### `database/migrations/*`
Migrations are scripts that define the structure of your MySQL database tables.

#### `2026_10_06_000001_create_reseller_profiles_table.php`
- `Schema::create('reseller_profiles', function (Blueprint $table)`: Creates the table.
- `$table->id()`: Primary auto-incrementing key.
- `$table->foreignId('user_id')->constrained()->onDelete('cascade')->unique()`: Links this profile to the `users` table. If the user is deleted, this profile is deleted (`cascade`). It's `unique` because it's a 1-to-1 relationship.
- `$table->decimal('wallet_balance', 12, 3)->default(0.000)`: The reseller's money. 12 total digits, 3 after the decimal (LYD uses 3 decimal places).
- `$table->decimal('commission_rate', 5, 2)->default(5.00)`: How much profit they make by default (e.g., 5.00%).

#### `2026_10_06_000004_create_vouchers_table.php`
- `$table->foreignId('voucher_plan_id')->constrained()->onDelete('restrict')`: Links to the plan. `restrict` means you cannot delete a plan if it has vouchers attached.
- `$table->string('code', 50)->unique()`: The 12-character unique string the customer scratches off.
- `$table->char('batch_id', 36)`: A UUID that groups vouchers generated at the exact same time so admins can manage them as a block.
- `$table->enum('status', ['available', 'reserved', 'sold', 'redeemed', 'expired', 'cancelled'])`: Tracks the exact lifecycle state of the voucher.
- Indexes like `$table->index('status')` make querying thousands of vouchers extremely fast.

#### `database/seeders/RolePermissionSeeder.php`
- `app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions()`: Clears memory cache so fresh roles don't throw errors.
- `$adminRole = Role::create(['name' => 'admin', 'guard_name' => 'web'])`: Creates the administrator role.
- `Permission::create(...)`: Defines specific actions users can take (e.g., `vouchers.generate`).
- `$adminRole->syncPermissions(...)`: Attaches the permissions to the roles.
- `User::create(...)`: Inserts the default test users (`admin@connect.ly`, `reseller@connect.ly`, etc.) so you can log in immediately.

---

## 2. Models (The Data Layer)

### `app/Models/User.php`
- `use HasRoles;`: Adds Spatie's role system so we can do `$user->hasRole('admin')`.
- `public function resellerProfile(): HasOne`: Defines that a User has one `ResellerProfile` record.
- `public function getWalletBalance(): float`: A helper function. It checks if the user has a reseller profile, and if so, returns their balance. If not, returns `0.0`.
- `public function getDashboardRoute(): string`: A PHP `match` statement that checks the user's role and returns the correct URL name (e.g., `admin.dashboard`).

### `app/Models/VoucherPlan.php`
- `use SoftDeletes;`: Instead of deleting plans (which would break history), it just marks them as deleted with a timestamp so they hide from the UI.
- `protected $casts = ['price_lyd' => 'decimal:3']`: Automatically converts the database string value into a clean PHP float with 3 decimal places.

---

## 3. Middlewares (Security & Routing)

### `app/Http/Middleware/ResellerMiddleware.php`
- `if (Auth::check() && Auth::user()->hasRole('reseller'))`: Checks if the person is logged in AND is officially a reseller.
- `return $next($request);`: If true, let them through to the page they wanted.
- `return redirect()->route('dashboard')->with('error', 'Unauthorized access.');`: If false, kick them out to the main router with an error.

### `app/Http/Controllers/Auth/AuthenticatedSessionController.php` (Modified)
- `return redirect()->intended(route($request->user()->getDashboardRoute(), absolute: false));`: When a user logs in, instead of sending everyone to the same page, it asks the User model where they belong based on their role and routes them dynamically.

---

## 4. Controllers (The Brains)

### `app/Http/Controllers/Admin/VoucherGeneratorController.php`
- `public function store(Request $request)`: Receives the form submission.
- `$validated = $request->validate(...)`: Security check ensuring they picked a real plan and requested 1 to 500 vouchers.
- `$batchId = (string) Str::uuid();`: Generates a random unique ID for this entire batch of vouchers.
- `for ($i = 0; $i < $validated['quantity']; $i++)`: Loops exactly the number of times requested.
- `$vouchersToInsert[] = [...]`: Builds a massive array of new vouchers in memory instead of saving them one by one.
- `Voucher::insert($chunk);`: Bulk inserts 100 vouchers at a time directly into MySQL. This is a massive performance optimization.
- `\App\Models\AuditLog::create(...)`: Writes a permanent security log detailing who generated the vouchers.

### `app/Http/Controllers/Reseller/ResellerVoucherController.php`
- `public function buyStore(Request $request)`: Handles when a reseller buys stock.
- `if ($reseller->getWalletBalance() < $totalCost)`: Fails early if they are broke.
- `DB::beginTransaction();`: Crucial for financial logic. If anything crashes during the process, it rolls back all database changes to prevent lost money.
- `$vouchers = Voucher::...->lockForUpdate()->get();`: Finds available vouchers and *locks* those rows in MySQL so no other reseller can buy the exact same vouchers at the exact same millisecond.
- `Voucher::whereIn(...)->update(['status' => 'reserved'])`: Transfers ownership to the reseller.
- `$reseller->resellerProfile->update(['wallet_balance' => $newBalance]);`: Takes their money.
- `WalletLedger::create(...)`: Writes a receipt to the accounting book for transparency.
- `DB::commit();`: Everything was successful, permanently save the changes.

### `app/Http/Controllers/Customer/CustomerVoucherController.php`
- `$code = strtoupper(trim($validated['voucher_code']));`: Cleans the user's input of spaces and lowercase letters.
- `$expiresAt = $now->copy()->addDays($voucher->plan->duration_days);`: Calculates the exact future date their internet will shut off based on the plan they bought.
- `$voucher->update(['status' => 'redeemed'...])`: Kills the voucher so it can never be used again.

---

## 5. Views & UI (The Face)

### `resources/css/app.css`
- `:root { --color-primary: #0071E3; }`: Sets up global CSS variables for the Apple design style. If you want to change the app color, you only change it here.
- `.card`: A reusable UI block with rounded corners, white background, and soft shadows. `transition: all 0.3s` ensures it animates smoothly when hovered.

### `resources/views/layouts/app.blade.php`
- `<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">`: Standard Laravel setup.
- `@vite(['resources/css/app.css', 'resources/js/app.js'])`: Tells the Vite build tool to load and compile our custom CSS.
- `@if(Auth::user()->hasRole('admin'))`: The sidebar navigation physically changes depending on who looks at it. Customers can't even see the HTML for the Admin links.
- `@yield('content')`: A placeholder where the actual page content (like the dashboard) gets injected.

### `resources/views/reseller/dashboard.blade.php`
- `{{ number_format(Auth::user()->getWalletBalance(), 3) }} LYD`: Prints the exact wallet balance formatted cleanly with commas and 3 decimal points.
- `<div style="background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark));">`: Uses inline styles hooked into our CSS variables to make the wallet card look like a premium, glowing credit card.
