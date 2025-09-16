<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Riego Automatizado</title>
    <!-- CSS de Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- JavaScript de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        /* Círculo de progreso */
        .progress-circle {
            width: 120px;
            height: 120px;
            background: conic-gradient(#28a745 0% 50%, #dc3545 50% 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            font-weight: bold;
            color: white;
            margin: 0 auto;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .main-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 80vh;
        }

        .control-container {
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin-bottom: 20px;
        }

        .botones-control button {
            width: 100%;
            margin: 10px 0;
        }

        /* Estilo del encabezado */
        .h1-container {
            margin-bottom: 30px;
        }

        @media (max-width: 767px) {
            .progress-circle {
                width: 100px;
                height: 100px;
                font-size: 18px;
            }

            .control-container {
                padding: 15px;
            }

            .botones-control button {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
<?php include 'partials/header.php'; ?>
<div class="container-fluid">
    <div class="main-container">
        <div class="control-container">
            <div class="h1-container">
                <h1 class="h2">Control de Bomba de Agua</h1>
            </div>

            <div class="estado-bomba mb-4">
                <h4>Estado de la Bomba</h4>
                <p id="estado-bomba">Cargando...</p>
                <div id="circle-progress" class="progress-circle">0%</div>
            </div>

            <div class="botones-control">
                <button onclick="enviarComando('ACTIVAR')" class="btn btn-lg btn-success">
                    Activar Bomba
                </button>
                <button onclick="enviarComando('DESACTIVAR')" class="btn btn-lg btn-danger">
                    Desactivar Bomba
                </button>
            </div>
        </div>

        <!-- Nueva sección de activación manual -->
        <div class="control-container">
            <h4>Activación Manual de la Bomba</h4>
            <button onclick="activarManual()" class="btn btn-lg btn-primary">
                Activar Manualmente
            </button>
        </div>
    </div>
</div>

<script>
    // Función para enviar el comando al ESP32
    function enviarComando(comando) {
        fetch('obtener_comando.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'comando=' + encodeURIComponent(comando)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la solicitud');
            }
            return response.text();
        })
        .then(data => {
            alert('Comando ' + comando + ' enviado');
            actualizarEstado();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ocurrió un error al enviar el comando');
        });
    }

    // Función para actualizar el estado de la bomba
    function actualizarEstado() {
        fetch('get_latest_data.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error al obtener datos');
                }
                return response.json();
            })
            .then(data => {
                const estadoBomba = document.getElementById('estado-bomba');
                const circleProgress = document.getElementById('circle-progress');
                
                if (data.bomba_activa) {
                    estadoBomba.textContent = 'Bomba Activa';
                    estadoBomba.style.color = '#28a745';
                    circleProgress.style.background = 'conic-gradient(#28a745 0% 100%)';
                    circleProgress.textContent = 'Encendido';
                } else {
                    estadoBomba.textContent = 'Bomba Inactiva';
                    estadoBomba.style.color = '#dc3545';
                    circleProgress.style.background = 'conic-gradient(#dc3545 0% 100%)';
                    circleProgress.textContent = 'Apagado';
                }
            })
            .catch(error => {
                console.error('Error al obtener el estado:', error);
                const estadoBomba = document.getElementById('estado-bomba');
                estadoBomba.textContent = 'Error al cargar estado';
                estadoBomba.style.color = '#dc3545';
            });
    }

    // Función para activar manualmente la bomba
    function activarManual() {
        fetch('activar_manual.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'manual=true'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la solicitud');
            }
            return response.text();
        })
        .then(data => {
            alert('Bomba activada manualmente');
            actualizarEstado();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ocurrió un error al activar la bomba');
        });
    }

    // Actualizar el estado cada 5 segundos
    setInterval(actualizarEstado, 5000);
    actualizarEstado();
</script>
<?php include 'partials/footer.html'; ?>
</body>
</html>
