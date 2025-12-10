<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $accion = $_POST['accion'] ?? '';
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
    default:
      echo "Acción no válida.";
      http_response_code(400);
      exit;
  }
  header("Location: configuracion.php");
  exit;
} else {
  echo "Acceso denegado.";
  http_response_code(403);
}
