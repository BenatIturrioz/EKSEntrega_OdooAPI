<?php

require "conexion.php";  

header("Content-Type: application/xml; charset=utf-8");

$query = "SELECT id, izena, deskribapena, prezioa, kantitatea FROM produktua"; 
$stmt = $pdo->prepare($query);
$stmt->execute();

$produktuak = $stmt->fetchAll(PDO::FETCH_ASSOC);

$xml = new SimpleXMLElement('<productos/>');

foreach ($produktuak as $produktua) {
    $item = $xml->addChild('produktua');
    $item->addChild('id', $produktua['id']);
    $item->addChild('izena', $produktua['izena']);
    $item->addChild('deskribapena', $produktua['deskribapena']);
    $item->addChild('prezioa', $produktua['prezioa']);
    $item->addChild('kantitatea', $produktua['kantitatea']);

}

echo $xml->asXML();
?>
