// Recupera l'ID del prodotto dalla query string
const urlParams = new URLSearchParams(window.location.search);
const productId = urlParams.get('id');

if (productId) {
  fetch('prodotti.json')
    .then(response => response.json())
    .then(products => {
      // Cerca il prodotto corrispondente all'ID
      const product = products.find(p => p.id == productId);
      if (product) {
        // Aggiorna l'immagine e gli altri dettagli
        document.getElementById('product-image').src = product.image;
        document.getElementById('product-name').innerText = product.nome;
        document.getElementById('product-price').innerText = `€${product.prezzo.toFixed(2)}`;
        // Se esiste una descrizione, puoi gestirla (qui non presente nel JSON)
        // document.getElementById('product-description').innerText = product.descrizione;
        document.getElementById('product-materiale').textContent = product.materiale;
        document.getElementById('product-taglio').textContent = product.taglio;
        
        // Per il campo "colore", crea dei bottoni per ogni colore disponibile
        const colorContainer = document.getElementById('product-colore');
        colorContainer.innerHTML = ""; // Pulisce eventuali contenuti precedenti
        product.colori.forEach(color => {
          const btn = document.createElement('button');
          btn.classList.add('color-button');
          // Imposta il background del bottone in base al nome del colore (es. "black", "pink", ecc.)
          btn.style.backgroundColor = color.name;
          btn.title = color.name;
          btn.addEventListener('click', () => {
            // Al click, aggiorna l'immagine del prodotto con quella corrispondente al colore
            document.getElementById('product-image').src = color.image;
          });
          colorContainer.appendChild(btn);
        });
        
        // Aggiorna la disponibilità (campo "quantità" nel JSON)
        document.getElementById('product-quantità').textContent = product["quantità"];
        
        // Imposta il listener per il pulsante "Aggiungi al Carrello"
        document.getElementById('add-to-cart').addEventListener('click', () => {
          addToCart(product);
        });
      } else {
        alert('Prodotto non trovato');
      }
    })
    .catch(error => console.error('Errore nel caricamento del prodotto:', error));
} else {
  alert('Nessun prodotto selezionato');
}
