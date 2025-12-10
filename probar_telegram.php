<?php
session_start();

// Solo permitir si está autenticado
if (!isset($_SESSION['acceso_configuracion'])) {
    die('Acceso denegado');
}

$config_path = __DIR__ . '/telegram_config.json';

if (!file_exists($config_path)) {
    die('Archivo de configuración de Telegram no encontrado.');
}

$config = json_decode(file_get_contents($config_path), true);

$token   = $config['token']   ?? '';
$chat_id = $config['chat_id'] ?? '';
$canal   = $config['canal']   ?? ''; // NUEVO

if (empty($token) || empty($chat_id)) {
    die('Token o Chat ID no configurados.');
}

$mensaje = "✅ *Prueba exitosa desde LuxLink Fusion!*\n\n"
         . "🔧 *Bot Token:* configurado correctamente.\n"
         . "💬 *Chat ID:* $chat_id\n";

if (!empty($canal)) {
    $mensaje .= "📢 *Canal Oficial:* $canal\n";
}

// Datos de envío
$data = [
    'chat_id' => $chat_id,
    'text' => $mensaje,
    'parse_mode' => 'Markdown'
];

$url = "https://api.telegram.org/bot$token/sendMessage";

$options = [
    'http' => [
        'header'  => "Content-Type:application/x-www-form-urlencoded",
        'method'  => 'POST',
        'content' => http_build_query($data)
    ]
];

$context = stream_context_create($options);
$result = @file_get_contents($url, false, $context);

// Mensaje final al usuario
echo '<div style="font-family:Montserrat, sans-serif; max-width:600px; margin:40px auto;">';

if ($result === FALSE) {
    echo "<h2 style='color:red;'>❌ Error al enviar mensaje de prueba a Telegram.</h2>";
} else {
    echo "<h2 style='color:green;'>✅ Mensaje de prueba enviado correctamente.</h2>";
}

echo '<br><a href="configuracion.php" style="display:inline-block;margin-top:20px;padding:10px 20px;background:#0d47a1;color:white;text-decoration:none;border-radius:5px;">Volver a Configuración</a>';

echo '</div>';
?>
