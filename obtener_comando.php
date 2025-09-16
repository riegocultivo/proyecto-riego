<?php
// Archivo que almacena el último comando enviado
$comandoArchivo = 'comando.txt';

// Verifica si se ha recibido un comando y lo guarda en el archivo
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comando = $_POST['comando'] ?? '';
    if ($comando) {
        file_put_contents($comandoArchivo, $comando);
        echo 'Comando recibido: ' . $comando;
    } else {
        echo 'No se ha recibido un comando.';
    }
} else {
    // Si no se ha enviado un comando, se lee el último comando desde el archivo
    if (file_exists($comandoArchivo)) {
        $comando = file_get_contents($comandoArchivo);
        echo $comando;
    } else {
        echo 'No hay comando guardado.';
    }
}
?>
