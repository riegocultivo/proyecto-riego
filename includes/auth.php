<?php
session_start();

function login($username, $password) {
    global $conn;
    
    // Cambiar la consulta para usar PostgreSQL
    $stmt = $conn->prepare("SELECT id, nombre_usuario, contraseña, rol FROM usuarios WHERE nombre_usuario = ?");
    $stmt->bindParam(1, $username, PDO::PARAM_STR);
    $stmt->execute();
    
    // Verificar si existe el usuario
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Simple comparación de contraseñas sin hash
        if ($password === $row['contraseña']) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['nombre_usuario'];
            $_SESSION['role'] = $row['rol'];
            return true;
        }
    }
    return false;
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