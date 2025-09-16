<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Dashboard - Riego Automatizado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <?php include 'partials/header.php'; ?>
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-4 text-center">
                        <div class="circle-progress mx-auto" id="tempCircle">
                            <div class="overlay"></div>
                            <span class="value" id="tempValue">-°C</span>
                        </div>
                        <h4 class="mt-3">Temperatura</h4>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="circle-progress mx-auto" id="humidityCircle">
                            <div class="overlay"></div>
                            <span class="value" id="humidityValue">-%</span>
                        </div>
                        <h4 class="mt-3">Humedad del Suelo</h4>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="circle-progress mx-auto" id="pumpCircle">
                            <div class="overlay"></div>
                            <span class="value" id="pumpValue">Desconocido</span>
                        </div>
                        <h4 class="mt-3">Estado de Bomba</h4>
                    </div>
                </div>
            </div>

            <div class="col-md-12 mt-4">
                <div class="row">
                    <div class="col-md-6">
                        <div class="chart-container">
                            <h4>Histórico de Temperatura (24h)</h4>
                            <canvas id="temperatureChart"></canvas>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="chart-container">
                            <h4>Histórico de Humedad del Suelo (24h)</h4>
                            <canvas id="humidityChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Chart initialization
        const temperatureCtx = document.getElementById('temperatureChart').getContext('2d');
        const temperatureChart = new Chart(temperatureCtx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Temperatura (°C)',
                    data: [],
                    borderColor: 'rgb(255, 99, 132)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: false
                    }
                }
            }
        });

        const humidityCtx = document.getElementById('humidityChart').getContext('2d');
        const humidityChart = new Chart(humidityCtx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Humedad del Suelo (%)',
                    data: [],
                    borderColor: 'rgb(54, 162, 235)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });

        // Utility function to update circle progress
        function updateCircleProgress(circleElement, valueElement, value, maxValue = 100) {
            const overlay = circleElement.querySelector('.overlay');
            const percentage = Math.min(Math.max(value, 0), maxValue);
            
            overlay.style.height = `${percentage}%`;
            valueElement.textContent = value + (maxValue === 100 ? '%' : '°C');

            // Color logic
            if (percentage < 30) {
                overlay.style.backgroundColor = '#dc3545'; // Red
            } else if (percentage < 60) {
                overlay.style.backgroundColor = '#ffc107'; // Yellow
            } else {
                overlay.style.backgroundColor = '#28a745'; // Green
            }
        }

        // Function to fetch and update historical and latest data
        // Fetch and update dashboard
        function updateDashboard() {
            // Fetch latest data
            fetch('get_latest_data.php')
                .then(response => response.json())
                .then(data => {
                    // Update real-time sensor values
                    updateCircleProgress(
                        document.getElementById('tempCircle'), 
                        document.getElementById('tempValue'), 
                        data.temperatura
                    );

                    updateCircleProgress(
                        document.getElementById('humidityCircle'), 
                        document.getElementById('humidityValue'), 
                        data.humedad_suelo
                    );

                    // Update pump status
                    const pumpCircle = document.getElementById('pumpCircle');
                    const pumpValue = document.getElementById('pumpValue');
                    const pumpOverlay = pumpCircle.querySelector('.overlay');
                    
                    if (data.bomba_activa) {  // Correct handling of boolean
                        pumpValue.textContent = 'Activa';
                        pumpOverlay.style.height = '100%';
                        pumpOverlay.style.backgroundColor = '#28a745';
                    } else {
                        pumpValue.textContent = 'Inactiva';
                        pumpOverlay.style.height = '0%';
                        pumpOverlay.style.backgroundColor = '#dc3545';
                    }
                });

            // Fetch historical data
            fetch('get_sensor_history.php')
                .then(response => response.json())
                .then(data => {
                    // Update Temperature Chart
                    temperatureChart.data.labels = data.horas;
                    temperatureChart.data.datasets[0].data = data.temperaturas;
                    temperatureChart.update();

                    // Update Humidity Chart
                    humidityChart.data.labels = data.horas;
                    humidityChart.data.datasets[0].data = data.humedades;
                    humidityChart.update();
                });
        }

        // Initial data load
        updateDashboard();

        // Periodic updates
        setInterval(updateDashboard, 5000); // Update every 5 seconds
    </script>
    <?php include 'partials/footer.html'; ?>
</body>
</html>