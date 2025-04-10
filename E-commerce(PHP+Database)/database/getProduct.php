<?php
require 'databaseconf.php';

$tagliaMapping = [
    1 => 'xs', 2 => 's', 3 => 'm', 4 => 'l', 5 => 'xl',
    6 => '35',7 => '36',8 => '37',9 => '38',10 => '39',
    11 => '40',12 => '41',13 => '42',14 => '43'
];
$reverseTagliaMapping = array_flip($tagliaMapping);

$data = json_decode(file_get_contents('php://input'), true);

try {
    $db = new PDO($config['dns'], $config['username'], $config['pass'], $config['options']);

    // Costruisci la query dinamicamente
    $query = "SELECT * FROM forx_web.prodotti WHERE id = :original_id";
    $params = [':original_id' => $data['original_id']];

    if(!empty($data['size'])) {
        $tagliaId = $reverseTagliaMapping[strtolower($data['size'])] ?? null;
        $query .= " AND taglia_id = :taglia_id";
        $params[':taglia_id'] = $tagliaId;
    }

    if(!empty($data['color'])) {
        $query .= " AND LOWER(colore) = LOWER(:colore)";
        $params[':colore'] = $data['color'];
    } else {
        $query .= " AND colore IS NULL";
    }

    $stmt = $db->prepare($query);
    $stmt->execute($params);

    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => !!$product,
        'product' => $product
    ]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}