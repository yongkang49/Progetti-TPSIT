let allProducts = []; // Variabile globale per memorizzare tutti i prodotti

function loadProducts() {
  fetch("prodotti.json")
    .then((response) => {
      if (!response.ok) {
        throw new Error("Errore nel caricamento dei prodotti");
      }
      return response.json();
    })
    .then((products) => {
      allProducts = products; // Salva tutti i prodotti
      // Log per debug
      console.log("Tutti i prodotti e le loro categorie:");
      products.forEach((p) => console.log(`${p.nome}: ${p.categoria}`));

      displayProducts(products);
    })
    .catch((error) => {
      console.error("Errore:", error);
    });
}

function displayProducts(products) {
  const productContainer = document.getElementById("products-list");
  productContainer.innerHTML = ""; // Pulisce il contenitore prima di aggiungere nuovi prodotti

  products.forEach((product) => {
    const productDiv = document.createElement("div");
    productDiv.classList.add("col-6", "col-md-4", "col-lg-2");

    productDiv.innerHTML = `
      <div class="card shadow-sm text-center">
        <img src="${product.image}" class="card-img-top p-2" alt="${
      product.id
    }" style="height: 300px; object-fit: contain;">
        <div class="card-body p-2">
          <h6 class="card-title">${product.nome}</h6>
          <p class="text-success fw-bold small">€${product.prezzo.toFixed(
            2
          )}</p>
          <a href="product.html?id=${
            product.id
          }" class="btn btn-sm btn-outline-primary w-100">Dettagli</a>
        </div>
      </div>
    `;

    productContainer.appendChild(productDiv);
  });

  // Aggiorna il conteggio dei prodotti visualizzati
  updateProductCount(products.length);
}

function filterProducts(category) {
  if (category === "all") {
    displayProducts(allProducts);
  } else {
    // Debug log
    console.log("Categoria selezionata:", category);
    console.log(
      "Tutte le categorie presenti:",
      allProducts.map((p) => p.categoria)
    );

    // Rendi il confronto case-insensitive e rimuovi spazi extra
    const filteredProducts = allProducts.filter((product) => {
      const productCategory = product.categoria.toLowerCase().trim();
      const selectedCategory = category.toLowerCase().trim();
      console.log(`Confronto: '${productCategory}' con '${selectedCategory}'`);
      return productCategory === selectedCategory;
    });

    console.log("Prodotti filtrati:", filteredProducts);
    displayProducts(filteredProducts);
  }
}

function updateProductCount(count) {
  const productContainer = document.getElementById("products-list");
  let countElement = document.getElementById("product-count");

  if (!countElement) {
    countElement = document.createElement("div");
    countElement.id = "product-count";
    countElement.classList.add("text-center", "mb-3", "text-muted");
    productContainer.parentNode.insertBefore(countElement, productContainer);
  }

  countElement.textContent = `${count} prodotti visualizzati`;
}

// Event listeners
document.addEventListener("DOMContentLoaded", () => {
  loadProducts();

  // Aggiungi l'event listener per il cambio di categoria
  document.getElementById("category-filter").addEventListener("change", (e) => {
    filterProducts(e.target.value);
  });
});
