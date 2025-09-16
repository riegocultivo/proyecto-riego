<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

$query = "SELECT 
    temperatura, 
    humedad_suelo, 
    TO_CHAR(fecha_hora, 'HH24:MI') AS hora
FROM lecturas 
WHERE fecha_hora >= NOW() - INTERVAL '24 HOURS' 
ORDER BY fecha_hora ASC";

$stmt = $conn->query($query);

$temperaturas = [];
$humedades = [];
$horas = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $temperaturas[] = $row['temperatura'];
    $humedades[] = $row['humedad_suelo'];
    $horas[] = $row['hora'];
}

echo json_encode([
    'temperaturas' => $temperaturas,
    'humedades' => $humedades,
    'horas' => $horas
]);
?>
