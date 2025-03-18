<?php
$host = "localhost";  // O tu IP/host
$dbname = "erronka1";   // Nombre de la base de datos
$username = "root"; // Tu usuario de base de datos
$password = "1WMG2023"; // Tu contraseña de base de datos

// Conectar a la base de datos
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
