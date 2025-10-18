<?php
$DB_HOST = 'dpg-d3q20l3ipnbc73a8s99g-a';  // Cambia el host según el tuyo
$DB_USER = 'riego_bdd_user';  // Tu nombre de usuario de la base de datos
$DB_PASS = '9gd793BzKHqx2A4IaU8E5W8syFS03jUP';  // Tu contraseña de la base de datos
$DB_NAME = 'riego_bdd';  // El nombre de tu base de datos

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
