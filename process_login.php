<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $conn->real_escape_string($_POST['password']);

    // Consulta a la base de datos
    $query = "SELECT * FROM usuarios WHERE nombre_usuario = '$username' AND contraseña = '$password'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        // Usuario autenticado
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: index.php");
        exit();
    } else {
        // Usuario o contraseña incorrectos
        header("Location: login.php?error=1");
        exit();
    }
}
?>
