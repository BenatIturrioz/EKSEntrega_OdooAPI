<?php

require "conexion.php";  

header("Content-Type: application/xml; charset=utf-8");

// Consulta 1: Datos de la tabla 'produktua'
$query = "
    SELECT 
        p.id, 
        p.izena, 
        p.deskribapena, 
        p.prezioa, 
        p.kantitatea, 
        p.mota AS mota_id, 
        m.mota AS mota 
    FROM 
        produktua p
    LEFT JOIN 
        produktumota m ON p.mota = m.id
"; 

$stmt = $pdo->prepare($query);
$stmt->execute();
$produktuak = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Consulta 2: Datos de la tabla 'categoria'
$query2 = "
    SELECT 
        p.id, 
        p.dni,
        p.izena, 
        p.abizena, 
        p.mota AS mota_id, 
        m.mota AS mota 
    FROM 
        langilea p
    LEFT JOIN 
        langilemota m ON p.mota = m.id
";  
$stmt2 = $pdo->prepare($query2);
$stmt2->execute();
$langileak = $stmt2->fetchAll(PDO::FETCH_ASSOC);

$query3 = "
    SELECT 
        e.id,
        l.izena AS zerbitzari_izena,         
        l.abizena AS zerbitzari_abizena,        
        e.erreserba_id,
        e.mahaia_id,
        e.data,
        e.prezioTotala
    FROM 
        eskaera e
    LEFT JOIN 
        langilea l ON e.langilea_id = l.id
";  

$stmt3 = $pdo->prepare($query3);
$stmt3->execute();
$eskaerak = $stmt3->fetchAll(PDO::FETCH_ASSOC);

$query4 = "SELECT id, mahaia_id, bezeroIzena, telf, data, bezeroKop FROM erreserba";
$stmt4 = $pdo->prepare($query4);
$stmt4->execute();
$erreserbak = $stmt4->fetchAll(PDO::FETCH_ASSOC);


// Crear el XML
$xml = new SimpleXMLElement('<data/>');

// Añadir productos
$produktuakXML = $xml->addChild('produktuak');
foreach ($produktuak as $produktua) {
    $item = $produktuakXML->addChild('produktua');
    $item->addChild('id', $produktua['id']);
    $item->addChild('izena', $produktua['izena']);
    $item->addChild('deskribapena', $produktua['deskribapena']);
    $item->addChild('prezioa', $produktua['prezioa']);
    $item->addChild('kantitatea', $produktua['kantitatea']);
    $item->addChild('mota', $produktua['mota']);
}

$langileakXML = $xml->addChild('langileak');
foreach ($langileak as $langilea) {
    $item = $langileakXML->addChild('langilea');
    $item->addChild('id', $langilea['id']);
    $item->addChild('dni', $langilea['dni']);
    $item->addChild('izena', $langilea['izena']);
    $item->addChild('abizena', $langilea['abizena']);
    $item->addChild('mota', $langilea['mota']);
}

$eskaerakXML = $xml->addChild('eskaerak');
foreach ($eskaerak as $eskaera) {
    $item = $eskaerakXML->addChild('eskaera');
    $item->addChild('id', $eskaera['id']);
    $item->addChild('zerbitzari_izena', $eskaera['zerbitzari_izena']);  
    $item->addChild('zerbitzari_abizena', $eskaera['zerbitzari_abizena']); 
    $item->addChild('erreserba_id', $eskaera['erreserba_id']);
    $item->addChild('mahaia_id', $eskaera['mahaia_id']);
    $item->addChild('data', $eskaera['data']);
    $item->addChild('prezioTotala', $eskaera['prezioTotala']);  
}

$erreserbakXML = $xml->addChild('erreserbak');
foreach ($erreserbak as $erreserba){
    $item = $erreserbakXML->addChild('erreserba');
    $item->addChild('id', $erreserba['id']);
    $item->addChild('mahaia_id', $erreserba['mahaia_id']);
    $item->addChild('bezeroIzena', $erreserba['bezeroIzena']);
    $item->addChild('telf', $erreserba['telf']);
    $item->addChild('data', $erreserba['data']);
    $item->addChild('bezeroKop', $erreserba['bezeroKop']);
}


echo $xml->asXML();

?>
