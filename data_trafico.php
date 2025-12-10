<?php
header('Content-Type: application/json');
date_default_timezone_set("America/Santiago");

$logDir = "/var/log/YSFReflector";
$archivos = glob("$logDir/YSFReflector-*.log");
rsort($archivos);
$archivos = array_slice($archivos, 0, 7); // últimos 7 días

$actividadPorHora = array_fill(0, 24, 0);
$conexionesPorDia = [];
$topIndicativos = [];
$lastHeard = [];
$totalTx = 0;
$usuariosUnicos = [];

foreach ($archivos as $archivo) {
    if (!file_exists($archivo)) continue;
    $lineas = file($archivo);
    $fechaArchivo = basename($archivo);
    if (preg_match('/YSFReflector-(\d{4}-\d{2}-\d{2})\.log/', $fechaArchivo, $match)) {
        $fecha = $match[1];
        $conexionesPorDia[$fecha] = $conexionesPorDia[$fecha] ?? 0;
    }

    foreach ($lineas as $linea) {
        if (strpos($linea, "Received data from") !== false) {
            if (preg_match('/(\d{2}):(\d{2}):(\d{2}).*?from\s+([A-Z0-9\-]+)\s+to\s+([A-Z0-9\-]+)/', $linea, $m)) {
                $hora = (int)$m[1];
                $minuto = $m[2];
                $segundo = $m[3];
                $indicativo = $m[4];
                $destino = $m[5];

                $actividadPorHora[$hora]++;
                $topIndicativos[$indicativo] = ($topIndicativos[$indicativo] ?? 0) + 1;
                if (isset($fecha)) $conexionesPorDia[$fecha]++;

                $usuariosUnicos[$indicativo] = true;
                $totalTx++;

                if (count($lastHeard) < 5) {
                    $lastHeard[] = [
                        "hora" => "$hora:$minuto",
                        "de" => $indicativo,
                        "a" => $destino
                    ];
                }
            }
        }
    }
}

ksort($conexionesPorDia);
arsort($topIndicativos);
$topIndicativos = array_slice($topIndicativos, 0, 10, true);

$resultado = [
    "actividad_por_hora" => $actividadPorHora,
    "conexiones_por_dia" => $conexionesPorDia,
    "top_indicativos" => $topIndicativos,
    "last_heard" => $lastHeard,
    "total_tx" => $totalTx,
    "total_usuarios" => count($usuariosUnicos)
];

echo json_encode($resultado);

