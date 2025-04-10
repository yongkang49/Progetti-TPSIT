let cart = [];
let couponData = { applied: false, code: null };

// Funzione per aggiornare il carrello
function updateCart() {
  document.getElementById(
      "cart-button"
  ).innerText = `Carrello (${cart.length})`;
  saveCartToStorage(); // Salva sempre il carrello dopo ogni aggiornamento
}

function loadCartFromStorage() {
  // Carica il carrello
  const storedCart = localStorage.getItem("cart");
  if (storedCart) {
    try {
      cart = JSON.parse(storedCart);

      // Verifica che ogni prodotto abbia i campi necessari
      cart = cart.filter(item => item.id && item.nome && item.prezzo);

      if (!Array.isArray(cart)) cart = []; // Verifica che sia un array
    } catch (error) {
      console.error("Errore nel parsing del carrello:", error);
      cart = [];
    }
  } else {
    cart = [];
  }

  // Carica il coupon
  const storedCoupon = localStorage.getItem("coupon");
  if (storedCoupon) {
    try {
      couponData = JSON.parse(storedCoupon);
    } catch (error) {
      console.error("Errore nel parsing del coupon:", error);
      couponData = { applied: false, code: null };
    }
  } else {
    couponData = { applied: false, code: null };
  }

  updateCart(); // Rende aggiornato il carrello visivamente
}

function saveCartToStorage() {
  localStorage.setItem("cart", JSON.stringify(cart));
}

function saveCouponToStorage() {
  localStorage.setItem("coupon", JSON.stringify(couponData));
}

// Modifica la funzione per trovare i prodotti per il bundle
function findBundleProducts(cart) {
  const categoryGroups = {};

  // Raggruppa i prodotti per categoria
  cart.forEach((item) => {
    if (!categoryGroups[item.categoria]) {
      categoryGroups[item.categoria] = [];
    }
    categoryGroups[item.categoria].push({ ...item });
  });

  // Se abbiamo meno di 3 categorie, non possiamo fare un bundle
  if (Object.keys(categoryGroups).length < 3) {
    return null;
  }

  // Prendi il prodotto meno costoso da ogni categoria
  const bundleProducts = Object.values(categoryGroups)
      .map((products) =>
          products.reduce((min, product) =>
              product.prezzo < min.prezzo ? product : min
          )
      )
      .slice(0, 3); // Prendi solo 3 prodotti

  // Crea una copia dei prodotti con quantità 1 per il bundle
  return bundleProducts.map((product) => ({
    ...product,
    quantita: 1,
  }));
}

