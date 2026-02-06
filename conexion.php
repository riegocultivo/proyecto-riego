<?php
$host = 'dpg-d62i4tonputs73b47f60-a';
$dbname = 'riego2';
$user = 'riego2';
$password = 'BilCdLcvjH59YKLlGMccY4NTlDeU8FI8';

try {
    // Conexión con PDO para PostgreSQL
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    // Establece el modo de errores a excepciones
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error al conectar a la base de datos: " . $e->getMessage();
}
?>
