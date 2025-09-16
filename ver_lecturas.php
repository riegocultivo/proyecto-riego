<?php
// Conexión a la base de datos
$host = 'dpg-ct33nf3qf0us73a4m0jg-a';
$dbname = 'riego';
$user = 'riego_user';
$password = 'hPZGQbOwfxJeOKbSGQ9IfCl3weGSeTNI';

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error al conectar a la base de datos: " . $e->getMessage());
}

// Consulta para obtener los datos de la tabla lecturas
try {
    $sql = "SELECT * FROM lecturas ORDER BY fecha_hora DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $lecturas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al realizar la consulta: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabla de Lecturas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #ddd;
        }
    </style>
</head>
<body>
    <h1>Lecturas Registradas</h1>
    <?php if (count($lecturas) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Temperatura (°C)</th>
                    <th>Humedad del Suelo (%)</th>
                    <th>Bomba Activa</th>
                    <th>Fecha y Hora</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lecturas as $lectura): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($lectura['id']); ?></td>
                        <td><?php echo htmlspecialchars($lectura['temperatura']); ?></td>
                        <td><?php echo htmlspecialchars($lectura['humedad_suelo']); ?></td>
                        <td><?php echo $lectura['bomba_activa'] ? 'Sí' : 'No'; ?></td>
                        <td><?php echo htmlspecialchars($lectura['fecha_hora']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No se encontraron lecturas registradas.</p>
    <?php endif; ?>
</body>
</html>
