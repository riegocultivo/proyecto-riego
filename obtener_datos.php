<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "riego_automatizado";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "Error de conexión a la base de datos"]));
}

// Obtener el último registro
$sql = "SELECT * FROM mediciones ORDER BY fecha DESC LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $ultimo_registro = $result->fetch_assoc();
    echo json_encode($ultimo_registro);
} else {
    echo json_encode(["error" => "No hay registros disponibles"]);
}

$conn->close();
?>
