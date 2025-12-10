<?php
date_default_timezone_set("America/Santiago");

// ==========================================================
// 1) Obtener SIEMPRE el log más reciente
// ==========================================================
$logs = glob("/var/log/YSFReflector/YSFReflector-*.log");

if (!$logs) {
    echo json_encode([
        "tx"          => null,
        "last_tx"     => null,
        "last_heard"  => [],
        "nodos"       => [],
        "podio"       => [],
        "repetidores" => [],
        "moviles"     => [],
        "bridges"     => [],
        "totales"     => [
            "repetidores" => 0,
            "moviles"     => 0,
            "bridges"     => 0,
            "otros"       => 0
        ]
    ]);
    exit;
}

// Ordenar por fecha (más nuevo primero)
usort($logs, function($a, $b) {
    return filemtime($b) - filemtime($a);
});
$logFile = $logs[0];

$lineas = @file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
if ($lineas === false) {
    echo json_encode([
        "tx"          => null,
        "last_tx"     => null,
        "last_heard"  => [],
        "nodos"       => [],
        "podio"       => [],
        "repetidores" => [],
        "moviles"     => [],
        "bridges"     => [],
        "totales"     => [
            "repetidores" => 0,
            "moviles"     => 0,
            "bridges"     => 0,
            "otros"       => 0
        ]
    ]);
    exit;
}

// Versión invertida para TX, last_heard, podio
$lineasRev = array_reverse($lineas);

$resultado = [
    "tx"          => null,
    "last_tx"     => null,
    "last_heard"  => [],
    "nodos"       => [],
    "podio"       => [],
    "repetidores" => [],
    "moviles"     => [],
    "bridges"     => [],
    "totales"     => [
        "repetidores" => 0,
        "moviles"     => 0,
        "bridges"     => 0,
        "otros"       => 0
    ]
];

$ultimosComunicados = [];
$ultimaHoraTX       = null;
$finDetectado       = false;
$actividad          = [];

// ==========================================================
// 2) NODOS CONECTADOS
//    a) Intentar usar el último bloque "Currently linked"
//    b) Si no existe, fallback a detección individual
// ==========================================================

$listaActual = [];
$lastBlockIndex = null;

// Buscar la ÚLTIMA ocurrencia del bloque "Currently linked"
for ($i = count($lineas) - 1; $i >= 0; $i--) {
    if (strpos($lineas[$i], "Currently linked repeaters/gateways") !== false) {
        $lastBlockIndex = $i;
        break;
    }
}

if ($lastBlockIndex !== null) {
    // Tenemos bloque → los nodos actuales son SOLO los listados después de esa línea
    for ($j = $lastBlockIndex + 1; $j < count($lineas); $j++) {
        $linea = $lineas[$j];

        // Si ya no calza con formato "M: <fecha> <indicativo> : <ip>:<puerto> ratio/..."
        if (!preg_match(
            '/^\w:\s(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})(?:\.\d+)?\s+([A-Z0-9\-]+)\s+:\s+([\d\.]+):(\d+)\s+(\d+)\/\d+/',
            $linea,
            $m
        )) {
            break; // se acabó el bloque
        }

        $indicativo = trim($m[2]);
        $ip         = $m[3];
        $puerto     = (int)$m[4];
        $ratio      = (int)$m[5];

        $key = $indicativo . '-' . $ip . ':' . $puerto;

        $listaActual[$key] = [
            "indicativo" => $indicativo,
            "ip"         => $ip,
            "puerto"     => $puerto,
            "ratio"      => $ratio
        ];
    }

    // Clasificar nodos de la lista actual
    foreach ($listaActual as $key => $node) {
        $puerto = $node["puerto"];
        $tipo   = "otro";

        if ($puerto === 4260) {
            $tipo = "repetidor";
            $resultado["repetidores"][] = $node;
            $resultado["totales"]["repetidores"]++;
        } elseif ($puerto >= 34000 && $puerto <= 60000) {
            $tipo = "movil";
            $resultado["moviles"][] = $node;
            $resultado["totales"]["moviles"]++;
        
	} elseif ($puerto >= 20000 && $puerto <= 33500) {
            $tipo = "movil";
            $resultado["moviles"][] = $node;
            $resultado["totales"]["moviles"]++;


         } elseif ($puerto >= 33700 && $puerto <=33900) {
            $tipo = "bridge";
            $resultado["bridges"][] = $node;
            $resultado["totales"]["bridges"]++;
        } else {
            $resultado["totales"]["otros"]++;
        }

        $node["tipo"] = $tipo;
        $resultado["nodos"][] = $node;
    }

} else {
    // ======================================================
    // Fallback: NO hay bloque "Currently linked"
    // Recorremos de atrás hacia adelante y tomamos los
    // nodos más recientes únicos por indicativo+IP+puerto
    // ======================================================
    $vistos = [];

    foreach ($lineasRev as $linea) {
        if (preg_match(
            '/^\w:\s(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})(?:\.\d+)?\s+([A-Z0-9\-]+)\s+:\s+([\d\.]+):(\d+)\s+(\d+)\/\d+/',
            $linea,
            $m
        )) {
            $indicativo = trim($m[2]);
            $ip         = $m[3];
            $puerto     = (int)$m[4];
            $ratio      = (int)$m[5];

            $key = $indicativo . '-' . $ip . ':' . $puerto;
            if (isset($vistos[$key])) {
                continue;
            }
            $vistos[$key] = true;

            $node = [
                "indicativo" => $indicativo,
                "ip"         => $ip,
                "puerto"     => $puerto,
                "ratio"      => $ratio
            ];

            if ($puerto === 4260) {
                $tipo = "repetidor";
                $resultado["repetidores"][] = $node;
                $resultado["totales"]["repetidores"]++;
            } elseif ($puerto >= 27000 && $puerto <= 29999) {
                $tipo = "movil";
                $resultado["moviles"][] = $node;
                $resultado["totales"]["moviles"]++;
            } elseif ($puerto >= 30000 && $puerto <= 35000) {
                $tipo = "bridge";
                $resultado["bridges"][] = $node;
                $resultado["totales"]["bridges"]++;
            } else {
                $tipo = "otro";
                $resultado["totales"]["otros"]++;
            }

            $node["tipo"] = $tipo;
            $resultado["nodos"][] = $node;
        }
    }
}

