<?php
$DB_HOST = 'dpg-d34rqr9r0fns73bj4kqg-a';  // Cambia el host según el tuyo
$DB_USER = 'dbriego_r97g_user';  // Tu nombre de usuario de la base de datos
$DB_PASS = 'dWPEOpXgMO9rkNJALg5HWdoUEmP2QcOg';  // Tu contraseña de la base de datos
$DB_NAME = 'dbriego_r97g';  // El nombre de tu base de datos

try {
    // Usamos PDO para conectar con PostgreSQL
    $conn = new PDO("pgsql:host=$DB_HOST;dbname=$DB_NAME", $DB_USER, $DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("SET TIME ZONE 'America/Lima';");
} catch (PDOException $e) {
    die("Error al conectar a la base de datos: " . $e->getMessage());
}
?>
