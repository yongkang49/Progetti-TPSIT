<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$config = require 'databaseconf.php';
$db = new PDO($config['dns'], $config['username'], $config['pass'], $config['options']);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($_SESSION['user_id']) || !$input || !isset($input['cart'])) {
    echo json_encode(['success' => false, 'message' => 'Dati mancanti']);
    exit();
}

// Ciclo su tutti gli articoli nel carrello e aggiorno la quantità
foreach ($input['cart'] as $item) {
    $idProdotto = $item['id'];
    $quantitaDaSottrarre = $item['quantita'];

    $query = "UPDATE forx_web.prodotti 
              SET quantita = quantita - :quantita 
              WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->execute([
        ':quantita' => $quantitaDaSottrarre,
        ':id' => $idProdotto
    ]);
}

echo json_encode(['success' => true]);
?>
