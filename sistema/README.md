# DELITEX Confecciones · Sistema web

Sistema web para la gestión y control de pedidos, producción e inventario de DELITEX Confecciones, empresa dedicada a la confección de ropa deportiva personalizada.

Desarrollado con Laravel 13, PHP 8.3 y MySQL 8.4 sobre el entorno local de Laragon.

## Requisitos

- Laragon Full con PHP 8.3 o superior, MySQL 8.4, Composer 2 y Node.js 22.
- Extensiones de PHP activas: `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `zip` e `intl`.

## Instalación

Los comandos se ejecutan en la terminal de Laragon, dentro de la carpeta `sistema`:

```bash
composer install
npm install
copy .env.example .env
php artisan key:generate
```

Crear la base de datos `delitex` y ejecutar las migraciones con los datos iniciales:

```bash
mysql -u root -e "CREATE DATABASE IF NOT EXISTS delitex CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
php artisan migrate --seed
php artisan storage:link
```

`storage:link` crea el acceso directo `public/storage`, necesario para ver las imágenes de los productos. Se ejecuta una vez en cada PC, porque ese acceso directo no se sube a GitHub.

Los datos iniciales crean los roles, las categorías de productos y un usuario de prueba por rol, todos con la contraseña `delitex123`:

| Rol | Correo |
|---|---|
| Dueña | maria.quispe@delitex.pe |
| Administradora | lucia.rojas@delitex.pe |
| Confeccionista | jorge.taipe@delitex.pe |

Encender el servidor y abrir `http://127.0.0.1:8000`:

```bash
php artisan serve
```

Todo el sistema pide iniciar sesión. Se ingresa con el correo o el DNI y la contraseña; cada usuario puede cambiar sus datos y su contraseña desde **Mi perfil** (menú del usuario, abajo a la izquierda).

## Módulos

| Grupo | Módulos |
|---|---|
| Principal | Dashboard con resumen (pedidos activos, producción, stock bajo y saldo por cobrar) y Reportes de ventas, pedidos, pagos, inventario y producción |
| Gestión | Clientes, Pedidos (con su seguimiento), Ventas, Producción, Productos, Inventario y alertas de stock, Pagos y Entregas |
| Configuraciones | Roles, Usuarios, Categorías y Mi perfil |

Flujo de un pedido personalizado: se registra el pedido → se envía a producción y avanza por etapas (corte, confección, estampado o bordado, acabado y control de calidad) → se registran los pagos → se confirma la entrega cuando está pagado → se genera su venta (comprobante), sin volver a cobrar.

Las ventas directas de prendas del catálogo se registran en Ventas y descuentan el stock del producto y de su inventario. Los insumos (telas, hilos, accesorios) son de uso del taller y no se venden.

## Permisos por rol

Cada rol tiene marcados los módulos a los que puede entrar (Configuraciones → Roles). El menú y el dashboard muestran solo esos módulos, y si alguien escribe la dirección de otro, el sistema responde «Sin permiso».

| Rol | Módulos |
|---|---|
| Dueña | Todos |
| Administradora | Todos menos Roles |
| Confeccionista | Seguimiento de pedidos (solo consulta), Producción, Productos e Inventario |

Un usuario solo puede asignar roles con los mismos permisos que el suyo o menos, no puede cambiar su propio rol ni eliminarse, y nadie puede quitarle a su propio rol el acceso a Roles.

Después de cada Pull, si hay migraciones nuevas, se ejecuta `php artisan migrate` (o `php artisan migrate:fresh --seed` para reconstruir la base desde cero).

## Estructura principal

| Carpeta | Contenido |
|---|---|
| `database/migrations` | Tablas creadas por niveles a partir del diagrama de clases |
| `database/seeders` | Roles, usuarios de prueba con contraseña y categorías de productos |
| `lang/es` | Mensajes de validación en español |
| `app/Models` | Modelos Eloquent con sus relaciones y los métodos del diagrama de clases |
| `app/Http/Controllers` | Controladores del sistema |
| `resources/views/template` | Plantilla padre `administrador.blade.php` |
| `resources/views/partials` | Menú lateral, encabezado, mensajes y modal de cierre de sesión |
| `resources/views/principal` | Dashboard, vista raíz del sistema |
| `public` | Estilos, imágenes y scripts del prototipo |

La revisión del diagrama de clases y el orden de creación de las tablas se encuentran en `doc/uml/DCLASS/Revision_diagrama_clases.md`.
