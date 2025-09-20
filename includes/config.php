<?php
$DB_HOST = 'dpg-d34utfd6ubrc73comehg-a';  // Cambia el host según el tuyo
$DB_USER = 'riego';  // Tu nombre de usuario de la base de datos
$DB_PASS = 'I63CK60lAkVJzf4IDpkxfKaBd3yh0pg3';  // Tu contraseña de la base de datos
$DB_NAME = 'riego_chz9';  // El nombre de tu base de datos

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
