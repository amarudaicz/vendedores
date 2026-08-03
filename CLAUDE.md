# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a hybrid web application for a perfume/sales distribution business with two main interfaces:
- **Customer-facing e-commerce** (public website with product catalog, cart, and orders)
- **Seller management panel** (Angular SPA for sellers to manage customers, products, and orders)

### Architecture

The project follows a **PHP backend + Angular frontend** architecture:

1. **PHP Backend** (`api.php`, `index.php`)
   - Custom MVC-like structure with Controllers, Models, Helpers, Filters
   - Session-based authentication using native PHP sessions
   - MySQL database via mysqli
   - API versioning at `/api/v1/*`

2. **Angular Frontend** (`public/pages/sellers/app/`)
   - Angular 19.1 with TypeScript
   - Standalone components architecture
   - NgRx for state management
   - Tailwind CSS for styling
   - Route-based lazy loading

### Key Directories

- `api/` - API endpoint handlers (Orders, Products, Sellers, Authentication, etc.)
- `models/` - Database models (Order, Product, Customer, Seller, Account, etc.)
- `controllers/` - Web page controllers (Home, App, Sellers)
- `helpers/` - Utility classes (Router, Response, Request, Session, Logger)
- `filters/` - Security filters (SessionFilter, AccountFilter)
- `config/` - Configuration classes (Database, Session, Email, PayU)
- `routes/` - Route definitions (web routes, API routes)
- `public/pages/sellers/app/` - Angular seller panel application

## Development Commands

### PHP Backend

```bash
# Install dependencies
composer install

# Run cron jobs (scheduled tasks)
php Application.php --cron

# Import products from CSV
php Application.php --products

# Display version/help
php Application.php --version
php Application.php --help
```

### Angular Seller Panel

```bash
cd public/pages/sellers/app

# Install dependencies
npm install

# Development server (auto-reloads)
npm start
# or
ng serve

# Build for production
npm run build
# or
ng build

# Build with watching
npm run watch

# Run tests
npm test
```

### Running the Application

The project uses a routing entry point (`rt.php`) that:
- Redirects `/api/v1/*` to `api.php` for API requests
- Redirects all other routes to `index.php` for web pages
- Serves Angular static files directly when using PHP built-in server

For local development, you can use:
```bash
php -S localhost:8000 -t .
```

The Angular dev server runs on port 4200 by default.

## Architecture Patterns

### API Layer

All API endpoints are defined in `routes/api.php` with the pattern:
```php
$router->get('/endpoint', 'api\ClassName::method');
$router->post('/endpoint', 'api\ClassName::method');
```

API handlers in `api/` follow this structure:
- Use `SessionFilter::validateApiSession()` for authentication
- Use `AccountFilter::filterApiCustomerAccount()` for authorization
- Use `Request::getJson()` to read JSON body
- Use `Response::append('key', $data)` to add response data
- Use `Response::setCode($code)` to set HTTP status code

### Model Layer

Models in `models/`:
- Use `Connection::getConn()` for database access
- Follow naming convention: `Model::getModelById()`, `Model::getAll()`
- Implement `JsonSerializable` for automatic JSON serialization
- Use prepared statements for security (see existing examples)

### Security Filters

- **SessionFilter**: Validates user is logged in
- **AccountFilter**: Validates user has appropriate account type (customer, seller, admin)

### Angular Application

The Angular app uses:
- **Standalone Components**: No NgModule declarations
- **HTTP Interceptors**: `SessionInterceptor` handles 401 errors globally
- **Guards**: `authGuard` and `loginGuard` for route protection
- **Lazy Loading**: Routes use `loadComponent()` for code splitting
- **Services**: Singleton services in `src/app/*/services/` for business logic

API calls from Angular:
```typescript
import { environment } from '../environment';

this.http.get(`${environment.apiUrl}endpoint`, { withCredentials: true })
```

The `environment.apiUrl` switches between `http://localhost:8000/api/v1/` (dev) and `/api/v1/` (prod).

## Session Management

PHP sessions are configured in `config/SessionConfiguration.php`:
- Session name: `sid`
- Cookie lifetime: 1440 minutes (24 hours)
- SameSite: `Lax`
- Secure: false (development)

Session data is stored serialized in `$_SESSION`:
- `$_SESSION['account']` - User account object

## Important Files

- `rt.php` - Main entry point for all requests
- `api.php` - API entry point
- `index.php` - Web pages entry point
- `Application.php` - CLI application runner
- `composer.json` - PHP dependencies
- `public/pages/sellers/app/package.json` - Angular dependencies
- `public/pages/sellers/app/environment.ts` - API endpoint configuration

## Payment Integration

PayU payment gateway is configured in `config/PayUConfiguration.php` with credentials loaded from `.env`.

## Database

- MySQL/MariaDB
- Configuration in `config/DatabaseConfiguration.php`
- Models use mysqli with prepared statements
- SQL schema available in `u918235402_tymeros.sql`