// Modifica la funzione showCart per gestire il bundle
function showCart() {
  const cartItemsList = document.getElementById("cart-items");
  cartItemsList.innerHTML = "";
  let totalPrice = 0;

  // Mostra tutti i prodotti normalmente
  cart.forEach((item, index) => {
    const li = document.createElement("li");
    li.style.display = "flex";
    li.style.alignItems = "center";
    li.style.marginBottom = "10px";

    // Creazione dell'immagine del prodotto
    const img = document.createElement("img");
    img.src = item.image;
    img.alt = item.nome;
    img.style.width = "50px";
    img.style.height = "50px";
    img.style.objectFit = "cover";
    img.style.marginRight = "10px";
    img.style.borderRadius = "5px";

    // Nome, prezzo, taglia e colore
    const textSpan = document.createElement("div");
    const sizeColor = [];
    if (item.selectedSize) sizeColor.push(`Taglia: ${item.selectedSize}`);
    if (item.selectedColor) sizeColor.push(`Colore: ${item.selectedColor}`);
    if (item.customization) {
      sizeColor.push(
          `Personalizzazione: ${
              item.customization.type === "text"
                  ? `Testo "${item.customization.text}" (${getPositionLabel(
                      item.customization.textPosition
                  )})`
                  : `Disegno ${item.customization.design}`
          }`
      );
    }

    textSpan.innerHTML = `${item.nome} ${
        sizeColor.length ? "(" + sizeColor.join(", ") + ")" : ""
    }`;

    // Pulsante per diminuire la quantità
    const decreaseButton = document.createElement("button");
    decreaseButton.textContent = "➖";
    decreaseButton.style.marginLeft = "10px";
    decreaseButton.style.backgroundColor = "white";
    decreaseButton.onclick = () => updateQuantity(index, item.quantita - 1);

    // Pulsante per aumentare la quantità
    const increaseButton = document.createElement("button");
    increaseButton.textContent = "➕";
    increaseButton.style.marginLeft = "5px";
    increaseButton.style.backgroundColor = "white";
    increaseButton.onclick = () => updateQuantity(index, item.quantita + 1);

    // Pulsante per rimuovere l'elemento
    const removeButton = document.createElement("button");
    removeButton.textContent = "❌";
    removeButton.style.marginLeft = "10px";
    removeButton.style.backgroundColor = "white";
    removeButton.onclick = () => removeFromCart(index);

    li.appendChild(img);
    li.appendChild(textSpan);
    li.appendChild(decreaseButton);
    li.appendChild(increaseButton);
    li.appendChild(removeButton);
    cartItemsList.appendChild(li);

    totalPrice += item.prezzo * item.quantita;
  });

  // Verifica se possiamo applicare lo sconto bundle
  const bundleProducts = findBundleProducts(cart);
  if (bundleProducts) {
    const bundlePrice = bundleProducts.reduce(
        (sum, item) => sum + item.prezzo, // Considera solo il prezzo base
        0
    );
    const bundleDiscount = bundlePrice * 0.1;

    // Mostra i prodotti nel bundle
    const bundleMessage = document.createElement("li");
    bundleMessage.innerHTML = `
      <span class="text-success">
        <strong>Sconto Bundle (-10%)</strong><br>
        Applicato a un'unità dei seguenti prodotti:<br>
        ${bundleProducts
        .map((p) => `- ${p.nome} (${p.categoria})`)
        .join("<br>")}
        <br>Risparmio: €${bundleDiscount.toFixed(2)}
      </span>
    `;
    cartItemsList.appendChild(bundleMessage);

    // Applica lo sconto solo al prezzo dei prodotti nel bundle
    totalPrice -= bundleDiscount;
  }

  // Applica il coupon dopo lo sconto bundle
  if (couponData.applied && couponData.code) {
    let discount = 0;
    if (couponData.code === "SCONTO10") {
      discount = totalPrice * 0.1;
    } else if (couponData.code === "SCONTO20") {
      discount = totalPrice * 0.2;
    }
    totalPrice -= discount;
  }

  // Mostra il totale
  const totalLi = document.createElement("li");
  totalLi.innerHTML = `<strong>Totale: €<span id='total-price'>${totalPrice.toFixed(
      2
  )}</span></strong>`;
  cartItemsList.appendChild(totalLi);

  document.getElementById("cart-modal").style.display = "flex";
}

function applyCoupon() {
  if (couponData.applied) {
    alert("Hai già utilizzato un coupon!");
    return;
  }

  const couponCode = document.getElementById("coupon-code").value;
  let discountPercent = 0;

  if (couponCode === "SCONTO10") {
    discountPercent = 0.1;
  } else if (couponCode === "SCONTO20") {
    discountPercent = 0.2;
  } else {
    alert("Codice coupon non valido!");
    return;
  }

  // Aggiorna lo stato del coupon
  couponData.applied = true;
  couponData.code = couponCode;
  saveCouponToStorage();

  // Disabilita il campo e il pulsante dopo l'uso
  document.getElementById("coupon-code").disabled = true;
  document.querySelector("#coupon-container button").disabled = true;

  // Aggiorna la visualizzazione del carrello
  showCart();
}

function removeFromCart(index) {
  cart.splice(index, 1); // Rimuove l'elemento dalla lista
  saveCartToStorage(); // Salva il carrello aggiornato nel localStorage
  showCart(); // Ricarica il carrello per aggiornare la visualizzazione
}

// Funzione per chiudere il modal
function closeCart() {
  document.getElementById("cart-modal").style.display = "none";
}

