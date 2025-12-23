<?php
session_start();

/* ================= SEGURIDAD ================= */

if (!isset($_SESSION['acceso_configuracion'])) {
    http_response_code(403);
    exit('⛔ Acceso no autorizado');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método no permitido');
}

/* ================= EJECUCIÓN ================= */

$script = '/var/www/html/includes/update.sh';
$cmd = "sudo $script 2>&1";
$output = shell_exec($cmd);

/* ================= LOG ================= */

$logDir = __DIR__ . '/logs';
if (!is_dir($logDir)) {
    mkdir($logDir, 750, true);
}

file_put_contents(
    $logDir . '/update.log',
    "[" . date('Y-m-d H:i:s') . "]\n" . $output . "\n\n",
    FILE_APPEND
);

/* ================= REDIRECT ================= */

header("Location: configuracion.php?update=ok");
exit;
