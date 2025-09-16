<?php
require_once 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['manual'])) {
    try {
        // PostgreSQL transaction to ensure atomic update
        $conn->beginTransaction();

        // Update the last record to set bomba_activa to true
        $sql = "UPDATE lecturas 
                SET bomba_activa = true 
                WHERE id = (SELECT MAX(id) FROM lecturas)";
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        $conn->commit();

        echo "Registro actualizado exitosamente";
    } catch (PDOException $e) {
        $conn->rollBack();
        echo "Error al actualizar registro: " . $e->getMessage();
    }
} else {
    echo "Solicitud no válida.";
}
?>