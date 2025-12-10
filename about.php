<?php
// Cargar estilos desde estilo.json
$estilo = json_decode(@file_get_contents(__DIR__ . '/includes/estilo.json'), true);
$colorPrimario = $estilo['color_primario'] ?? '#0d47a1';
$colorSecundario = $estilo['color_secundario'] ?? '#eeeeee';
$titulo = $estilo['titulo'] ?? 'LuxLink Fusion';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($titulo) ?> - Acerca de</title>
  <link rel="icon" type="image/png" href="img/favicon.png">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/style_sidebar.css">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">

  <style>
    .redes {
        margin-top: 20px;
        text-align: center;
    }
    .redes a {
        display: inline-block;
        margin: 10px;
        text-decoration: none;
        color: #333;
        font-weight: bold;
        font-size: 0.95rem;
    }
    .redes a:hover {
        color: <?= $colorPrimario ?>;
    }
    .redes img {
        width: 32px;
        height: 32px;
        vertical-align: middle;
        margin-right: 6px;
    }
  </style>
</head>
<body style="background-color: <?= htmlspecialchars($colorSecundario) ?>;">


<header style="background-color: <?= htmlspecialchars($colorPrimario) ?>;">
  <h1>ACERCA DE LUXLINK FUSION</h1>
</header>

<?php include 'includes/sidebar.php'; ?>
<script src="https://unpkg.com/lucide@latest"></script>

<div class="container">

  <div class="grafico">

    <h1 style="color:<?= htmlspecialchars($colorPrimario) ?>;">Acerca de <?= htmlspecialchars($titulo) ?></h1>

    <p>
      <strong>LUXLINK FUSION</strong> es una plataforma moderna y amigable de monitoreo para
      <strong>YSFReflector</strong>, diseñada para entregar información clara, rápida y visual sobre tráfico,
      conexiones activas y estado general del sistema.
    </p>

    <h2 style="color:<?= htmlspecialchars($colorPrimario) ?>;">👦 Inspiración</h2>
    <p>
      Este proyecto nace como un homenaje para <strong>Lucas</strong>, mi hijo.
      Su alegría, energía, detalles y curiosidad dieron vida a este panel.
      Él es la razón del nombre <strong>LuxLink Fusion</strong> (Lux = “Luz / Lucas”).
      Su espíritu brillante está presente en cada sección.
    </p>

    <h2 style="color:<?= htmlspecialchars($colorPrimario) ?>;">⚙ Motor de Conectividad</h2>
    <p>
      El corazón de esta plataforma corre gracias al robusto
      <strong><a href="https://github.com/g4klx/" target="_blank">YSFReflector</a></strong>
      desarrollado por <strong>G4KLX</strong>, y las bases de instalación gracias al repositorio
      de <strong><a href="https://github.com/nostar/DVReflectors" target="_blank">DVReflector</a></strong>.
    </p>

    <h2 style="color:<?= htmlspecialchars($colorPrimario) ?>;">🙌 Agradecimientos</h2>

    <div class="creditos">
      <p><strong>YSFReflector – G4KLX:</strong> Motor principal que hace posible esta plataforma.</p>
      <p><strong>DVReflector – NOSTAR:</strong> Base del sistema que posibilitó esta instalación.</p>
      <p><strong>DVRef – KC1AWV:</strong> Información y estructura usada como referencia.</p>
      <p><strong>CA2RDP – Román :</strong> Creación, diseño, integración y desarrollo completo del sistema.</p>
    </div>

    <h2 style="color:<?= htmlspecialchars($colorPrimario) ?>;">📜 Consideraciones Éticas</h2>
    <p>
      LUXLINK FUSION es un desarrollo independiente inspirado en tecnologías abiertas como
      YSFReflector o dashboards independientes.
    </p>
    <p>
      Este sistema mantiene el espíritu del software libre, colaboración y crecimiento de la
      radioafición digital.
    </p>

    <!-- DONAR -->
    <div class="donar" style="text-align:center;margin:30px 0;">
      <form action="https://www.paypal.com/donate" method="post" target="_top">
        <input type="hidden" name="hosted_button_id" value="3889KP3YBVXLE" />
        <button style="padding: 10px 20px; background-color: #ffc439; border: none; color: black; font-weight: bold; border-radius: 5px; cursor: pointer;">
          ☕ Invítame un café vía PayPal
        </button>
      </form>
    </div>

    <!-- REDES SOCIALES -->
    <div class="redes">
        <h3 style="color:<?= htmlspecialchars($colorPrimario) ?>;">🌐 Visita mis Redes Sociales</h3>

        <a href="https://www.youtube.com/@telecoviajero" target="_blank">
            <img src="img/icon_youtube.png" alt="YouTube"> YouTube
        </a>

        <a href="https://tiktok.com/@telecoviajero" target="_blank">
            <img src="img/icon_tiktok.png" alt="Tiktok"> Tiktok
        </a>

        <a href="https://instagram.com/telecoviajero" target="_blank">
            <img src="img/icon_instagram.png" alt="Instagram"> Instagram
        </a>

        <a href="https://github.com/telecov" target="_blank">
            <img src="img/icon_github.png" alt="GitHub"> GitHub
        </a>
    </div>

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
