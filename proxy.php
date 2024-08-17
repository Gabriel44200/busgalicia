<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Permitir todas las solicitudes CORS

// Obtener los parámetros de la URL
$dato = isset($_GET['dato']) ? $_GET['dato'] : '';
$mostrar = isset($_GET['mostrar']) ? $_GET['mostrar'] : '';
$func = isset($_GET['func']) ? $_GET['func'] : '';

// Construir la URL de la API externa
$apiUrl = "https://itranvias.com/queryitr_v3.php?&dato=$dato&mostrar=$mostrar&func=$func";

// Inicializar cURL
$ch = curl_init();

// Configurar cURL para la solicitud
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

// Ejecutar la solicitud y capturar la respuesta
$response = curl_exec($ch);

// Verificar si hay errores en la solicitud
if(curl_errno($ch)) {
    echo json_encode(['error' => curl_error($ch)]);
}

// Cerrar cURL
curl_close($ch);

// Devolver la respuesta al frontend
echo $response;
