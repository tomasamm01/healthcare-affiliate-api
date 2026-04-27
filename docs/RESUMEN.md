# Resumen del Proyecto - Healthcare Affiliate API

## Descripción General
API REST profesional para sistema de gestión de afiliados de salud construida con Laravel 11.

## Componentes Implementados

### Estructura del Proyecto
```
app/
├── Enums/
│   └── AffiliateStatus.php          # Enum para estados (pending, active, suspended, inactive)
├── Events/
│   └── AffiliateUpdated.php          # Evento para cambios en afiliados
├── Http/
│   ├── Controllers/Api/V1/
│   │   ├── AffiliateController.php  # Endpoints de afiliados
│   │   ├── PlanController.php       # Endpoints de planes
│   │   ├── CoverageController.php   # Validación de cobertura
│   │   └── AuthController.php       # Autenticación
│   ├── Requests/
│   │   ├── Affiliate/               # Validadores de afiliados
│   │   ├── Plan/                    # Validadores de planes
│   │   └── ValidateCoverageRequest.php
│   └── Resources/
│       ├── AffiliateResource.php   # Transformador de respuestas afiliados
│       ├── PlanResource.php        # Transformador de respuestas planes
│       ├── AuditLogResource.php    # Transformador de logs de auditoría
│       └── FamilyGroupResource.php # Transformador de grupos familiares
├── Listeners/
│   └── LogAudit.php                # Listener para auditoría automática
├── Models/
│   ├── Affiliate.php                # Modelo Afiliado con soft deletes
│   ├── Plan.php                    # Modelo Plan
│   ├── AuditLog.php                # Modelo de logs de auditoría
│   └── User.php                    # Modelo Usuario con Sanctum
├── Providers/
│   ├── AppServiceProvider.php
│   ├── EventServiceProvider.php     # Registro de eventos y listeners
│   └── RouteServiceProvider.php
└── Services/
    ├── AffiliateService.php        # Lógica de negocio de afiliados
    └── CoverageService.php         # Lógica de validación de cobertura
```

### Base de Datos
**Migraciones:**
- `create_plans_table` - Planes de cobertura con JSON para detalles
- `create_affiliates_table` - Afiliados con soft deletes y self-relation
- `create_audit_logs_table` - Logs de auditoría
- `create_users_table` - Usuarios para autenticación
- `create_personal_access_tokens_table` - Tokens de Sanctum

**Factories:**
- `PlanFactory` - Genera planes con servicios de cobertura
- `AffiliateFactory` - Genera afiliados con estados variados
- `UserFactory` - Genera usuarios de prueba

**Seeders:**
- `PlanSeeder` - 4 planes realistas (Basic, Standard, Premium, Family)
- `AffiliateSeeder` - 10 afiliados con grupos familiares
- `DatabaseSeeder` - Ejecuta todos los seeders

### Autenticación
- Laravel Sanctum para token-based authentication
- Endpoints públicos: register, login
- Endpoints protegidos: logout, me, affiliates, plans, coverage
- Middleware `auth:sanctum` en rutas protegidas

### API Versionada
Todos los endpoints bajo `/api/v1/`:
- `/api/v1/auth/*` - Autenticación
- `/api/v1/affiliates/*` - Gestión de afiliados
- `/api/v1/plans/*` - Gestión de planes
- `/api/v1/coverage/*` - Validación de cobertura

## Endpoints Principales

### Autenticación
- `POST /api/v1/auth/register` - Registro de usuario
- `POST /api/v1/auth/login` - Login
- `POST /api/v1/auth/logout` - Logout (requiere auth)
- `GET /api/v1/auth/me` - Usuario actual (requiere auth)

