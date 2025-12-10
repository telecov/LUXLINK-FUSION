<?php
$estilo = json_decode(@file_get_contents(__DIR__ . '/includes/estilo.json'), true);
$colorPrimario = $estilo['color_primario'] ?? '#0d47a1';
$colorSecundario = $estilo['color_secundario'] ?? '#eeeeee';
$titulo = $estilo['titulo'] ?? 'LuxLink Fusion';
$banner = 'img/' . ($estilo['banner'] ?? 'banner_luxlinkfusion.jpg');
$radioaficionado = $estilo['radioaficionado'] ?? 'Radioaficionado';
$zonaHoraria = $estilo['zona_horaria'] ?? 'America/Santiago';
$ciudadClima = $estilo['ubicacion_clima'] ?? 'Santiago';
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


<header style="background-color: <?= htmlspecialchars($colorPrimario) ?>;">
    <h1>PERSONALIZACION LUXLINK FUSION</h1>
</header>

<?php include 'includes/sidebar.php'; ?>
<script src="https://unpkg.com/lucide@latest"></script>

<div class="content">
<div class="formulario-wrapper">

    <?php if (isset($_GET['exito']) && $_GET['exito'] == 1): ?>
        <div class="mensaje-exito">✅ ¡Personalización guardada correctamente!</div>
    <?php endif; ?>

    <form action="includes/guardar_personalizacion.php" method="post" enctype="multipart/form-data">

        <div class="form-group">
            <label for="titulo">Título del sitio:</label>
            <input type="text" name="titulo" id="titulo" value="<?= htmlspecialchars($titulo) ?>">
        </div>

        <div class="form-group">
            <label for="radioaficionado">Nombre del Radioaficionado:</label>
            <input type="text" name="radioaficionado" id="radioaficionado" value="<?= htmlspecialchars($radioaficionado) ?>">
        </div>

        <div class="form-group">
            <label for="color_primario">Color primario:</label>
            <input type="color" name="color_primario" id="color_primario" value="<?= htmlspecialchars($colorPrimario) ?>">
        </div>

        <div class="form-group">
            <label for="color_secundario">Color secundario:</label>
            <input type="color" name="color_secundario" id="color_secundario" value="<?= htmlspecialchars($colorSecundario) ?>">
        </div>

        <div class="form-group">
            <label for="banner">Subir nuevo banner (1200x150px):</label>
            <input type="file" name="banner" id="banner">
        </div>

        <div class="form-group">
            <label for="zona_horaria">Zona horaria:</label>
            <input type="text" name="zona_horaria" id="zona_horaria"
                   value="<?= htmlspecialchars($zonaHoraria) ?>"
                   placeholder="Ej: America/Santiago, UTC">
        </div>

        <div class="form-group">
            <label for="ubicacion_clima">Ciudad para el clima:</label>
            <input type="text" name="ubicacion_clima" id="ubicacion_clima" value="<?= htmlspecialchars($ciudadClima) ?>">
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
