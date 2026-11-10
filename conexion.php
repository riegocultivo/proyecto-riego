
<?php

// DATOS DE CONEXIÓN POSTGRESQL
$host = 'dpg-dbphq6jrjlhs73fvij60-a';
$port = '5432';
$dbname = 'riego_3_q1vs';
$user = 'riego_3';

// IMPORTANTE: coloca aquí la contraseña nueva de PostgreSQL
$password = 'TU_NUEVA_CONTRASEÑA';

try {

    $pdo = new PDO(
        "pgsql:host=$host;port=$port;dbname=$dbname",
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    $pdo->exec("SET TIME ZONE 'America/La_Paz'");

    // Compatibilidad con los dos nombres
    $conn = $pdo;

} catch (PDOException $e) {

    error_log("Error PostgreSQL: " . $e->getMessage());
    die("Error al conectar con la base de datos.");

}
?>
