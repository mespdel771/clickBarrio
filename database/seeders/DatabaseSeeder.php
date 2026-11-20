<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeder principal encargado de configurar el usuario restringido en el motor MySQL
 * y ejecutar de forma secuencial todos los seeders de la aplicación.
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Ejecuta la configuración de privilegios en base de datos y lanza los seeders
     * de roles, categorías, tiendas, usuarios, productos, precios, stock y reservas.
     *
     * @return void
     */
    public function run(): void
    {
        /*
         Seguridad del Motor de Base de Datos        
         Creamos un usuario específico para la aplicación en el motor MySQL
         y le asignamos únicamente los privilegios requeridos para la base de datos.
        */
        try {
            // 1. Crear el usuario del motor si no existe con una contraseña segura.
            DB::statement("CREATE USER IF NOT EXISTS 'click_user'@'127.0.0.1' IDENTIFIED BY 'ClickBarrio2026*';");
            DB::statement("CREATE USER IF NOT EXISTS 'click_user'@'localhost' IDENTIFIED BY 'ClickBarrio2026*';");

            // 2. Otorgar únicamente los privilegios de datos requeridos.
            DB::statement("GRANT SELECT, INSERT, UPDATE, DELETE ON `click`.* TO 'click_user'@'127.0.0.1';");
            DB::statement("GRANT SELECT, INSERT, UPDATE, DELETE ON `click`.* TO 'click_user'@'localhost';");

            // 3. Recargar los privilegios en el motor de base de datos.
            DB::statement("FLUSH PRIVILEGES;");
        } catch (\Exception $e) {
            // Captura el flujo de forma silenciosa.
        }

        /*       
         Ejecución de los Seeders en Orden Cronológico
        */
        $this->call([
            // 1. Roles
            RoleSeeder::class,

            // 2. Categorías
            CategoriaSeeder::class,

            // 3. Tiendas
            TiendaSeeder::class,

            // 4. Usuarios y Gestores
            UserSeeder::class,

            // 5. Productos
            ProductoSeeder::class,

            // 6. Precios
            PrecioSeeder::class,

            // 7. Stock
            StockSeeder::class,

            // 8. Reservas
            ReservaSeeder::class,
        ]);
    }
}
