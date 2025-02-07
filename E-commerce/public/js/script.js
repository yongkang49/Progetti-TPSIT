// Recupera il carrello dal localStorage o inizializza un array vuoto
let cart = [];

// Funzione per aggiornare il carrello
function updateCart() {
    document.getElementById('cart-button').innerText = `Carrello (${cart.length})`;
}

// Funzione per mostrare il carrello nel modal
function showCart() {
    const cartItemsList = document.getElementById('cart-items');
    //cartItemsList.innerHTML = ''; // Resetta il carrello
    cart.forEach(item => {
        const li = document.createElement('li');
        li.innerHTML = `${item.name} - €${item.price.toFixed(2)}`;
        cartItemsList.appendChild(li);
        localStorage.setItem('cart', `${li}`);
    });
    document.getElementById('cart-modal').style.display = 'flex';
}

// Funzione per chiudere il modal
function closeCart() {
    document.getElementById('cart-modal').style.display = 'none';
}

// Funzione per caricare i prodotti con fetch
function loadProducts() {
    fetch('prodotti.json')  
        .then(response => {
            if (!response.ok) {
                throw new Error('Errore nel caricamento dei prodotti');
            }
            return response.json();
        })
        .then(products => {
            displayProducts(products);  // Passa i prodotti alla funzione di visualizzazione
        })
        .catch(error => {
            console.error('Errore:', error);
        });
}
// Funzione per aggiungere un prodotto al carrello
function addToCart(product) {
    cart.push(product);
    updateCart();
}

// Funzione per generare e mostrare i prodotti sulla pagina
function displayProducts(products) {
    const productContainer = document.getElementById('products-list');
    products.forEach(product => {
        const productDiv = document.createElement('div');
        productDiv.classList.add('product');
        productDiv.innerHTML = `
            <img src="${product.image}" alt="${product.name}">
            <h3>${product.name}</h3>
            <p>€${product.price.toFixed(2)}</p>
            <button class="add-to-cart-button">Aggiungi al carrello</button>
        `;
        
        // Aggiungi l'evento "click" al pulsante "Aggiungi al carrello"
        const addButton = productDiv.querySelector('.add-to-cart-button');
        addButton.addEventListener('click', () => addToCart(product)); // Passa l'oggetto prodotto direttamente

        productContainer.appendChild(productDiv);
    });
}

// Event listeners
document.getElementById('cart-button').addEventListener('click', showCart);
document.getElementById('close-cart').addEventListener('click', closeCart);

let currentSlide = 0;
const slides = document.querySelectorAll(".slide");

function showSlide(index) {
    slides.forEach(slide => slide.classList.remove("active"));
    slides[index].classList.add("active");
}

function nextSlide() {
    currentSlide = (currentSlide + 1) % slides.length;
    showSlide(currentSlide);
}

function prevSlide() {
    currentSlide = (currentSlide - 1 + slides.length) % slides.length;
    showSlide(currentSlide);
}

// Cambio automatico ogni 3 secondi
setInterval(nextSlide, 3000);

// Mostra la prima immagine all'avvio
showSlide(currentSlide);

// Carica i prodotti all'avvio
loadProducts();