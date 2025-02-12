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
        img.src = item.image; // Assicurati che ogni oggetto "item" abbia una proprietà "image" con il percorso dell'immagine
        img.alt = item.name;
        img.style.width = '40px';
        img.style.height = '40px';
        img.style.marginRight = '10px';
        img.style.borderRadius = '5px';

        // Nome e prezzo del prodotto
        const textSpan = document.createElement('span');
        textSpan.innerHTML = `${item.name} - €${item.price.toFixed(2)}`;

        // Pulsante per rimuovere l'elemento
        const removeButton = document.createElement('button');
        removeButton.textContent = '❌';
        removeButton.style.marginLeft = '10px';
        removeButton.style.backgroundColor = 'white';
        removeButton.onclick = () => removeFromCart(index);

        li.appendChild(img);
        li.appendChild(textSpan);
        li.appendChild(removeButton);
        cartItemsList.appendChild(li);

        totalPrice += item.price;
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
    cart.push(product);
    updateCart();
}
// Event listeners
document.getElementById('cart-button').addEventListener('click', showCart);
document.getElementById('close-cart').addEventListener('click', closeCart);
// carica i prodotti dentro localstorage se presente 
loadCartFromStorage();