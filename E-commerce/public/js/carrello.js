let cart = [];
// Funzione per aggiornare il carrello
function updateCart() {
    document.getElementById('cart-button').innerText = `Carrello (${cart.length})`;
    saveCartToStorage(); // Salva sempre il carrello dopo ogni aggiornamento
}

function loadCartFromStorage() {
    const storedCart = localStorage.getItem('cart');
    if (storedCart) {
        cart = JSON.parse(storedCart);
        updateCart();
    }
}
function saveCartToStorage() {
    localStorage.setItem('cart', JSON.stringify(cart));
}
// Funzione per mostrare il carrello nel modal
function showCart() {
    const cartItemsList = document.getElementById('cart-items');
    cartItemsList.innerHTML = ''; // Resetta il carrello
    let totalPrice = 0;

    cart.forEach((item, index) => {
        const li = document.createElement('li');
        li.style.display = 'flex';
        li.style.alignItems = 'center';
        li.style.marginBottom = '10px';

        // Creazione dell'immagine del prodotto
        const img = document.createElement('img');
        img.src = item.image;
        img.alt = item.nome;
        img.style.width = '40px';
        img.style.height = '40px';
        img.style.marginRight = '10px';
        img.style.borderRadius = '5px';

        // Nome, prezzo e quantità
        const textSpan = document.createElement('span');
        textSpan.innerHTML = `${item.nome} - €${item.prezzo.toFixed(2)} x ${item.quantita}`;

        // Pulsante per diminuire la quantità
        const decreaseButton = document.createElement('button');
        decreaseButton.textContent = '➖';
        decreaseButton.style.textAlign = 'left';
        decreaseButton.style.marginLeft = '10px';
        decreaseButton.style.backgroundColor = 'white';
        decreaseButton.onclick = () => updateQuantity(index, item.quantita - 1);

        // Pulsante per aumentare la quantità
        const increaseButton = document.createElement('button');
        increaseButton.textContent = '➕';
        increaseButton.style.marginLeft = '5px';
        increaseButton.style.backgroundColor = 'white';
        increaseButton.onclick = () => updateQuantity(index, item.quantita + 1);

        // Pulsante per rimuovere l'elemento
        const removeButton = document.createElement('button');
        removeButton.textContent = '❌';
        removeButton.style.marginLeft = '10px';
        removeButton.style.backgroundColor = 'white';
        removeButton.onclick = () => removeFromCart(index);

        li.appendChild(img);
        li.appendChild(textSpan);
        li.appendChild(decreaseButton);
        li.appendChild(increaseButton);
        li.appendChild(removeButton);
        cartItemsList.appendChild(li);

        totalPrice += item.prezzo * item.quantita;
    });

    // Aggiunge il totale al carrello
    const totalLi = document.createElement('li');
    totalLi.innerHTML = `<strong>Totale: €${totalPrice.toFixed(2)}</strong>`;
    cartItemsList.appendChild(totalLi);

    document.getElementById('cart-modal').style.display = 'flex';
}



function removeFromCart(index) {
    cart.splice(index, 1); // Rimuove l'elemento dalla lista
    saveCartToStorage(); // Salva il carrello aggiornato nel localStorage
    showCart(); // Ricarica il carrello per aggiornare la visualizzazione
}

// Funzione per chiudere il modal
function closeCart() {
    document.getElementById('cart-modal').style.display = 'none';
}

// Funzione per aggiungere un prodotto al carrello
function addToCart(product) {
    let existingProduct = cart.find(item => item.id === product.id);

    if (existingProduct) {
        existingProduct.quantita += 1;
    } else {
        product.quantita = 1;
        cart.push(product);
    }

    localStorage.setItem('cart', JSON.stringify(cart));
    updateCart();
    
    // Mostra il modale di conferma
    showConfirmationModal();
}
function showConfirmationModal() {
    const modal = document.createElement('div');
    modal.innerText = 'Prodotto aggiunto con successo!';
    
    // Stili per centrare il modale
    modal.style.position = 'fixed';
    modal.style.top = '50%';
    modal.style.left = '50%';
    modal.style.transform = 'translate(-50%, -50%)'; // Centra esattamente
    modal.style.padding = '15px 20px';
    modal.style.backgroundColor = '#28a745';
    modal.style.color = 'white';
    modal.style.fontSize = '18px';
    modal.style.borderRadius = '5px';
    modal.style.boxShadow = '0px 4px 6px rgba(0, 0, 0, 0.1)';
    modal.style.zIndex = '1000';
    modal.style.textAlign = 'center';

    document.body.appendChild(modal);

    setTimeout(() => {
        modal.remove();
    }, 2000);
}


function updateQuantity(index, newQuantity) {
    if (newQuantity < 1) {
        removeFromCart(index); // Se la quantità diventa 0, rimuove l'elemento
        return;
    }

    cart[index].quantita = newQuantity;
    localStorage.setItem('cart', JSON.stringify(cart)); // Salva nel localStorage
    showCart(); // Aggiorna il carrello
}

// Event listeners
document.getElementById('cart-button').addEventListener('click', showCart);
document.getElementById('close-cart').addEventListener('click', closeCart);

window.addEventListener('beforeunload', () => {
    localStorage.removeItem('cart'); // Cancella solo il carrello
});
// carica i prodotti dentro localstorage se presente 
loadCartFromStorage();

