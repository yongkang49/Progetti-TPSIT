// Recupera l'ID del prodotto dall'URL
const urlParams = new URLSearchParams(window.location.search);
const productId = urlParams.get('id');

if (productId) {
    fetch('prodotti.json')
        .then(response => response.json())
        .then(products => {
            const product = products.find(p => p.id == productId);
            if (product) {
                document.getElementById('product-image').src = product.image;
                document.getElementById('product-name').innerText = product.name;
                document.getElementById('product-price').innerText = `€${product.price.toFixed(2)}`;
                document.getElementById('product-description').innerText = product.description;

                document.getElementById('add-to-cart').addEventListener('click', () => {
                    addToCart(product);
                });
            } else {
                document.getElementById('product-details').innerHTML = '<p>Prodotto non trovato</p>';
            }
        })
        .catch(error => console.error('Errore nel caricamento del prodotto:', error));
} else {
    document.getElementById('product-details').innerHTML = '<p>Nessun prodotto selezionato</p>';
}
