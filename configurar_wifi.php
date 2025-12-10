<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ssid = trim($_POST['ssid'] ?? '');
    $pass = trim($_POST['wifi_pass'] ?? '');

    if ($ssid && $pass) {
        $interface = 'wlan0'; // Tu interfaz WiFi según lo que me mostraste

        // Crea una nueva conexión WiFi con NetworkManager (nmcli)
        // Primero borra si ya existe una conexión previa con el mismo nombre
        shell_exec("sudo nmcli connection delete '$ssid' 2>/dev/null");

        // Ahora crea y configura la nueva conexión
        shell_exec("sudo nmcli device wifi connect \"$ssid\" password \"$pass\" ifname $interface");

        // Levanta la conexión por si no quedó activa
        shell_exec("sudo nmcli connection up '$ssid'");
    }

    header("Location: configuracion.php");
    exit;
} else {
    echo "Acceso denegado.";
    http_response_code(403);
}
?>

