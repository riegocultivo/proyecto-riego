<header class="navbar navbar-expand-lg navbar-dark bg-dark p-3">
    <div class="container-fluid">
        <img src="img/logo-icon.png" alt="Logo" class="me-3" style="width: 40px; height: 40px;">
        <a class="navbar-brand" href="index.php">Riego Automatizado - Control</a>

        <!-- Botón de hamburguesa -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Opciones del menú -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="monitoreo.php">Monitoreo</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="estado.php">Estado</a>
                </li>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="crud.php">Gestión de Usuarios</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>

        <!-- Información del usuario y botón de salida -->
        <div class="d-flex ms-3">
            <span class="me-3 text-light">Usuario: <strong><?php echo $_SESSION['username']; ?></strong></span>
            <a href="logout.php" class="btn btn-sm btn-danger">Salir</a>
        </div>
    </div>
</header>
