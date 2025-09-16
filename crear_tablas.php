<?php
// Conexión a la base de datos PostgreSQL
$host = 'dpg-d34utfd6ubrc73comehg-a';
$dbname = 'riego_chz9';
$user = 'riego';
$password = 'I63CK60lAkVJzf4IDpkxfKaBd3yh0pg3';
$port = "5432";

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Crear tabla `lecturas`
    $sql_lecturas = "
    CREATE TABLE IF NOT EXISTS lecturas (
        id SERIAL PRIMARY KEY,
        temperatura FLOAT NOT NULL,
        humedad_suelo INT NOT NULL,
        bomba_activa BOOLEAN NOT NULL,
        fecha_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL
    );";
    $pdo->exec($sql_lecturas);

    // Crear tabla `usuarios`
    $sql_usuarios = "
    CREATE TABLE IF NOT EXISTS usuarios (
        id SERIAL PRIMARY KEY,
        nombre_usuario VARCHAR(255) NOT NULL,
        contraseña VARCHAR(255) NOT NULL,
        rol VARCHAR(10) DEFAULT 'user',
        creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL
    );";
    $pdo->exec($sql_usuarios);

    // Insertar datos iniciales en la tabla `usuarios`
    $sql_insert_usuarios = "
    INSERT INTO usuarios (nombre_usuario, contraseña, rol, creado_en) VALUES
    ('admin', 'admin', 'admin', '2024-11-18 13:57:16'),
    ('user', 'user', 'user', '2024-11-18 13:57:16')
    ON CONFLICT DO NOTHING;";
    $pdo->exec($sql_insert_usuarios);

    echo "Tablas creadas e inicializadas correctamente.<br>";

    // Preparar la consulta SQL para insertar datos de simulación
    $sql = "INSERT INTO lecturas (temperatura, humedad_suelo, bomba_activa, fecha_hora) VALUES (:temperatura, :humedad_suelo, :bomba_activa, :fecha_hora)";
    $stmt = $pdo->prepare($sql);

    // Generar datos para los últimos 7 días
    $fecha_inicial = strtotime('-7 days');
    $registros_por_dia = 24; // Un registro cada hora

    for ($dia = 0; $dia < 7; $dia++) {
        for ($hora = 0; $hora < $registros_por_dia; $hora++) {
            // Calcular timestamp para este registro
            $timestamp = $fecha_inicial + ($dia * 86400) + ($hora * 3600);
            
            // Generar datos simulados
            // Temperatura entre 15°C y 35°C con variación diurna
            $base_temp = 25; // temperatura base
            $variacion_temp = 10 * sin(($hora / 24) * 2 * M_PI); // variación diurna
            $temperatura = $base_temp + $variacion_temp + rand(-2, 2); // añadir algo de ruido aleatorio

            // Humedad del suelo entre 0 y 100
            // Más baja durante el día, más alta durante la noche
            $base_humedad = 60;
            $variacion_humedad = -20 * sin(($hora / 24) * 2 * M_PI);
            $humedad_suelo = max(0, min(100, $base_humedad + $variacion_humedad + rand(-10, 10)));

            // Bomba activa cuando la humedad es baja
            $bomba_activa = ($humedad_suelo < 40) ? true : false;

            // Insertar el registro
            $stmt->execute([
                ':temperatura' => round($temperatura, 1),
                ':humedad_suelo' => (int)$humedad_suelo,
                ':bomba_activa' => $bomba_activa,
                ':fecha_hora' => date('Y-m-d H:i:s', $timestamp)
            ]);
        }
    }

    echo "Se han insertado " . (7 * $registros_por_dia) . " registros de simulación correctamente.";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
