# Tests

Este directorio contiene las pruebas unitarias y de integración para la API de afiliados de salud.

## Estructura

- `Unit/` - Pruebas unitarias para modelos y componentes individuales
  - `Models/` - Tests para modelos (Affiliate, Plan, AuditLog)
- `Feature/` - Pruebas de integración para endpoints API
  - `AuthTest.php` - Tests de autenticación (registro, login, logout)
  - `AffiliateApiTest.php` - Tests para endpoints de afiliados
  - `PlanApiTest.php` - Tests para endpoints de planes

## Ejecutar Tests

### Ejecutar todos los tests
```bash
php artisan test
```

O usando PHPUnit directamente:
```bash
./vendor/bin/phpunit
```

### Ejecutar solo tests unitarios
```bash
php artisan test --testsuite=Unit
```

### Ejecutar solo tests de integración
```bash
php artisan test --testsuite=Feature
```

### Ejecutar un test específico
```bash
php artisan test --filter AffiliateTest
```

### Ejecutar tests con cobertura
```bash
php artisan test --coverage
```

## Configuración

Los tests usan:
- Base de datos SQLite en memoria (`:memory:`)
- Cache driver `array`
- Session driver `array`
- Queue connection `sync`

Esta configuración está definida en `phpunit.xml`.

## Factories

Los tests utilizan factories definidos en `database/factories/`:
- `AffiliateFactory` - Genera datos de prueba para afiliados
- `PlanFactory` - Genera datos de prueba para planes
- `UserFactory` - Genera datos de prueba para usuarios

## Convenciones

- Los tests unitarios prueban la lógica de modelos, scopes, relaciones y casts
- Los tests de integración prueban endpoints HTTP completos
- Se usa `RefreshDatabase` para limpiar la base de datos entre tests
- Se usa `actingAs` para simular autenticación con Sanctum