// Funzione per aggiungere un prodotto al carrello
function addToCart(product) {
  // Recupera l'immagine attualmente visualizzata (colore selezionato)
  const currentImage = document.getElementById("product-image").src;

  // Aggiorna l'immagine del prodotto con quella selezionata
  product.image = currentImage;

  // Cerca nel carrello se esiste già questo prodotto con lo stesso colore (ossia stessa immagine)
  let existingProduct = cart.find(
      (item) => item.id === product.id && item.image === product.image
  );

  if (existingProduct) {
    existingProduct.quantita += 1;
  } else {
    product.quantita = 1;
    cart.push(product);
  }

  saveCartToStorage(); // Salva il carrello dopo l'aggiornamento
  updateCart();

  // Mostra il modale di conferma
  showConfirmationModal();
}

function showConfirmationModal() {
  const modal = document.createElement("div");
  modal.innerText = "Prodotto aggiunto con successo!";

  // Stili per centrare il modale
  modal.style.position = "fixed";
  modal.style.top = "50%";
  modal.style.left = "50%";
  modal.style.transform = "translate(-50%, -50%)"; // Centra esattamente
  modal.style.padding = "15px 20px";
  modal.style.backgroundColor = "#28a745";
  modal.style.color = "white";
  modal.style.fontSize = "18px";
  modal.style.borderRadius = "5px";
  modal.style.boxShadow = "0px 4px 6px rgba(0, 0, 0, 0.1)";
  modal.style.zIndex = "1000";
  modal.style.textAlign = "center";

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
  saveCartToStorage(); // Salva nel localStorage
  showCart(); // Aggiorna il carrello
}

// Event listeners
document.getElementById("cart-button").addEventListener("click", showCart);
document.getElementById("close-cart").addEventListener("click", closeCart);

// carica i prodotti dentro localstorage se presente
loadCartFromStorage();

// Funzione per tradurre i valori delle posizioni
function getPositionLabel(position) {
  const positions = {
    "front-center": "Centro frontale",
    "front-top": "Alto frontale",
    "front-bottom": "Basso frontale",
    "back-center": "Centro posteriore",
    "back-top": "Alto posteriore",
    "sleeve-left": "Manica sinistra",
    "sleeve-right": "Manica destra",
  };
  return positions[position] || position;
}

// Checkout
document.getElementById("checkout-button").addEventListener("click", () => {
  window.location.href = "checkout.php";
});

// Funzione per il bundle
function showBundleInfo() {
  const modal = document.createElement("div");
  modal.className = "bundle-modal";
  modal.style.display = "flex";

  modal.innerHTML = `
    <div class="bundle-modal-content">
      <button class="close-bundle-info">&times;</button>
      <h2 class="bundle-title">🎁 Offerta Bundle Speciale!</h2>
      <div class="bundle-description">
        <p>Acquista prodotti da 3 categorie diverse e ottieni uno sconto del 10% su un'unità di ciascun prodotto!</p>
        <div class="bundle-example">
          <strong>Come funziona:</strong>
          <ul>
            <li>Aggiungi al carrello almeno un prodotto da tre categorie diverse (vestiti, scarpe, gonne)</li>
            <li>Lo sconto verrà applicato automaticamente sui 3 prodotti più economici di categorie diverse</li>
            <li>Lo sconto si applica a una sola unità per prodotto</li>
          </ul>
        </div>
        <p><strong>Esempio:</strong> Se acquisti un vestito, una gonna e un paio di scarpe, riceverai automaticamente lo sconto del 10% su questi tre prodotti!</p>
      </div>
    </div>
  `;
  document.body.appendChild(modal);

  const closeButton = modal.querySelector(".close-bundle-info");
  closeButton.addEventListener("click", () => {
    modal.remove();
  });

  // Chiudi il modal anche cliccando fuori
  modal.addEventListener("click", (e) => {
    if (e.target === modal) {
      modal.remove();
    }
  });
}

// Event listener per il bundle
document
    .getElementById("bundle-info")
    .addEventListener("click", showBundleInfo);
