#!/bin/bash
cd "$(dirname "$0")"
gnome-terminal -- bash -c "
echo '==========================================================';
echo '🚀 LEVANTANDO SERVIDOR MULTITIENDA LARAVEL 12...';
echo '==========================================================';
echo '';
echo '🔄 1. Asegurando permisos de almacenamiento...';
sudo chmod -R 775 storage bootstrap/cache;
echo '🗄️ 2. Actualizando tablas y cargando datos (Seeders)...';
php artisan migrate:fresh --seed;
echo '🖼️ 3. Verificando enlaces de imágenes...';
php artisan storage:link;
echo '';
echo '🌐 4. Iniciando servidor local...';
echo '👉 Abre en tu navegador: http://127.0.0.1:8000';
echo '';
php artisan serve;
exec bash"
