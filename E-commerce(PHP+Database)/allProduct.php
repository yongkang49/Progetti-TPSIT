<?php
if(session_status() == PHP_SESSION_NONE)
{
    session_start();
}
$config = require 'database/databaseconf.php';
$db = new PDO($config['dns'], $config['username'], $config['pass'], $config['options']);

$categoria = $_GET['categoria'] ?? 'all';

try {
    if ($categoria === 'all') {
        $query = "SELECT * FROM forx_web.prodotti p GROUP BY p.nome ORDER BY p.id;";
    } else if ($categoria === 'vestito') {
        $query = "SELECT * FROM forx_web.prodotti p WHERE categoria_id = '1' GROUP BY p.nome ORDER BY p.id;";
    } else if ($categoria === 'gonne') {
        $query = "SELECT * FROM forx_web.prodotti p WHERE categoria_id = '2' GROUP BY p.nome ORDER BY p.id;";
    } else if ($categoria === 'scarpe') {
        $query = "SELECT * FROM forx_web.prodotti p WHERE categoria_id = '3' GROUP BY p.nome ORDER BY p.id;";
    }
    $stmt = $db->prepare($query);
    $stmt->execute();
    $prodotti = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Errore DB: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>All Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css"/>
</head>
<body>

<?php require "Components/header.php"?>

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-md-4 mx-auto">
            <form method="GET" action="">
                <select name="categoria" id="category-filter" class="form-select" onchange="this.form.submit()">
                    <option value="all" <?= $categoria === 'all' ? 'selected' : '' ?>>Tutte le categorie</option>
                    <option value="vestito" <?= $categoria === 'vestito' ? 'selected' : '' ?>>Vestiti</option>
                    <option value="gonne" <?= $categoria === 'gonne' ? 'selected' : '' ?>>Gonne</option>
                    <option value="scarpe" <?= $categoria === 'scarpe' ? 'selected' : '' ?>>Scarpe</option>
                </select>
            </form>
        </div>
    </div>

    <div class="text-center text-muted mb-3">
        <?= count($prodotti) ?> prodotti visualizzati
    </div>

    <div id="products-list" class="row g-3 justify-content-center">
        <?php
        foreach ($prodotti as $prodotto) {
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
        }
        ?>
    </div>
</div>

<script
        src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
        integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p"
        crossorigin="anonymous"
></script>
<script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"
        integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF"
        crossorigin="anonymous"
></script>
</body>
</html>
