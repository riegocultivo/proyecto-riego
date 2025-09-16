<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

$query = "SELECT 
    temperatura, 
    humedad_suelo, 
    bomba_activa, 
    fecha_hora 
FROM lecturas 
ORDER BY fecha_hora DESC 
LIMIT 1";

$stmt = $conn->query($query);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode([
    'temperatura' => round($data['temperatura'], 1),
    'humedad_suelo' => round($data['humedad_suelo'], 1),
    'bomba_activa' => (bool) $data['bomba_activa'],  // Convertir a booleano
    'timestamp' => $data['fecha_hora']
]);
?>
