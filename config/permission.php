<?php

use Spatie\Permission\DefaultTeamResolver;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return [

    'models' => [

        /*
         * Al utilizar el trait "HasPermissions" de este paquete, necesitamos saber qué
         * modelo Eloquent debe usarse para recuperar tus permisos. Por supuesto, suele
         * ser simplemente el modelo "Permission", pero puedes usar el que prefieras.
         *
         * El modelo que desees utilizar como modelo de Permisos debe implementar el
         * contrato `Spatie\Permission\Contracts\Permission`.
         */

        'permission' => Permission::class,

        /*
         * Al utilizar el trait "HasRoles" de este paquete, necesitamos saber qué
         * modelo Eloquent debe usarse para recuperar tus roles. Por supuesto, suele
         * ser simplemente el modelo "Role", pero puedes usar el que prefieras.
         *
         * El modelo que desees utilizar como modelo de Roles debe implementar el
         * contrato `Spatie\Permission\Contracts\Role`.
         */

        'role' => Role::class,

    ],

    'table_names' => [

        /*
         * Al utilizar el trait "HasRoles" de este paquete, necesitamos saber qué
         * tabla debe usarse para recuperar tus roles. Hemos elegido un valor
         * predeterminado básico, pero puedes cambiarlo fácilmente por la tabla que quieras.
         */

        'roles' => 'roles',

        /*
         * Al utilizar el trait "HasPermissions" de este paquete, necesitamos saber qué
         * tabla debe usarse para recuperar tus permisos. Hemos elegido un valor
         * predeterminado básico, pero puedes cambiarlo fácilmente por la tabla que quieras.
         */

        'permissions' => 'permissions',

        /*
         * Al utilizar el trait "HasPermissions" de este paquete, necesitamos saber qué
         * tabla debe usarse para recuperar los permisos de tus modelos. Hemos elegido
         * un valor predeterminado básico, pero puedes cambiarlo por cualquier tabla.
         */

        'model_has_permissions' => 'model_has_permissions',

        /*
         * Al utilizar el trait "HasRoles" de este paquete, necesitamos saber qué
         * tabla debe usarse para recuperar los roles de tus modelos. Hemos elegido
         * un valor predeterminado básico, pero puedes cambiarlo por cualquier tabla.
         */

        'model_has_roles' => 'model_has_roles',

        /*
         * Al utilizar el trait "HasRoles" de este paquete, necesitamos saber qué
         * tabla debe usarse para recuperar los permisos de tus roles. Hemos elegido
         * un valor predeterminado básico, pero puedes cambiarlo por cualquier tabla.
         */

        'role_has_permissions' => 'role_has_permissions',
    ],

    'column_names' => [
        /*
         * Cambia esto si deseas nombrar las claves pivote relacionadas de forma distinta a la predeterminada.
         */
        'role_pivot_key' => null, // por defecto 'role_id',
        'permission_pivot_key' => null, // por defecto 'permission_id',

        /*
         * Cambia esto si deseas nombrar la clave primaria del modelo relacionado de forma
         * distinta a `model_id`.
         *
         * Por ejemplo, esto es útil si todas tus claves primarias son UUID. En ese
         * caso, nómbrala `model_uuid`.
         */

        'model_morph_key' => 'model_id',

        /*
         * Cambia esto si deseas utilizar la funcionalidad de equipos (teams) y la clave
         * foránea de tu modelo relacionado es distinta a `team_id`.
         */

        'team_foreign_key' => 'team_id',
    ],

    /*
     * Cuando se establece en true, el método para comprobar permisos se registrará en el Gate.
     * Establécelo en false si deseas implementar una lógica personalizada para verificar permisos.
     */

    'register_permission_check_method' => true,

    /*
     * Cuando se establece en true, se registrará el listener del evento Laravel\Octane\Events\OperationTerminated,
     * lo que actualizará los permisos en cada TickTerminated, TaskTerminated y RequestTerminated.
     * NOTA: No debería ser necesario en la mayoría de los casos, pero resulta útil en combinaciones Octane/Vapor.
     */
    'register_octane_reset_listener' => false,

    /*
     * Se dispararán eventos cuando se asigne o desasigne un rol o permiso:
     * \Spatie\Permission\Events\RoleAttached
     * \Spatie\Permission\Events\RoleDetached
     * \Spatie\Permission\Events\PermissionAttached
     * \Spatie\Permission\Events\PermissionDetached
     *
     * Para activarlo, establécelo en true y crea listeners para escuchar estos eventos.
     */
    'events_enabled' => false,

    /*
     * Funcionalidad de Equipos (Teams).
     * Cuando se establece en true, el paquete implementa equipos usando 'team_foreign_key'.
     * Si deseas que las migraciones registren 'team_foreign_key', debes establecer esto
     * en true antes de ejecutar la migración.
     * Si ya ejecutaste la migración, deberás crear una nueva migración para añadir
     * 'team_foreign_key' a 'roles', 'model_has_roles' y 'model_has_permissions'
     * (consulta la última versión del archivo de migración de este paquete).
     */

    'teams' => false,

    /*
     * La clase que se utilizará para resolver el identificador (id) del equipo de permisos.
     */
    'team_resolver' => DefaultTeamResolver::class,

    /*
     * Concesión de Credenciales de Cliente de Passport.
     * Cuando se establece en true, el paquete utilizará el cliente de Passport para comprobar permisos.
     */

    'use_passport_client_credentials' => false,

    /*
     * Cuando se establece en true, los nombres de los permisos requeridos se añaden a los mensajes de excepción.
     * Esto podría considerarse una fuga de información en algunos contextos, por lo que el valor
     * predeterminado es false para una seguridad óptima.
     */

    'display_permission_in_exception' => false,

    /*
     * Cuando se establece en true, los nombres de los roles requeridos se añaden a los mensajes de excepción.
     * Esto podría considerarse una fuga de información en algunos contextos, por lo que el valor
     * predeterminado es false para una seguridad óptima.
     */

    'display_role_in_exception' => false,

    /*
     * Por defecto, las búsquedas de permisos con comodines (wildcards) están deshabilitadas.
     * Consulta la documentación para conocer la sintaxis soportada.
     */

    'enable_wildcard_permission' => false,

    /*
     * La clase que se utilizará para interpretar permisos con comodines.
     * Si necesitas modificar los delimitadores, sobrescribe la clase e indica su nombre aquí.
     */
    // 'wildcard_permission' => Spatie\Permission\WildcardPermission::class,

    /* Ajustes específicos de la caché */

    'cache' => [

        /*
         * Por defecto, todos los permisos se almacenan en caché durante 24 horas para mejorar el rendimiento.
         * Cuando se actualizan permisos o roles, la caché se vacía automáticamente.
         */

        'expiration_time' => DateInterval::createFromDateString('24 hours'),

        /*
         * La clave de caché utilizada para almacenar todos los permisos.
         */

        'key' => 'spatie.permission.cache',

        /*
         * Opcionalmente puedes indicar un controlador de caché específico para almacenar permisos
         * y roles utilizando cualquiera de los controladores `store` listados en config/cache.php.
         * Usar 'default' aquí significa utilizar el valor `default` configurado en cache.php.
         */

        'store' => 'default',
    ],
];
