<?php
$servername = "localhost";
$username = "root"; // Cambia esto si usas un nombre de usuario diferente
$password = ""; // Cambia esto si has establecido una contraseña
$dbname = "coruna"; // Asegúrate de que la base de datos existe

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
