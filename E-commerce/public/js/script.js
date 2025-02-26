const visualizza = [3,4,5,6,8,10,11,13,15,16,17,20] ;
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

function displayProducts(products) {
    const productContainer = document.getElementById('products-list');
    productContainer.classList.add('row', 'g-3'); // Riduce lo spazio tra le carte

    let count = 0;
    let sectionCounter = 1;

    products.forEach(product => {
        if (visualizza.includes(product.id)) {
            // Ogni 4 prodotti, aggiungi un titolo di sezione con il link "vedi tutto"
            if (count % 4 === 0) {
                // Crea un contenitore flex per il titolo e il link
                const sectionHeader = document.createElement('div');
                sectionHeader.classList.add('d-flex', 'justify-content-between', 'align-items-center', 'w-100', 'mt-4', 'mb-2');

                const sectionTitle = document.createElement('h3');
                sectionTitle.textContent = `Sezione ${sectionCounter}`;
                sectionHeader.appendChild(sectionTitle);

                const seeAllLink = document.createElement('a');
                seeAllLink.href = "allProduct.html"; // Puoi modificare l'URL se necessario
                seeAllLink.textContent = "vedi tutto";
                seeAllLink.classList.add('text-decoration-none'); // opzionale: rimuove la sottolineatura
                sectionHeader.appendChild(seeAllLink);

                productContainer.appendChild(sectionHeader);
                sectionCounter++;
            }

            // Crea la scheda del prodotto
            const productDiv = document.createElement('div');
            productDiv.classList.add('col-6', 'col-md-4', 'col-lg-2'); // 6 per riga su desktop

            productDiv.innerHTML = `
            <div class="card shadow-sm text-center">
                <img src="${product.image}" class="card-img-top p-2" alt="${product.id}" style="height: 300px; object-fit: contain;">
                <div class="card-body p-2">
                    <h6 class="card-title">${product.nome}</h6>
                    <p class="text-success fw-bold small">€${product.prezzo.toFixed(2)}</p>
                    <a href="product.html?id=${product.id}" class="btn btn-sm btn-outline-primary w-100">Dettagli</a>
                    <button class="btn btn-sm btn-success w-100 mt-1 add-to-cart-button">Aggiungi al carrello</button>
                </div>
            </div>
            `;
            const addButton = productDiv.querySelector('.add-to-cart-button');
            addButton.addEventListener('click', () => addToCart(product));
            productContainer.appendChild(productDiv);

            count++;
        }
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
