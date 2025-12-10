<?php
session_start();
$clave_configuracion = 'luxlink2024'; // Puedes cambiarla

// Validación de acceso
if (!isset($_SESSION['acceso_configuracion'])) {
    if (!isset($_POST['clave']) || $_POST['clave'] !== $clave_configuracion) {

        echo '<form method="post" 
                style="
                max-width:400px;
                margin:80px auto;
                font-family:Montserrat,sans-serif;
                background:white;
                padding:25px;
                border-radius:12px;
                box-shadow:0 2px 10px rgba(0,0,0,0.1);
                ">
            <h2 style="text-align:center;">🔐 Acceso a Configuración</h2>

            <label style="font-weight:bold;">Contraseña:</label>
            <input type="password" name="clave"
                style="
                width:100%;
                padding:10px;
                margin-top:10px;
                margin-bottom:15px;
                border:1px solid #ccc;
                border-radius:6px;
                ">

            <button type="submit"
                style="
                background:#0d47a1;
                color:white;
                padding:12px;
                width:100%;
                border:none;
                border-radius:6px;
                font-weight:bold;
                cursor:pointer;
                ">
                Entrar
            </button>

          </form>';
        exit;
    } else {
        $_SESSION['acceso_configuracion'] = true;
    }
}

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

// Cargar configuración YSFReflector.ini
$ini = [];
$current_section = null;

if (file_exists('/etc/YSFReflector.ini')) {
    $lines = file('/etc/YSFReflector.ini');
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === ';' || $line[0] === '#') continue;

        if (preg_match('/^\[(.+)\]$/', $line, $m)) {
            $current_section = $m[1];
            $ini[$current_section] = [];
        } elseif (strpos($line, '=') !== false && $current_section !== null) {
            list($key, $value) = explode('=', $line, 2);
            $ini[$current_section][trim($key)] = trim($value, " \"");
        }
    }
}

$name  = $ini['Info']['Name'] ?? '';
$desc  = $ini['Info']['Description'] ?? '';
$port  = $ini['Network']['Port'] ?? '';

$ip_eth  = trim(shell_exec("hostname -I | awk '{print $1}'"));
$ip_wlan = trim(shell_exec("hostname -I | awk '{print $2}'"));
$redes   = shell_exec("sudo nmcli -t -f SSID device wifi list");
$ssids   = array_filter(array_unique(explode("\n", trim($redes))));

// Cargar config Telegram
$telegram_json = __DIR__ . '/telegram_config.json';
$telegram_cfg  = file_exists($telegram_json)
    ? json_decode(file_get_contents($telegram_json), true)
    : [];

$token_actual   = $telegram_cfg['token'] ?? '';
$chat_id_actual = $telegram_cfg['chat_id'] ?? '';
$canal_actual = $telegram_cfg['canal'] ?? '';

// Cargar estilo dinámico
$estilo = json_decode(@file_get_contents(__DIR__ . '/includes/estilo.json'), true);
$colorPrimario = $estilo['color_primario'] ?? '#0d47a1';
$colorSecundario = $estilo['color_secundario'] ?? '#eeeeee';
$banner = $estilo['banner'] ?? 'banner_luxlinkfusion.jpg';
$titulo = $estilo['titulo'] ?? 'LuxLink Fusion';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($titulo) ?> - Configuración</title>
    <link rel="icon" type="image/png" href="img/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/style_sidebar.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
</head>
<body style="background-color: <?= htmlspecialchars($colorSecundario) ?>;">


<header style="background-color: <?= htmlspecialchars($colorPrimario) ?>;">
    <h1>CONFIGURACION LUXLINK FUSION</h1>
    </header>

<?php include 'includes/sidebar.php'; ?>
<script src="https://unpkg.com/lucide@latest"></script>

