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

## Seguridad y Control de Acceso 
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
   Agrega la siguiente variable a tu archivo `.env`: `envL5_SWAGGER_GENERATE_ALWAYS=true`

## Documentación de la API
Una vez hayas configurado el proyecto correctamente, puedes acceder a la documentación interactiva hecha con Swagger en:
`http://[dominio-del-proyecto]/api/documentation`

Nota: si desea obtener la API KEY para probar la creacion de rutas o existen dudas, no dude en ponerse en contacto con nosotros.

## Endpoints en postman (hacer export)

{
  "info": {
    "_postman_id": "306252f9-d181-44cd-b747-a7af687dd75c",
    "name": "Backend Proyecto Cátedra API",
    "description": "Colección Postman v2.1 generada a partir de routes/api.php y los controladores subidos. Usa {{baseUrl}} = http://backend-proyecto-catedra.test y agrega /api/v1 en cada request. Incluye requests para todos los endpoints detectados, con payloads de prueba y captura automática de IDs/token cuando la respuesta lo permite.\n\nVariables que probablemente debes completar antes de correr todo de punta a punta:\n- email / password: credenciales válidas para /login\n- existingUserId: user_id existente para crear un conductor (no se subió endpoint de usuarios)\n- vehicleId / routeId / etc. se autocompletan al crear registros.\n\nNota: algunos flujos de soft delete/restore y asignaciones dependen del estado actual de datos y políticas/permissions.",
    "schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json",
    "_exporter_id": "51672627",
    "_collection_link": "https://go.postman.co/collection/51672627-306252f9-d181-44cd-b747-a7af687dd75c?source=collection_link"
  },
  "item": [
    {
      "name": "Auth",
      "item": [
        {
          "name": "Login",
          "event": [
            {
              "listen": "test",
              "script": {
                "type": "text/javascript",
                "exec": [
                  "pm.test(\"Login ok\", function () { pm.expect(pm.response.code).to.be.oneOf([200, 201]); });",
                  "const json = pm.response.json();",
                  "if (json.access_token) pm.collectionVariables.set(\"access_token\", json.access_token);",
                  "if (json.user && json.user.id) pm.collectionVariables.set(\"login_user_id\", String(json.user.id));"
                ]
              }
            }
          ],
          "request": {
            "auth": {
              "type": "noauth"
            },
            "method": "POST",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              },
              {
                "key": "Content-Type",
                "value": "application/json"
              }
            ],
            "body": {
              "mode": "raw",
              "raw": "{\n  \"email\": \"{{email}}\",\n  \"password\": \"{{password}}\"\n}"
            },
            "url": {
              "raw": "{{baseUrl}}/api/v1/login",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "login"
              ]
            }
          },
          "response": []
        },
        {
          "name": "Logout",
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "POST",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              }
            ],
            "url": {
              "raw": "{{baseUrl}}/api/v1/logout",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "logout"
              ]
            }
          },
          "response": []
        }
      ]
    },
    {
      "name": "Roles",
      "item": [
        {
          "name": "Listar roles",
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "GET",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              }
            ],
            "url": {
              "raw": "{{baseUrl}}/api/v1/roles",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "roles"
              ]
            }
          },
          "response": []
        },
        {
          "name": "Listar roles eliminados",
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "GET",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              }
            ],
            "url": {
              "raw": "{{baseUrl}}/api/v1/roles?trashed=true",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "roles"
              ],
              "query": [
                {
                  "key": "trashed",
                  "value": "true"
                }
              ]
            }
          },
          "response": []
        },
        {
          "name": "Filtrar roles por nombre",
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "GET",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              }
            ],
            "url": {
              "raw": "{{baseUrl}}/api/v1/roles?name=Admin",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "roles"
              ],
              "query": [
                {
                  "key": "name",
                  "value": "Admin"
                }
              ]
            }
          },
          "response": []
        },
        {
          "name": "Crear rol",
          "event": [
            {
              "listen": "test",
              "script": {
                "type": "text/javascript",
                "exec": [
                  "pm.test(\"Status code is < 500\", function () { pm.expect(pm.response.code).to.be.below(500); });",
                  "let json = {};",
                  "try { json = pm.response.json(); } catch (e) {}",
                  "function firstId(obj) {",
                  "  if (!obj) return undefined;",
                  "  if (obj.id !== undefined) return obj.id;",
                  "  if (obj.data) {",
                  "    if (Array.isArray(obj.data) && obj.data.length && obj.data[0].id !== undefined) return obj.data[0].id;",
                  "    if (obj.data.id !== undefined) return obj.data.id;",
                  "  }",
                  "  if (obj.vehicle_route && obj.vehicle_route.id !== undefined) return obj.vehicle_route.id;",
                  "  return undefined;",
                  "}",
                  "",
                  "const id = firstId(json);",
                  "if (id !== undefined) pm.collectionVariables.set(\"roleId\", String(id));"
                ]
              }
            }
          ],
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "POST",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              },
              {
                "key": "Content-Type",
                "value": "application/json"
              }
            ],
            "body": {
              "mode": "raw",
              "raw": "{\n  \"name\": \"Supervisor QA\"\n}"
            },
            "url": {
              "raw": "{{baseUrl}}/api/v1/roles",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "roles"
              ]
            }
          },
          "response": []
        },
        {
          "name": "Ver rol",
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "GET",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              }
            ],
            "url": {
              "raw": "{{baseUrl}}/api/v1/roles/{{roleId}}",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "roles",
                "{{roleId}}"
              ]
            }
          },
          "response": []
        },
        {
          "name": "Actualizar rol",
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "PUT",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              },
              {
                "key": "Content-Type",
                "value": "application/json"
              }
            ],
            "body": {
              "mode": "raw",
              "raw": "{\n  \"name\": \"Supervisor Operaciones\"\n}"
            },
            "url": {
              "raw": "{{baseUrl}}/api/v1/roles/{{roleId}}",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "roles",
                "{{roleId}}"
              ]
            }
          },
          "response": []
        },
        {
          "name": "Eliminar rol",
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "DELETE",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              }
            ],
            "url": {
              "raw": "{{baseUrl}}/api/v1/roles/{{roleId}}",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "roles",
                "{{roleId}}"
              ]
            }
          },
          "response": []
        },
        {
          "name": "Restaurar rol",
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "POST",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              }
            ],
            "url": {
              "raw": "{{baseUrl}}/api/v1/roles/{{roleId}}/restore",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "roles",
                "{{roleId}}",
                "restore"
              ]
            }
          },
          "response": []
        }
      ]
    },
    {
      "name": "Flotas",
      "item": [
        {
          "name": "Listar flotas",
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "GET",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              }
            ],
            "url": {
              "raw": "{{baseUrl}}/api/v1/fleets",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "fleets"
              ]
            }
          },
          "response": []
        },
        {
          "name": "Listar flotas eliminadas",
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "GET",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              }
            ],
            "url": {
              "raw": "{{baseUrl}}/api/v1/fleets?trashed=true",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "fleets"
              ],
              "query": [
                {
                  "key": "trashed",
                  "value": "true"
                }
              ]
            }
          },
          "response": []
        },
        {
          "name": "Crear flota",
          "event": [
            {
              "listen": "test",
              "script": {
                "type": "text/javascript",
                "exec": [
                  "pm.test(\"Status code is < 500\", function () { pm.expect(pm.response.code).to.be.below(500); });",
                  "let json = {};",
                  "try { json = pm.response.json(); } catch (e) {}",
                  "function firstId(obj) {",
                  "  if (!obj) return undefined;",
                  "  if (obj.id !== undefined) return obj.id;",
                  "  if (obj.data) {",
                  "    if (Array.isArray(obj.data) && obj.data.length && obj.data[0].id !== undefined) return obj.data[0].id;",
                  "    if (obj.data.id !== undefined) return obj.data.id;",
                  "  }",
                  "  if (obj.vehicle_route && obj.vehicle_route.id !== undefined) return obj.vehicle_route.id;",
                  "  return undefined;",
                  "}",
                  "",
                  "const id = firstId(json);",
                  "if (id !== undefined) pm.collectionVariables.set(\"fleetId\", String(id));"
                ]
              }
            }
          ],
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "POST",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              },
              {
                "key": "Content-Type",
                "value": "application/json"
              }
            ],
            "body": {
              "mode": "raw",
              "raw": "{\n  \"name\": \"Flota Norte QA\",\n  \"type\": \"liviana\",\n  \"description\": \"Flota de prueba creada desde Postman\"\n}"
            },
            "url": {
              "raw": "{{baseUrl}}/api/v1/fleets",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "fleets"
              ]
            }
          },
          "response": []
        },
        {
          "name": "Ver flota",
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "GET",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              }
            ],
            "url": {
              "raw": "{{baseUrl}}/api/v1/fleets/{{fleetId}}",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "fleets",
                "{{fleetId}}"
              ]
            }
          },
          "response": []
        },
        {
          "name": "Actualizar flota",
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "PUT",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              },
              {
                "key": "Content-Type",
                "value": "application/json"
              }
            ],
            "body": {
              "mode": "raw",
              "raw": "{\n  \"name\": \"Flota Norte QA Actualizada\",\n  \"type\": \"pesada\",\n  \"description\": \"Descripción actualizada\"\n}"
            },
            "url": {
              "raw": "{{baseUrl}}/api/v1/fleets/{{fleetId}}",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "fleets",
                "{{fleetId}}"
              ]
            }
          },
          "response": []
        },
        {
          "name": "Asignar vehículos a flota",
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "POST",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              },
              {
                "key": "Content-Type",
                "value": "application/json"
              }
            ],
            "body": {
              "mode": "raw",
              "raw": "{\n  \"vehicles\": [\n    \"{{vehicleId}}\"\n  ]\n}"
            },
            "url": {
              "raw": "{{baseUrl}}/api/v1/fleets/{{fleetId}}/vehicles",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "fleets",
                "{{fleetId}}",
                "vehicles"
              ]
            }
          },
          "response": []
        },
        {
          "name": "Desvincular vehículo de flota",
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "DELETE",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              }
            ],
            "url": {
              "raw": "{{baseUrl}}/api/v1/fleets/{{fleetId}}/vehicles/{{vehicleId}}",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "fleets",
                "{{fleetId}}",
                "vehicles",
                "{{vehicleId}}"
              ]
            }
          },
          "response": []
        },
        {
          "name": "Eliminar flota",
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "DELETE",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              }
            ],
            "url": {
              "raw": "{{baseUrl}}/api/v1/fleets/{{fleetId}}",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "fleets",
                "{{fleetId}}"
              ]
            }
          },
          "response": []
        },
        {
          "name": "Restaurar flota",
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "POST",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              }
            ],
            "url": {
              "raw": "{{baseUrl}}/api/v1/fleets/{{fleetId}}/restore",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "fleets",
                "{{fleetId}}",
                "restore"
              ]
            }
          },
          "response": []
        }
      ]
    },
    {
      "name": "Vehículos",
      "item": [
        {
          "name": "Listar vehículos",
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "GET",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              }
            ],
            "url": {
              "raw": "{{baseUrl}}/api/v1/vehicles",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "vehicles"
              ]
            }
          },
          "response": []
        },
        {
          "name": "Listar vehículos eliminados",
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "GET",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              }
            ],
            "url": {
              "raw": "{{baseUrl}}/api/v1/vehicles?trashed=true",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "vehicles"
              ],
              "query": [
                {
                  "key": "trashed",
                  "value": "true"
                }
              ]
            }
          },
          "response": []
        },
        {
          "name": "Filtrar vehículos",
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "GET",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              }
            ],
            "url": {
              "raw": "{{baseUrl}}/api/v1/vehicles?plate=P123456&year=2024&type=camion&status=disponible&fuel=60&capacity=5000&mileage=50000",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "vehicles"
              ],
              "query": [
                {
                  "key": "plate",
                  "value": "P123456"
                },
                {
                  "key": "year",
                  "value": "2024"
                },
                {
                  "key": "type",
                  "value": "camion"
                },
                {
                  "key": "status",
                  "value": "disponible"
                },
                {
                  "key": "fuel",
                  "value": "60"
                },
                {
                  "key": "capacity",
                  "value": "5000"
                },
                {
                  "key": "mileage",
                  "value": "50000"
                }
              ]
            }
          },
          "response": []
        },
        {
          "name": "Crear vehículo",
          "event": [
            {
              "listen": "test",
              "script": {
                "type": "text/javascript",
                "exec": [
                  "pm.test(\"Status code is < 500\", function () { pm.expect(pm.response.code).to.be.below(500); });",
                  "let json = {};",
                  "try { json = pm.response.json(); } catch (e) {}",
                  "function firstId(obj) {",
                  "  if (!obj) return undefined;",
                  "  if (obj.id !== undefined) return obj.id;",
                  "  if (obj.data) {",
                  "    if (Array.isArray(obj.data) && obj.data.length && obj.data[0].id !== undefined) return obj.data[0].id;",
                  "    if (obj.data.id !== undefined) return obj.data.id;",
                  "  }",
                  "  if (obj.vehicle_route && obj.vehicle_route.id !== undefined) return obj.vehicle_route.id;",
                  "  return undefined;",
                  "}",
                  "",
                  "const id = firstId(json);",
                  "if (id !== undefined) pm.collectionVariables.set(\"vehicleId\", String(id));"
                ]
              }
            }
          ],
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "POST",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              },
              {
                "key": "Content-Type",
                "value": "application/json"
              }
            ],
            "body": {
              "mode": "raw",
              "raw": "{\n  \"plate_number\": \"PQA-1001\",\n  \"model\": \"Actros\",\n  \"brand\": \"Mercedes\",\n  \"year\": 2024,\n  \"type\": \"camion\",\n  \"capacity_weight_kg\": 5000,\n  \"current_mileage\": 50000,\n  \"fuel_percentage\": 80,\n  \"tank_capacity_gallons\": 150,\n  \"fuel_consumption_per_km\": 0.35,\n  \"status\": \"disponible\"\n}"
            },
            "url": {
              "raw": "{{baseUrl}}/api/v1/vehicles",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "vehicles"
              ]
            }
          },
          "response": []
        },
        {
          "name": "Ver vehículo",
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "GET",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              }
            ],
            "url": {
              "raw": "{{baseUrl}}/api/v1/vehicles/{{vehicleId}}",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "vehicles",
                "{{vehicleId}}"
              ]
            }
          },
          "response": []
        },
        {
          "name": "Actualizar vehículo",
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "PUT",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              },
              {
                "key": "Content-Type",
                "value": "application/json"
              }
            ],
            "body": {
              "mode": "raw",
              "raw": "{\n  \"plate_number\": \"PQA-1001\",\n  \"model\": \"Actros 1845\",\n  \"brand\": \"Mercedes-Benz\",\n  \"year\": 2025,\n  \"type\": \"camion\",\n  \"capacity_weight_kg\": 5200,\n  \"current_mileage\": 50500,\n  \"fuel_percentage\": 78,\n  \"tank_capacity_gallons\": 150,\n  \"fuel_consumption_per_km\": 0.36,\n  \"status\": \"disponible\"\n}"
            },
            "url": {
              "raw": "{{baseUrl}}/api/v1/vehicles/{{vehicleId}}",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "vehicles",
                "{{vehicleId}}"
              ]
            }
          },
          "response": []
        },
        {
          "name": "Eliminar vehículo",
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "DELETE",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              }
            ],
            "url": {
              "raw": "{{baseUrl}}/api/v1/vehicles/{{vehicleId}}",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "vehicles",
                "{{vehicleId}}"
              ]
            }
          },
          "response": []
        },
        {
          "name": "Restaurar vehículo",
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "POST",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              }
            ],
            "url": {
              "raw": "{{baseUrl}}/api/v1/vehicles/{{vehicleId}}/restore",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "vehicles",
                "{{vehicleId}}",
                "restore"
              ]
            }
          },
          "response": []
        }
      ]
    },
    {
      "name": "Conductores",
      "item": [
        {
          "name": "Listar conductores",
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "GET",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              }
            ],
            "url": {
              "raw": "{{baseUrl}}/api/v1/drivers",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "drivers"
              ]
            }
          },
          "response": []
        },
        {
          "name": "Listar conductores disponibles",
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "GET",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              }
            ],
            "url": {
              "raw": "{{baseUrl}}/api/v1/drivers?available=true",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "drivers"
              ],
              "query": [
                {
                  "key": "available",
                  "value": "true"
                }
              ]
            }
          },
          "response": []
        },
        {
          "name": "Listar conductores eliminados",
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "GET",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              }
            ],
            "url": {
              "raw": "{{baseUrl}}/api/v1/drivers?trashed=true",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "drivers"
              ],
              "query": [
                {
                  "key": "trashed",
                  "value": "true"
                }
              ]
            }
          },
          "response": []
        },
        {
          "name": "Crear conductor",
          "event": [
            {
              "listen": "test",
              "script": {
                "type": "text/javascript",
                "exec": [
                  "pm.test(\"Status code is < 500\", function () { pm.expect(pm.response.code).to.be.below(500); });",
                  "let json = {};",
                  "try { json = pm.response.json(); } catch (e) {}",
                  "function firstId(obj) {",
                  "  if (!obj) return undefined;",
                  "  if (obj.id !== undefined) return obj.id;",
                  "  if (obj.data) {",
                  "    if (Array.isArray(obj.data) && obj.data.length && obj.data[0].id !== undefined) return obj.data[0].id;",
                  "    if (obj.data.id !== undefined) return obj.data.id;",
                  "  }",
                  "  if (obj.vehicle_route && obj.vehicle_route.id !== undefined) return obj.vehicle_route.id;",
                  "  return undefined;",
                  "}",
                  "",
                  "const id = firstId(json);",
                  "if (id !== undefined) pm.collectionVariables.set(\"driverId\", String(id));"
                ]
              }
            }
          ],
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "POST",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              },
              {
                "key": "Content-Type",
                "value": "application/json"
              }
            ],
            "body": {
              "mode": "raw",
              "raw": "{\n  \"user_id\": \"{{existingUserId}}\",\n  \"license_number\": \"LIC-QA-1001\",\n  \"license_expiration\": \"2099-12-31\"\n}"
            },
            "url": {
              "raw": "{{baseUrl}}/api/v1/drivers",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "drivers"
              ]
            },
            "description": "Requiere un user_id existente. Ajusta {{existingUserId}} antes de ejecutar."
          },
          "response": []
        },
        {
          "name": "Ver conductor",
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "GET",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              }
            ],
            "url": {
              "raw": "{{baseUrl}}/api/v1/drivers/{{driverId}}",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "drivers",
                "{{driverId}}"
              ]
            }
          },
          "response": []
        },
        {
          "name": "Actualizar conductor",
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "PUT",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              },
              {
                "key": "Content-Type",
                "value": "application/json"
              }
            ],
            "body": {
              "mode": "raw",
              "raw": "{\n  \"license_number\": \"LIC-QA-1001-UPDATED\",\n  \"license_expiration\": \"2099-12-31\"\n}"
            },
            "url": {
              "raw": "{{baseUrl}}/api/v1/drivers/{{driverId}}",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "drivers",
                "{{driverId}}"
              ]
            }
          },
          "response": []
        },
        {
          "name": "Asignar conductor a vehículo",
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "POST",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              },
              {
                "key": "Content-Type",
                "value": "application/json"
              }
            ],
            "body": {
              "mode": "raw",
              "raw": "{\n  \"vehicle_id\": \"{{vehicleId}}\"\n}"
            },
            "url": {
              "raw": "{{baseUrl}}/api/v1/drivers/{{driverId}}/assign",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "drivers",
                "{{driverId}}",
                "assign"
              ]
            }
          },
          "response": []
        },
        {
          "name": "Desvincular conductor de vehículo",
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "DELETE",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              }
            ],
            "url": {
              "raw": "{{baseUrl}}/api/v1/drivers/{{driverId}}/assign",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "drivers",
                "{{driverId}}",
                "assign"
              ]
            }
          },
          "response": []
        },
        {
          "name": "Eliminar conductor",
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "DELETE",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              }
            ],
            "url": {
              "raw": "{{baseUrl}}/api/v1/drivers/{{driverId}}",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "drivers",
                "{{driverId}}"
              ]
            }
          },
          "response": []
        },
        {
          "name": "Restaurar conductor",
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "POST",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              }
            ],
            "url": {
              "raw": "{{baseUrl}}/api/v1/drivers/{{driverId}}/restore",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "drivers",
                "{{driverId}}",
                "restore"
              ]
            }
          },
          "response": []
        }
      ]
    },
    {
      "name": "Rutas",
      "item": [
        {
          "name": "Listar rutas",
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "GET",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              }
            ],
            "url": {
              "raw": "{{baseUrl}}/api/v1/routes",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "routes"
              ]
            }
          },
          "response": []
        },
        {
          "name": "Listar rutas eliminadas",
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "GET",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              }
            ],
            "url": {
              "raw": "{{baseUrl}}/api/v1/routes?trashed=true",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "routes"
              ],
              "query": [
                {
                  "key": "trashed",
                  "value": "true"
                }
              ]
            }
          },
          "response": []
        },
        {
          "name": "Filtrar rutas",
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "GET",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              }
            ],
            "url": {
              "raw": "{{baseUrl}}/api/v1/routes?origin=San Salvador&destination=Santa Ana",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "routes"
              ],
              "query": [
                {
                  "key": "origin",
                  "value": "San Salvador"
                },
                {
                  "key": "destination",
                  "value": "Santa Ana"
                }
              ]
            }
          },
          "response": []
        },
        {
          "name": "Crear ruta",
          "event": [
            {
              "listen": "test",
              "script": {
                "type": "text/javascript",
                "exec": [
                  "pm.test(\"Status code is < 500\", function () { pm.expect(pm.response.code).to.be.below(500); });",
                  "let json = {};",
                  "try { json = pm.response.json(); } catch (e) {}",
                  "function firstId(obj) {",
                  "  if (!obj) return undefined;",
                  "  if (obj.id !== undefined) return obj.id;",
                  "  if (obj.data) {",
                  "    if (Array.isArray(obj.data) && obj.data.length && obj.data[0].id !== undefined) return obj.data[0].id;",
                  "    if (obj.data.id !== undefined) return obj.data.id;",
                  "  }",
                  "  if (obj.vehicle_route && obj.vehicle_route.id !== undefined) return obj.vehicle_route.id;",
                  "  return undefined;",
                  "}",
                  "",
                  "const id = firstId(json);",
                  "if (id !== undefined) pm.collectionVariables.set(\"routeId\", String(id));"
                ]
              }
            }
          ],
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "POST",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              },
              {
                "key": "Content-Type",
                "value": "application/json"
              }
            ],
            "body": {
              "mode": "raw",
              "raw": "{\n  \"origin\": \"San Salvador, El Salvador\",\n  \"destination\": \"Santa Ana, El Salvador\"\n}"
            },
            "url": {
              "raw": "{{baseUrl}}/api/v1/routes",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "routes"
              ]
            }
          },
          "response": []
        },
        {
          "name": "Ver ruta",
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "GET",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              }
            ],
            "url": {
              "raw": "{{baseUrl}}/api/v1/routes/{{routeId}}",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "routes",
                "{{routeId}}"
              ]
            }
          },
          "response": []
        },
        {
          "name": "Actualizar ruta",
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "PUT",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              },
              {
                "key": "Content-Type",
                "value": "application/json"
              }
            ],
            "body": {
              "mode": "raw",
              "raw": "{\n  \"origin\": \"San Salvador, El Salvador\",\n  \"destination\": \"Sonsonate, El Salvador\"\n}"
            },
            "url": {
              "raw": "{{baseUrl}}/api/v1/routes/{{routeId}}",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "routes",
                "{{routeId}}"
              ]
            }
          },
          "response": []
        },
        {
          "name": "Eliminar ruta",
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "DELETE",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              }
            ],
            "url": {
              "raw": "{{baseUrl}}/api/v1/routes/{{routeId}}",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "routes",
                "{{routeId}}"
              ]
            }
          },
          "response": []
        },
        {
          "name": "Restaurar ruta",
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "POST",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              }
            ],
            "url": {
              "raw": "{{baseUrl}}/api/v1/routes/{{routeId}}/restore",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "routes",
                "{{routeId}}",
                "restore"
              ]
            }
          },
          "response": []
        }
      ]
    },
    {
      "name": "Asignación de rutas",
      "item": [
        {
          "name": "Listar asignaciones de ruta",
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "GET",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              }
            ],
            "url": {
              "raw": "{{baseUrl}}/api/v1/vehicle-route",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "vehicle-route"
              ]
            }
          },
          "response": []
        },
        {
          "name": "Listar asignaciones filtradas",
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "GET",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              }
            ],
            "url": {
              "raw": "{{baseUrl}}/api/v1/vehicle-route?vehicle={{vehicleId}}&route={{routeId}}&status=aprobada",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "vehicle-route"
              ],
              "query": [
                {
                  "key": "vehicle",
                  "value": "{{vehicleId}}"
                },
                {
                  "key": "route",
                  "value": "{{routeId}}"
                },
                {
                  "key": "status",
                  "value": "aprobada"
                }
              ]
            }
          },
          "response": []
        },
        {
          "name": "Listar asignaciones eliminadas",
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "GET",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              }
            ],
            "url": {
              "raw": "{{baseUrl}}/api/v1/vehicle-route?trashed=true",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "vehicle-route"
              ],
              "query": [
                {
                  "key": "trashed",
                  "value": "true"
                }
              ]
            }
          },
          "response": []
        },
        {
          "name": "Crear asignación de ruta",
          "event": [
            {
              "listen": "test",
              "script": {
                "type": "text/javascript",
                "exec": [
                  "pm.test(\"Status code is < 500\", function () { pm.expect(pm.response.code).to.be.below(500); });",
                  "let json = {};",
                  "try { json = pm.response.json(); } catch (e) {}",
                  "function firstId(obj) {",
                  "  if (!obj) return undefined;",
                  "  if (obj.id !== undefined) return obj.id;",
                  "  if (obj.data) {",
                  "    if (Array.isArray(obj.data) && obj.data.length && obj.data[0].id !== undefined) return obj.data[0].id;",
                  "    if (obj.data.id !== undefined) return obj.data.id;",
                  "  }",
                  "  if (obj.vehicle_route && obj.vehicle_route.id !== undefined) return obj.vehicle_route.id;",
                  "  return undefined;",
                  "}",
                  "",
                  "const id = firstId(json);",
                  "if (id !== undefined) pm.collectionVariables.set(\"vehicleRouteId\", String(id));"
                ]
              }
            }
          ],
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "POST",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              },
              {
                "key": "Content-Type",
                "value": "application/json"
              }
            ],
            "body": {
              "mode": "raw",
              "raw": "{\n  \"vehicle_id\": \"{{vehicleId}}\",\n  \"route_id\": \"{{routeId}}\",\n  \"load_weight\": 1200,\n  \"departure_datetime\": \"2099-12-31 12:00:00\",\n  \"status\": \"pendiente\"\n}"
            },
            "url": {
              "raw": "{{baseUrl}}/api/v1/vehicle-route",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "vehicle-route"
              ]
            }
          },
          "response": []
        },
        {
          "name": "Ver asignación de ruta",
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "GET",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              }
            ],
            "url": {
              "raw": "{{baseUrl}}/api/v1/vehicle-route/{{vehicleRouteId}}",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "vehicle-route",
                "{{vehicleRouteId}}"
              ]
            }
          },
          "response": []
        },
        {
          "name": "Actualizar asignación de ruta",
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "PUT",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              },
              {
                "key": "Content-Type",
                "value": "application/json"
              }
            ],
            "body": {
              "mode": "raw",
              "raw": "{\n  \"vehicle_id\": \"{{vehicleId}}\",\n  \"route_id\": \"{{routeId}}\",\n  \"load_weight\": 1000,\n  \"departure_datetime\": \"2099-12-31 13:00:00\",\n  \"status\": \"pendiente\"\n}"
            },
            "url": {
              "raw": "{{baseUrl}}/api/v1/vehicle-route/{{vehicleRouteId}}",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "vehicle-route",
                "{{vehicleRouteId}}"
              ]
            }
          },
          "response": []
        },
        {
          "name": "Eliminar asignación de ruta",
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "DELETE",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              }
            ],
            "url": {
              "raw": "{{baseUrl}}/api/v1/vehicle-route/{{vehicleRouteId}}",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "vehicle-route",
                "{{vehicleRouteId}}"
              ]
            }
          },
          "response": []
        },
        {
          "name": "Restaurar asignación de ruta",
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "POST",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              }
            ],
            "url": {
              "raw": "{{baseUrl}}/api/v1/vehicle-route/{{vehicleRouteId}}/restore",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "vehicle-route",
                "{{vehicleRouteId}}",
                "restore"
              ]
            }
          },
          "response": []
        }
      ]
    },
    {
      "name": "Abastecimiento de combustible",
      "item": [
        {
          "name": "Listar abastecimientos",
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "GET",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              }
            ],
            "url": {
              "raw": "{{baseUrl}}/api/v1/fuel-supplies",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "fuel-supplies"
              ]
            }
          },
          "response": []
        },
        {
          "name": "Listar abastecimientos filtrados",
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "GET",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              }
            ],
            "url": {
              "raw": "{{baseUrl}}/api/v1/fuel-supplies?vehicle={{vehicleId}}&route={{routeId}}&date=2099-12-31",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "fuel-supplies"
              ],
              "query": [
                {
                  "key": "vehicle",
                  "value": "{{vehicleId}}"
                },
                {
                  "key": "route",
                  "value": "{{routeId}}"
                },
                {
                  "key": "date",
                  "value": "2099-12-31"
                }
              ]
            }
          },
          "response": []
        },
        {
          "name": "Listar abastecimientos eliminados",
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "GET",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              }
            ],
            "url": {
              "raw": "{{baseUrl}}/api/v1/fuel-supplies?trashed=true",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "fuel-supplies"
              ],
              "query": [
                {
                  "key": "trashed",
                  "value": "true"
                }
              ]
            }
          },
          "response": []
        },
        {
          "name": "Crear abastecimiento",
          "event": [
            {
              "listen": "test",
              "script": {
                "type": "text/javascript",
                "exec": [
                  "pm.test(\"Status code is < 500\", function () { pm.expect(pm.response.code).to.be.below(500); });",
                  "let json = {};",
                  "try { json = pm.response.json(); } catch (e) {}",
                  "function firstId(obj) {",
                  "  if (!obj) return undefined;",
                  "  if (obj.id !== undefined) return obj.id;",
                  "  if (obj.data) {",
                  "    if (Array.isArray(obj.data) && obj.data.length && obj.data[0].id !== undefined) return obj.data[0].id;",
                  "    if (obj.data.id !== undefined) return obj.data.id;",
                  "  }",
                  "  if (obj.vehicle_route && obj.vehicle_route.id !== undefined) return obj.vehicle_route.id;",
                  "  return undefined;",
                  "}",
                  "",
                  "const id = firstId(json);",
                  "if (id !== undefined) pm.collectionVariables.set(\"fuelSupplyId\", String(id));"
                ]
              }
            }
          ],
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "POST",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              },
              {
                "key": "Content-Type",
                "value": "application/json"
              }
            ],
            "body": {
              "mode": "raw",
              "raw": "{\n  \"vehicle_id\": \"{{vehicleId}}\",\n  \"route_id\": \"{{routeId}}\",\n  \"amount_gallons\": 20.5,\n  \"price_per_gallon\": 4.6,\n  \"date\": \"2099-12-31\"\n}"
            },
            "url": {
              "raw": "{{baseUrl}}/api/v1/fuel-supplies",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "fuel-supplies"
              ]
            },
            "description": "Requiere que exista relación vehicle-route entre {{vehicleId}} y {{routeId}}."
          },
          "response": []
        },
        {
          "name": "Ver abastecimiento",
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "GET",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              }
            ],
            "url": {
              "raw": "{{baseUrl}}/api/v1/fuel-supplies/{{fuelSupplyId}}",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "fuel-supplies",
                "{{fuelSupplyId}}"
              ]
            }
          },
          "response": []
        },
        {
          "name": "Actualizar abastecimiento",
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "PUT",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              },
              {
                "key": "Content-Type",
                "value": "application/json"
              }
            ],
            "body": {
              "mode": "raw",
              "raw": "{\n  \"price_per_gallon\": 4.75,\n  \"date\": \"2099-12-31\",\n  \"status\": \"completado\"\n}"
            },
            "url": {
              "raw": "{{baseUrl}}/api/v1/fuel-supplies/{{fuelSupplyId}}",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "fuel-supplies",
                "{{fuelSupplyId}}"
              ]
            }
          },
          "response": []
        },
        {
          "name": "Eliminar abastecimiento",
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "DELETE",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              }
            ],
            "url": {
              "raw": "{{baseUrl}}/api/v1/fuel-supplies/{{fuelSupplyId}}",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "fuel-supplies",
                "{{fuelSupplyId}}"
              ]
            }
          },
          "response": []
        },
        {
          "name": "Restaurar abastecimiento",
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "POST",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              }
            ],
            "url": {
              "raw": "{{baseUrl}}/api/v1/fuel-supplies/{{fuelSupplyId}}/restore",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "fuel-supplies",
                "{{fuelSupplyId}}",
                "restore"
              ]
            }
          },
          "response": []
        },
        {
          "name": "New Request",
          "request": {
            "method": "GET",
            "header": []
          },
          "response": []
        }
      ]
    },
    {
      "name": "Mantenimientos",
      "item": [
        {
          "name": "Listar mantenimientos",
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "GET",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              }
            ],
            "url": {
              "raw": "{{baseUrl}}/api/v1/maintenances",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "maintenances"
              ]
            }
          },
          "response": []
        },
        {
          "name": "Filtrar mantenimientos por vehículo",
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "GET",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              }
            ],
            "url": {
              "raw": "{{baseUrl}}/api/v1/maintenances?vehicle_id={{vehicleId}}",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "maintenances"
              ],
              "query": [
                {
                  "key": "vehicle_id",
                  "value": "{{vehicleId}}"
                }
              ]
            }
          },
          "response": []
        },
        {
          "name": "Crear mantenimiento",
          "event": [
            {
              "listen": "test",
              "script": {
                "type": "text/javascript",
                "exec": [
                  "pm.test(\"Status code is < 500\", function () { pm.expect(pm.response.code).to.be.below(500); });",
                  "let json = {};",
                  "try { json = pm.response.json(); } catch (e) {}",
                  "function firstId(obj) {",
                  "  if (!obj) return undefined;",
                  "  if (obj.id !== undefined) return obj.id;",
                  "  if (obj.data) {",
                  "    if (Array.isArray(obj.data) && obj.data.length && obj.data[0].id !== undefined) return obj.data[0].id;",
                  "    if (obj.data.id !== undefined) return obj.data.id;",
                  "  }",
                  "  if (obj.vehicle_route && obj.vehicle_route.id !== undefined) return obj.vehicle_route.id;",
                  "  return undefined;",
                  "}",
                  "",
                  "const id = firstId(json);",
                  "if (id !== undefined) pm.collectionVariables.set(\"maintenanceId\", String(id));"
                ]
              }
            }
          ],
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "POST",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              },
              {
                "key": "Content-Type",
                "value": "application/json"
              }
            ],
            "body": {
              "mode": "raw",
              "raw": "{\n  \"vehicle_id\": \"{{vehicleId}}\",\n  \"description\": \"Cambio de aceite y revisión general\",\n  \"cost\": 150.75,\n  \"date\": \"2099-12-31\",\n  \"next_maintenance_mileage\": 60000\n}"
            },
            "url": {
              "raw": "{{baseUrl}}/api/v1/maintenances",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "maintenances"
              ]
            }
          },
          "response": []
        },
        {
          "name": "Ver mantenimiento",
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "GET",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              }
            ],
            "url": {
              "raw": "{{baseUrl}}/api/v1/maintenances/{{maintenanceId}}",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "maintenances",
                "{{maintenanceId}}"
              ]
            }
          },
          "response": []
        },
        {
          "name": "Actualizar mantenimiento",
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "PUT",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              },
              {
                "key": "Content-Type",
                "value": "application/json"
              }
            ],
            "body": {
              "mode": "raw",
              "raw": "{\n  \"vehicle_id\": \"{{vehicleId}}\",\n  \"description\": \"Ajuste de frenos y revisión de motor\",\n  \"cost\": 210.0,\n  \"date\": \"2099-12-31\",\n  \"next_maintenance_mileage\": 65000\n}"
            },
            "url": {
              "raw": "{{baseUrl}}/api/v1/maintenances/{{maintenanceId}}",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "maintenances",
                "{{maintenanceId}}"
              ]
            }
          },
          "response": []
        },
        {
          "name": "Eliminar mantenimiento",
          "request": {
            "auth": {
              "type": "bearer",
              "bearer": [
                {
                  "key": "token",
                  "value": "{{access_token}}",
                  "type": "string"
                }
              ]
            },
            "method": "DELETE",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              }
            ],
            "url": {
              "raw": "{{baseUrl}}/api/v1/maintenances/{{maintenanceId}}",
              "host": [
                "{{baseUrl}}"
              ],
              "path": [
                "api",
                "v1",
                "maintenances",
                "{{maintenanceId}}"
              ]
            }
          },
          "response": []
        }
      ]
    }
  ],
  "variable": [
    {
      "key": "baseUrl",
      "value": "http://backend-proyecto-catedra.test"
    },
    {
      "key": "email",
      "value": "admin@example.com"
    },
    {
      "key": "password",
      "value": "password"
    },
    {
      "key": "access_token",
      "value": ""
    },
    {
      "key": "login_user_id",
      "value": ""
    },
    {
      "key": "existingUserId",
      "value": "2"
    },
    {
      "key": "roleId",
      "value": ""
    },
    {
      "key": "fleetId",
      "value": ""
    },
    {
      "key": "vehicleId",
      "value": ""
    },
    {
      "key": "routeId",
      "value": ""
    },
    {
      "key": "vehicleRouteId",
      "value": ""
    },
    {
      "key": "fuelSupplyId",
      "value": ""
    },
    {
      "key": "maintenanceId",
      "value": ""
    },
    {
      "key": "driverId",
      "value": ""
    }
  ]
}

