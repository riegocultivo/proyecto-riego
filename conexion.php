<?php
$host = 'dpg-d4jpoinpm1nc7385ua80-a';
$dbname = 'riego_fk6e';
$user = 'riego';
$password = 'lYc6RU7XIyU4vTJpKnqctqqRGSQa9Npb';

try {
    // Conexión con PDO para PostgreSQL
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    // Establece el modo de errores a excepciones
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error al conectar a la base de datos: " . $e->getMessage();
}
?>
