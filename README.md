# Healthcare Affiliate API

Professional REST API for healthcare affiliate management system built with Laravel 11.

## Features

- **Affiliate Management**: Full CRUD operations for healthcare affiliates
- **Family Groups**: Support for holders and dependents with hierarchical relationships
- **Plan Management**: Create and manage healthcare coverage plans
- **Real-time Coverage Validation**: Validate medical service coverage instantly
- **Audit Logging**: Automatic tracking of all critical entity changes
- **Authentication**: Token-based authentication using Laravel Sanctum
- **API Versioning**: Versioned endpoints (`/api/v1/`)
- **Clean Architecture**: Services layer for business logic separation

## Tech Stack

- **PHP**: 8.2+
- **Laravel**: 11.x
- **Laravel Sanctum**: 4.x (Authentication)
- **Database**: MySQL/PostgreSQL/SQLite

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

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| POST | `/api/v1/auth/register` | Register new user | No |
| POST | `/api/v1/auth/login` | Login user | No |
| POST | `/api/v1/auth/logout` | Logout user | Yes |
| GET | `/api/v1/auth/me` | Get current user | Yes |

### Affiliates

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/api/v1/affiliates` | List all affiliates | Yes |
| POST | `/api/v1/affiliates` | Create new affiliate | Yes |
| GET | `/api/v1/affiliates/{id}` | Get affiliate details | Yes |
| PUT/PATCH | `/api/v1/affiliates/{id}` | Update affiliate | Yes |
| DELETE | `/api/v1/affiliates/{id}` | Delete affiliate | Yes |
| POST | `/api/v1/affiliates/{id}/status` | Change affiliate status | Yes |
| GET | `/api/v1/affiliates/{id}/status` | Get affiliate status | Yes |
| POST | `/api/v1/affiliates/{id}/dependents` | Add dependent to holder | Yes |
| GET | `/api/v1/affiliates/{id}/family-group` | Get family group | Yes |
| DELETE | `/api/v1/affiliates/{id}/dependents/{dependent}` | Remove dependent | Yes |

### Plans

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/api/v1/plans` | List all plans | Yes |
| POST | `/api/v1/plans` | Create new plan | Yes |
| GET | `/api/v1/plans/{id}` | Get plan details | Yes |
| PUT/PATCH | `/api/v1/plans/{id}` | Update plan | Yes |
| DELETE | `/api/v1/plans/{id}` | Delete plan | Yes |

### Coverage

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| POST | `/api/v1/coverage/validate` | Validate service coverage | Yes |
| GET | `/api/v1/coverage/affiliate/{id}` | Get affiliate coverage details | Yes |

## Affiliate Statuses

- `pending`: Affiliate is pending activation
- `active`: Affiliate is active and can validate coverage
- `suspended`: Affiliate is temporarily suspended
- `inactive`: Affiliate is inactive

## Usage Examples

### Authentication

#### Register
```bash
curl -X POST http://localhost:8000/api/v1/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123"
  }'
```

#### Login
```bash
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

Use the returned `token` in the `Authorization` header for authenticated requests:
```bash
Authorization: Bearer {token}
```

### Create Affiliate
```bash
curl -X POST http://localhost:8000/api/v1/affiliates \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {token}" \
  -d '{
    "first_name": "Juan",
    "last_name": "García",
    "dni": "12345678",
    "plan_id": 1,
    "status": "active"
  }'
```

### Validate Coverage
```bash
curl -X POST http://localhost:8000/api/v1/coverage/validate \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {token}" \
  -d '{
    "affiliate_id": 1,
    "service_code": "CONSULT"
  }'
```

### Add Dependent
```bash
curl -X POST http://localhost:8000/api/v1/affiliates/1/dependents \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {token}" \
  -d '{
    "first_name": "Ana",
    "last_name": "García",
    "dni": "12345679"
  }'
```

## Project Structure

```
app/
├── Enums/
│   └── AffiliateStatus.php          # Affiliate status enum
├── Events/
│   └── AffiliateUpdated.php          # Event for affiliate changes
├── Http/
│   ├── Controllers/Api/V1/
│   │   ├── AffiliateController.php  # Affiliate endpoints
│   │   ├── PlanController.php       # Plan endpoints
│   │   ├── CoverageController.php   # Coverage validation
│   │   └── AuthController.php       # Authentication
│   ├── Requests/
│   │   ├── Affiliate/               # Affiliate validation
│   │   ├── Plan/                    # Plan validation
│   │   └── ValidateCoverageRequest.php
│   └── Resources/
│       ├── AffiliateResource.php   # Affiliate response transformer
│       ├── PlanResource.php        # Plan response transformer
│       └── AuditLogResource.php    # Audit log transformer
├── Listeners/
│   └── LogAudit.php                # Audit logging listener
├── Models/
│   ├── Affiliate.php                # Affiliate model
│   ├── Plan.php                    # Plan model
│   ├── AuditLog.php                # Audit log model
│   └── User.php                    # User model
└── Services/
    ├── AffiliateService.php        # Affiliate business logic
    └── CoverageService.php         # Coverage validation logic

database/
├── factories/                      # Model factories
├── migrations/                      # Database migrations
└── seeders/                         # Database seeders

routes/
├── api.php                         # API routes
└── web.php                         # Web routes
```

## Architecture Patterns

### Service Layer
Business logic is encapsulated in Services to keep Controllers lean:
- `AffiliateService`: Handles affiliate operations and family group management
- `CoverageService`: Handles coverage validation logic

### Event-Driven Architecture
Critical changes trigger events for side effects:
- `AffiliateUpdated` event fires on create/update/delete
- `LogAudit` listener automatically logs changes to `audit_logs` table

### Request Validation
Form Requests handle validation:
- Centralized validation rules
- Custom error messages
- Authorization checks

### API Resources
Resources transform models into API responses:
- Consistent response format
- Conditional relationship loading
- Data transformation

## Best Practices Applied

1. **Clean Architecture**: Separation of concerns with Services layer
2. **Soft Deletes**: Critical entities use soft deletes
3. **Audit Trail**: Automatic logging of all changes
4. **API Versioning**: Versioned endpoints from the start
5. **Enum Usage**: Type-safe status management
6. **Relationship Management**: Well-defined Eloquent relationships
7. **Validation**: Request-level validation with custom messages
8. **Error Handling**: Proper HTTP status codes and error responses
9. **Pagination**: Consistent pagination for list endpoints
10. **Authentication**: Token-based auth with Sanctum

## Testing

Run the test suite:
```bash
php artisan test
```

## License

This project is licensed under the MIT License.
