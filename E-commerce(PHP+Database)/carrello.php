<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$couponMessage = '';
$couponSuccess = false;

// Gestione richieste coupon
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['coupon_code'])) {
    $couponCode = strtoupper(trim($_POST['coupon_code']));
    $validCoupons = ['SCONTO10' => 0.10, 'SCONTO20' => 0.20];

    if (isset($_SESSION['applied_coupon'])) {
        $couponMessage = 'Hai già utilizzato uno sconto!';
    } elseif (array_key_exists($couponCode, $validCoupons)) {
        $_SESSION['applied_coupon'] = [
            'code' => $couponCode,
            'discount' => $validCoupons[$couponCode]
        ];
        $couponMessage = 'Coupon applicato con successo!';
        $couponSuccess = true;
    } else {
        $couponMessage = 'Coupon non valido';
    }
}

// Rimozione coupon
if (isset($_GET['remove_coupon'])) {
    unset($_SESSION['applied_coupon']);
    header("Location: carrello.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Carrello</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .cart-image { height: 80px; object-fit: cover; }
        .original-price { text-decoration: line-through; opacity: 0.7; }
        .discounted-price { color: #dc3545; }
        .coupon-active { background-color: #f8f9fa; border-radius: 5px; }
    </style>
</head>
<body>

<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Il tuo Carrello</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <div id="empty-cart-message" class="alert alert-info d-none">Il carrello è vuoto</div>

            <div id="cart-content">
                <ul id="cart-items" class="list-group mb-3"></ul>

                <div class="d-flex justify-content-between">
                    <span>Subtotale (escl. IVA):</span>
                    <span id="subtotal-netto">€0.00</span>
                </div>
                <div id="vat-row" class="d-flex justify-content-between d-none">
                    <span>IVA 22%:</span>
                    <span id="vat-amount">€0.00</span>
                </div>
                <div id="discount-row" class="d-flex justify-content-between text-success d-none">
                    <span>Sconto:</span>
                    <span id="discount-amount">-€0.00</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between fw-bold align-items-center">
                    <span>Totale (inc. IVA):</span>
                    <div class="text-end">
                        <div id="original-total" class="original-price small d-none">€0.00</div>
                        <span id="total" class="discounted-price">€0.00</span>
                    </div>
                </div>
            </div>

            <form id="coupon-form" method="post" class="mb-3 <?= isset($_SESSION['applied_coupon']) ? 'coupon-active p-3' : '' ?>">
                <div class="input-group">
                    <input type="text"
                           name="coupon_code"
                           id="coupon-code"
                           class="form-control"
                           placeholder="Inserisci codice coupon"
                        <?= isset($_SESSION['applied_coupon']) ? 'disabled' : '' ?>>
                    <button type="submit"
                            class="btn btn-<?= isset($_SESSION['applied_coupon']) ? 'success' : 'outline-primary' ?>"
                        <?= isset($_SESSION['applied_coupon']) ? 'disabled' : '' ?>>
                        <?= isset($_SESSION['applied_coupon']) ? '✅ Applicato' : 'Applica' ?>
                    </button>
                </div>
                <div id="coupon-feedback" class="mt-2 small <?= $couponSuccess ? 'text-success' : 'text-danger' ?>">
                    <?= $couponMessage ?>
                    <?php if(isset($_SESSION['applied_coupon'])): ?>
                        <div class="mt-1">
                            <small>
                                Codice: <strong><?= $_SESSION['applied_coupon']['code'] ?></strong>
                                (<a href="carrello.php?remove_coupon=1" class="text-danger">Rimuovi</a>)
                            </small>
                        </div>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <a id="checkout-button" href="checkout.php" class="btn btn-primary w-100">Procedi al Checkout</a>
            <button type="button" class="btn btn-secondary w-100 mt-2" data-bs-dismiss="modal">Continua lo Shopping</button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        function refreshCartDisplay() {
            const cart = JSON.parse(localStorage.getItem('cart')) || [];
            const discountRate = <?= $_SESSION['applied_coupon']['discount'] ?? 0 ?>;

            // Render articoli
            const cartItems = document.getElementById('cart-items');
            cartItems.innerHTML = '';

            cart.forEach((item, index) => {
                const li = document.createElement('li');
                li.className = 'list-group-item';
                li.innerHTML = `
                <div class="row align-items-center">
                    <div class="col-auto">
                        <img src="${item.image}" class="cart-image" alt="${item.name}">
                    </div>
                    <div class="col">
                        <h6 class="mb-1">${item.name}</h6>
                        ${item.selectedSize ? `<small class="d-block">Taglia: ${item.selectedSize}</small>` : ''}
                        ${item.selectedColor ? `<small class="d-block">Colore: ${item.selectedColor}</small>` : ''}
                        ${item.customization?.type === 'text' ?
                    `<small class="d-block">Personalizzazione: "${item.customization.text}"</small>` :
                    item.customization?.type === 'design' ?
                        `<small class="d-block">Personalizzazione: Design selezionato</small>` : ''}
                    </div>
                    <div class="col-auto d-flex align-items-center">
                        <input type="number"
                               class="form-control form-control-sm me-2 quantity-input"
                               data-index="${index}"
                               value="${item.quantita}"
                               min="1"
                               style="width: 60px;">
                        <span class="badge bg-primary rounded-pill me-2">
                            €${(item.price * item.quantita).toFixed(2)}
                        </span>
                        <button class="btn btn-danger btn-sm remove-item" data-index="${index}">×</button>
                    </div>
                </div>
            `;
                cartItems.appendChild(li);
            });

            // Calcoli totali
            const subtotalLordo = cart.reduce((acc, item) => acc + (item.price * item.quantita), 0);
            const iva = subtotalLordo * 0.22;
            const discountValue = subtotalLordo * discountRate;
            const total = (subtotalLordo + iva) - discountValue;

            // Aggiorna UI
            document.getElementById('subtotal-netto').textContent = `€${subtotalLordo.toFixed(2)}`;
            document.getElementById('vat-amount').textContent = `€${iva.toFixed(2)}`;
            document.getElementById('total').textContent = `€${total.toFixed(2)}`;

            const originalTotalElement = document.getElementById('original-total');
            if(discountRate > 0) {
                document.getElementById('discount-row').classList.remove('d-none');
                document.getElementById('discount-amount').textContent = `-€${discountValue.toFixed(2)}`;
                originalTotalElement.classList.remove('d-none');
                originalTotalElement.textContent = `€${(subtotalLordo + iva).toFixed(2)}`;
            } else {
                document.getElementById('discount-row').classList.add('d-none');
                originalTotalElement.classList.add('d-none');
            }

            // Mostra/nascondi carrello vuoto
            document.getElementById('empty-cart-message').classList.toggle('d-none', cart.length > 0);
            document.getElementById('cart-content').classList.toggle('d-none', cart.length === 0);
        }

        // Gestione eventi
        document.addEventListener('input', (e) => {
            if (e.target.classList.contains('quantity-input')) {
                const index = e.target.dataset.index;
                const newQty = Math.max(1, parseInt(e.target.value) || 1);
                const cart = JSON.parse(localStorage.getItem('cart')) || [];
                cart[index].quantita = newQty;
                localStorage.setItem('cart', JSON.stringify(cart));
                refreshCartDisplay();
            }
        });

        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-item')) {
                const index = e.target.dataset.index;
                const cart = JSON.parse(localStorage.getItem('cart')) || [];
                cart.splice(index, 1);
                localStorage.setItem('cart', JSON.stringify(cart));
                refreshCartDisplay();
            }
        });

        // Gestione coupon
        document.getElementById('coupon-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const feedback = document.getElementById('coupon-feedback');

            try {
                const response = await fetch('apply_coupon.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                feedback.textContent = result.message;

                if (result.success) {
                    feedback.style.color = 'green';
                    location.reload(); // Ricarica per aggiornare la sessione
                } else {
                    feedback.style.color = 'red';
                }
            } catch (error) {
                feedback.style.color = 'red';
                feedback.textContent = 'Errore di connessione';
            }
        });

        refreshCartDisplay();
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>