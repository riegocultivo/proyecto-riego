<?php
$host = 'dpg-d34rqr9r0fns73bj4kqg-a';
$dbname = 'dbriego_r97g';
$user = 'dbriego_r97g_user';
$password = 'dWPEOpXgMO9rkNJALg5HWdoUEmP2QcOg';

try {
    // Conexión con PDO para PostgreSQL
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    // Establece el modo de errores a excepciones
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error al conectar a la base de datos: " . $e->getMessage();
}
?>
