<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Paths
    |--------------------------------------------------------------------------
    |
    | Aquí defines los endpoints que estarán sujetos a CORS. Normalmente
    | tus rutas API van con el prefijo "api/*".
    |
    */
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    /*
    |--------------------------------------------------------------------------
    | Allowed Methods
    |--------------------------------------------------------------------------
    |
    | Métodos HTTP permitidos. '*' permite todos.
    |
    */
    'allowed_methods' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | Allowed Origins
    |--------------------------------------------------------------------------
    |
    | Orígenes permitidos para realizar las solicitudes. Puedes agregar
    | localhost, la IP de tu máquina, o '*' para permitir todos (no recomendado en producción).
    |
    */
    'allowed_origins' => [
        'https://coopceo.ddns.net:8000',
    ],

    /*
    |--------------------------------------------------------------------------
    | Allowed Origins Patterns
    |--------------------------------------------------------------------------
    |
    | Permite usar expresiones regulares para coincidir con orígenes.
    |
    */
    'allowed_origins_patterns' => [],

    /*
    |--------------------------------------------------------------------------
    | Allowed Headers
    |--------------------------------------------------------------------------
    |
    | Los headers permitidos en las solicitudes. '*' permite todos.
    |
    */
    'allowed_headers' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | Exposed Headers
    |--------------------------------------------------------------------------
    |
    | Headers que se exponen al frontend.
    |
    */
    'exposed_headers' => [],

    /*
    |--------------------------------------------------------------------------
    | Max Age
    |--------------------------------------------------------------------------
    |
    | Tiempo que los navegadores pueden cachear la respuesta CORS preflight.
    |
    */
    'max_age' => 0,

    /*
    |--------------------------------------------------------------------------
    | Supports Credentials
    |--------------------------------------------------------------------------
    |
    | Si tu frontend envía cookies o tokens de sesión, esto debe estar en true.
    |
    */
    'supports_credentials' => true,

];