<!-- CUERPO -->
<div class="container">

    <!-- YSFReflector.ini -->
    <div class="grafico">
        <h3>YSFReflector.ini</h3>
        <form action="guardar_configuracion.php" method="post">

            <div class="form-group">
                <label>[Info] Name</label>
                <input type="text" name="name" value="<?= htmlspecialchars($name) ?>">
            </div>

            <div class="form-group">
                <label>[Info] Description</label>
                <input type="text" name="description" value="<?= htmlspecialchars($desc) ?>">
            </div>

            <div class="form-group">
                <label>[Network] Port</label>
                <input type="text" name="port" value="<?= htmlspecialchars($port) ?>">
            </div>

            <button type="submit">Aplicar Cambios</button>
        </form>
    </div>

        <!-- Telegram -->
    <div class="grafico">
        <h3>Configuración de Telegram</h3>
        <form action="guardar_telegram.php" method="post">

            <div class="form-group">
                <label>Bot Token</label>
                <input type="text" name="token" value="<?= htmlspecialchars($token_actual) ?>">
            </div>

            <div class="form-group">
                <label>Chat ID (administradores)</label>
                <input type="text" name="chat_id" value="<?= htmlspecialchars($chat_id_actual) ?>">
            </div>

            <div class="form-group">
                <label>Canal Oficial (URL)</label>
                <input type="text" name="canal"
                       placeholder="https://t.me/LUXLINK_FUSION"
                       value="<?= htmlspecialchars($canal_actual) ?>">
            </div>

            <button type="submit">Guardar Configuración</button>

            <a href="probar_telegram.php"
                style="
                display:block;
                background:#4caf50;
                color:white;
                padding:12px;
                text-align:center;
                border-radius:6px;
                text-decoration:none;
                margin-top:10px;
                font-weight:bold;
                ">
                Probar Telegram
            </a>
        </form>
    </div>


    <!-- Acciones -->
    <div class="grafico">
        <h3>Acciones del Sistema</h3>
        <form action="accion_servicio.php" method="post"
            style="display:flex;flex-wrap:wrap;gap:10px;">
            <button name="accion" value="start">Iniciar Servicio</button>
            <button name="accion" value="stop">Detener Servicio</button>
            <button name="accion" value="restart">Reiniciar Servicio</button>
            <button name="accion" value="reboot">Reiniciar Servidor</button>
        </form>
    </div>

    <!-- WiFi -->
    <div class="grafico">
        <h3>Configuración WiFi</h3>

        <form action="configurar_wifi.php" method="post">
            <div class="form-group">
                <label>SSID</label>
                <select name="ssid">
                    <?php foreach ($ssids as $s): ?>
                        <option value="<?= htmlspecialchars($s) ?>">
                            <?= htmlspecialchars($s) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Contraseña</label>
                <input type="text" name="wifi_pass">
            </div>

            <button type="submit">Aplicar WiFi</button>
        </form>
    </div>

    <!-- IP Ethernet -->
    <div class="grafico">
        <h3>IP Ethernet</h3>
        <form action="configurar_eth.php" method="post">

            <div class="form-group">
                <label>IP actual: <?= htmlspecialchars($ip_eth) ?></label>
                <input type="text" name="ip_eth" value="<?= htmlspecialchars($ip_eth) ?>">
            </div>

            <div class="form-group">
                <label>Gateway</label>
                <input type="text" name="gw_eth">
            </div>

            <button type="submit">Aplicar IP ETH</button>
        </form>
    </div>

    <!-- IP WiFi -->
    <div class="grafico">
        <h3>IP WiFi</h3>
        <form action="configurar_wlan.php" method="post">

            <div class="form-group">
                <label>IP actual: <?= htmlspecialchars($ip_wlan) ?></label>
                <input type="text" name="ip_wlan" value="<?= htmlspecialchars($ip_wlan) ?>">
            </div>

            <div class="form-group">
                <label>Gateway</label>
                <input type="text" name="gw_wlan">
            </div>

            <button type="submit">Aplicar IP WLAN</button>
        </form>
    </div>

</div>

<div class="footer">
  🚀 Dashboard web LUXLINK FUSION Desarrollado por <strong>Telecoviajero - CA2RDP</strong> |
     <a href="https://github.com/telecov/LUXLINK-FUSION" target="_blank" class="text-info text-decoration-none">GitHub</a>
     2024 -2025 Telecoviajero – CA2RDP.
</div>


<script>
  lucide.createIcons();
</script>

</body>
</html>
