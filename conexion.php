<?php
$host = 'dpg-d6qo4rua2pns73a4sbo0-a';
$dbname = 'riego_3';
$user = 'riego_3';
$password = 'l85jgMNb8KAik0tECdnka9AcrjhGEgUH';

try {
    // Conexión con PDO para PostgreSQL
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    // Establece el modo de errores a excepciones
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error al conectar a la base de datos: " . $e->getMessage();
}
?>
