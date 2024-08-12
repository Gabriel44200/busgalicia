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
            background-color: #f4f4f4; /* Color de fondo */
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
            padding: 20px; /* Añadir un poco de espacio */
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
            text-decoration: none; /* Sin subrayado */
            color: black; /* Color de texto */
            transition: background-color 0.3s; /* Transición suave */
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
            text-decoration: none; /* Sin subrayado */
        }
        .nombre-linea {
            font-size: 18px;
        }
        .linea:hover {
            background-color: #eaeaea; /* Color de fondo al pasar el ratón */
        }
        .circle:hover {
            opacity: 0.8; /* Efecto al pasar el ratón */
        }
        h2 {
            padding: 20px;
            font-weight: normal;
        }
    </style>
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
                <li><a href="#">Mapa</a></li>
            </ul>
        </nav>
        <div class="search-bar">
            <input type="text" placeholder="Buscar...">
            <button>🔍</button>
        </div>
    </header>

    <?php
    // Obtener líneas desde la base de datos y ordenarlas de manera que los números con letras se mantengan juntos
    $result = $conn->query("
        SELECT * FROM lineas 
        ORDER BY 
            CASE 
                WHEN numero REGEXP '^[0-9]+' THEN 1  -- Primero, las líneas que comienzan con un número
                ELSE 2  -- Luego, las líneas que no comienzan con un número (ej. BUH, UDC)
            END,
            CAST(numero AS UNSIGNED),  -- Ordena por la parte numérica
            numero  -- Luego por la parte alfabética
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
    ?>

    <h2>Regulares <span style="font-weight: lighter; font-size: 14px;">Líneas comunes, habitualmente de L a D</span></h2>
    <div class="lineas">
        <?php foreach ($regulares as $linea): ?>
            <a class="linea" href="lineas/<?php echo $linea['id']; ?>">
                <div class="circle" style="background-color: <?php echo $linea['color']; ?>;"><?php echo $linea['numero']; ?></div>
                <div class="nombre-linea"><?php echo $linea['nombre']; ?></div>
            </a>
        <?php endforeach; ?>
    </div>

    <h2>Auxiliares <span style="font-weight: lighter; font-size: 14px;">Líneas comunes, pero sólo funcionan algunos días</span></h2>
    <div class="lineas">
        <?php foreach ($auxiliares as $linea): ?>
            <a class="linea" href="lineas/<?php echo $linea['id']; ?>">
                <div class="circle" style="background-color: <?php echo $linea['color']; ?>;"><?php echo $linea['numero']; ?></div>
                <div class="nombre-linea"><?php echo $linea['nombre']; ?></div>
            </a>
        <?php endforeach; ?>
    </div>

    <h2>Eventuales <span style="font-weight: lighter; font-size: 14px;">Líneas que funcionan en ocasiones especiales, como San Juan</span></h2>
    <div class="lineas">
        <?php foreach ($eventuales as $linea): ?>
            <a class="linea" href="lineas/<?php echo $linea['id']; ?>">
                <div class="circle" style="background-color: <?php echo $linea['color']; ?>;"><?php echo $linea['numero']; ?></div>
                <div class="nombre-linea"><?php echo $linea['nombre']; ?></div>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        // Inicializar el mapa aquí
    </script>
</body>
</html>