### Afiliados
- `GET /api/v1/affiliates` - Listar afiliados (con filtros)
- `POST /api/v1/affiliates` - Crear afiliado
- `GET /api/v1/affiliates/{id}` - Ver afiliado
- `PUT/PATCH /api/v1/affiliates/{id}` - Actualizar afiliado
- `DELETE /api/v1/affiliates/{id}` - Eliminar afiliado (soft delete)
- `POST /api/v1/affiliates/{id}/status` - Cambiar estado
- `GET /api/v1/affiliates/{id}/status` - Ver estado
- `POST /api/v1/affiliates/{id}/dependents` - Agregar dependiente
- `GET /api/v1/affiliates/{id}/family-group` - Ver grupo familiar
- `DELETE /api/v1/affiliates/{id}/dependents/{dependent}` - Eliminar dependiente

### Planes
- `GET /api/v1/plans` - Listar planes
- `POST /api/v1/plans` - Crear plan
- `GET /api/v1/plans/{id}` - Ver plan
- `PUT/PATCH /api/v1/plans/{id}` - Actualizar plan
- `DELETE /api/v1/plans/{id}` - Eliminar plan

### Cobertura
- `POST /api/v1/coverage/validate` - Validar cobertura de servicio
- `GET /api/v1/coverage/affiliate/{id}` - Ver detalles de cobertura

## Estados de Afiliado
- `pending` - Pendiente de activación
- `active` - Activo (puede validar cobertura)
- `suspended` - Suspendido temporalmente
- `inactive` - Inactivo

## Buenas Prácticas Aplicadas

1. **Arquitectura Limpia**
   - Controllers livianos que delegan a Services
   - Lógica de negocio encapsulada en Services
   - Separación clara de responsabilidades

2. **Event-Driven Architecture**
   - Events para cambios críticos
   - Listeners para side-effects (auditoría)
   - Desacoplamiento de funcionalidades

3. **Soft Deletes**
   - Eliminación suave en entidades críticas (Affiliate)
   - Recuperación de datos eliminados
   - Historial de cambios

4. **Auditoría Automática**
   - Logging automático de cambios
   - Registro de old_values y new_values
   - Tracking de usuario que realizó el cambio

5. **API Versionada**
   - Endpoints versionados desde el inicio
   - Facilidad para evolucionar la API
   - Backward compatibility

6. **Type-Safe Enums**
   - PHP Enums para estados
   - Métodos helper en enums
   - Validación automática

7. **Relaciones Eloquent**
   - Self-relation para titular/dependientes
   - Relaciones bien definidas
   - Eager loading optimizado

8. **Validación Centralizada**
   - Form Requests para validación
   - Mensajes de error personalizados
   - Reglas reutilizables

9. **API Resources**
   - Transformación consistente de respuestas
   - Conditional relationship loading
   - Formato estandarizado

10. **Autenticación Profesional**
    - Laravel Sanctum
    - Token-based authentication
    - Rutas protegidas por middleware

## Instrucciones de Instalación

```bash
# 1. Instalar dependencias
composer install

# 2. Configurar entorno
cp .env.example .env
php artisan key:generate

# 3. Configurar base de datos en .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=healthcare_affiliate_api
DB_USERNAME=your_username
DB_PASSWORD=your_password

# 4. Ejecutar migraciones
php artisan migrate

# 5. Seedear datos de prueba
php artisan db:seed

# 6. Iniciar servidor
php artisan serve
```

## Ejemplo de Uso

### Login
```bash
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email": "admin@example.com", "password": "password"}'
```

### Crear Afiliado
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

### Validar Cobertura
```bash
curl -X POST http://localhost:8000/api/v1/coverage/validate \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {token}" \
  -d '{
    "affiliate_id": 1,
    "service_code": "CONSULT"
  }'
```

## Stack Tecnológico
- PHP 8.2+
- Laravel 11.x
- Laravel Sanctum 4.x
- MySQL/PostgreSQL/SQLite

## Características Destacadas
- Gestión completa de afiliados (CRUD)
- Grupos familiares (titular + dependientes)
- Validación de cobertura en tiempo real
- Auditoría automática de cambios
- Autenticación token-based
- API versionada
- Soft deletes
- Datos de prueba realistas
- Código limpio y mantenible