// ==========================================================
// 3) TX, LAST_TX, LAST_HEARD, PODIO
// ==========================================================
foreach ($lineasRev as $linea) {

    // A) Transmisiones: TX, LAST_TX, LAST_HEARD, PODIO
    if (strpos($linea, "Received data from") !== false) {
        if (preg_match(
            '/(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})(?:\.\d+)? .*?from\s+([A-Z0-9\-]+)\s+to\s+([A-Z0-9\-]+)/',
            $linea,
            $m
        )) {
            $horaLog = $m[1];
            $de      = trim($m[2]);
            $a       = trim($m[3]);

            $horaUTC   = gmdate("Y-m-d H:i:s", strtotime($horaLog));
            $horaLocal = date("Y-m-d H:i:s", strtotime($horaUTC . " UTC"));
            $horaUnix  = strtotime($horaLocal);

            // 1) Últimos 5 comunicados
            if (count($ultimosComunicados) < 5) {
                $ultimosComunicados[] = [
                    "hora" => $horaLocal,
                    "de"   => $de,
                    "a"    => $a
                ];
            }

            // 2) TX activa si aún no vimos "end of transmission"
            if (!$finDetectado && $resultado["tx"] === null) {
                $resultado["tx"] = [
                    "de"     => $de,
                    "a"      => $a,
                    "hora"   => $horaLocal,
                    "estado" => "activo"
                ];
            }

            // 3) Última TX
            if ($resultado["last_tx"] === null) {
                $resultado["last_tx"] = [
                    "de"   => $de,
                    "a"    => $a,
                    "hora" => $horaLocal
                ];
            }

            if ($ultimaHoraTX === null) {
                $ultimaHoraTX = $horaUnix;
            }

            // 4) Actividad para podio
            if (!isset($actividad[$de])) {
                $actividad[$de] = [
                    "indicativo" => $de,
                    "tx"         => 0,
                    "ultima"     => $horaLocal
                ];
            }
            $actividad[$de]["tx"]++;
        }
    }

    // Fin de transmisión
    if (!$finDetectado && strpos($linea, "Received end of transmission") !== false) {
        $finDetectado = true;
    }
}

// TX reciente (si fue hace menos de 30s)
if ($resultado["tx"] === null && $ultimaHoraTX !== null && $resultado["last_tx"] !== null) {
    if ((time() - $ultimaHoraTX) <= 30) {
        $resultado["tx"]           = $resultado["last_tx"];
        $resultado["tx"]["estado"] = "reciente";
    }
}

/* ==========================================================
   3-BIS) TIMEOUT DE TX – CORTA TRANSMISIÓN COLGADA
========================================================== */

$TX_TIMEOUT = 60;

if (!empty($resultado["tx"])) {

    if (empty($resultado["tx"]["hora"])) {
        $resultado["tx"] = null;

    } else {
        $horaTX = strtotime($resultado["tx"]["hora"]);

        if ((time() - $horaTX) > $TX_TIMEOUT) {
            $resultado["tx"] = null;
        }
    }
}
// Guardar last_heard
$resultado["last_heard"] = $ultimosComunicados;

// Podio (orden descendente por TX)
if (!empty($actividad)) {
    usort($actividad, function($a, $b) {
        return $b["tx"] <=> $a["tx"];
    });
    $resultado["podio"] = array_values($actividad);
}

// ==========================================================
// 4) Respuesta final JSON
// ==========================================================
header("Content-Type: application/json; charset=utf-8");
echo json_encode($resultado);
