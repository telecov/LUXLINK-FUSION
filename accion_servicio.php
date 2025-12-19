<?php
session_start();

/* =====================================
   SEGURIDAD: SOLO CONFIGURACIÓN
===================================== */

// Solo permitir acceso con sesión válida
if (!isset($_SESSION['acceso_configuracion'])) {
    http_response_code(403);
    exit('Acceso denegado');
}

// Solo permitir método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método no permitido');
}

// Acciones permitidas (whitelist)
$accion = $_POST['accion'] ?? '';

$acciones_permitidas = [
    'start',
    'stop',
    'restart',
    'reboot'
];

if (!in_array($accion, $acciones_permitidas, true)) {
    http_response_code(400);
    exit('Acción no válida');
}

/* =====================================
   EJECUCIÓN CONTROLADA
===================================== */

switch ($accion) {
    case 'start':
        shell_exec('sudo systemctl start ysfreflector.service');
        break;

    case 'stop':
        shell_exec('sudo systemctl stop ysfreflector.service');
        break;

    case 'restart':
        shell_exec('sudo systemctl restart ysfreflector.service');
        break;

    case 'reboot':
        shell_exec('sudo reboot');
        break;
}

// Volver a configuración
header('Location: configuracion.php');
exit;
