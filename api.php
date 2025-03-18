<?php
// Conexión a la base de datos (ver código anterior)
require "conexion.php";  // Asegúrate de tener la conexión en un archivo separado

header("Content-Type: application/xml; charset=utf-8");

// Realizamos la consulta para obtener los productos
$query = "SELECT id, izena, deskribapena, prezioa, kantitatea FROM produktua"; // Cambia esto según tu estructura de base de datos
$stmt = $pdo->prepare($query);
$stmt->execute();

// Obtener los resultados
$produktuak = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Crear un objeto XML
$xml = new SimpleXMLElement('<productos/>');

// Recorrer los productos y añadirlos al XML
foreach ($produktuak as $produktua) {
    $item = $xml->addChild('produktua');
    $item->addChild('id', $produktua['id']);
    $item->addChild('izena', $produktua['izena']);
    $item->addChild('deskribapena', $produktua['deskribapena']);
    $item->addChild('prezioa', $produktua['prezioa']);
    $item->addChild('kantitatea', $produktua['kantitatea']);

}

// Mostrar el XML
echo $xml->asXML();
?>
