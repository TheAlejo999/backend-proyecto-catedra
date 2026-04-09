# Sistema de Gestión de Flotas - API Backend

### Proyecto de Cátedra: Desarrollo Backend
**Escuela Superior de Economía y Negocios (ESEN)**

---

## Catedrático
* **José Manzanares**

---

## Integrantes del Equipo
* Kathleen Argueta
* Alejandro Durón
* Reina Sosa
* Roberto Milan
* Alejandro Orellana

**Fecha de entrega:** 9 de abril de 2026

---

## Descripción del Proyecto
Este sistema es una API robusta diseñada para centralizar y optimizar la gestión de flotas vehiculares. Permite el control exhaustivo de unidades, conductores, asignación de rutas, monitoreo de combustible y programación de mantenimientos preventivos y correctivos.

## Seguridad y Control de Acceso (RBAC)
El sistema implementa un modelo de seguridad basado en **Laravel Sanctum** para la autenticación y **Laravel Policies** para la autorización. Se han definido formalmente 3 roles:

1. **Administrador**: Posee gestión total sobre todas las entidades, usuarios y configuraciones de seguridad.
2. **Logística**: Perfil operativo con facultades para gestionar Rutas, Asignaciones de Viajes y Suministro de Combustible.
3. **Conductor**: Perfil de consulta. Solo tiene acceso de lectura a los recursos asignados (Vehículo, Rutas y Mantenimientos).

## Stack Tecnológico
- **Framework**: Laravel 11
- **Base de Datos**: MySQL
- **Autenticación**: Laravel Sanctum (Bearer Tokens)
- **Seguridad**: Policies & FormRequests
- **Documentación**: L5 Swagger (OpenAPI 3.0)

## Entidades del Sistema
- **Roles y Usuarios**: Gestión de perfiles y acceso.
- **Vehículos y Flotas**: Clasificación y estado de unidades.
- **Conductores**: Perfiles de manejo y asignaciones.
- **Rutas**: Catálogo de trayectos y consumos estimados.
- **Asignación de rutas**: Relación dinámica entre vehículos y rutas.
- **Suministro de Combustible**: Control de carga y actualización de nivel de tanque.
- **Mantenimientos**: Historial técnico y control de disponibilidad de vehículos.

## Guía de Instalación
1. **Clonar repositorio**: `git clone [url-del-repo]`
2. **Instalar dependencias**: `composer install`
3. **Configurar entorno**: `cp .env.example .env` puede usar el SGBD de su preferencia.
4. **Generar App Key**: `php artisan key:generate`
5. **Migraciones y Seeders**: `php artisan migrate:fresh --seed`
6. **Generar Documentación en swagger**:
   `composer require darkaonline/l5-swagger:"8.6.0" --update-with-dependencies`
   `php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider" --force`
   `php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider" --tag=views --force`
   `php artisan l5-swagger:generate`
   Agrega la siguiente variable a tu archivo `.env`: envL5_SWAGGER_GENERATE_ALWAYS=true

## Documentación de la API
Una vez iniciado el servidor, puedes acceder a la documentación interactiva hecha con Swagger en:
`http://[dominio-del-proyecto]/api/documentation`

Nota: si desea obtener la API KEY para probar la creacion de rutas, no dude en ponerse en contacto con nosotros.
