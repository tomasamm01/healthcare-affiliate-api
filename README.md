# Healthcare Affiliate API

[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat&logo=php&logoColor=white)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=flat&logo=laravel&logoColor=white)](https://laravel.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![API](https://img.shields.io/badge/API-REST-009688?style=flat)](https://en.wikipedia.org/wiki/Representational_state_transfer)

Professional REST API for healthcare affiliate management system built with Laravel 11.

## Highlights

- **Family Group Management**: Hierarchical holder-dependent relationships with automatic validation
- **Real-time Coverage Validation**: Instant medical service coverage checks
- **Event-Driven Audit Trail**: Automatic logging of all critical entity changes
- **Clean Architecture**: Service layer with separated business logic
- **Type-Safe Status Management**: PHP Enums for affiliate states
- **API Versioning**: Structured `/api/v1/` endpoints from day one

## Architecture

```
┌─────────────────┐
│   API Layer     │ ← Controllers (thin, orchestration only)
├─────────────────┤
│  Service Layer  │ ← Business logic (AffiliateService, CoverageService)
├─────────────────┤
│   Domain Layer  │ ← Models, Enums, Events, Listeners
├─────────────────┤
│  Data Layer     │ ← Migrations, Seeders, Factories
└─────────────────┘
```

### Design Decisions

- **Service Layer Pattern**: Controllers delegate to services for testability and reusability
- **Event-Driven Audit**: `AffiliateUpdated` event triggers `LogAudit` listener for non-intrusive logging
- **API Resources**: Transform models with consistent response format and conditional relationships
- **Form Requests**: Centralized validation with custom messages and authorization
- **Soft Deletes**: Critical entities (Affiliates, Plans) preserve data integrity
- **Enum-Based Status**: `AffiliateStatus` enum prevents invalid states at compile time

## Tech Stack

| Component | Technology |
|-----------|------------|
| Runtime | PHP 8.2+ |
| Framework | Laravel 11.x |
| Authentication | Laravel Sanctum 4.x |
| Database | MySQL / PostgreSQL / SQLite |
| Testing | PHPUnit 11.x |
| Code Style | Laravel Pint |

## Installation

### Prerequisites

- PHP 8.2 or higher
- Composer
- MySQL/PostgreSQL/SQLite

### Setup Steps

1. Clone the repository:
```bash
git clone https://github.com/tomasamm01/healthcare-affiliate-api.git
cd healthcare-affiliate-api
```

2. Install dependencies:
```bash
composer install
```

3. Configure environment:
```bash
cp .env.example .env
php artisan key:generate
```

4. Configure your database in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=healthcare_affiliate_api
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. Run migrations:
```bash
php artisan migrate
```

6. Seed the database (optional):
```bash
php artisan db:seed
```

7. Start the development server:
```bash
php artisan serve
```

The API will be available at `http://localhost:8000`

## API Endpoints

### Authentication
| Method | Endpoint | Auth |
|--------|----------|------|
| POST | `/api/v1/auth/register` | No |
| POST | `/api/v1/auth/login` | No |
| POST | `/api/v1/auth/logout` | Yes |
| GET | `/api/v1/auth/me` | Yes |

### Affiliates
| Method | Endpoint | Auth |
|--------|----------|------|
| GET | `/api/v1/affiliates` | Yes |
| POST | `/api/v1/affiliates` | Yes |
| GET | `/api/v1/affiliates/{id}` | Yes |
| PUT/PATCH | `/api/v1/affiliates/{id}` | Yes |
| DELETE | `/api/v1/affiliates/{id}` | Yes |
| POST | `/api/v1/affiliates/{id}/status` | Yes |
| GET | `/api/v1/affiliates/{id}/family-group` | Yes |
| POST | `/api/v1/affiliates/{id}/dependents` | Yes |
| DELETE | `/api/v1/affiliates/{id}/dependents/{dependent}` | Yes |

### Plans
| Method | Endpoint | Auth |
|--------|----------|------|
| GET | `/api/v1/plans` | Yes |
| POST | `/api/v1/plans` | Yes |
| GET | `/api/v1/plans/{id}` | Yes |
| PUT/PATCH | `/api/v1/plans/{id}` | Yes |
| DELETE | `/api/v1/plans/{id}` | Yes |

### Coverage
| Method | Endpoint | Auth |
|--------|----------|------|
| POST | `/api/v1/coverage/validate` | Yes |
| GET | `/api/v1/coverage/affiliate/{id}` | Yes |

### Affiliate Statuses
- `pending` - Pending activation
- `active` - Can validate coverage
- `suspended` - Temporarily suspended
- `inactive` - Inactive

## Usage Examples

### Authentication
```bash
# Register
curl -X POST http://localhost:8000/api/v1/auth/register \
  -H "Content-Type: application/json" \
  -d '{"name":"John Doe","email":"john@example.com","password":"password123"}'

# Login (returns token)
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"john@example.com","password":"password123"}'
```

Use the returned token in the `Authorization` header:
```bash
-H "Authorization: Bearer {token}"
```

### Create Affiliate
```bash
curl -X POST http://localhost:8000/api/v1/affiliates \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {token}" \
  -d '{"first_name":"Juan","last_name":"García","dni":"12345678","plan_id":1,"status":"active"}'
```

### Validate Coverage
```bash
curl -X POST http://localhost:8000/api/v1/coverage/validate \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {token}" \
  -d '{"affiliate_id":1,"service_code":"CONSULT"}'
```

### Add Dependent
```bash
curl -X POST http://localhost:8000/api/v1/affiliates/1/dependents \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {token}" \
  -d '{"first_name":"Ana","last_name":"García","dni":"12345679"}'
```

## Project Structure

```
app/
├── Enums/
│   └── AffiliateStatus.php
├── Events/
│   └── AffiliateUpdated.php
├── Http/
│   ├── Controllers/Api/V1/
│   │   ├── AffiliateController.php
│   │   ├── PlanController.php
│   │   ├── CoverageController.php
│   │   └── AuthController.php
│   ├── Requests/
│   │   ├── Affiliate/
│   │   ├── Plan/
│   │   └── ValidateCoverageRequest.php
│   └── Resources/
│       ├── AffiliateResource.php
│       ├── PlanResource.php
│       ├── AuditLogResource.php
│       └── FamilyGroupResource.php
├── Listeners/
│   └── LogAudit.php
├── Models/
│   ├── Affiliate.php
│   ├── Plan.php
│   ├── AuditLog.php
│   └── User.php
└── Services/
    ├── AffiliateService.php
    └── CoverageService.php

database/
├── factories/
├── migrations/
└── seeders/

routes/
├── api.php
└── web.php
```

## Testing

```bash
php artisan test
```

## License

MIT License - see [LICENSE](LICENSE) for details.
