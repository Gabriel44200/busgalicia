<?php
// Incluir el archivo de conexión
include 'conexion.php';

// Función para obtener el número de línea
function obtenerNumeroLinea($conn, $linea_id) {
    $sql = "SELECT numero FROM lineas WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $linea_id);
    $stmt->execute();
    $stmt->bind_result($numero);
    $stmt->fetch();
    $stmt->close();
    return $numero;
}

// Función para obtener el nombre de la última parada
function obtenerNombreParada($conn, $parada_id) {
    $sql = "SELECT nombre FROM paradas WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $parada_id);
    $stmt->execute();
    $stmt->bind_result($nombre);
    $stmt->fetch();
    $stmt->close();
    return $nombre;
}

// Función para obtener el color de la línea
function obtenerColorLinea($conn, $linea_id) {
    $sql = "SELECT color FROM lineas WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $linea_id);
    $stmt->execute();
    $stmt->bind_result($color);
    $stmt->fetch();
    $stmt->close();
    return $color;
}

// Obtener el ID de parada desde la URL (modificado para trabajar con URLs amigables)
$id_parada = isset($_GET['id']) ? (int)$_GET['id'] : null;

// Comprobar si se ha pasado un ID de parada
if ($id_parada) {
    // Hacer la petición a la URL y obtener el JSON
    $url = "https://itranvias.com/queryitr_v3.php?&dato={$id_parada}&func=0";
    $response = file_get_contents($url);
    $data = json_decode($response, true);

    // Verificar que la petición fue exitosa
    if ($data['resultado'] === 'OK' && isset($data['buses']['lineas'])) {
        $lineas = $data['buses']['lineas'];
    } else {
        $lineas = [];
    }

    // Obtener el nombre de la parada
    $nombre_parada = obtenerNombreParada($conn, $id_parada);
    ?>

    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo htmlspecialchars($id_parada); ?> - <?php echo htmlspecialchars($nombre_parada); ?> - BusGalicia</title>
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <style>
            body {
                font-family: 'Roboto', sans-serif;
                margin: 0;
                padding: 0;
                background-color: #f7f8fa;
                color: #333;
            }
            header {
                background-color: #ff2020;
                padding: 20px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            }
            header h1 {
                margin: 0;
                color: white;
                font-size: 24px;
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
                font-weight: 700;
                transition: color 0.3s;
            }
            nav ul li a:hover {
                color: #ffcccc;
            }
            h2 {
                padding: 20px;
                font-weight: 700;
                color: #ff2020;
                text-align: center;
            }
            .info-parada {
                max-width: 900px;
                margin: 20px auto;
                background-color: white;
                padding: 30px;
                border-radius: 10px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            }
            .linea {
                margin-bottom: 30px;
            }
            .bus {
                display: flex;
                align-items: center;
                background-color: #f8f9fa;
                padding: 15px;
                border-radius: 8px;
                margin-top: 10px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
                transition: transform 0.3s ease;
                cursor: pointer;
            }
            .bus:hover {
                transform: translateY(-5px);
            }
            .linea-numero {
                width: 50px;
                height: 50px;
                color: white;
                font-size: 18px;
                font-weight: bold;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-right: 20px;
            }
            .bus .bus-detalles {
                flex-grow: 1;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .bus .bus-detalles .detalles {
                display: flex;
                align-items: center;
            }
            .bus .bus-detalles .detalles p {
                margin: 0 15px 0 0;
                font-size: 16px;
                display: flex;
                align-items: center;
            }
            .bus .bus-detalles .detalles p .material-icons {
                margin-right: 8px;
                color: #ff2020;
            }
            .bus .tiempo {
                font-size: 18px;
                font-weight: bold;
                color: #333;
            }
            .detalles-ocultos {
                display: none;
                margin-top: 15px;
            }
            .detalles-ocultos .bus {
                margin-top: 5px;
                background-color: #f0f0f5;
            }
        </style>
        <script>
            function toggleDetalles(id) {
                const detalles = document.getElementById(id);
                if (detalles.style.display === 'none' || detalles.style.display === '') {
                    detalles.style.display = 'block';
                } else {
                    detalles.style.display = 'none';
                }
            }
        </script>
    </head>
    <body>

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
    </header>

    <h2><?php echo htmlspecialchars($id_parada); ?> - <?php echo htmlspecialchars($nombre_parada); ?></h2>

    <div class="info-parada">
        <?php if (!empty($lineas)): ?>
            <?php foreach ($lineas as $linea): ?>
                <?php
                // Obtener el número de línea
                $numeroLinea = obtenerNumeroLinea($conn, $linea['linea']);
                // Obtener el color de la línea
                $color = obtenerColorLinea($conn, $linea['linea']);
                // Obtener los buses de la línea
                $buses = $linea['buses'];
                // Excluir el primer bus (el más próximo) para los detalles ocultos
                $primerBus = array_shift($buses);
                ?>
                <div class="linea">
                    <div class="bus" onclick="toggleDetalles('detalles-<?php echo htmlspecialchars($linea['linea']); ?>')">
                        <div class="linea-numero" style="background-color: <?php echo htmlspecialchars($color); ?>;">
                            <?php echo htmlspecialchars($numeroLinea); ?>
                        </div>
                        <div class="bus-detalles">
                            <div class="detalles">
                                <p><span class="material-icons">directions_bus</span><?php echo htmlspecialchars($primerBus['bus']); ?></p>
                                <p><span class="material-icons">straighten</span><?php echo htmlspecialchars($primerBus['distancia']); ?> m</p>
                                <p><span class="material-icons">location_on</span><?php echo htmlspecialchars(obtenerNombreParada($conn, $primerBus['ult_parada'])); ?></p>
                            </div>
                            <div class="tiempo">
                                <?php echo htmlspecialchars($primerBus['tiempo']); ?> min
                            </div>
                        </div>
                    </div>
                    <?php if (!empty($buses)): ?>
                        <div class="detalles-ocultos" id="detalles-<?php echo htmlspecialchars($linea['linea']); ?>">
                            <?php foreach ($buses as $bus): ?>
                                <div class="bus">
                                    <div class="bus-detalles">
                                        <div class="detalles">
                                            <p><span class="material-icons">directions_bus</span><?php echo htmlspecialchars($bus['bus']); ?></p>
                                            <p><span class="material-icons">straighten</span><?php echo htmlspecialchars($bus['distancia']); ?> m</p>
                                            <p><span class="material-icons">location_on</span><?php echo htmlspecialchars(obtenerNombreParada($conn, $bus['ult_parada'])); ?></p>
                                        </div>
                                        <div class="tiempo">
                                            <?php echo htmlspecialchars($bus['tiempo']); ?> min
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No hay datos disponibles para esta parada.</p>
        <?php endif; ?>
    </div>

    </body>
    </html>

    <?php
} else {
    // Si no se ha pasado un ID, mostrar la lista de paradas
    $resultados_por_pagina = 20; // Cambiar a 20 elementos por página
    $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
    $primer_resultado = ($pagina - 1) * $resultados_por_pagina;

    $sql = "SELECT id, nombre, enlaces FROM paradas LIMIT ?, ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $primer_resultado, $resultados_por_pagina);
    $stmt->execute();
    $resultado = $stmt->get_result();

    $total_resultados = $conn->query("SELECT COUNT(*) AS total FROM paradas")->fetch_assoc()['total'];
    $total_paginas = ceil($total_resultados / $resultados_por_pagina);
    ?>

    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Lista de Paradas - BusGalicia</title>
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
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
                justify-content: space-between;
                align-items: center;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            }
            header h1 {
                margin: 0;
                color: white;
                font-size: 24px;
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
                transition: color 0.3s;
            }
            nav ul li a:hover {
                color: #ffcccc;
            }
            h2 {
                padding: 20px;
                font-weight: 700;
                color: #333;
                text-align: center;
            }
            .lista-paradas {
                max-width: 90%;
                margin: 20px auto;
                background-color: white;
                padding: 20px;
                border-radius: 10px;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            }
            table {
                width: 100%;
                border-collapse: collapse;
            }
            th, td {
                padding: 10px;
                text-align: left;
                border-bottom: 1px solid #e0e0e0;
            }
            th {
                background-color: #ff2020;
                color: white;
            }
            .pagination {
                text-align: center;
                margin: 20px 0;
            }
            .pagination a {
                margin: 0 5px;
                text-decoration: none;
                padding: 5px 10px;
                border: 1px solid #ff2020;
                color: #ff2020;
                border-radius: 5px;
                transition: background-color 0.3s;
            }
            .pagination a:hover {
                background-color: #ff2020;
                color: white;
            }
            .lineas-numeros {
                display: flex;
                align-items: center;
                padding: 0;
                margin: 0;
            }
            .linea-numero {
                width: 40px;
                height: 40px;
                color: white;
                font-size: 16px;
                font-weight: bold;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-right: 5px;
                background-color: #ccc;
                box-sizing: border-box;
            }
            td.enlaces {
                padding: 0;
            }
        </style>
    </head>
    <body>

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
    </header>

    <h2>Lista de Paradas</h2>

    <div class="lista-paradas">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th class="enlaces">Enlaces</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $resultado->fetch_assoc()): ?>
                    <tr onclick="location.href='paradas.php?id=<?php echo htmlspecialchars($row['id']); ?>'" style="cursor: pointer;">
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                        <td class="lineas-numeros enlaces">
                            <?php
                            // Obtener enlaces de líneas para esta parada
                            $enlaces_lineas = explode(',', $row['enlaces']);
                            foreach ($enlaces_lineas as $lineaId) {
                                $numero = obtenerNumeroLinea($conn, $lineaId);
                                $color = obtenerColorLinea($conn, $lineaId);
                                if ($numero) {
                                    echo "<div class='linea-numero' style='background-color: {$color};'>{$numero}</div>";
                                }
                            }
                            ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div class="pagination">
        <?php if ($pagina > 1): ?>
            <a href="paradas.php?pagina=<?php echo htmlspecialchars($pagina - 1); ?>">Anterior</a>
            <a href="paradas.php?pagina=<?php echo htmlspecialchars(1); ?>">Primero</a>
        <?php endif; ?>

        <?php
        // Calcular el rango de páginas que se mostrarán
        $rango = 2; // Rango de páginas a mostrar (ej. 1, 2, 3)
        $pagina_inicial = max(1, $pagina - $rango);
        $pagina_final = min($total_paginas, $pagina + $rango);

        for ($i = $pagina_inicial; $i <= $pagina_final; $i++): ?>
            <a href="paradas.php?pagina=<?php echo htmlspecialchars($i); ?>" <?php if ($i == $pagina) echo 'style="font-weight: bold;"'; ?>>
                <?php echo htmlspecialchars($i); ?>
            </a>
        <?php endfor; ?>

        <?php if ($pagina < $total_paginas): ?>
            <a href="paradas.php?pagina=<?php echo htmlspecialchars($total_paginas); ?>">Último</a>
            <a href="paradas.php?pagina=<?php echo htmlspecialchars($pagina + 1); ?>">Siguiente</a>
        <?php endif; ?>
    </div>

    </body>
    </html>

    <?php
}
?>
