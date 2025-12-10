<?php
$estilo = json_decode(@file_get_contents(__DIR__ . '/includes/estilo.json'), true);

$colorPrimario = $estilo['color_primario'] ?? '#0d47a1';
$colorSecundario = $estilo['color_secundario'] ?? '#eeeeee';

?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Tráfico YSF - LuxLink Fusion</title>
  <link rel="icon" type="image/png" href="img/favicon.png">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/style_sidebar.css">  

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
</head>
<body style="background-color: <?= htmlspecialchars($colorSecundario) ?>;">


<header style="background-color: <?= htmlspecialchars($colorPrimario) ?>;">
  <h1>TRAFICO REFLECTOR YSF</h1>
  
</header>

<?php include 'includes/sidebar.php'; ?>


<div class="container">
  <div class="grafico"><h3>Actividad por Hora</h3><div class="scroll-x">
    <canvas id="graficoHora"></canvas></div>
  </div>
  <div class="grafico"><h3>Conexiones por Día</h3><div class="scroll-x">
    <canvas id="graficoDia"></canvas></div>
  </div>
  <div class="grafico"><h3>Top 10 Indicativos</h3><div class="scroll-x">
    <canvas id="graficoTop"></canvas></div>
  </div>
  <div class="grafico"><h3>Distribución por País</h3><div class="scroll-x">
    <canvas id="graficoDistribucion"></canvas></div>
  </div>
  <div class="grafico"><h3>Últimos 5 Comunicados</h3><div class="scroll-x">
    <ul id="listaUltimos"></ul></div>
  </div>
  <div class="grafico"><h3>Resumen Diario</h3><div class="scroll-x">
    <table style="width:100%">
      <tr><td>Total TX:</td><td id="resumenTx"></td></tr>
      <tr><td>Usuarios Únicos:</td><td id="resumenUsuarios"></td></tr>
    </table></div>
  </div>
</div>

<script>
// Primero cargamos prefijos.json
fetch('prefijos.json')
  .then(response => response.json())
  .then(prefijoPais => {

    function obtenerPais(indicativo) {
      const prefijo2 = indicativo.substring(0, 2).toUpperCase();
      const prefijo1 = indicativo.substring(0, 1).toUpperCase();
      return prefijoPais[prefijo2] || prefijoPais[prefijo1] || 'Desconocido';
    }

    // Ahora cargamos el tráfico de datos
    fetch('data_trafico.php')
      .then(res => res.json())
      .then(data => {
        const horas = [...Array(24).keys()].map(h => h.toString().padStart(2, '0'));
        new Chart(document.getElementById('graficoHora'), {
          type: 'bar',
          data: {
            labels: horas,
            datasets: [{ label: 'TX por Hora', data: data.actividad_por_hora, backgroundColor: '#0d47a1' }]
          },
          options: { responsive: true, maintainAspectRatio: false }
        });

        const fechas = Object.keys(data.conexiones_por_dia);
        const conexiones = Object.values(data.conexiones_por_dia);
        new Chart(document.getElementById('graficoDia'), {
          type: 'line',
          data: {
            labels: fechas,
            datasets: [{ label: 'Conexiones', data: conexiones, borderColor: '#0d47a1', backgroundColor: 'rgba(13,71,161,0.1)', fill: true }]
          },
          options: { responsive: true, maintainAspectRatio: false }
        });

        const topLabels = Object.keys(data.top_indicativos);
        const topData = Object.values(data.top_indicativos);
        new Chart(document.getElementById('graficoTop'), {
          type: 'bar',
          data: {
            labels: topLabels,
            datasets: [{ label: 'TX por Indicativo', data: topData, backgroundColor: '#0d47a1' }]
          },
          options: { responsive: true, maintainAspectRatio: false }
        });

        const paises = {};
        topLabels.forEach((indicativo, i) => {
          const pais = obtenerPais(indicativo);
          paises[pais] = (paises[pais] || 0) + topData[i];
        });
        const paisLabels = Object.keys(paises);
        const paisData = Object.values(paises);
        new Chart(document.getElementById('graficoDistribucion'), {
          type: 'pie',
          data: {
            labels: paisLabels,
            datasets: [{
              label: 'Distribución por País',
              data: paisData,
              backgroundColor: paisLabels.map((_, i) => `hsl(${i * 36}, 70%, 60%)`)
            }]
          },
          options: { responsive: true, maintainAspectRatio: false }
        });

        const ul = document.getElementById('listaUltimos');
        data.last_heard.forEach(e => {
          const li = document.createElement('li');
          li.textContent = `${e.hora} - ${e.de} → ${e.a}`;
          ul.appendChild(li);
        });

        document.getElementById('resumenTx').innerText = data.total_tx || 0;
        document.getElementById('resumenUsuarios').innerText = data.total_usuarios || 0;
      });
  })
  .catch(error => console.error('Error cargando prefijos:', error));
</script>
<script>
  lucide.createIcons();
</script>

<div class="footer">
  🚀 Dashboard web LUXLINK FUSION Desarrollado por <strong>Telecoviajero - CA2RDP</strong> |
     <a href="https://github.com/telecov/LUXLINK-FUSION" target="_blank" class="text-info text-decoration-none">GitHub</a>
     2024 -2025 Telecoviajero – CA2RDP.
</div>



</body>

</html>

