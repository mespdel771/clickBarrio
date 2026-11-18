<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Controlador de Sesión Predeterminado
    |--------------------------------------------------------------------------
    |
    | Esta opción determina el controlador de sesión por defecto que se utilizará
    | para las solicitudes entrantes. Laravel soporta diversas opciones de
    | almacenamiento para persistir los datos de sesión; la base de datos es una
    | excelente opción predeterminada.
    |
    | Soportados: "file", "cookie", "database", "memcached",
    |             "redis", "dynamodb", "array"
    |
    */

    'driver' => env('SESSION_DRIVER', 'database'),

    /*
    |--------------------------------------------------------------------------
    | Tiempo de Vida de la Sesión
    |--------------------------------------------------------------------------
    |
    | Aquí puedes especificar el número de minutos que deseas permitir que la
    | sesión permanezca inactiva antes de expirar. Si deseas que expire de
    | inmediato al cerrar el navegador, puedes indicarlo mediante la opción de
    | configuración expire_on_close.
    |
    */

    'lifetime' => (int) env('SESSION_LIFETIME', 120),

    'expire_on_close' => env('SESSION_EXPIRE_ON_CLOSE', false),

    /*
    |--------------------------------------------------------------------------
    | Cifrado de la Sesión
    |--------------------------------------------------------------------------
    |
    | Esta opción te permite especificar fácilmente que todos los datos de tu
    | sesión deben cifrarse antes de almacenarse. Todo el cifrado lo realiza
    | Laravel automáticamente y puedes usar la sesión con total normalidad.
    |
    */

    'encrypt' => env('SESSION_ENCRYPT', false),

    /*
    |--------------------------------------------------------------------------
    | Ubicación de Archivos de Sesión
    |--------------------------------------------------------------------------
    |
    | Cuando se utiliza el controlador de sesión "file", los archivos de sesión
    | se guardan en el disco. Aquí se define la ubicación predeterminada; no
    | obstante, puedes indicar otra ruta donde deban almacenarse.
    |
    */

    'files' => storage_path('framework/sessions'),

    /*
    |--------------------------------------------------------------------------
    | Conexión de Base de Datos para Sesiones
    |--------------------------------------------------------------------------
    |
    | Al utilizar los controladores de sesión "database" o "redis", puedes
    | especificar una conexión para gestionar estas sesiones. Esta debe
    | corresponder a una conexión definida en tus opciones de base de datos.
    |
    */

    'connection' => env('SESSION_CONNECTION'),

    /*
    |--------------------------------------------------------------------------
    | Tabla de Base de Datos para Sesiones
    |--------------------------------------------------------------------------
    |
    | Cuando utilices el controlador de sesión "database", puedes especificar la
    | tabla que se empleará para almacenar las sesiones. Por defecto ya hay una
    | definida, pero puedes cambiarla por otra tabla si lo deseas.
    |
    */

    'table' => env('SESSION_TABLE', 'sessions'),

    /*
    |--------------------------------------------------------------------------
    | Almacén de Caché para Sesiones
    |--------------------------------------------------------------------------
    |
    | Cuando utilices uno de los motores de sesión basados en caché del framework,
    | puedes definir el almacén de caché que guardará los datos entre peticiones.
    | Debe coincidir con alguno de tus almacenes de caché configurados.
    |
    | Afecta a: "dynamodb", "memcached", "redis"
    |
    */

    'store' => env('SESSION_STORE'),

    /*
    |--------------------------------------------------------------------------
    | Lotería de Limpieza de Sesiones
    |--------------------------------------------------------------------------
    |
    | Algunos controladores de sesión deben limpiar manualmente su ubicación de
    | almacenamiento para eliminar sesiones antiguas. Aquí se definen las
    | probabilidades de que ocurra en una petición (por defecto, 2 de cada 100).
    |
    */

    'lottery' => [2, 100],

    /*
    |--------------------------------------------------------------------------
    | Nombre de la Cookie de Sesión
    |--------------------------------------------------------------------------
    |
    | Aquí puedes cambiar el nombre de la cookie de sesión creada por el
    | framework. Por lo general, no necesitas modificar este valor ya que hacerlo
    | no aporta una mejora significativa de seguridad.
    |
    */

    'cookie' => env(
        'SESSION_COOKIE',
        Str::slug((string) env('APP_NAME', 'laravel')).'-session'
    ),

    /*
    |--------------------------------------------------------------------------
    | Ruta de la Cookie de Sesión
    |--------------------------------------------------------------------------
    |
    | La ruta de la cookie de sesión determina el directorio para el cual la
    | cookie se considerará disponible. Normalmente será la ruta raíz de tu
    | aplicación, pero puedes modificarla cuando sea necesario.
    |
    */

    'path' => env('SESSION_PATH', '/'),

    /*
    |--------------------------------------------------------------------------
    | Dominio de la Cookie de Sesión
    |--------------------------------------------------------------------------
    |
    | Este valor determina el dominio y los subdominios para los que estará
    | disponible la cookie de sesión. Por defecto estará disponible para el
    | dominio raíz sin subdominios. Habitualmente no debe cambiarse.
    |
    */

    'domain' => env('SESSION_DOMAIN'),

    /*
    |--------------------------------------------------------------------------
    | Cookies Solo por HTTPS
    |--------------------------------------------------------------------------
    |
    | Al establecer esta opción en true, las cookies de sesión solo se enviarán
    | de vuelta al servidor si el navegador dispone de una conexión HTTPS. Esto
    | evita que la cookie se envíe cuando no pueda hacerse de forma segura.
    |
    */

    'secure' => env('SESSION_SECURE_COOKIE'),

    /*
    |--------------------------------------------------------------------------
    | Acceso Solo por HTTP (HttpOnly)
    |--------------------------------------------------------------------------
    |
    | Establecer este valor en true evitará que JavaScript acceda al valor de la
    | cookie, haciéndola accesible únicamente mediante el protocolo HTTP. No es
    | recomendable desactivar esta opción.
    |
    */

    'http_only' => env('SESSION_HTTP_ONLY', true),

    /*
    |--------------------------------------------------------------------------
    | Cookies Same-Site
    |--------------------------------------------------------------------------
    |
    | Esta opción determina cómo se comportan tus cookies cuando se producen
    | solicitudes entre sitios (cross-site) y ayuda a mitigar ataques CSRF. Por
    | defecto se establece en "lax" para permitir peticiones seguras entre sitios.
    |
    | Ver: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Set-Cookie#samesitesamesite-value
    |
    | Soportados: "lax", "strict", "none", null
    |
    */

    'same_site' => env('SESSION_SAME_SITE', 'lax'),

    /*
    |--------------------------------------------------------------------------
    | Cookies Particionadas
    |--------------------------------------------------------------------------
    |
    | Establecer este valor en true vinculará la cookie al sitio de nivel
    | superior en un contexto entre sitios. Los navegadores aceptan cookies
    | particionadas cuando llevan "secure" y el atributo Same-Site en "none".
    |
    */

    'partitioned' => env('SESSION_PARTITIONED_COOKIE', false),

];
