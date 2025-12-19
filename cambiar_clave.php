<?php
session_start();

if (!isset($_SESSION['acceso_configuracion'])) {
    header('Location: configuracion.php');
    exit;
}

$nueva   = $_POST['nueva_clave'] ?? '';
$repite  = $_POST['repetir_clave'] ?? '';

if ($nueva !== $repite) {
    die('❌ Las contraseñas no coinciden');
}

if (strlen($nueva) < 8) {
    die('❌ La contraseña debe tener al menos 8 caracteres');
}

$hash = password_hash($nueva, PASSWORD_DEFAULT);

$config_file = __DIR__ . '/config_seguridad.json';

file_put_contents($config_file, json_encode([
    'password_hash' => $hash
], JSON_PRETTY_PRINT));

header('Location: configuracion.php?clave=ok');
exit;
