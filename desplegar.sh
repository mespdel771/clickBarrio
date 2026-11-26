#!/bin/bash
cd "$(dirname "$0")"
gnome-terminal -- bash -c "
echo '==========================================================';
echo ' LEVANTANDO SERVIDOR MULTITIENDA LARAVEL 12...';
echo '==========================================================';
echo '';
if [ ! -d \"vendor\" ]; then
    echo ' 1. Instalando dependencias de PHP (Composer)...';
    composer install;
fi
if [ ! -f \".env\" ]; then
    echo '️ 2. Configurando entorno (.env) y clave de aplicación...';
    cp .env.example .env;
    php artisan key:generate;
fi
echo ' 3. Asegurando permisos de almacenamiento...';
sudo chmod -R 775 storage bootstrap/cache;
echo '️ 4. Actualizando tablas y cargando datos (Seeders)...';
php artisan migrate:fresh --seed;
echo '️ 5. Verificando enlaces de imágenes...';
php artisan storage:link;
echo '';
echo ' 6. Iniciando servidor local...';
echo ' Abre en tu navegador: http://127.0.0.1:8000';
echo '';
php artisan serve;
exec bash"

