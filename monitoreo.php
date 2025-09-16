<?php
// Incluir configuración y autenticación
require_once 'includes/config.php';
require_once 'includes/auth.php';

// Verificar que el usuario esté autenticado
requireLogin();

// Establecer la cantidad de registros por página
$registrosPorPagina = 25;
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina - 1) * $registrosPorPagina;

// Consultar las tablas con consultas preparadas
$queryLecturas = "SELECT 
    *, 
    date_trunc('second', fecha_hora) as fecha_hora_sin_decimales 
FROM lecturas 
ORDER BY fecha_hora DESC 
LIMIT :limit OFFSET :offset";
$stmtLecturas = $conn->prepare($queryLecturas);
$stmtLecturas->bindParam(':limit', $registrosPorPagina, PDO::PARAM_INT);
$stmtLecturas->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmtLecturas->execute();
$lecturas = $stmtLecturas->fetchAll(PDO::FETCH_ASSOC);

// Obtener el número total de registros
$queryTotalLecturas = "SELECT COUNT(*) AS total FROM lecturas";
$stmtTotalLecturas = $conn->prepare($queryTotalLecturas);
$stmtTotalLecturas->execute();
$totalLecturas = $stmtTotalLecturas->fetch(PDO::FETCH_ASSOC)['total'];

// Calcular el número total de páginas
$totalPaginasLecturas = ceil($totalLecturas / $registrosPorPagina);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Monitoreo - Sistema de Riego</title>
    <!-- CSS de Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        table th, table td {
            text-align: center;
        }
        h1 {
            color: #28a745;
            font-weight: bold;
        }
        .btn-custom {
            margin: 5px;
        }
    </style>
</head>
<body>
    <!-- Cabecera -->
    <?php include 'partials/header.php'; ?>

    <div class="container mt-4">
        <h1 class="text-center mb-4">Monitoreo del Sistema de Riego</h1>
        
        <!-- Tabla de Lecturas -->
        <div class="table-responsive">
            <h3 class="mb-3">Lecturas de Sensores</h3>
            <table class="table table-bordered table-striped">
                <thead class="table-success">
                    <tr>
                        <th>ID</th>
                        <th>Temperatura</th>
                        <th>Humedad del Suelo</th>
                        <th>Bomba Activa</th>
                        <th>Fecha y Hora</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($lecturas)): ?>
                        <?php foreach ($lecturas as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['id']) ?></td>
                                <td><?= htmlspecialchars($row['temperatura']) ?> °C</td>
                                <td><?= htmlspecialchars($row['humedad_suelo']) ?>%</td>
                                <td><?= $row['bomba_activa'] ? 'Sí' : 'No' ?></td>
                                <td><?= htmlspecialchars($row['fecha_hora_sin_decimales']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">No hay datos disponibles.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Paginación -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <li class="page-item <?= ($pagina <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?pagina=1">Primera</a>
                </li>
                <li class="page-item <?= ($pagina <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?pagina=<?= $pagina - 1 ?>">Anterior</a>
                </li>
                <li class="page-item <?= ($pagina >= $totalPaginasLecturas) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?pagina=<?= $pagina + 1 ?>">Siguiente</a>
                </li>
                <li class="page-item <?= ($pagina >= $totalPaginasLecturas) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?pagina=<?= $totalPaginasLecturas ?>">Última</a>
                </li>
            </ul>
        </nav>

        <!-- Acciones y reportes -->
        <div class="text-center mt-4">
            <form class="d-inline-block mb-3" action="generar_pdf.php" method="GET">
                <input type="hidden" name="tipo" value="promedio">
                <div class="row g-2 align-items-center">
                    <div class="col-auto">
                        <label for="fecha_inicio" class="form-label">Fecha Inicio:</label>
                        <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control" required>
                    </div>
                    <div class="col-auto">
                        <label for="fecha_fin" class="form-label">Fecha Fin:</label>
                        <input type="date" id="fecha_fin" name="fecha_fin" class="form-control" required>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-success btn-custom">Reporte Promedio</button>
                    </div>
                </div>
            </form>
            
            <button class="btn btn-primary btn-custom" onclick="window.location.reload();">Actualizar Página</button>
            <button class="btn btn-info btn-custom" onclick="generarReporte('general');">Reporte General</button>
            <button class="btn btn-warning btn-custom" onclick="generarReporte('bomba_activa');">Reporte Bombas Activas</button>

            <form class="d-inline-block mt-2" action="includes/eliminar_registros.php" method="POST">
                <input type="hidden" name="eliminar_lecturas" value="true">
                <button type="submit" class="btn btn-primary btn-custom" name="eliminar" onclick="return confirm('¿Estás seguro de eliminar todos los registros?')">Eliminar Todos los Registros</button>
                </button>
            </form>
            
        </div>
    </div>

    <!-- Script de reportes -->
    <script>
        function generarReporte(tipo) {
            let url = `generar_pdf.php?tipo=${tipo}`;
            if (tipo === 'promedio') {
                const fechaInicio = prompt('Ingrese la fecha de inicio (YYYY-MM-DD):');
                const fechaFin = prompt('Ingrese la fecha de fin (YYYY-MM-DD):');
                if (fechaInicio && fechaFin) {
                    url += `&fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`;
                } else {
                    alert('Debe ingresar ambas fechas.');
                    return;
                }
            }
            window.location.href = url;
        }
    </script>

    <!-- Pie de página -->
    <?php include 'partials/footer.html'; ?>
</body>
</html>
