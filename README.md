# TrustFactory - Ecommerce App

--- 

[![Trustfactory-Task-Laravel](https://github.com/mubasharkk/trustfactory/actions/workflows/laravel.yml/badge.svg)](https://github.com/mubasharkk/trustfactory/actions/workflows/laravel.yml)

A full-stack e-commerce application built with Laravel and Inertia.js, featuring product catalog management, shopping cart functionality, order processing, and automated inventory notifications. The application includes user authentication with email verification, real-time stock management, and comprehensive order tracking.

## Development Approach

This project follows a clean architecture pattern with clear separation of concerns:

- **Laravel Actions**: Business logic is encapsulated in reusable Action classes, keeping controllers thin and focused on HTTP concerns
- **Policy-Based Authorization**: Authorization logic is centralized in policies, ensuring consistent access control across the application
- **Observer Pattern**: Product stock changes are monitored via observers, triggering automated notifications when thresholds are crossed
- **Scheduled Jobs**: Background jobs handle time-sensitive tasks like daily sales reports, ensuring non-blocking execution
- **Unit Testing**: Comprehensive unit tests focus on business logic (Actions and Policies) rather than HTTP layer, ensuring fast and reliable test execution
- **Configuration-Driven**: Key application settings (stock thresholds, report times, admin emails) are configurable via environment variables for flexibility across environments

The architecture prioritizes maintainability, testability, and scalability while following Laravel best practices and SOLID principles.

## Implemented Files

---

### Database Migrations
- `database/migrations/`

### App Folder
- `app/Actions`
- `app/Http/Controllers`
- `app/Http/Requests/`
- `app/Models/`
- `app/Jobs/`
- `app/Observers/`

## Getting Started

---

### Prerequisites

- Docker Desktop (or Docker Engine + Docker Compose)
- Git

### Installation

1. **Environment setup**
   ```bash
   cp .env.example .env
   ```

   Update the following configurations in `.env` according to your local preferences:
    - `APP_PORT` - Application port (default: 80)
    - `FORWARD_DB_PORT` - Database port forwarding (default: 3306)
    - `DAILY_SALES_REPORT_TIME` - Time for daily sales report job to run, format HH:MM 24-hour (default: 20:00)
    - `PRODUCT_LOW_STOCK_THRESHOLD` - Minimum stock quantity before low stock notification is triggered (default: 10)
    - `PRODUCT_ADMIN_EMAIL` - Email address to receive low stock notifications (default: admin@example.com)


2. **Clone the repository**
   ```bash
   git clone https://github.com/mubasharkk/trustfactory.git
   cd trustfactory
   ```

3. **Start Laravel Sail**
   ```bash
   ./vendor/bin/sail up -d
   ```

4. **Install PHP dependencies**
   ```bash
   ./vendor/bin/sail composer install
   ```

5. **Generate application key**
   ```bash
   ./vendor/bin/sail artisan key:generate
   ```

6. **Run database migrations and seeders**
   ```bash
   ./vendor/bin/sail artisan migrate
   ./vendor/bin/sail artisan db:seed
   ```

7. **Attach Public Storage**
   ```bash
   ./vendor/bin/sail artisan storage:link
   ```

### Using Sail Commands

All Laravel artisan commands should be prefixed with `./vendor/bin/sail`:

```bash
# Run composer commands
./vendor/bin/sail composer install

# Run migrations
./vendor/bin/sail artisan migrate

# Run tests
./vendor/bin/sail artisan test

# Access tinker
./vendor/bin/sail artisan tinker

# Running queues
./vendor/bin/sail artisan queue:work --once

```

### Stopping Sail

To stop the containers:
```bash
./vendor/bin/sail down
```

## Testing

---

Run the test suite:
```bash
./vendor/bin/sail artisan test
```
