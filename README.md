# 🛒 ClickBarrio - Las Tiendas del Barrio

Plataforma web desarrollada en **Laravel 12** para la gestión integral de comercios locales, catálogo de productos multi-tienda, carrito de compras y sistema de reservas con generación automática de facturas y comprobantes en PDF.

---

## ✨ Funcionalidades principales

* **Control de acceso por roles:**
  * **Administrador:** Gestión global de usuarios, categorías, tiendas y consulta de todas las reservas del sistema.
  * **Gestor:** Administración de sus comercios asignados, inventario (`stocks`), precios y pedidos recibidos en su tienda.
  * **Cliente (`usuario`):** Exploración de productos por tienda o categoría, gestión de carrito multi-tienda e historial de reservas con descarga de factura en PDF.
* **Carrito y reservas transaccionales:** Validación de disponibilidad de stock y precios vigentes en tiempo real bajo transacciones seguras (`DB::transaction`).
* **Notificaciones por correo electrónico y PDF:**
  * Envío de correo de confirmación al cliente con los productos **agrupados y ordenados por tienda**, adjuntando el comprobante en PDF (`DomPDF`).
  * Envío individualizado de aviso de pedido a cada tienda implicada en la reserva con su desglose parcial en PDF.
* **Documentación técnica:** Código fuente documentado con estándares PHPDoc (`docs/api/index.html`).

---

## 🛠️ Requisitos del sistema (Windows, macOS y Linux)

* **PHP:** 8.2 o superior
* **Composer**
* **Node.js y NPM**
* **Servidor web y base de datos:** XAMPP / Laragon / MAMP / Apache y MySQL / MariaDB

---

## 🚀 Instalación y puesta en marcha

1. **Clonar el repositorio:**
   ```bash
   git clone https://github.com
   cd clickBarrio
   ```

2. **Instalar dependencias de PHP y JavaScript:**
   ```bash
   composer install
   npm install && npm run build
   ```

3. **Configurar variables de entorno y enlace de imágenes:**
   Copia el archivo de ejemplo según tu sistema operativo:
   * **En Linux, macOS o Git Bash / PowerShell (Windows):**
     ```bash
     cp .env.example .env
     ```
   * **En Símbolo del sistema de Windows (CMD):**
     ```cmd
     copy .env.example .env
     ```

   A continuación, genera la clave de aplicación y crea el enlace simbólico para las imágenes:
   ```bash
   php artisan key:generate
   php artisan storage:link
   ```
   Asegúrate de tener creada la base de datos `click` en MySQL con las credenciales definidas en `.env.example` (o ajústalas en tu archivo `.env` según tu entorno local).

4. **Ejecutar migraciones y datos de prueba (Seeders):**
   ```bash
   php artisan migrate:fresh --seed
   ```

5. **Configurar envío de correos de prueba (Mailtrap):**
   Para visualizar los correos de confirmación y los PDFs adjuntos sin enviar emails reales, introduce tus credenciales de **Mailtrap (Email Sandbox)** en el archivo `.env`:
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=sandbox.smtp.mailtrap.io
   MAIL_PORT=2525
   MAIL_USERNAME=tu_usuario_mailtrap
   MAIL_PASSWORD=tu_password_mailtrap
   ```
   Limpia la caché de configuración tras guardar:
   ```bash
   php artisan config:clear
   ```

6. **Arrancar el servidor local:**
   ```bash
   php artisan serve
   ```
   Accede desde tu navegador a `http://127.0.0.1:8000` (o a través de tu VirtualHost en XAMPP/Laragon).

---

## 👥 Usuarios de prueba (Generados por `UserSeeder`)

Todos los usuarios creados por defecto comparten la misma contraseña (`password`):

| Rol | Email | Contraseña | Descripción |
| :--- | :--- | :--- | :--- |
| **Administrador** | `admin@gmail.com` | `password` | Acceso total a la gestión de la plataforma |
| **Gestor de Tienda** | `gestor1@gmail.com` *(y `gestor2@gmail.com`, etc.)* | `password` | Propietario y gestor de su tienda asignada según ID |
| **Cliente principal** | `usuario@gmail.com` | `password` | Usuario comprador de prueba |
| **Clientes adicionales** | `user1@gmail.com` hasta `user5@gmail.com` | `password` | Cuentas adicionales de clientes para pruebas |

---

## 🧪 Ejecución de pruebas automatizadas

El proyecto incluye pruebas unitarias y de integración (`tests/Unit` y `tests/Feature`). Para ejecutarlas:
```bash
php artisan test
```

---

## 📚 Documentación técnica (phpDocumentor)

La documentación completa de clases, controladores, modelos, seeders y mailables se encuentra generada en formato web estático dentro del proyecto. Puedes consultarla abriendo en cualquier navegador el archivo:

`docs/api/index.html`
