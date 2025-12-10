<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $ip = trim($_POST['ip_eth'] ?? '');
  $gw = trim($_POST['gw_eth'] ?? '');

  if ($ip && $gw) {
    $dhcpcd_conf = "/etc/dhcpcd.conf";
    $lineas = file($dhcpcd_conf);
    $nuevo = [];
    $encontrado = false;

    foreach ($lineas as $linea) {
      if (strpos($linea, "interface eth0") !== false) {
        $encontrado = true;
        $nuevo[] = "interface eth0\n";
        $nuevo[] = "static ip_address=$ip/24\n";
        $nuevo[] = "static routers=$gw\n";
        $nuevo[] = "static domain_name_servers=8.8.8.8 1.1.1.1\n";
      } elseif ($encontrado && trim($linea) === "") {
        $encontrado = false;
      } elseif (!$encontrado) {
        $nuevo[] = $linea;
      }
    }

    file_put_contents($dhcpcd_conf, implode("", $nuevo));
    shell_exec("sudo systemctl restart dhcpcd.service");
  }
  header("Location: configuracion.php");
  exit;
} else {
  echo "Acceso denegado.";
  http_response_code(403);
}

