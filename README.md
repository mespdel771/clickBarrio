# ClickBarrio - Las Tiendas del Barrio

Aplicación web hecha con Laravel 12 para gestionar las tiendas del barrio, ver sus productos, hacer la compra en un carrito multi-tienda y reservar pedidos generando las facturas y comprobantes en PDF automáticamente.

## Qué hace la aplicación

- Diferentes tipos de usuario (roles):
  - Administrador: Puede gestionar todos los usuarios, categorías, tiendas y ver todas las reservas que se hacen en la web.
  - Gestor: Administra su propia tienda (o tiendas asignadas), controla el stock, cambia precios y revisa los pedidos que le llegan.
  - Cliente: Puede mirar productos por tienda o categoría, añadir cosas de varias tiendas al mismo carrito, hacer su reserva y descargar el comprobante en PDF.
- Carrito y reservas seguras: Antes de confirmar una reserva, el sistema comprueba que haya stock suficiente y usa transacciones de base de datos para que no se quede nada a medias si ocurre algún fallo.
- Correos automáticos y PDFs:
  - Cuando un cliente hace un pedido, le llega un correo de confirmación con sus productos agrupados por tienda y un PDF adjunto generado con DomPDF.
  - A cada tienda implicada en la compra le llega también su propio correo avisando del pedido con un PDF que incluye solo los productos de su comercio.
- Documentación del código: Todo el código está comentado con PHPDoc y generado en web dentro de docs/api/index.html.

## Qué necesitas tener instalado

- PHP 8.2 o superior
- Composer
- Node.js y NPM
- Un entorno local con MySQL o MariaDB (XAMPP, Laragon, MAMP o similar)

## Instalación paso a paso según tu sistema operativo

### En Windows (usando XAMPP o Laragon)

1. Instala XAMPP o Laragon (asegúrate de que tenga PHP 8.2 o superior), Composer y Node.js.
2. Abre el panel de XAMPP o Laragon y arranca los servicios de Apache y MySQL.
3. Entra a phpMyAdmin (normalmente en http://localhost/phpmyadmin) y crea una base de datos vacía llamada click.
4. Abre la terminal (PowerShell, Git Bash o la terminal de Laragon) dentro de la carpeta donde guardes tus proyectos (por ejemplo C:\xampp\htdocs o C:\laragon\www) y clona el repositorio:
   git clone https://github.com
   cd clickBarrio
   (Recuerda poner aquí la URL real de tu repositorio).
5. Instala las dependencias del proyecto:
   composer install
   npm install && npm run build
6. Crea el archivo de configuración copiando el de ejemplo:
   - Si usas PowerShell o Git Bash: cp .env.example .env
   - Si usas el Símbolo del sistema (CMD): copy .env.example .env
7. Abre el archivo .env y comprueba que los datos de la base de datos coinciden con los tuyos (en XAMPP/Laragon por defecto el usuario es root y la contraseña va vacía):
   DB_DATABASE=click
   DB_USERNAME=root
   DB_PASSWORD=
8. Genera la clave de Laravel, crea el enlace para las imágenes y carga las tablas con los datos de prueba:
   php artisan key:generate
   php artisan storage:link
   php artisan migrate:fresh --seed
9. Arranca el servidor con php artisan serve y abre http://127.0.0.1:8000 en tu navegador.

### En macOS (usando MAMP, Laravel Herd o Homebrew)

1. Instala PHP 8.2+, Composer, Node.js y un servidor con MySQL/MariaDB (por ejemplo MAMP o instalando mysql con Homebrew).
2. Arranca MySQL y crea una base de datos vacía llamada click (desde phpMyAdmin en MAMP o desde tu gestor de bases de datos).
3. Abre la Terminal de Mac, ve a tu carpeta de proyectos y clona el repositorio:
   git clone https://github.com
   cd clickBarrio
4. Instala las dependencias de PHP y JavaScript:
   composer install
   npm install && npm run build
5. Copia el archivo de entorno:
   cp .env.example .env
6. Edita el archivo .env con los datos de conexión a tu MySQL (en MAMP por defecto tanto el usuario como la contraseña suelen ser root):
   DB_DATABASE=click
   DB_USERNAME=root
   DB_PASSWORD=root
7. Genera la clave, enlaza la carpeta de imágenes y crea las tablas con los datos de prueba:
   php artisan key:generate
   php artisan storage:link
   php artisan migrate:fresh --seed
8. Inicia el servidor ejecutando php artisan serve y entra a http://127.0.0.1:8000.

### En Linux (Ubuntu / Debian y derivados)

1. Abre la terminal e instala PHP 8.2 o superior (con sus extensiones habituales para Laravel como mbstring, xml, curl, zip, gd y mysql), Composer, Node.js, NPM y MySQL o MariaDB:
   sudo apt update
   sudo apt install php php-cli php-mbstring php-xml php-curl php-zip php-gd php-mysql mysql-server composer nodejs npm unzip
2. Inicia el servicio de MySQL y crea la base de datos click:
   sudo service mysql start
   sudo mysql -e "CREATE DATABASE click CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
   (Si tu usuario root de MySQL no tiene contraseña o usa autenticación por socket, crea un usuario con permisos sobre la base de datos click para ponerlo en el .env).
3. Clona el repositorio y entra en la carpeta del proyecto:
   git clone https://github.com
   cd clickBarrio
4. Instala las dependencias:
   composer install
   npm install && npm run build
5. Copia el archivo .env.example a .env y configura el acceso a tu base de datos:
   cp .env.example .env
6. Genera la clave de la aplicación, crea el enlace simbólico para las imágenes y ejecuta las migraciones con los seeders:
   php artisan key:generate
   php artisan storage:link
   php artisan migrate:fresh --seed
7. Si tienes algún problema de permisos al subir imágenes o guardar caché, dales permisos de escritura a las carpetas storage y bootstrap/cache:
   chmod -R 775 storage bootstrap/cache
8. Arranca el proyecto con php artisan serve y abre http://127.0.0.1:8000 en el navegador.

## Configurar Mailtrap para probar los correos

Para poder ver los correos de confirmación y descargar los PDFs sin tener que mandar emails reales, pon tus datos de Mailtrap dentro del archivo .env (funciona igual en Windows, macOS y Linux):

MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=tu_usuario_mailtrap
MAIL_PASSWORD=tu_password_mailtrap

Cuando guardes los cambios, limpia la caché de configuración para que Laravel los coja:
php artisan config:clear

## Usuarios de prueba

Al ejecutar los seeders se crean varias cuentas listas para probar cada rol. Todas tienen la misma contraseña (password):

- Administrador: admin@gmail.com | Contraseña: password (Tiene acceso total a toda la aplicación)
- Gestor de Tienda: gestor1@gmail.com, gestor2@gmail.com, etc. | Contraseña: password (Gestiona su tienda asignada, sus productos y sus pedidos)
- Cliente principal: usuario@gmail.com | Contraseña: password (Cuenta principal para probar compras como cliente)
- Otros clientes: user1@gmail.com hasta user5@gmail.com | Contraseña: password (Cuentas extra de clientes por si hacen falta más pruebas)

## Ejecutar los tests

El proyecto tiene pruebas unitarias y de integración en las carpetas tests/Unit y tests/Feature. Para lanzarlas todas:
php artisan test

## Ver la documentación del código

Si quieres consultar la explicación de las clases, controladores, modelos y seeders del proyecto, solo tienes que abrir en tu navegador el archivo docs/api/index.html.
