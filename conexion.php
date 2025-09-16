<?php
$host = 'dpg-d34utfd6ubrc73comehg-a';
$dbname = 'riego_chz9';
$user = 'riego';
$password = 'I63CK60lAkVJzf4IDpkxfKaBd3yh0pg3';

try {
    // Conexión con PDO para PostgreSQL
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    // Establece el modo de errores a excepciones
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error al conectar a la base de datos: " . $e->getMessage();
}
?>
