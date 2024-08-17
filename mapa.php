<?php
include 'conexion.php';

// Manejar solicitudes para obtener las líneas
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action == 'get_lineas') {
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *'); // Permitir todas las solicitudes CORS

    $sql = "SELECT id, numero, color FROM lineas"; // Asumiendo que el color está en un campo llamado 'color'
    $result = $conn->query($sql);

    if (!$result) {
        echo json_encode(['error' => 'Error en la consulta SQL: ' . $conn->error]);
        $conn->close();
        exit();
    }

    $lineas = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $lineas[] = $row; // Incluye id, número y color de la línea
        }
    }

    echo json_encode($lineas);
    $conn->close();
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapa de Buses - BusGalicia</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f5;
        }
        header {
            background-color: #ff2020;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            color: white;
        }
        header h1 {
            margin: 0;
            font-size: 24px;
        }
        nav {
            margin-top: 10px;
        }
        nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            gap: 20px;
        }
        nav ul li {
            display: inline;
        }
        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            padding: 8px 16px;
            border-radius: 5px;
            transition: background-color 0.3s, color 0.3s;
        }
        nav ul li a:hover {
            background-color: #ffffff33;
            color: #ffcccc;
        }
        #map {
            width: 100%;
            height: 500px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin: 20px auto;
        }
        .bus-icon {
            background-color: #000;
            color: #fff;
            border-radius: 4px;
            width: 24px;
            height: 24px;
            text-align: center;
            line-height: 24px;
            font-size: 12px;
            font-weight: bold;
        }
        .bus-icon span {
            display: block;
            color: white;
        }
        .bus-icon span.number {
            font-size: 10px;
        }
    </style>
</head>
<body>
    <header>
        <h1>BusGalicia - Mapa</h1>
        <nav>
            <ul>
                <li><a href="index.html">Inicio</a></li>
                <li><a href="lineas.php">Líneas</a></li>
                <li><a href="buses.php">Buses</a></li>
                <li><a href="paradas.php">Paradas</a></li>
                <li><a href="mapa.php">Mapa</a></li>
            </ul>
        </nav>
    </header>

    <div id="map"></div>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        var map = L.map('map').setView([43.3623436, -8.4115401], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
        }).addTo(map);

        var marcadores = {};
        var lineas = [];
        var indiceLinea = 0;

        function iniciarActualizacion() {
            fetch('mapa.php?action=get_lineas')
                .then(response => response.json())
                .then(data => {
                    if (Array.isArray(data) && data.length > 0) {
                        lineas = data;
                        actualizarPosiciones();
                    } else {
                        console.error('Error: No se obtuvieron las líneas');
                    }
                })
                .catch(error => {
                    console.error('Error al obtener las líneas:', error);
                });
        }

        function actualizarPosiciones() {
            if (lineas.length === 0) return;

            var linea = lineas[indiceLinea];
            var apiUrl = `http://localhost:8080/busgalicia/proxy.php?&dato=${linea.id}&mostrar=B&func=99`;

            fetch(apiUrl)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.resultado === "OK") {
                        data.mapas[0].buses.forEach(sentido => {
                            sentido.buses.forEach(bus => {
                                var latLng = [bus.posy, bus.posx];
                                var key = `${latLng[0]}_${latLng[1]}_${linea.id}`;

                                var color = linea.color || '#000000'; // Default a negro si no se encuentra color

                                var iconHtml = `
                                    <div class="bus-icon" style="background-color: ${color};">
                                        <span class="number">${bus.bus}</span>
                                    </div>
                                `;

                                var icon = L.divIcon({
                                    className: 'bus-icon',
                                    html: iconHtml,
                                    iconSize: [24, 24]
                                });

                                if (!marcadores[key]) {
                                    marcadores[key] = L.marker(latLng, { icon: icon }).addTo(map);
                                } else {
                                    marcadores[key].setIcon(icon);
                                }

                                marcadores[key].bindPopup(`Bus: ${bus.bus}<br>Línea: ${linea.numero}<br>Sentido: ${sentido.sentido}`);
                            });
                        });
                    } else {
                        console.error("Error en la respuesta de la API:", data);
                    }
                })
                .catch(error => {
                    console.error('Error al obtener los datos de la API:', error);
                });

            indiceLinea = (indiceLinea + 1) % lineas.length;

            setTimeout(actualizarPosiciones, 15000);
        }

        iniciarActualizacion();
    </script>
</body>
</html>
