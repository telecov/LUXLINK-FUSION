<?php
session_start();

// Solo permitir si está autenticado
if (!isset($_SESSION['acceso_configuracion'])) {
    die('Acceso denegado');
}

// Validar datos
if (isset($_POST['token']) && isset($_POST['chat_id'])) {

    $token   = trim($_POST['token']);
    $chat_id = trim($_POST['chat_id']);
    $canal   = trim($_POST['canal'] ?? ''); // NUEVO CAMPO

    // Construir el arreglo completo
    $config = [
        'token'   => $token,
        'chat_id' => $chat_id,
        'canal'   => $canal
    ];

    // Guardar en JSON
    file_put_contents(
        __DIR__ . '/telegram_config.json',
        json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    );

    // Redirigir de vuelta a configuración
    header('Location: configuracion.php?ok=1');
    exit;

} else {
    echo "Datos inválidos.";
}
?>
