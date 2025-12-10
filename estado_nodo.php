<?php
header('Content-Type: application/json');

function getCPU() {
    $load = sys_getloadavg();
    return round($load[0], 2);
}

function getRAM() {
    $mem = @file("/proc/meminfo", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $total = 0;
    $free  = 0;

    foreach ($mem as $line) {
        if (!str_contains($line, ":")) continue;

        list($key, $val) = explode(":", $line, 2);
        $val = trim(filter_var($val, FILTER_SANITIZE_NUMBER_INT));

        if ($key === "MemTotal") $total = (int)$val;
        if ($key === "MemAvailable") $free = (int)$val;
    }

    if ($total === 0) return 0;

    $used = $total - $free;
    return round(($used / $total) * 100, 1); // devuelve solo 0–100.0
}


function getOS() {
    return php_uname();
}

echo json_encode([
    "cpu" => getCPU(),
    "ram" => getRAM(),
    "so"  => getOS()
]);

