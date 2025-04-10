<?php
if(session_status() == PHP_SESSION_NONE)
{
    session_start();
}
require 'database/databaseconf.php'; // Assicurati che questo percorso sia corretto

// Funzione per calcolare lo sconto bundle
function calculateBundleDiscount($cart) {
    $categories = [];
    foreach ($cart as $item) {
        if (!in_array($item['categoria'], $categories)) {
            $categories[] = $item['categoria'];
        }
    }

    if (count($categories) >= 3) {
        $cheapest = [];
        foreach ($cart as $id => $item) {
            if (!isset($cheapest[$item['category']]) || $item['price'] < $cheapest[$item['category']]['price']) {
                $cheapest[$item['category']] = $item;
            }
        }

        if (count($cheapest) >= 3) {
            $discount = 0;
            $bundleProducts = array_slice($cheapest, 0, 3);
            foreach ($bundleProducts as $product) {
                $discount += $product['price'] * 0.1;
            }
            return [
                'discount' => $discount,
                'products' => $bundleProducts
            ];
        }
    }
    return null;
}

// Calcolo totale
$subtotal = 0;
$bundleDiscount = 0;
$couponDiscount = 0;
$bundleInfo = null;

if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $id => $item) {
        $subtotal += $item['price'] * $item['quantity'];
    }

    // Applica sconto bundle
    $bundleData = calculateBundleDiscount($_SESSION['cart']);
    if ($bundleData) {
        $bundleDiscount = $bundleData['discount'];
        $bundleInfo = $bundleData['products'];
    }
}

// Applica coupon
if (isset($_SESSION['coupon'])) {
    $couponDiscount = ($subtotal - $bundleDiscount) * $_SESSION['coupon'];
}

$total = $subtotal - $bundleDiscount - $couponDiscount;

// Processa checkout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_checkout'])) {
    unset($_SESSION['cart']);
    unset($_SESSION['coupon']);
    header('Location: index.php?checkout=success');
    exit;
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css"/>
</head>
<body>

<?php require "Components/header.php"?>

<div class="container my-5">
    <h1 class="mb-4">Checkout</h1>

    <?php if (empty($_SESSION['cart'])): ?>
        <div class="alert alert-info">
            Il carrello è vuoto
        </div>
    <?php else: ?>
        <div id="checkout-container" class="card">
            <div class="card-body">
                <ul id="checkout-items" class="list-group mb-3">
                    <?php foreach ($_SESSION['cart'] as $id => $item): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <img src="<?= htmlspecialchars($item['image']) ?>"
                                     alt="<?= htmlspecialchars($item['name']) ?>"
                                     class="img-thumbnail me-3"
                                     style="width:50px; height:50px; object-fit: contain;">
                                <div>
                                    <h6 class="mb-0"><?= htmlspecialchars($item['name']) ?></h6>
                                    <small class="text-muted">
                                        €<?= number_format($item['price'], 2) ?> x <?= $item['quantity'] ?>
                                        <?php if (!empty($item['size'])): ?>
                                            <br>Taglia: <?= htmlspecialchars($item['size']) ?>
                                        <?php endif; ?>
                                        <?php if (!empty($item['color'])): ?>
                                            <br>Colore: <?= htmlspecialchars($item['color']) ?>
                                        <?php endif; ?>
                                    </small>
                                </div>
                            </div>
                            <span class="text-muted">
                            €<?= number_format($item['price'] * $item['quantity'], 2) ?>
                        </span>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <div id="summary" class="mb-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="lead">Subtotale:</span>
                        <span class="lead">€<?= number_format($subtotal, 2) ?></span>
                    </div>

                    <?php if ($bundleDiscount > 0): ?>
                        <div class="d-flex justify-content-between text-success mb-2">
                            <span>Sconto Bundle:</span>
                            <span>-€<?= number_format($bundleDiscount, 2) ?></span>
                        </div>
                        <p id="bundle-info-text" class="text-success small">
                            <strong>Applicato a:</strong><br>
                            <?php foreach ($bundleInfo as $product): ?>
                                <?= htmlspecialchars($product['name']) ?> (<?= $product['category'] ?>)<br>
                            <?php endforeach; ?>
                        </p>
                    <?php endif; ?>

                    <?php if ($couponDiscount > 0): ?>
                        <div class="d-flex justify-content-between text-success mb-2">
                            <span>Sconto Coupon:</span>
                            <span>-€<?= number_format($couponDiscount, 2) ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="d-flex justify-content-between border-top pt-2">
                        <strong>Totale:</strong>
                        <strong>€<?= number_format($total, 2) ?></strong>
                    </div>
                </div>

                <form method="post">
                    <button type="submit" name="confirm_checkout" class="btn btn-primary w-100">
                        Conferma Acquisto
                    </button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
</body>
</html>