<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ip = trim($_POST['ip_wlan'] ?? '');
    $gw = trim($_POST['gw_wlan'] ?? '');

    if ($ip && $gw) {
        // Define el nombre de la conexión
        $conexion = 'wlan0';

        // Primero cambia el método a manual (estático)
        shell_exec("sudo nmcli con modify $conexion ipv4.method manual");

        // Asigna IP, gateway y DNS
        shell_exec("sudo nmcli con modify $conexion ipv4.addresses $ip/24");
        shell_exec("sudo nmcli con modify $conexion ipv4.gateway $gw");
        shell_exec("sudo nmcli con modify $conexion ipv4.dns '8.8.8.8 1.1.1.1'");

        // Aplica los cambios reiniciando la conexión
        shell_exec("sudo nmcli con down $conexion");
        shell_exec("sudo nmcli con up $conexion");
    }
    header("Location: configuracion.php");
    exit;
} else {
    echo "Acceso denegado.";
    http_response_code(403);
}
?>

