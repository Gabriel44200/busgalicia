<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BusGalicia</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            background-color: #f4f4f4;
        }
        header {
            background-color: #ff2020;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        header h1 {
            margin: 0;
            color: white;
        }
        nav ul {
            list-style: none;
            display: flex;
            margin: 0;
            padding: 0;
        }
        nav ul li {
            margin-left: 20px;
        }
        nav ul li a {
            color: white;
            text-decoration: none;
        }
        .search-bar {
            display: flex;
            align-items: center;
        }
        .search-bar input {
            padding: 5px;
            font-size: 16px;
        }
        .search-bar button {
            padding: 5px 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        .lineas {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            padding: 20px;
        }
        .linea {
            display: flex;
            align-items: center;
            background-color: white;
            border-radius: 50px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            padding: 10px 20px;
            min-width: 200px;
            flex: 1;
            max-width: 45%;
            text-decoration: none;
            color: black;
            transition: background-color 0.3s;
        }
        .circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
            font-size: 18px;
            margin-right: 15px;
            flex-shrink: 0;
            text-decoration: none;
        }
        .nombre-linea {
            font-size: 18px;
        }
        .linea:hover {
            background-color: #eaeaea;
        }
        .circle:hover {
            opacity: 0.8;
        }
        h2 {
            padding: 20px;
            font-weight: normal;
        }
        .contenido-linea {
            display: flex;
            gap: 20px;
            padding: 20px;
        }
        .termometro {
            flex: 1;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }
        .sentido {
            margin-bottom: 40px;
        }
        .sentido h3 {
            margin-bottom: 10px;
            font-weight: normal;
            color: #333;
        }
        .paradas {
            list-style: none;
            padding-left: 20px;
            position: relative;
        }
        .paradas::before {
            content: "";
            position: absolute;
            left: 20px;
            top: 0;
            bottom: 0;
            width: 2px;
            background-color: #333;
        }
        .parada {
            position: relative;
            padding: 10px 0;
            padding-left: 40px;
            font-size: 16px;
            color: #555;
        }
        .parada::before {
            content: "";
            position: absolute;
            left: 10px;
            top: 12px;
            width: 12px;
            height: 12px;
            background-color: #ff2020;
            border-radius: 50%;
        }
        .map-container {
            flex: 1;
            position: relative;
        }
        .map {
            width: 100%;
            height: 400px;
            border: 1px solid #ddd;
        }
    </style>
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
</head>
<body>
    <?php include 'conexion.php'; ?>

    <header>
        <h1>BusGalicia</h1>
        <nav>
            <ul>
                <li><a href="index.html">Índice</a></li>
                <li><a href="lineas.php">Líneas</a></li>
                <li><a href="buses.php">Buses</a></li>
                <li><a href="paradas.php">Paradas</a></li>
                <li><a href="mapa.php">Mapa</a></li>
            </ul>
        </nav>
        <div class="search-bar">
            <input type="text" placeholder="Buscar...">
            <button>🔍</button>
        </div>
    </header>

    <?php
    // Verificar si se ha pasado un parámetro 'id' en la URL
    if (isset($_GET['id'])) {
        $linea_id = $_GET['id'];
        
        // Realizar la solicitud a la API para obtener los datos de la línea específica
        $url = "http://localhost:8080/busgalicia/proxy.php?&dato=" . urlencode($linea_id) . "&mostrar=PRB&func=99"; // Actualiza la URL de la API
        $response = file_get_contents($url);
        $data = json_decode($response, true);
        
        // Verificar si la respuesta de la API es exitosa
        if ($data['resultado'] === 'OK') {
            echo "<h1>Detalles de la Línea " . htmlspecialchars($linea_id) . "</h1>";
            echo "<div class='contenido-linea'>";
            echo "<div class='termometro'>";
            echo "<p>Fecha de la petición: " . htmlspecialchars($data['fecha_peticion']) . "</p>";
            
            // Mostrar las paradas de la línea en formato termómetro
            foreach ($data['mapas'][0]['paradas'] as $sentido) {
                echo "<div class='sentido'>";
                echo "<h3>Sentido " . htmlspecialchars($sentido['sentido']) . "</h3>";
                echo "<ul class='paradas'>";
                foreach ($sentido['paradas'] as $parada) {
                    echo "<li class='parada'>" . htmlspecialchars($parada['parada']) . "</li>";
                }
                echo "</ul>";
                echo "</div>";
            }
            echo "</div>";

            // Añadir el contenedor del mapa y pasar los datos a JavaScript
            echo "<div class='map-container'>";
            echo "<div id='map' class='map'></div>";
            echo "<script>
                var paradas = " . json_encode($data['mapas'][0]['paradas']) . ";
                var recorrido = " . (isset($data['mapas'][0]['recorrido']) ? json_encode($data['mapas'][0]['recorrido']) : '[]') . ";
            </script>";
            echo "</div>";
        } else {
            echo "<p>No se encontraron detalles para la línea " . htmlspecialchars($linea_id) . ".</p>";
        }
    } else {
        // Obtener líneas desde la base de datos y ordenarlas
        $result = $conn->query("
            SELECT * FROM lineas 
            ORDER BY 
                CASE 
                    WHEN id REGEXP '^[0-9]+' THEN 1
                    ELSE 2
                END,
                CAST(id AS UNSIGNED),
                id
        ");

        $regulares = [];
        $auxiliares = [];
        $eventuales = [];

        // Clasificar las líneas por tipo
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                if ($row['tipo'] == 'regular') {
                    $regulares[] = $row;
                } elseif ($row['tipo'] == 'auxiliar') {
                    $auxiliares[] = $row;
                } elseif ($row['tipo'] == 'eventual') {
                    $eventuales[] = $row;
                }
            }
        }

        // Mostrar la lista de líneas
        echo '<h2>Regulares <span style="font-weight: lighter; font-size: 14px;">Líneas comunes, habitualmente de L a D</span></h2>';
        echo '<div class="lineas">';
        foreach ($regulares as $linea) {
            echo '<a class="linea" href="lineas.php?id=' . urlencode($linea['id']) . '">';
            echo '<div class="circle" style="background-color: ' . htmlspecialchars($linea['color']) . ';">' . htmlspecialchars($linea['numero']) . '</div>';
            echo '<span class="nombre-linea">' . htmlspecialchars($linea['nombre']) . '</span>';
            echo '</a>';
        }
        echo '</div>';
    }
    ?>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        function initMap() {
            var map = L.map('map').setView([42.0, -8.0], 12); // Coordenadas y nivel de zoom inicial

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            var paradas = window.paradas || [];
            var recorrido = window.recorrido || [];

            if (recorrido.length > 0) {
                var polyline = L.polyline(recorrido, {color: 'blue'}).addTo(map);
                map.fitBounds(polyline.getBounds());
            }

            paradas.forEach(function(sentido) {
                sentido.paradas.forEach(function(parada) {
                    if (parada.latitud && parada.longitud) {
                        L.marker([parada.latitud, parada.longitud]).addTo(map)
                            .bindPopup(parada.nombre || 'Parada sin nombre');
                    }
                });
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            initMap();
        });
    </script>
</body>
</html>
