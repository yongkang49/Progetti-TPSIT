<?php
if(session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Calcola totali dalla sessione
$cart = $_SESSION['cart'] ?? [];
$discountRate = $_SESSION['applied_coupon']['discount'] ?? 0;

$subtotalLordo = array_reduce($cart, fn($acc, $item) => $acc + ($item['price'] * $item['quantita']), 0);
$iva = $subtotalLordo * 0.22;
$discountValue = $subtotalLordo * $discountRate;
$total = ($subtotalLordo + $iva) - $discountValue;
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Forx</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .original-price { text-decoration: line-through; opacity: 0.7; }
        .discounted-price { color: #dc3545; font-weight: bold; }
    </style>
</head>
<body class="bg-light">

<?php require "Components/header.php"?>

<div class="container py-5">
    <div class="row g-5">
        <!-- Sezione Articoli -->
        <div class="col-lg-8">
            <h2 class="mb-4">Riepilogo Ordine</h2>
            <div id="cart-items-container">
                <!-- Gli articoli verranno inseriti qui via JavaScript -->
            </div>

            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title">Riepilogo Costi</h5>
                    <div class="d-flex justify-content-between">
                        <span>Subtotale (escl. IVA):</span>
                        <span id="checkout-subtotal">€0.00</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>IVA 22%:</span>
                        <span id="checkout-vat">€0.00</span>
                    </div>
                    <div class="d-flex justify-content-between text-success">
                        <span>Sconto:</span>
                        <span id="checkout-discount">-€0.00</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold align-items-center">
                        <span>Totale:</span>
                        <div class="text-end">
                            <div class="original-price small <?= $discountRate > 0 ? '' : 'd-none' ?>">
                                €<?= number_format($subtotalLordo + $iva, 2) ?>
                            </div>
                            <div id="checkout-total" class="text-danger">
                                €<?= number_format($total, 2) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sezione Pagamento -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h2 class="h4 mb-4">Dettagli di Pagamento</h2>

                    <form id="checkout-form">
                        <!-- Dati Cliente -->
                        <div class="mb-3">
                            <label for="fullname" class="form-label">Nome e Cognome</label>
                            <input type="text" class="form-control" id="fullname" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" required>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Indirizzo</label>
                            <input type="text" class="form-control" id="address" required>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label for="city" class="form-label">Città</label>
                                <input type="text" class="form-control" id="city" required>
                            </div>
                            <div class="col-md-6">
                                <label for="zip" class="form-label">CAP</label>
                                <input type="text" class="form-control" id="zip" required>
                            </div>
                        </div>

                        <!-- Dati Pagamento -->
                        <div class="mb-3">
                            <label for="card-number" class="form-label">Numero Carta</label>
                            <input type="text" class="form-control" id="card-number" placeholder="4242 4242 4242 4242" required>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label for="card-expiry" class="form-label">Scadenza</label>
                                <input type="text" class="form-control" id="card-expiry" placeholder="MM/AA" required>
                            </div>
                            <div class="col-md-6">
                                <label for="card-cvc" class="form-label">CVC</label>
                                <input type="text" class="form-control" id="card-cvc" placeholder="123" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Completa l'Ordine</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const cart = JSON.parse(localStorage.getItem('cart')) || [];
        const discountRate = parseFloat(localStorage.getItem('couponDiscount')) || 0;

        function renderCheckoutItems() {
            const container = document.getElementById('cart-items-container');
            container.innerHTML = '';

            cart.forEach(item => {
                const itemEl = document.createElement('div');
                itemEl.className = 'card mb-3';
                itemEl.innerHTML = `
                    <div class="card-body">
                        <div class="row">
                            <div class="col-3">
                                <img src="${item.image}" class="img-fluid rounded" alt="${item.name}">
                            </div>
                            <div class="col-9">
                                <h5 class="h6">${item.name}</h5>
                                ${item.selectedSize ? `<p class="small mb-1">Taglia: ${item.selectedSize}</p>` : ''}
                                ${item.selectedColor ? `<p class="small mb-1">Colore: ${item.selectedColor}</p>` : ''}
                                ${item.customization ? `<p class="small mb-1">Personalizzazione: ${item.customization.text || 'Design'}</p>` : ''}
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="small">Quantità: ${item.quantita}</span>
                                    <span class="text-muted">€${(item.price * item.quantita).toFixed(2)}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                container.appendChild(itemEl);
            });
        }

        function updateTotals() {
            const subtotalLordo = cart.reduce((sum, item) => sum + (item.price * item.quantita), 0);
            const ivaRate = 0.22;
            const subtotalNetto = subtotalLordo / (1 + ivaRate);
            const vatAmount = subtotalLordo - subtotalNetto;
            const discountValue = subtotalLordo * discountRate;
            const originalTotal = subtotalLordo + vatAmount;
            const total = originalTotal - discountValue;

            // Aggiorna i totali
            document.getElementById('checkout-subtotal').textContent = `€${subtotalNetto.toFixed(2)}`;
            document.getElementById('checkout-vat').textContent = `€${vatAmount.toFixed(2)}`;
            document.getElementById('checkout-discount').textContent = `-€${discountValue.toFixed(2)}`;
            document.getElementById('checkout-total').textContent = `€${total.toFixed(2)}`;

            // Mostra/nascondi prezzo originale
            const originalTotalElement = document.getElementById('original-total');
            if(discountRate > 0) {
                originalTotalElement.classList.remove('d-none');
                originalTotalElement.textContent = `€${originalTotal.toFixed(2)}`;
            } else {
                originalTotalElement.classList.add('d-none');
            }
        }

        // Gestione Submit Ordine
        document.getElementById('checkout-form').addEventListener('submit', async (e) => {
            e.preventDefault();

            const orderData = {
                customer: {
                    name: document.getElementById('fullname').value,
                    email: document.getElementById('email').value,
                    address: document.getElementById('address').value,
                    city: document.getElementById('city').value,
                    zip: document.getElementById('zip').value
                },
                cart: cart,
                total: document.getElementById('checkout-total').textContent
            };

            try {
                const res = await fetch('database/confermaAcquisto.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(orderData)
                });

                const result = await res.json();
                if (result.success) {
                    localStorage.removeItem('cart');
                    localStorage.removeItem('couponDiscount');
                    window.location.href = 'successo.php';
                } else {
                    alert("Errore durante l'acquisto");
                }
            } catch (error) {
                console.log(error);
                alert("Si è verificato un errore durante il pagamento");
            }
        });

        // Inizializzazione
        if (cart.length === 0) {
            window.location.href = 'carrello.php';
        } else {
            renderCheckoutItems();
            updateTotals();
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>