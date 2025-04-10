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

                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Riepilogo</h5>
                        <div class="d-flex justify-content-between">
                            <span>Subtotale:</span>
                            <span id="subtotal">€0.00</span>
                        </div>
                        <div id="discount-row" class="d-flex justify-content-between text-success d-none">
                            <span>Sconto:</span>
                            <span id="discount-amount">-€0.00</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold">
                            <span>Totale:</span>
                            <span id="total">€0.00</span>
                        </div>
                    </div>
                </div>
            </div>

            <form id="coupon-form" method="post" class="mb-3">
                <div class="input-group">
                    <input type="text" name="coupon_code" id="coupon-code" class="form-control"
                           placeholder="Inserisci codice coupon">
                    <button type="submit" class="btn btn-outline-primary">Applica</button>
                </div>
                <div id="coupon-feedback" class="mt-2 small"></div>
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
            const cartItems = document.getElementById('cart-items');
            const emptyMessage = document.getElementById('empty-cart-message');
            const cartContent = document.getElementById('cart-content');

            cartItems.innerHTML = '';

            if (cart.length === 0) {
                emptyMessage.classList.remove('d-none');
                cartContent.classList.add('d-none');
                return;
            }

            emptyMessage.classList.add('d-none');
            cartContent.classList.remove('d-none');

            let subtotal = 0;

            cart.forEach((item, index) => {
                const li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between align-items-center';

                const details = document.createElement('div');
                details.innerHTML = `
                <h6>${item.name}</h6>
                <small class="d-block">Quantità: ${item.quantita}</small>
                ${item.selectedSize ? `<small class="d-block">Taglia: ${item.selectedSize}</small>` : ''}
                ${item.selectedColor ? `<small class="d-block">Colore: ${item.selectedColor}</small>` : ''}
                ${item.customization && item.customization.type !== 'none' ? `
                    <small class="d-block">Personalizzazione: ${item.customization.type === 'text' ?
                    'Testo - "' + item.customization.text + '"' :
                    'Design selezionato'}
                    </small>
                ` : ''}
            `;

                const priceDiv = document.createElement('div');
                priceDiv.innerHTML = `
                <span class="badge bg-primary rounded-pill">€${(item.price * item.quantita).toFixed(2)}</span>
                <button class="btn btn-danger btn-sm ms-2 remove-item" data-index="${index}">X</button>
            `;

                li.appendChild(details);
                li.appendChild(priceDiv);
                cartItems.appendChild(li);

                subtotal += item.price * item.quantita;
            });

            // Aggiorna i totali
            document.getElementById('subtotal').textContent = `€${subtotal.toFixed(2)}`;
            const discount = localStorage.getItem('couponDiscount') || 0;
            const total = subtotal * (1 - discount);

            if (discount > 0) {
                document.getElementById('discount-row').classList.remove('d-none');
                document.getElementById('discount-amount').textContent = `-€${(subtotal * discount).toFixed(2)}`;
            } else {
                document.getElementById('discount-row').classList.add('d-none');
            }

            document.getElementById('total').textContent = `€${total.toFixed(2)}`;
        }

        // Gestione rimozione item
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
            const response = await fetch('apply_coupon.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            const feedback = document.getElementById('coupon-feedback');

            if (result.success) {
                feedback.style.color = 'green';
                localStorage.setItem('couponDiscount', result.discount);
            } else {
                feedback.style.color = 'red';
            }

            feedback.textContent = result.message;
            refreshCartDisplay();
        });

        // Inizializza il carrello al primo caricamento
        refreshCartDisplay();
    });
</script>