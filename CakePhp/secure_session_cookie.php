Set secure flag to create csrfToken cookie to prevent `TLS cookie without secure flag set` attack
<?php
# In src/Application.php
function middleware(MiddlewareQueue $middlewareQueue): MiddlewareQueue
{
    $csrf = new CsrfProtectionMiddleware([
            'httponly' => true,
            'secure' => !Configure::read('IS_DEVELOPMENT_MODE'),
            'samesite' => 'Strict'
        ]);
}