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


$query6 = "
    SELECT 
    l.id, 
    l.izena, 
    l.abizena, 
    COUNT(e.id) AS total_eskari
FROM 
    langilea l
LEFT JOIN 
    eskaera e ON e.langilea_id = l.id
GROUP BY 
    l.id, l.izena, l.abizena
LIMIT 0, 1000
";   
$stmt6 = $pdo->prepare($query6);
$stmt6->execute();
$langileak1 = $stmt6->fetchAll(PDO::FETCH_ASSOC);

$query7 = "
    SELECT 
        ep.produktu_izena, 
        SUM(ep.produktuaKop) AS total_eskari
    FROM 
        eskaeraproduktua ep
    LEFT JOIN 
        eskaera e ON e.id = ep.erreserba_id
    GROUP BY 
        ep.produktu_izena
";   
$stmt7 = $pdo->prepare($query7);
$stmt7->execute();
$produktuak1 = $stmt7->fetchAll(PDO::FETCH_ASSOC);

$query8 = "
    SELECT 
        DAYOFWEEK(e.data) AS dia_semana, 
        COUNT(e.id) AS total_eskari
    FROM 
        eskaera e
    GROUP BY 
        dia_semana
    ORDER BY 
        dia_semana
";   
$stmt8 = $pdo->prepare($query8);
$stmt8->execute();
$dia_semana_eskari = $stmt8->fetchAll(PDO::FETCH_ASSOC);

$query9 = "
    SELECT 
    DAYOFWEEK(e.data) AS dia_semana, 
    SUM(e.prezioTotala) AS total_factura
FROM 
    eskaera e
GROUP BY 
    dia_semana
ORDER BY 
    dia_semana
";   
$stmt9 = $pdo->prepare($query9);
$stmt9->execute();
$dia_semana_factura = $stmt9->fetch(PDO::FETCH_ASSOC);


$query10 = "
    SELECT 
        DAY(e.data) AS dia_mes, 
        COUNT(e.id) AS total_eskari
    FROM 
        eskaera e
    GROUP BY 
        dia_mes
    ORDER BY 
        total_eskari DESC
    LIMIT 1
";   
$stmt10 = $pdo->prepare($query10);
$stmt10->execute();
$dia_mes_eskari = $stmt10->fetch(PDO::FETCH_ASSOC);

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

$langileak1XML = $xml->addChild('langileak1');
foreach ($langileak1 as $langilea1) {
    $item = $langileak1XML->addChild('langilea1');
    $item->addChild('id', $langilea1['id']);
    $item->addChild('izena', $langilea1['izena']);
    $item->addChild('abizena', $langilea1['abizena']);
    $item->addChild('total_eskari', $langilea1['total_eskari']);
}

$produktuak1XML = $xml->addChild('produktuak1');
foreach ($produktuak1 as $produktua1) {
    $item = $produktuak1XML->addChild('produktua');
    $item->addChild('produktu_izena', $produktua1['produktu_izena']);
    $item->addChild('total_eskari', $produktua1['total_eskari']);
}

if (!empty($dia_semana_eskari)) {
    $dia_semana_eskariXML = $xml->addChild('dia_semana_eskari');
    $dia_semana_eskariXML->addChild('dia_semana', $dia_semana_eskari[0]['dia_semana']);
    $dia_semana_eskariXML->addChild('total_eskari', $dia_semana_eskari[0]['total_eskari']);
}

$dia_semana_facturaXML = $xml->addChild('dia_semana_factura');
$dia_semana_facturaXML->addChild('dia_semana', $dia_semana_factura['dia_semana']);
$dia_semana_facturaXML->addChild('total_factura', $dia_semana_factura['total_factura']);


$dia_mes_eskariXML = $xml->addChild('dia_mes_eskari');
$dia_mes_eskariXML->addChild('dia_mes', $dia_mes_eskari['dia_mes']);
$dia_mes_eskariXML->addChild('total_eskari', $dia_mes_eskari['total_eskari']);


echo $xml->asXML();

?>
