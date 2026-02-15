<?php
// =====================================================
// crear_tablas.php (versión segura para Render + PostgreSQL)
// - No rompe el sistema actual (mantiene lecturas/usuarios)
// - Agrega tablas nuevas
// - Arregla ON CONFLICT creando índice UNIQUE aunque la tabla ya existiera
// - Inserta datos de simulación SOLO si lecturas está vacía
// =====================================================

// Mostrar errores (útil para debug; si quieres, luego lo desactivas)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Conexión a PostgreSQL (Render recomienda usar DATABASE_URL)
$databaseUrl = getenv('DATABASE_URL');

try {
    if ($databaseUrl) {
        $db = parse_url($databaseUrl);
        $host = $db['host'] ?? 'localhost';
        $port = $db['port'] ?? 5432;
        $dbname = ltrim($db['path'] ?? '', '/');
        $user = $db['user'] ?? '';
        $password = $db['pass'] ?? '';
    } else {
        // Fallback local (si ejecutas en tu PC). AJUSTA si lo necesitas.   
        $host = 'dpg-d62i4tonputs73b47f60-a';
        $port = 5432;
        $dbname = 'riego2';
        $user = 'riego2';
        $password = 'BilCdLcvjH59YKLlGMccY4NTlDeU8FI8';
    }

    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // =====================================================
    // 1) TABLAS EXISTENTES (NO ROMPER)
    // =====================================================

    // Crear tabla `lecturas` (si no existe)
    $sql_lecturas = "
    CREATE TABLE IF NOT EXISTS lecturas (
        id SERIAL PRIMARY KEY,
        temperatura FLOAT NOT NULL,
        humedad_suelo INT NOT NULL,
        bomba_activa BOOLEAN NOT NULL,
        fecha_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL
    );";
    $pdo->exec($sql_lecturas);

    // Crear tabla `usuarios` (si no existe)
    // Nota: Si ya existía sin UNIQUE, este CREATE no la modifica. Por eso creamos un índice abajo.
    $sql_usuarios = "
    CREATE TABLE IF NOT EXISTS usuarios (
        id SERIAL PRIMARY KEY,
        nombre_usuario VARCHAR(255) NOT NULL,
        contraseña VARCHAR(255) NOT NULL,
        rol VARCHAR(10) DEFAULT 'user',
        creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL
    );";
    $pdo->exec($sql_usuarios);

    
    // Asegurar UNIQUE en nombre_usuario aunque la tabla ya existía desde antes sin UNIQUE
    $pdo->exec("CREATE UNIQUE INDEX IF NOT EXISTS usuarios_nombre_usuario_uq ON usuarios(nombre_usuario);");

    // Insertar datos iniciales en `usuarios` sin error de ON CONFLICT
    $sql_insert_usuarios = "
    INSERT INTO usuarios (nombre_usuario, contraseña, rol, creado_en) VALUES
    ('admin', 'admin', 'admin', '2024-11-18 13:57:16'),
    ('user', 'user', 'user', '2024-11-18 13:57:16')
    ON CONFLICT (nombre_usuario) DO NOTHING;";
    $pdo->exec($sql_insert_usuarios);

    // =====================================================
    // 2) TABLAS NUEVAS (estructura relacional, sin romper)
    // =====================================================

    $pdo->exec("
    CREATE TABLE IF NOT EXISTS roles (
        id SERIAL PRIMARY KEY,
        nombre VARCHAR(50) UNIQUE NOT NULL
    );");

    $pdo->exec("
    CREATE TABLE IF NOT EXISTS zonas (
        id SERIAL PRIMARY KEY,
        nombre VARCHAR(100) NOT NULL,
        descripcion TEXT
    );");

    $pdo->exec("
    CREATE TABLE IF NOT EXISTS sensores (
        id SERIAL PRIMARY KEY,
        tipo VARCHAR(50) NOT NULL,
        ubicacion VARCHAR(100),
        zona_id INT REFERENCES zonas(id)
    );");

    $pdo->exec("
    CREATE TABLE IF NOT EXISTS bombas (
        id SERIAL PRIMARY KEY,
        nombre VARCHAR(100),
        estado BOOLEAN DEFAULT FALSE,
        zona_id INT REFERENCES zonas(id)
    );");

    $pdo->exec("
    CREATE TABLE IF NOT EXISTS cultivos (
        id SERIAL PRIMARY KEY,
        nombre VARCHAR(100) NOT NULL,
        tipo VARCHAR(50),
        humedad_minima INT,
        humedad_maxima INT,
        zona_id INT REFERENCES zonas(id)
    );");

    $pdo->exec("
    CREATE TABLE IF NOT EXISTS riegos (
        id SERIAL PRIMARY KEY,
        fecha_inicio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        fecha_fin TIMESTAMP,
        usuario_id INT REFERENCES usuarios(id),
        bomba_id INT REFERENCES bombas(id)
    );");

    echo "✅ Tablas creadas / verificadas correctamente.<br>";

    // =====================================================
    // 3) DATOS DE SIMULACIÓN (solo si lecturas está vacía)
    // =====================================================

    $count = (int)$pdo->query("SELECT COUNT(*) FROM lecturas;")->fetchColumn();

    if ($count === 0) {
        // Preparar inserción de datos simulados
        $sql = "INSERT INTO lecturas (temperatura, humedad_suelo, bomba_activa, fecha_hora)
                VALUES (:temperatura, :humedad_suelo, :bomba_activa, :fecha_hora)";
        $stmt = $pdo->prepare($sql);

        // Generar datos para los últimos 7 días
        $fecha_inicial = strtotime('-7 days');
        $registros_por_dia = 24; // 1 registro por hora

        for ($dia = 0; $dia < 7; $dia++) {
            for ($hora = 0; $hora < $registros_por_dia; $hora++) {
                $timestamp = $fecha_inicial + ($dia * 86400) + ($hora * 3600);

                // Temperatura (15 a 35 aprox) con variación diurna
                $base_temp = 25;
                $variacion_temp = 10 * sin(($hora / 24) * 2 * M_PI);
                $temperatura = $base_temp + $variacion_temp + rand(-2, 2);

                // Humedad del suelo (0 a 100)
                $base_humedad = 60;
                $variacion_humedad = -20 * sin(($hora / 24) * 2 * M_PI);
                $humedad_suelo = max(0, min(100, $base_humedad + $variacion_humedad + rand(-10, 10)));

                // Bomba activa cuando la humedad es baja
                $bomba_activa = ($humedad_suelo < 40);

                $stmt->execute([
                    ':temperatura' => round($temperatura, 1),
                    ':humedad_suelo' => (int)$humedad_suelo,
                    ':bomba_activa' => $bomba_activa,
                    ':fecha_hora' => date('Y-m-d H:i:s', $timestamp)
                ]);
            }
        }

        echo "✅ Se insertaron " . (7 * $registros_por_dia) . " registros de simulación (porque lecturas estaba vacía).";
    } else {
        echo "ℹ️ No se insertó simulación: ya existen $count registros en lecturas.";
    }

} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
