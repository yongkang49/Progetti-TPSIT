function loadCheckoutData() {
  // Recupera i dati del carrello
  let cart = [];
  const storedCart = localStorage.getItem("cart");
  if (storedCart) {
    try {
      cart = JSON.parse(storedCart);
      if (!Array.isArray(cart)) cart = [];
    } catch (error) {
      console.error("Errore nel parsing del carrello:", error);
      cart = [];
    }
  }

  // Recupera i dati del coupon
  let couponData = { applied: false, code: null };
  const storedCoupon = localStorage.getItem("coupon");
  if (storedCoupon) {
    try {
      couponData = JSON.parse(storedCoupon);
    } catch (error) {
      console.error("Errore nel parsing del coupon:", error);
      couponData = { applied: false, code: null };
    }
  }

  // Calcola il totale e mostra gli articoli
  let totalPrice = 0;
  const checkoutItemsList = document.getElementById("checkout-items");
  checkoutItemsList.innerHTML = ""; // Reset lista

  cart.forEach((item) => {
    const li = document.createElement("li");
    li.className =
      "list-group-item d-flex justify-content-between align-items-center";
    li.innerHTML = `
        <div class="d-flex align-items-center">
          <img src="${item.image}" alt="${
      item.nome
    }" class="img-thumbnail me-3" style="width:50px; height:50px; object-fit: contain;">
          <div>
            <h6 class="mb-0">${item.nome}</h6>
            <small class="text-muted">€${item.prezzo.toFixed(2)} x ${
      item.quantita
    }</small>
            ${
              item.selectedSize
                ? `<small class="d-block">Taglia: ${item.selectedSize}</small>`
                : ""
            }
            ${
              item.selectedColor
                ? `<small class="d-block">Colore: ${item.selectedColor}</small>`
                : ""
            }
          </div>
        </div>
        <span class="text-muted">€${(item.prezzo * item.quantita).toFixed(
          2
        )}</span>
      `;
    checkoutItemsList.appendChild(li);

    totalPrice += item.prezzo * item.quantita;
  });

  // Verifica e applica lo sconto bundle
  const bundleProducts = findBundleProducts(cart);
  if (bundleProducts) {
    const bundlePrice = bundleProducts.reduce(
      (sum, item) => sum + item.prezzo,
      0
    );
    const bundleDiscount = bundlePrice * 0.1;
    totalPrice -= bundleDiscount;

    // Mostra info bundle
    const bundleInfo = document.getElementById("bundle-info-text");
    bundleInfo.innerHTML = `
        <strong>Sconto Bundle (-10%)</strong><br>
        Applicato a un'unità dei seguenti prodotti:<br>
        ${bundleProducts
          .map((p) => `- ${p.nome} (${p.categoria})`)
          .join("<br>")}<br>
        Risparmio: €${bundleDiscount.toFixed(2)}
      `;
  }

  // Applica il coupon se presente
  if (couponData.applied && couponData.code) {
    let discount = 0;
    if (couponData.code === "SCONTO10") {
      discount = totalPrice * 0.1;
    } else if (couponData.code === "SCONTO20") {
      discount = totalPrice * 0.2;
    }
    totalPrice -= discount;
    document.getElementById(
      "coupon-info"
    ).innerHTML = `<strong>Coupon applicato:</strong> ${
      couponData.code
    } - Sconto: €${discount.toFixed(2)}`;
  }

  // Mostra il totale
  document.getElementById("total-price").textContent = totalPrice.toFixed(2);
}

document.getElementById("confirm-checkout").addEventListener("click", () => {
  alert("Acquisto confermato! Grazie per il tuo ordine.");
  localStorage.removeItem("cart");
  localStorage.removeItem("coupon");
  window.location.href = "index.php";
});

// Carica i dati al caricamento della pagina
window.onload = loadCheckoutData;
