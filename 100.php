<?php
// URL de la API
$url = 'https://itranvias.com/queryitr_v3.php?&dato=100&mostrar=PRB&func=99';

// Realiza la solicitud HTTP GET
$response = file_get_contents($url);

// Verifica si la respuesta es válida
if ($response === FALSE) {
    die('Error al realizar la solicitud');
}

// Decodifica la respuesta JSON
$data = json_decode($response, true);

// Verifica si la decodificación fue exitosa
if ($data === NULL) {
    die('Error al decodificar el JSON');
}

// Inicializamos las coordenadas de las paradas
$paradas = [];

if (isset($data['mapas'])) {
    foreach ($data['mapas'] as $mapa) {
        if (isset($mapa['paradas'])) {
            foreach ($mapa['paradas'] as $sentido) {
                if (isset($sentido['paradas']) && is_array($sentido['paradas'])) {
                    foreach ($sentido['paradas'] as $parada) {
                        if (isset($parada['posx']) && isset($parada['posy'])) {
                            // Guardamos las paradas en un array
                            $paradas[] = [
                                'posx' => $parada['posx'],
                                'posy' => $parada['posy'],
                                'nombre' => $parada['nombre'], // Suponiendo que hay un nombre
                            ];
                        }
                    }
                }
            }
        }
    }
}

// Convertir las paradas en formato JSON para pasarlas a JavaScript
$paradas_json = json_encode($paradas);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gráfico de Rutas</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <style>
        #map { height: 400px; } /* Define la altura del mapa */
    </style>
</head>
<body>
    <h1>Mapa de Rutas</h1>
    <div id="map"></div>

    <script>
        // Recibimos las paradas desde PHP
        var paradas = <?php echo $paradas_json; ?>;

        // Inicializamos el mapa
        var map = L.map('map').setView([42.1, -8.5], 13); // Ajusta el centro y el zoom según sea necesario

        // Añadir la capa de mapa
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        // Añadir las paradas al mapa
        paradas.forEach(function(parada) {
            L.marker([parada.posy, parada.posx]).addTo(map)
                .bindPopup(parada.nombre); // Muestra el nombre de la parada en un popup
        });
    </script>
</body>
</html>
