<?php
include 'telegram_alert.php';

// Configuraciones de uso
define('CPU_THRESHOLD', 75); // CPU alto
define('RAM_THRESHOLD', 80); // RAM alta

$last_daily_report = 0;

function getSystemStats() {
    $uptime = @shell_exec("uptime -p");
    $temp_raw = @file_get_contents('/sys/class/thermal/thermal_zone0/temp');
    $temp = $temp_raw ? round($temp_raw / 1000) . '°C' : 'No disponible';

    $cpu_load = sys_getloadavg();
    $cpu = round(($cpu_load[0] / 4) * 100);

    $meminfo = @file('/proc/meminfo');
    $mem_total = 0;
    $mem_available = 0;
    foreach ($meminfo as $line) {
        if (strpos($line, 'MemTotal:') === 0) {
            $mem_total = intval(preg_replace('/\D/', '', $line));
        }
        if (strpos($line, 'MemAvailable:') === 0) {
            $mem_available = intval(preg_replace('/\D/', '', $line));
        }
    }
    $ram = ($mem_total > 0) ? round((($mem_total - $mem_available) / $mem_total) * 100) : 0;

    return [
        'uptime' => trim($uptime),
        'cpu' => $cpu,
        'ram' => $ram,
        'temp' => $temp
    ];
}

while (true) {
    $fecha = date('Y-m-d');
    $log_file = "/var/log/YSFReflector/YSFReflector-$fecha.log";
    $pos_file = __DIR__ . "/log_position_$fecha.txt";

    $last_pos = file_exists($pos_file) ? intval(file_get_contents($pos_file)) : 0;

    if (file_exists($log_file)) {
        $fp = fopen($log_file, 'r');
        if ($fp) {
            fseek($fp, $last_pos);
            $lines = [];
            while (($line = fgets($fp)) !== false) {
                $lines[] = $line;
            }

            foreach ($lines as $index => $line) {
                // Desconexión
                if (strpos($line, 'Removing') !== false) {
                    if (preg_match('/Removing ([A-Z0-9\-]+)/', $line, $matches)) {
                        $indicativo = trim($matches[1]);
                        $hora = date('Y-m-d H:i:s');
                        $mensaje = "❌ Desconexión detectada\n";
                        $mensaje .= "🔹 Indicativo: *$indicativo*\n";
                        $mensaje .= "🔌 Tipo: *Estación*\n";
                        $mensaje .= "🕒 Hora UTC: *$hora*";
                        enviarAlertaTelegram($mensaje);
                    }
                }
                // Conexión
                elseif (strpos($line, 'Adding') !== false) {
                    if (preg_match('/Adding ([A-Z0-9\-]+).*?:\d+/', $line, $matches)) {
                        $indicativo = trim($matches[1]);
                        $puerto = 0;
                        preg_match('/\(([^:]+):(\d+)\)/', $line, $ipParts);
                        if (isset($ipParts[2])) {
                            $puerto = intval($ipParts[2]);
                        }
                        $tipo = ($puerto === 4260) ? 'Nodo' : 'Estación';
                        $hora = date('Y-m-d H:i:s');
                        $mensaje = "✅ Nueva conexión detectada\n";
                        $mensaje .= "🔹 Indicativo: *$indicativo*\n";
                        $mensaje .= "🔌 Tipo: *$tipo*\n";
                        $mensaje .= "🕒 Hora UTC: *$hora*";
                        enviarAlertaTelegram($mensaje);
                    }
                }
                
            }

            $last_pos = ftell($fp);
            file_put_contents($pos_file, $last_pos);
            fclose($fp);
        }
    }

    // Estadísticas del sistema
    $stats = getSystemStats();
    if ($stats['cpu'] >= CPU_THRESHOLD) {
        enviarAlertaTelegram("⚠️ *Alerta de CPU Alta*\nUso actual: *{$stats['cpu']}%*");
    }
    if ($stats['ram'] >= RAM_THRESHOLD) {
        enviarAlertaTelegram("⚠️ *Alerta de RAM Alta*\nUso actual: *{$stats['ram']}%*");
    }

    // Reporte diario
    $hora_actual = date('H:i');
    if ($hora_actual == '12:00' && date('Ymd', $last_daily_report) != date('Ymd')) {
        enviarAlertaTelegram("📊 *Reporte Diario LuxLink Fusion*\n🕐 *Uptime:* {$stats['uptime']}\n⚡ *CPU:* {$stats['cpu']}%\n📈 *RAM:* {$stats['ram']}%\n🌡️ *Temp CPU:* {$stats['temp']}\n✅ *Estado general:* Operativo");
        $last_daily_report = time();
    }

    sleep(5);
}
?>

