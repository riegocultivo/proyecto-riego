<?php
$DB_HOST = 'dpg-dbphq6jrjlhs73fvij60-a';  // Cambia el host según el tuyo
$DB_USER = 'riego_3';  // Tu nombre de usuario de la base de datos
$DB_PASS = 'ftynYIL7NNsyYRSsmMc2E0Kxrj6Vb1qb';  // Tu contraseña de la base de datos
$DB_NAME = 'riego_3_q1vs';  // El nombre de tu base de datos

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
