
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

// Funzione per generare e mostrare i prodotti sulla pagina
function displayProducts(products) {
    const productContainer = document.getElementById('products-list');
    productContainer.classList.add('row', 'g-3'); // Riduce lo spazio tra le carte

    products.forEach(product => {
        const productDiv = document.createElement('div');
        productDiv.classList.add('col-6', 'col-md-4', 'col-lg-2'); // 6 per riga su desktop

        productDiv.innerHTML = `
            <div class="card shadow-sm text-center">
                <img src="${product.image}" class="card-img-top p-2" alt="${product.name}" style="height: 300px; object-fit: contain;">
                <div class="card-body p-2">
                    <h6 class="card-title">${product.name}</h6>
                    <p class="text-success fw-bold small">€${product.price.toFixed(2)}</p>
                    <a href="product.html?id=${product.id}" class="btn btn-sm btn-outline-primary w-100">Dettagli</a>
                    <button class="btn btn-sm btn-success w-100 mt-1 add-to-cart-button">Aggiungi</button>
                </div>
            </div>
        `;
        
        const addButton = productDiv.querySelector('.add-to-cart-button');
        addButton.addEventListener('click', () => addToCart(product));

        productContainer.appendChild(productDiv);
    });
}

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
