<?php
// Leer canal oficial desde telegram_config.json
$telegram_json = __DIR__ . '/../telegram_config.json';

$telegram_cfg = file_exists($telegram_json)
    ? json_decode(file_get_contents($telegram_json), true)
    : [];

$canal_oficial = $telegram_cfg['canal'] ?? '';
?>

<nav class="sidebar">
  <ul>
    <li><a href="index.php"><i data-lucide="home"></i> Inicio</a></li>
    <li><a href="trafico_ysf.php"><i data-lucide="radio"></i> Tráfico</a></li>
    <li><a href="conexiones_ysf.php"><i data-lucide="users"></i> Conexiones</a></li>
    <li><a href="personalizacion.php"><i data-lucide="settings"></i> Personalización</a></li>
    <li><a href="configuracion.php"><i data-lucide="sliders"></i> Configuración</a></li>
    <li><a href="about.php"><i data-lucide="info"></i> Acerca</a></li>

    <!-- 🔵 NUEVO BOTÓN TELEGRAM EN SIDEBAR -->
    <?php if (!empty($canal_oficial)): ?>
<li>
  <a href="<?= htmlspecialchars($canal_oficial) ?>" target="_blank" class="telegram-sidebar">
    <i data-lucide="send"></i> Telegram
  </a>
</li>
<?php endif; ?>

  </ul>
</nav>
