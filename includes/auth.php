
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function login($username, $password) {
    global $conn;

    try {
        // Crear tabla si no existe
        $conn->exec("
            CREATE TABLE IF NOT EXISTS public.usuarios (
                id SERIAL PRIMARY KEY,
                nombre_usuario VARCHAR(255) NOT NULL UNIQUE,
                contraseña VARCHAR(255) NOT NULL,
                rol VARCHAR(10) DEFAULT 'user',
                creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL
            )
        ");

        // Crear administrador si no existe
        $stmt = $conn->prepare("
            INSERT INTO public.usuarios
                (nombre_usuario, contraseña, rol)
            VALUES ('admin', 'admin', 'admin')
            ON CONFLICT (nombre_usuario) DO NOTHING
        ");
        $stmt->execute();

        // Consultar usuario
        $stmt = $conn->prepare("
            SELECT id, nombre_usuario, contraseña, rol
            FROM public.usuarios
            WHERE nombre_usuario = :usuario
            LIMIT 1
        ");

        $stmt->execute([':usuario' => $username]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && $password === $row['contraseña']) {
            session_regenerate_id(true);

            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['nombre_usuario'];
            $_SESSION['role'] = $row['rol'];

            return true;
        }

        return false;

    } catch (PDOException $e) {
        error_log("Error login: " . $e->getMessage());
        return false;
    }
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

function requireAdmin() {
    requireLogin();

    if ($_SESSION['role'] !== 'admin') {
        die("Access Denied");
    }
}
?>
