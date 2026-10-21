
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $token = htmlspecialchars(
            $_SESSION['csrf_token'],
            ENT_QUOTES,
            'UTF-8'
        );

        return '<input type="hidden" name="csrf_token" value="' . $token . '">';
    }
}
