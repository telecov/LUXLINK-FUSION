<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(403);
    exit("Acceso denegado");
}

$baseDir   = dirname(__DIR__);        // /var/www/html/ysf
$imgDir    = $baseDir . '/img';       // carpeta img
$configDir = __DIR__;                 // carpeta includes

$estiloFile = $configDir . '/estilo.json';

// Cargar configuración anterior para NO perder valores
$prev = [];
if (file_exists($estiloFile)) {
    $prev = json_decode(file_get_contents($estiloFile), true);
}

// Nueva configuración con fallback
$data = [
    'titulo'           => $_POST['titulo']           ?? ($prev['titulo'] ?? 'LuxLink Fusion'),
    'radioaficionado'  => $_POST['radioaficionado']  ?? ($prev['radioaficionado'] ?? 'Radioaficionado'),
    'color_primario'   => $_POST['color_primario']   ?? ($prev['color_primario'] ?? '#0d47a1'),
    'color_secundario' => $_POST['color_secundario'] ?? ($prev['color_secundario'] ?? '#eeeeee'),
    'zona_horaria'     => $_POST['zona_horaria']     ?? ($prev['zona_horaria'] ?? 'America/Santiago'),
    'ubicacion_clima'  => $_POST['ubicacion_clima']  ?? ($prev['ubicacion_clima'] ?? 'Santiago'),
    'banner'           => $prev['banner'] ?? 'banner_luxlinkfusion.jpg'
];

// ----------- BANNER -----------
if (isset($_FILES['banner']) && $_FILES['banner']['error'] === UPLOAD_ERR_OK) {

    $ext = strtolower(pathinfo($_FILES['banner']['name'], PATHINFO_EXTENSION));

    // Extensiones permitidas
    $permitidas = ['jpg', 'jpeg', 'png', 'webp'];
    if (in_array($ext, $permitidas)) {

        $nombreArchivo = 'banner_luxlinkfusion.' . $ext;
        $rutaDestino   = $imgDir . '/' . $nombreArchivo;

        if (move_uploaded_file($_FILES['banner']['tmp_name'], $rutaDestino)) {
            $data['banner'] = $nombreArchivo;
        }
    }
}

// ----------- GUARDAR ESTILO.JSON -----------

file_put_contents(
    $estiloFile,
    json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
);

// Redirigir al formulario
header('Location: ../personalizacion.php?exito=1');
exit;
