<?php
// Configuración de la base de datos
$host = 'dpg-ct33nf3qf0us73a4m0jg-a';
$dbname = 'riego';
$user = 'riego_user';
$password = 'hPZGQbOwfxJeOKbSGQ9IfCl3weGSeTNI';

try {
    // Conexión a PostgreSQL
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verificar si se recibió una solicitud POST
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Obtener valores desde el POST o asignar valores por defecto
        $temperatura = $_POST['temperatura'] ?? 20; // 20 como valor por defecto
        $humedad_suelo = $_POST['humedad_suelo'] ?? 30; // 30 como valor por defecto
        $bomba_activa = $_POST['bomba_activa'] ?? false; // false como valor por defecto

        // Insertar los datos en la tabla "lecturas"
        $sql = "INSERT INTO lecturas (temperatura, humedad_suelo, bomba_activa) VALUES (:temperatura, :humedad_suelo, :bomba_activa)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':temperatura' => $temperatura,
            ':humedad_suelo' => $humedad_suelo,
            ':bomba_activa' => filter_var($bomba_activa, FILTER_VALIDATE_BOOLEAN)
        ]);

        // Responder al ESP32
        date_default_timezone_set('America/La_Paz');
        $fecha_hora = date('Y-m-d H:i:s');
        echo "Datos almacenados correctamente\n";
        echo "Temperatura: $temperatura\n";
        echo "Humedad del suelo: $humedad_suelo\n";
        echo "Bomba activa: " . ($bomba_activa ? 'true' : 'false') . "\n";
        echo "Fecha y hora: $fecha_hora";
    } else {
        echo "Método no permitido";
    }
} catch (PDOException $e) {
    // Manejar errores de conexión o ejecución
    echo "Error al conectar o insertar en la base de datos: " . $e->getMessage();
}
?>
