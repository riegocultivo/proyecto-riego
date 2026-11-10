<?php
$host = 'dpg-dbphq6jrjlhs73fvij60-a';
$dbname = 'riego_3_q1vs';
$user = 'riego_3';
$password = 'ftynYIL7NNsyYRSsmMc2E0Kxrj6Vb1qb';

try {
    // Conexión con PDO para PostgreSQL
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    // Establece el modo de errores a excepciones
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error al conectar a la base de datos: " . $e->getMessage();
}
?>
