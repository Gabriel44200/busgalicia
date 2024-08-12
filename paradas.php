<?php
// Incluir el archivo de conexión
include 'conexion.php';

// Definir el número de resultados por página
$resultados_por_pagina = 20;

// Determinar la página actual
if (isset($_GET['pagina'])) {
    $pagina = (int)$_GET['pagina'];
} else {
    $pagina = 1;
}

// Calcular el primer resultado de la página actual
$primer_resultado = ($pagina - 1) * $resultados_por_pagina;

// Obtener los datos de la tabla `paradas`
$sql = "SELECT * FROM paradas LIMIT $primer_resultado, $resultados_por_pagina";
$result = $conn->query($sql);

// Contar el número total de resultados en la tabla `paradas`
$sql_total = "SELECT COUNT(*) AS total FROM paradas";
$result_total = $conn->query($sql_total);
$row_total = $result_total->fetch_assoc();
$total_resultados = $row_total['total'];

// Calcular el número total de páginas
$total_paginas = ceil($total_resultados / $resultados_por_pagina);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paradas - BusGalicia</title>
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
        .search-bar {
            display: flex;
            align-items: center;
        }
        .search-bar input {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-right: 10px;
        }
        .search-bar button {
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .search-bar button:hover {
            background-color: #45a049;
        }
        h2 {
            padding: 20px;
            font-weight: 700;
            color: #333;
            text-align: center;
        }
        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: white;
            border-radius: 10px;
            overflow: hidden; /* Para redondear bordes en las celdas */
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        table, th, td {
            border: none; /* Quitamos los bordes de la tabla */
        }
        th, td {
            padding: 15px;
            text-align: left;
        }
        th {
            background-color: #ff2020;
            color: white;
            font-weight: bold;
        }
        td {
            background-color: #f9f9f9;
            transition: background-color 0.3s;
        }
        td:hover {
            background-color: #f1f1f1; /* Color al pasar el ratón */
        }
        .pagination {
            margin: 20px auto;
            text-align: center;
        }
        .pagination a {
            margin: 0 5px;
            text-decoration: none;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            color: #333;
            transition: background-color 0.3s;
        }
        .pagination a:hover {
            background-color: #e0e0e0;
        }
        .pagination a.active {
            background-color: #4CAF50;
            color: white;
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
            <li><a href="#">Mapa</a></li>
        </ul>
    </nav>
    <div class="search-bar">
        <input type="text" placeholder="Buscar...">
        <button>🔍</button>
    </div>
</header>

<h2>Lista de Paradas</h2>

<table>
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Posición X</th>
        <th>Posición Y</th>
        <th>Enlaces</th>
    </tr>
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['nombre']; ?></td>
                <td><?php echo $row['posx']; ?></td>
                <td><?php echo $row['posy']; ?></td>
                <td>
                    <?php 
                    $enlaces = $row['enlaces']; // Obtenemos los enlaces directamente
                    if ($enlaces) {
                        echo $enlaces; // Mostramos los enlaces directamente
                    } else {
                        echo 'N/A'; // Si no hay enlaces, mostramos N/A
                    }
                    ?>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="5">No se encontraron resultados.</td>
        </tr>
    <?php endif; ?>
</table>

<div class="pagination">
    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
        <a href="paradas.php?pagina=<?php echo $i; ?>" class="<?php echo $i == $pagina ? 'active' : ''; ?>"><?php echo $i; ?></a>
    <?php endfor; ?>
</div>

</body>
</html>

<?php
// Cerrar la conexión
$conn->close();
?>
