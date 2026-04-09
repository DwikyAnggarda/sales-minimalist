# 📊 BI Sales Dashboard

> A Business Intelligence Sales Dashboard built with the **TALL Stack** (Tailwind CSS, Alpine.js, Laravel, Livewire).  
> Designed for multi-branch retail operations — providing real-time KPI monitoring, interactive data visualization, and full CRUD sales management via a reactive single-page interface.

[![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?logo=php&logoColor=white)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-13.x-FF2D20?logo=laravel&logoColor=white)](https://laravel.com)
[![Livewire](https://img.shields.io/badge/Livewire-4.x-FB70A9?logo=livewire&logoColor=white)](https://livewire.laravel.com)
[![Flux UI](https://img.shields.io/badge/Flux_UI-2.x-6C63FF)](https://fluxui.dev)
[![Redis](https://img.shields.io/badge/Redis-Cache_&_Session-DC382D?logo=redis&logoColor=white)](https://redis.io)
[![Pest](https://img.shields.io/badge/Tests-Pest_PHP-E7332B)](https://pestphp.com)
[![Railway](https://img.shields.io/badge/Deploy-Railway.app-0B0D0E?logo=railway&logoColor=white)](https://railway.app)

---

## ✨ Key Features

### 🏢 Multi-Branch System
The application models a hierarchical **Branch → Store → Sale** data structure. Each branch contains multiple stores, and every sales transaction is attributed to a specific store. A global branch filter (persisted in the URL query string) lets managers isolate and analyze performance data for any individual branch without a page reload.

### 📈 BI Metrics Calculation
Four core KPI cards are computed on every request using targeted, conflict-free SQL aggregate queries:
- **Total Revenue** — sum of all `sales.amount` within the selected date range and branch filter.
- **Total Transactions** — count of individual sale records.
- **Average Order Value (AOV)** — revenue divided by transaction count.
- **Top Performing Store** — the store with the highest revenue in the period.

Two interactive **Chart.js** visualizations complement the KPI cards:
1. A **Daily Revenue Trend** line chart showing sales velocity over the selected period.
2. A **Top 5 Stores** bar chart ranked by total revenue.

### 🖥️ CRUD via Flux Modal
Create, Read, Update, and Delete operations for sales records are handled entirely within a **Flux UI modal** component, eliminating full-page redirects. The modal is reactive: opening it for an edit pre-populates all form fields via Livewire's model binding, while the store dropdown always reflects the complete store list regardless of the active branch filter.

### ⚡ Redis Caching & Cache Invalidation
Session state and application cache both run through **Redis**, providing sub-millisecond response times for repeated data access. The `app:generate-random-sales` Artisan command demonstrates the **cache invalidation pattern**: after inserting a new sale, it uses `Redis::keys()` with a glob pattern (`sales_data_*`, `sales_stats_*`) to selectively purge stale dashboard cache entries, ensuring users always see fresh data without a full cache flush.

### 🤖 Automation / Scheduler (Cron)
The Laravel **Task Scheduler** (`php artisan schedule:run`) drives automated data generation. The `App\Console\Commands\GenerateRandomSales` command can be registered in `routes/console.php` to run on any cadence (e.g., every minute), simulating live sales activity and keeping the demo dashboard actively populated without manual intervention.

---

## 🛠️ Prerequisites

Ensure the following are installed on your local machine before proceeding:

| Dependency | Minimum Version | Notes |
|---|---|---|
| **PHP** | `>= 8.3` | Required extensions: `pdo_pgsql`, `redis`, `mbstring`, `xml`, `curl`, `zip` |
| **Composer** | `>= 2.x` | PHP dependency manager |
| **Node.js** | `>= 20.x` | Required for Vite asset bundling |
| **npm** | `>= 10.x` | Bundled with Node.js |
| **PostgreSQL** | `>= 15.x` | Primary relational database |
| **Redis** | `>= 7.x` | Required for session, cache, and queue drivers |

> **Note for Windows users:** Use [Laravel Herd](https://herd.laravel.com/) for a zero-configuration local PHP + PostgreSQL + Redis environment.

---

## 🚀 Local Installation Guide

Follow these steps in order to get the application running on your local machine.

### 1. Clone the Repository

```bash
git clone https://github.com/your-username/sales-minimalist.git
cd sales-minimalist
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Configure Environment Variables

Copy the example environment file and open it for editing:

```bash
cp .env.example .env
```

At minimum, update the following values in your `.env` file. Refer to the [Configuration Guide](#-configuration-guide) below for full details.

```dotenv
APP_URL=http://localhost:8000

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=sales
DB_USERNAME=your_postgres_user
DB_PASSWORD=your_postgres_password

REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Install Node.js Dependencies

```bash
npm install
```

### 6. Run Database Migrations

This will create all required tables (`branches`, `stores`, `sales`, `users`, etc.):

```bash
php artisan migrate
```

### 7. Seed the Database

This populates the database with a default admin user and a sample multi-branch store structure (3 branches × 3 stores each):

```bash
php artisan db:seed
```

Default admin credentials after seeding:

| Field | Value |
|---|---|
| **Email** | `admin@gmail.com` |
| **Password** | `12345678` |

### 8. Publish Flux UI Assets

Flux UI requires its compiled assets to be published to your `public/` directory:

```bash
php artisan flux:publish
```

### 9. Build Frontend Assets

```bash
npm run build
```

### 10. Start the Development Server

```bash
php artisan serve
```

The application will be available at `http://localhost:8000`.

For a concurrent development experience (server + Vite HMR + queue worker), use the Composer dev script:

```bash
composer run dev
```

---

## ⚙️ Configuration Guide

### Redis Configuration (Local)

This application uses Redis as the driver for **three services**: session, cache, and queue. All three are configured via shared Redis connection variables in `.env`.

```dotenv
# --- Redis Connection ---
# The PHP client library used to talk to Redis.
# 'predis' is a pure-PHP client and requires no PHP extension.
# Use 'phpredis' if you have the php-redis C extension installed.
REDIS_CLIENT=predis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null       # Set this if your local Redis requires authentication
REDIS_PORT=6379

# --- Service Drivers ---
# These variables tell Laravel to use Redis for each service:
SESSION_DRIVER=redis
CACHE_STORE=redis
QUEUE_CONNECTION=redis
```

> **Important:** If you change `REDIS_CLIENT` from `predis` to `phpredis`, ensure the `php-redis` extension is enabled in your `php.ini`. Mismatching the client with the available extension is the most common Redis connectivity issue.

---

## 🧪 Testing

This project uses **[Pest PHP](https://pestphp.com/)**, a modern testing framework built on PHPUnit with an expressive, fluent syntax.

### Run All Tests

```bash
php artisan test
```

Or using the Pest binary directly:

```bash
./vendor/bin/pest
```

### Run Tests with Coverage Report

```bash
./vendor/bin/pest --coverage
```

### Run a Specific Test File

```bash
./vendor/bin/pest tests/Feature/DashboardTest.php
```

### What is Tested

| Test File | Scope | Description |
|---|---|---|
| `tests/Feature/DashboardTest.php` | Route & Auth | Verifies guests are redirected to login; authenticated users can access the dashboard |
| `tests/Feature/Livewire/` | Livewire Components | Component rendering and interaction tests |
| `tests/Feature/Auth/` | Authentication | Login, registration, and password flows |

> **Note:** Tests run against a separate SQLite in-memory database by default (configured in `phpunit.xml`). No Redis instance is required for the test suite.

---

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

