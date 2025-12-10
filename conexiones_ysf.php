<?php
$estilo = json_decode(@file_get_contents(__DIR__ . '/includes/estilo.json'), true);
$colorPrimario   = $estilo['color_primario']   ?? '#0d47a1';
$colorSecundario = $estilo['color_secundario'] ?? '#eeeeee';
$titulo          = "Conexiones Activas YSF - LuxLink Fusion";
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($titulo) ?></title>
  <link rel="icon" type="image/png" href="img/favicon.png">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/style_sidebar.css">
  <script src="https://unpkg.com/lucide@latest"></script>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">

  <style>
    /* === TARJETAS CLARAS ESTILO LUXLINK FUSION === */
    .ysf-card {
      background: #ffffff;
      border-radius: 15px;
      padding: 15px;
      color: #333;
      border: 1px solid #e0e0e0;
      border-left: 8px solid #4caf50;
      box-shadow: 0 3px 6px rgba(0,0,0,0.08);
      transition: 0.25s;
    }
    .ysf-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 5px 12px rgba(0,0,0,0.12);
    }

    .ysf-title {
      font-size: 1.2em;
      font-weight: 700;
      margin-bottom: 10px;
      display: flex;
      align-items: center;
      gap: 6px;
      color: #222;
    }
    .label {
      font-size: .95em;
      color: #555;
    }

    .badge-activo {
      padding: 6px 12px;
      border-radius: 20px;
      font-size: .85em;
      background: #4caf50;
      color: white;
      display: inline-block;
      margin-top: 10px;
      font-weight: bold;
    }

    /* Colores laterales */
    .rep { border-left-color: #1976d2 !important; } /* azul */
    .mov { border-left-color: #ffb300 !important; } /* amarillo */
    .bri { border-left-color: #43a047 !important; } /* verde */

    /* Contenedor */
    .container {
      margin-top: 20px;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
      gap: 20px;
    }
  </style>
</head>

<body style="background-color: <?= htmlspecialchars($colorSecundario) ?>;">


<header style="background-color: <?= htmlspecialchars($colorPrimario) ?>;">
  <h1>CONEXIONES ACTIVAS YSF</h1>
</header>

<?php include 'includes/sidebar.php'; ?>

<div class="container" id="contenedorYSF">
  <div class="ysf-card" style="grid-column: 1 / -1; text-align:center;" id="loadingCard">
    <h3 style="margin:0;">Cargando conexiones...</h3>
  </div>
</div>

<div class="footer">
  🚀 Dashboard web LUXLINK FUSION Desarrollado por <strong>Telecoviajero - CA2RDP</strong> |
     <a href="https://github.com/telecov/LUXLINK-FUSION" target="_blank" class="text-info text-decoration-none">GitHub</a>
     2024 -2025 Telecoviajero – CA2RDP.
</div>


<script>
  lucide.createIcons();

  function cargarConexiones() {
    fetch("data_index.php")
      .then(res => res.json())
      .then(data => {
        
        const cont = document.getElementById("contenedorYSF");
        cont.innerHTML = ""; 

        const todas = [
          ...(data.repetidores || []),
          ...(data.moviles     || []),
          ...(data.bridges     || [])
        ];

        if (todas.length === 0) {
          cont.innerHTML = `
            <div class="ysf-card" style="grid-column:1 / -1; text-align:center;">
              <h3>Sin conexiones activas</h3>
              <p>No hay estaciones conectadas actualmente al reflector.</p>
            </div>
          `;
          return;
        }

        todas.forEach(n => {
          let clase = "bri";
          let tipo  = "Bridge Interno";
          let icono = "git-branch";

          if (n.tipo === "repetidor") {
            clase = "rep";
            tipo  = "Repetidor / Hotspot";
            icono = "radio";
          }
          if (n.tipo === "movil") {
            clase = "mov";
            tipo  = "Estación Móvil / App";
            icono = "smartphone";
          }

          cont.innerHTML += `
            <div class="ysf-card ${clase}">
              <div class="ysf-title">
                <i data-lucide="${icono}"></i> ${n.indicativo}
              </div>
              <div class="label"><strong>Tipo:</strong> ${tipo}</div>
              <div class="label"><strong>IP:</strong> ${n.ip}:${n.puerto}</div>
              <div class="label"><strong>Ratio:</strong> ${n.ratio}</div>
              <span class="badge-activo">Activo</span>
            </div>
          `;
        });

        lucide.createIcons();
      });
  }

  cargarConexiones();
  setInterval(cargarConexiones, 4000);
</script>

</body>
</html>
