<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $archivo = '/etc/YSFReflector.ini';
  $ini = parse_ini_file($archivo, true);

  $ini['Info']['Name'] = $_POST['name'] ?? $ini['Info']['Name'];
  $ini['Info']['Description'] = $_POST['description'] ?? $ini['Info']['Description'];
  $ini['Network']['Port'] = $_POST['port'] ?? $ini['Network']['Port'];

  $contenido = "";
  foreach ($ini as $seccion => $parametros) {
    $contenido .= "[$seccion]\n";
    foreach ($parametros as $clave => $valor) {
      $contenido .= "$clave=$valor\n";
    }
    $contenido .= "\n";
  }

  file_put_contents($archivo, $contenido);
  shell_exec("sudo systemctl restart ysreflector.service");
  header('Location: configuracion.php');
  exit;
} else {
  echo "Acceso denegado.";
  http_response_code(403);
}

