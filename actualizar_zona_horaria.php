<?php
// actualizar_zona_horaria.php
require_once 'includes/config.php';

try {
    // Iniciar transacción
    $conn->beginTransaction();

    // Paso 1: Modificar la columna para manejar zonas horarias
    $sql1 = "ALTER TABLE lecturas 
             ALTER COLUMN fecha_hora TYPE TIMESTAMP WITH TIME ZONE 
             USING fecha_hora AT TIME ZONE 'America/Lima'";
    $conn->exec($sql1);

    // Paso 2: Establecer el valor por defecto con zona horaria de Lima
    $sql2 = "ALTER TABLE lecturas 
             ALTER COLUMN fecha_hora SET DEFAULT CURRENT_TIMESTAMP AT TIME ZONE 'America/Lima'";
    $conn->exec($sql2);

    // Commit de la transacción
    $conn->commit();

    echo "Tabla 'lecturas' actualizada exitosamente. Zona horaria cambiada a América/Lima.";

} catch (PDOException $e) {
    // Rollback en caso de error
    $conn->rollBack();
    echo "Error al actualizar la tabla: " . $e->getMessage();
}
?>