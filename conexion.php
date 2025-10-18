<?php
$host = 'dpg-d3q20l3ipnbc73a8s99g-a';
$dbname = 'riego_bdd';
$user = 'riego_bdd_user';
$password = '9gd793BzKHqx2A4IaU8E5W8syFS03jUP';

try {
    // Conexión con PDO para PostgreSQL
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    // Establece el modo de errores a excepciones
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error al conectar a la base de datos: " . $e->getMessage();
}
?>
