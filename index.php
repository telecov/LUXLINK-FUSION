<?php
$estilo = json_decode(@file_get_contents(__DIR__ . '/includes/estilo.json'), true);
$versionData = json_decode(@file_get_contents(__DIR__ . '/includes/version.json'), true);

$versionSistema = $versionData['version'] ?? 'dev';
$colorPrimario   = $estilo['color_primario']   ?? '#0d47a1';
$colorSecundario = $estilo['color_secundario'] ?? '#eeeeee';
$titulo          = $estilo['titulo']           ?? 'LucasReflector - Dashboard';
$banner = 'img/' . ($estilo['banner'] ?? 'banner_luxlinkfusion.jpg');
$zonaHoraria     = $estilo['zona_horaria']     ?? 'America/Santiago';
$ciudadClima     = $estilo['ubicacion_clima']  ?? 'Santiago';
$unidadTemp = $estilo['unidad_temperatura'] ?? 'F';
$radioaficionado = $estilo['radioaficionado']  ?? 'Radioaficionado';

date_default_timezone_set($zonaHoraria);
$uptime  = @shell_exec('uptime -p');
$cpu_load = sys_getloadavg();
$cpu = round(($cpu_load[0] / 4) * 100);
$hora_actual = date('H:i');
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($titulo) ?></title>
  <link rel="icon" type="image/png" href="img/favicon.png">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/lucide@latest"></script>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/style_sidebar.css">
</head>
<body style="background-color: <?= htmlspecialchars($colorSecundario) ?>;">


<header style="background-color: <?= htmlspecialchars($colorPrimario) ?>;">
  <h1><?= htmlspecialchars($titulo) ?></h1>
  <img src="<?= htmlspecialchars($banner) ?>" alt="Banner" class="banner">
</header>

<?php include 'includes/sidebar.php'; ?>

<div class="container">
  <div class="card tx-card" id="txCard">
    <h3><i data-lucide="radio"></i> Transmisión en Vivo</h3>
    <p><strong>Estado:</strong> <span id="txEstado">Inactivo</span></p>
    <p><strong>Usuario:</strong> <span id="txUsuario">-</span></p>
  </div>

  <div class="card">
    <h3><i data-lucide="home"></i> Bienvenido <?= htmlspecialchars($radioaficionado) ?></h3>
    <p class="mb-1">🕐 Hora actual: <?= $hora_actual ?> UTC</p>
    <p class="mb-1">⚡ Estado Nodo: <span class="text-success fw-bold">Operativo</span></p>
    <p class="mb-1">🕰️ <?= htmlspecialchars(trim($uptime)) ?></p>
  </div>

  <div class="card">
    <h3><i data-lucide="server"></i> Conectividad YSF</h3>
    <p class="mb-1">🔵 Repetidores / Hotspots: <strong><span id="totalRepetidores">0</span></strong></p>
    <p class="mb-1">🟩 Bridges : <strong><span id="totalBridges">0</span></strong></p>
    <p class="mb-1">🟨 Estaciones / Apps móviles: <strong><span id="totalMoviles">0</span></strong></p>
    <p class="mb-1">👥 Usuarios activos (hoy): <strong><span id="totalUsuarios">0</span></strong></p>
  </div>

  <div class="card" id="horaCard">
    <h3><i data-lucide="clock"></i> Hora</h3>
    <p><strong>Local:</strong> <span id="horaLocal">-</span></p>
    <p><strong>UTC:</strong> <span id="horaUTC">-</span></p>
  </div>

  <div class="card" id="climaCard">
    <h3><i data-lucide="cloud-sun"></i> Clima</h3>
    <p><span id="climaInfo">Cargando...</span></p>
  </div>

  <div class="card" id="sistemaCard">
    <h3><i data-lucide="cpu"></i> Estado del Sistema</h3>
    <p><strong>CPU:</strong> <span id="cpuInfo">Cargando...</span></p>
    <p><strong>RAM:</strong> <span id="ramInfo">Cargando...</span></p>
    <p><strong>SO:</strong> <span id="soInfo">Cargando...</span></p>
  </div>
</div>

<div class="container" style="grid-template-columns: 1fr;">

  <!-- 🔗 TABLA ÚNICA UNIFICADA -->
  <div class="card">
    <h3>🔗 Conectados YSF (Unificado)</h3>
    <table id="tablaUnificada">
      <thead>
        <tr>
          <th>Indicativo</th>
          <th>IP</th>
          <th>Puerto</th>
          <th>Tipo</th>
          <th>Ratio</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>

  <div class="card">
    <h3>🏆 Podio de Actividad YSF</h3>
    <table id="tablaPodio">
      <thead><tr><th>#</th><th>Indicativo</th><th>TX</th><th>Última</th></tr></thead>
      <tbody></tbody>
    </table>
  </div>

  <div class="card">
    <h3>📻 Últimos 5 Comunicados</h3>
    <table id="tablaLastHeard">
      <thead><tr><th>Hora</th><th>Desde</th><th>Hacia</th></tr></thead>
      <tbody></tbody>
    </table>
  </div>

</div>

<div class="footer">
  🚀 Dashboard web LUXLINK FUSION Desarrollado por <strong>Telecoviajero - CA2RDP</strong> | version <?= htmlspecialchars($versionSistema) ?>
     <a href="https://github.com/telecov/LUXLINK-FUSION" target="_blank" class="text-info text-decoration-none">GitHub</a>
     2024 -2025 Telecoviajero – CA2RDP.
</div>



<script>
  const CONFIG = {
    ciudadClima: "<?= htmlspecialchars($ciudadClima) ?>",
    zonaHoraria: "<?= htmlspecialchars($zonaHoraria) ?>",
    radioaficionado: "<?= htmlspecialchars($radioaficionado) ?>",
    unidadTemperatura: "<?= htmlspecialchars($unidadTemp) ?>"
    // agrega más variables si las necesitas...
  };
</script>

<script src="js/index.js"></script>


</body>
</html>

