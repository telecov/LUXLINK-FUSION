<?php
session_start();

/* ==================================================
   LOGIN / PROTECCIÓN
   Usa config_seguridad.json EXISTENTE
================================================== */

$config_file = __DIR__ . '/config_seguridad.json';

// Validación básica de instalación
if (!file_exists($config_file)) {
    http_response_code(500);
    exit('Error crítico: falta config_seguridad.json');
}

$config_seguridad = json_decode(file_get_contents($config_file), true);
$hash_guardado = $config_seguridad['password_hash'] ?? '';

if (!$hash_guardado) {
    http_response_code(500);
    exit('Error crítico: hash inválido');
}

// Login si no hay sesión
if (!isset($_SESSION['acceso_configuracion'])) {
    if (!isset($_POST['clave']) || !password_verify($_POST['clave'], $hash_guardado)) {

        echo '
        <form method="post" style="
            max-width:400px;
            margin:80px auto;
            font-family:Montserrat,sans-serif;
            background:white;
            padding:25px;
            border-radius:12px;
            box-shadow:0 2px 10px rgba(0,0,0,0.1);
        ">
            <h2 style="text-align:center;">🔐 Acceso a Personalización</h2>

            <label><strong>Contraseña</strong></label>
            <input type="password" name="clave" required
                style="
                width:100%;
                padding:10px;
                margin:10px 0 15px;
                border-radius:6px;
                border:1px solid #ccc;
                ">

            <button type="submit"
                style="
                width:100%;
                padding:12px;
                background:#0d47a1;
                color:white;
                border:none;
                border-radius:6px;
                font-weight:bold;
                cursor:pointer;
                ">
                Entrar
            </button>
        </form>';
        exit;
    }

    // Clave correcta → crear sesión
    $_SESSION['acceso_configuracion'] = true;
}

/* ==================================================
   CARGA DE ESTILO ACTUAL
================================================== */

$estilo = json_decode(@file_get_contents(__DIR__ . '/includes/estilo.json'), true);

$colorPrimario   = $estilo['color_primario']   ?? '#0d47a1';
$colorSecundario = $estilo['color_secundario'] ?? '#eeeeee';
$titulo          = $estilo['titulo']           ?? 'LuxLink Fusion';
$banner          = 'img/' . ($estilo['banner'] ?? 'banner_luxlinkfusion.jpg');
$radioaficionado = $estilo['radioaficionado']  ?? 'Radioaficionado';
$zonaHoraria     = $estilo['zona_horaria']     ?? 'America/Santiago';
$ciudadClima     = $estilo['ubicacion_clima']  ?? 'Santiago';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($titulo) ?> - Personalización</title>
<link rel="icon" type="image/png" href="img/favicon.png">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="css/style_sidebar.css">
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
</head>

<body style="background-color: <?= htmlspecialchars($colorSecundario) ?>;">

<header style="background-color: <?= htmlspecialchars($colorPrimario) ?>; position:relative;">
    <h1>PERSONALIZACIÓN LUXLINK FUSION</h1>

    <!-- Cerrar sesión -->
    <form action="logout.php" method="post"
          style="position:absolute; top:14px; right:18px;">
        <button type="submit"
            style="
            background:#c62828;
            color:white;
            border:none;
            padding:8px 14px;
            border-radius:8px;
            font-weight:bold;
            cursor:pointer;
            ">
            🔒 Cerrar sesión
        </button>
    </form>
</header>

<?php include 'includes/sidebar.php'; ?>
<script src="https://unpkg.com/lucide@latest"></script>

<div class="content">
<div class="formulario-wrapper">

    <?php if (isset($_GET['exito']) && $_GET['exito'] == 1): ?>
        <div class="mensaje-exito">
            ✅ ¡Personalización guardada correctamente!
        </div>
    <?php endif; ?>

    <form action="includes/guardar_personalizacion.php"
          method="post"
          enctype="multipart/form-data">

        <div class="form-group">
            <label>Título del sitio</label>
            <input type="text" name="titulo"
                   value="<?= htmlspecialchars($titulo) ?>">
        </div>

        <div class="form-group">
            <label>Nombre del Radioaficionado</label>
            <input type="text" name="radioaficionado"
                   value="<?= htmlspecialchars($radioaficionado) ?>">
        </div>

        <div class="form-group">
            <label>Color primario</label>
            <input type="color" name="color_primario"
                   value="<?= htmlspecialchars($colorPrimario) ?>">
        </div>

        <div class="form-group">
            <label>Color secundario</label>
            <input type="color" name="color_secundario"
                   value="<?= htmlspecialchars($colorSecundario) ?>">
        </div>

        <div class="form-group">
            <label>Subir nuevo banner</label>
            <input type="file" name="banner"
                   accept="image/png,image/jpeg,image/jpg">
        </div>

        <div class="form-group">
            <label>Zona horaria</label>
            <input type="text" name="zona_horaria"
                   value="<?= htmlspecialchars($zonaHoraria) ?>">
        </div>

        <div class="form-group">
            <label>Ciudad para el clima</label>
            <input type="text" name="ubicacion_clima"
                   value="<?= htmlspecialchars($ciudadClima) ?>">
        </div>

        <button type="submit">Guardar cambios</button>
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
