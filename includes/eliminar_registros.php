<?php
// Iniciar la sesión
session_start();

// Verificar si el usuario tiene permisos de administrador
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Acceso Denegado");
}

// Incluir la configuración de la base de datos
include 'config.php'; // Asegúrate de que config.php tenga la conexión correcta

// Verificar si se presionó el botón para eliminar los registros
if (isset($_POST['eliminar'])) {
    try {
        // Iniciar una transacción para asegurar la consistencia de las operaciones
        $conn->beginTransaction();

        // Deshabilitar las restricciones de claves foráneas temporalmente
        $conn->exec("ALTER TABLE lecturas DISABLE TRIGGER ALL");

        // Eliminar los registros de la tabla 'lecturas'
        $conn->exec("DELETE FROM lecturas");

        // Si tienes otras tablas que también necesiten ser limpiadas, agrega las sentencias DELETE aquí.
        // $conn->exec("DELETE FROM otra_tabla");

        // Volver a habilitar las restricciones de claves foráneas
        $conn->exec("ALTER TABLE lecturas ENABLE TRIGGER ALL");

        // Confirmar la transacción
        $conn->commit();

        echo "Todos los registros han sido eliminados exitosamente.";
    } catch (PDOException $e) {
        // Si algo falla, revertir la transacción
        $conn->rollBack();
        echo "Error al eliminar los registros: " . $e->getMessage();
    }
} else {
    echo "No se ha enviado la solicitud de eliminación.";
}
?>
