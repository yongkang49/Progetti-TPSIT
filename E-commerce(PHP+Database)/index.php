<?php
if(session_status() == PHP_SESSION_NONE)
{
    session_start();
}
$config = require 'database/databaseconf.php';
$db = new PDO($config['dns'], $config['username'], $config['pass'], $config['options']);
$count = 0;
$sectionCounter = 1;
$visualizza = ['9', '12', '15', '35', '44', '53', '57', '69', '83', '95', '101', '119'];
$query = "SELECT * 
FROM forx_web.prodotti p
GROUP BY p.nome
ORDER BY p.id;";
$stmt = $db->prepare($query);
$stmt->execute();
$prodotti = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Inizializza il carrello se non esiste
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Gestione rimozione articolo
if (isset($_GET['remove_item'])) {
    $item_id = $_GET['remove_item'];
    if (isset($_SESSION['cart'][$item_id])) {
        unset($_SESSION['cart'][$item_id]);
    }
}

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>FORX</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css"/>
</head>
<body>

<?php require "Components/header.php"?>

<div id="slideShow" class="carousel slide shadow-lg" data-bs-ride="carousel">
    <div class="carousel-inner rounded-4 overflow-hidden">
        <div class="carousel-item active">
            <img src="images/slide2.jpg" class="d-block img-fluid" alt="Slide 1">
        </div>
    </div>
</div>

<section id="products-list" style="display: flex; flex-wrap: wrap; justify-content: center; gap: 20px; margin: 20px;">
    <?php
    $count = 0;
    $sectionCounter = 1;
    foreach ($prodotti as $prodotto) {
        if (in_array($prodotto['id'], $visualizza)) {
            if ($count % 4 === 0) {
                echo '
                <div style="flex-basis: 100%;" class="d-flex justify-content-between align-items-center mt-4 mb-2">
                    <h3>Sezione ' . $sectionCounter . '</h3>
                    <a href="allProduct.php" class="text-decoration-none">vedi tutto</a>
                </div>';
                $sectionCounter++;
            }
            echo '
            <div style="width: 200px;">
                <div class="card shadow-sm text-center">
                    <img src="' . htmlspecialchars($prodotto['immagine']) . '" class="card-img-top p-2" alt="' . htmlspecialchars($prodotto['id']) . '" style="height: 300px; object-fit: contain;">
                    <div class="card-body p-2">
                        <h6 class="card-title">' . htmlspecialchars($prodotto['nome']) . '</h6>
                        <p class="text-success fw-bold small">€' . number_format($prodotto['prezzo'], 2, ',', '.') . '</p>
                        <a href="product.php?id=' . urlencode($prodotto['id']) . '" class="btn btn-sm btn-outline-primary w-100">Dettagli</a>
                    </div>
                </div>
            </div>';
            $count++;
        }
    }
    ?>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/script.js"></script>
</body>
</html>