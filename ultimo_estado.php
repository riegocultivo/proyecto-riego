<?php
header('Content-Type: application/json');

// Conectar a tu base de datos y obtener el último registro
$conexion = new mysqli("localhost", "usuario", "contraseña", "nombre_base_datos");
$consulta = $conexion->query("SELECT temperatura, humedad_suelo, bomba_activa FROM datos ORDER BY id DESC LIMIT 1");
$datos = $consulta->fetch_assoc();

echo json_encode($datos);
$conexion->close();
?>