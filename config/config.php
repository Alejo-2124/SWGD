<?php

// Configuración de la aplicación
define('BASE_URL', 'http://localhost:8080');
define('APP_NAME', 'Medical DMS');
define('UPLOAD_DIR', __DIR__ . '/../uploads/');

// Configuración de sesión - SOLO si no hay sesión activa
if (session_status() === PHP_SESSION_NONE) {
    // Solo configuramos sesiones si no están activas
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0); // Cambiar a 1 si se usa HTTPS
    
    // Configuración adicional de seguridad
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_samesite', 'Lax');
}

// Tipos de archivo permitidos
define('ALLOWED_FILE_TYPES', [
    'application/pdf',
    'image/jpeg',
    'image/png',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
]);

define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB

// Crear directorio de uploads si no existe
if (!file_exists(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}