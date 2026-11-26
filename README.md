# ClickBarrio - Las Tiendas del Barrio

Aplicación web hecha con Laravel 12 para gestionar las tiendas del barrio, ver sus productos, hacer la compra en un carrito multi-tienda y reservar pedidos generando las facturas y comprobantes en PDF automáticamente.

## Versión desplegada en producción

La aplicación está desplegada y operativa en Railway para su revisión directa sin necesidad de instalación local:

- **URL de acceso:** [https://clickbarrio-production.up.railway.app/](https://clickbarrio-production.up.railway.app/)
- *(Puedes iniciar sesión directamente usando cualquiera de las cuentas de prueba indicadas en la sección [Usuarios de prueba](#usuarios-de-prueba)).*

## Qué hace la aplicación

- **Diferentes tipos de usuario (roles):**
  - **Administrador:** Puede gestionar todos los usuarios, categorías, tiendas y ver todas las reservas que se hacen en la web.
  - **Gestor:** Administra su propia tienda (o tiendas asignadas), controla el stock, cambia precios y revisa los pedidos que le llegan.
  - **Cliente:** Puede mirar productos por tienda o categoría, añadir cosas de varias tiendas al mismo carrito, hacer su reserva y descargar el comprobante en PDF.
- **Carrito y reservas seguras:** Antes de confirmar una reserva, el sistema comprueba que haya stock suficiente y usa transacciones de base de datos para que no se quede nada a medias si ocurre algún fallo.
- **Correos automáticos y PDFs:**
  - Cuando un cliente hace un pedido, le llega un correo de confirmación con sus productos agrupados por tienda y un PDF adjunto generado con DomPDF.
  - A cada tienda implicada en la compra le llega también su propio correo avisando del pedido con un PDF que incluye solo los productos de su comercio.
- **Documentación del código:** Todo el código está comentado con PHPDoc y generado en web dentro de `docs/api/index.html`.

## Qué necesitas tener instalado (para ejecución en local)

- PHP 8.2 o superior
- Composer
- Node.js y NPM (opcional: los assets compilados ya se incluyen en `public/build/`)
- Un entorno local con MySQL o MariaDB (XAMPP, Laragon, MAMP o similar)

## Preparación de la Base de Datos (válido para Windows, macOS y Linux)

El archivo `.env.example` viene configurado para conectarse con un usuario específico (`click_user`) con privilegios limitados sobre la base de datos `click`:

- **Opción recomendada (usando los scripts SQL incluidos):**
  1. Abre **phpMyAdmin** (`http://localhost/phpmyadmin`) con `root`.
  2. Importa primero **`01_creacion.sql`** (crea la base de datos `click`, todas las tablas y el usuario `click_user` con contraseña `ClickBarrio2026*`).
  3. Importa después **`02_llenado.sql`** para cargar las categorías, tiendas, productos, precios, stock y usuarios de prueba.
- **Opción alternativa (migraciones de Laravel):** Si prefieres usar `php artisan migrate:fresh --seed`, pon temporalmente `DB_USERNAME=root` y `DB_PASSWORD=` en el `.env` para crear las tablas y vuelve a dejar `click_user` después.

## Instalación paso a paso según tu sistema operativo

### En Windows (usando XAMPP o Laragon)

1. Instala XAMPP o Laragon (con PHP 8.2 o superior) y Composer.
2. Arranca Apache y MySQL desde el panel de control e importa `01_creacion.sql` y `02_llenado.sql` en phpMyAdmin (`http://localhost/phpmyadmin`).
3. Abre la terminal (PowerShell, Git Bash o CMD) dentro de la carpeta del proyecto (`click/`):
   ```bash
   composer install
   copy .env.example .env
   php artisan key:generate
   php artisan storage:link
   php artisan serve
   ```
4. Abre `http://127.0.0.1:8000` en tu navegador.

### En macOS (usando MAMP, XAMPP o Homebrew)

1. Arranca MySQL e importa `01_creacion.sql` y `02_llenado.sql` en phpMyAdmin.
2. Abre la Terminal dentro de la carpeta del proyecto (`click/`) y ejecuta:
   ```bash
   composer install
   cp .env.example .env
   php artisan key:generate
   chmod -R 775 storage bootstrap/cache
   php artisan storage:link
   php artisan serve
   ```
3. Entra en `http://127.0.0.1:8000`.

### En Linux (Ubuntu / Debian con XAMPP)

1. Puedes usar el lanzador gráfico **`Instalador.desktop`** o ejecutar **`./desplegar.sh`** en la raíz de `click/`, o bien hacerlo paso a paso desde la terminal (tras importar `01_creacion.sql` y `02_llenado.sql` en phpMyAdmin):
   ```bash
   composer install
   cp .env.example .env
   php artisan key:generate
   chmod -R 775 storage bootstrap/cache
   php artisan storage:link
   php artisan serve
   ```
2. Abre `http://127.0.0.1:8000` en el navegador.

## Correos electrónicos y Mailtrap

Por defecto el proyecto viene configurado con `MAIL_MAILER=log` para que los correos y PDFs adjuntos se registren en `storage/logs/laravel.log` sin dar errores si no hay conexión SMTP. Si quieres verlos en bandeja web con **Mailtrap**, cambia estos valores en `.env`:

```ini
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=tu_usuario_mailtrap
MAIL_PASSWORD=tu_password_mailtrap
```

Cuando guardes los cambios, limpia la caché de configuración:
```bash
php artisan config:clear
```

## Usuarios de prueba

Tanto en la versión desplegada como al importar `02_llenado.sql` (o ejecutar los seeders), se crean las siguientes cuentas listas para probar cada rol (todas con contraseña **`password`**):

- **Administrador:** `admin@gmail.com` | Contraseña: `password` (Acceso total a toda la aplicación)
- **Gestor de Tienda:** `gestor1@gmail.com` hasta `gestor20@gmail.com` | Contraseña: `password` (Gestiona su tienda o tiendas asignadas, precios, stock y pedidos)
- **Cliente principal:** `usuario@gmail.com` (y `user1@gmail.com` hasta `user5@gmail.com`) | Contraseña: `password` (Compras en carrito multi-tienda, reservas y descarga de PDF)

## Ejecutar los tests

El proyecto tiene 45 pruebas unitarias y de integración en `tests/Unit` y `tests/Feature`. Para lanzarlas todas en local:
```bash
php artisan test
```
*(Nota: Si los tests modifican la estructura de tablas durante la ejecución, asegúrate de tener `DB_USERNAME=root` en tu `.env` local o de que `click_user` disponga de permisos de creación de tablas).*

## Ver la documentación del código

Para consultar la explicación de las clases, controladores, modelos y seeders del proyecto, abre en tu navegador el archivo `docs/api/index.html`.
