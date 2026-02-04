# CRUD de Usuarios - Laravel API

Este proyecto es una API REST profesional para la gestión de empleados, desarrollada con Laravel 11. Incluye validaciones avanzadas, transformación de datos con Resources y borrado lógico (Soft Deletes).

## Características principales
- **CRUD Completo**: Creación, lectura, actualización y eliminación de usuarios.
- **Validaciones**: Reglas estrictas para DUI (formato 00000000-0), correos únicos y fechas.
- **Soft Deletes**: Los registros no se borran físicamente; se pueden filtrar y restaurar.
- **API Resources**: Respuestas JSON estandarizadas y limpias.

## Requisitos
- PHP 8.2+
- Composer
- Laravel Herd / Valet / Artisan Serve
- MySQL / PostgreSQL

## Endpoints Principales
- `GET /api/v1/users`: Lista usuarios activos.
- `GET /api/v1/users?is_trashed=true`: Lista solo usuarios eliminados.
- `POST /api/v1/users`: Crea un usuario.
- `PUT/PATCH /api/v1/users/{id}`: Actualiza un usuario.
- `DELETE /api/v1/users/{id}`: Borrado lógico.
- `POST /api/v1/users/{id}/restore`: Restaura un usuario eliminado.
