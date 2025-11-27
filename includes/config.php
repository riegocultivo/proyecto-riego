<?php
$DB_HOST = 'dpg-d4jpoinpm1nc7385ua80-a';  // Cambia el host según el tuyo
$DB_USER = 'dpg-d4jpoinpm1nc7385ua80-a';  // Tu nombre de usuario de la base de datos
$DB_PASS = 'lYc6RU7XIyU4vTJpKnqctqqRGSQa9Npb';  // Tu contraseña de la base de datos
$DB_NAME = 'riego';  // El nombre de tu base de datos

try {
    // Usamos PDO para conectar con PostgreSQL
    // Usamos PDO para conectar con PostgreSQL
    $conn = new PDO("pgsql:host=$DB_HOST;dbname=$DB_NAME", $DB_USER, $DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("SET TIME ZONE 'America/Lima';");
} catch (PDOException $e) {
    die("Error al conectar a la base de datos: " . $e->getMessage());
}
?>
