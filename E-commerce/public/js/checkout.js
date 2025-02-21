function loadCheckoutData() {
    // Recupera i dati del carrello
    let cart = [];
    const storedCart = localStorage.getItem('cart');
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
    const storedCoupon = localStorage.getItem('coupon');
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
    const checkoutItemsList = document.getElementById('checkout-items');
    checkoutItemsList.innerHTML = ''; // Reset lista
    
    cart.forEach(item => {
      const li = document.createElement('li');
      li.className = 'list-group-item d-flex justify-content-between align-items-center';
      li.innerHTML = `
        <div class="d-flex align-items-center">
          <img src="${item.image}" alt="${item.nome}" class="img-thumbnail mr-3" style="width:50px; height:50px;">
          <div>
            <h5 class="mb-1">${item.nome}</h5>
            <small>€${item.prezzo.toFixed(2)} x ${item.quantita}</small>
          </div>
        </div>
        <span class="text-muted">€${(item.prezzo * item.quantita).toFixed(2)}</span>
      `;
      checkoutItemsList.appendChild(li);
      
      totalPrice += item.prezzo * item.quantita;
    });
    
    // Applica il coupon se presente
    if (couponData.applied && couponData.code) {
      let discount = 0;
      if (couponData.code === 'SCONTO10') {
        discount = totalPrice * 0.10;
      } else if (couponData.code === 'SCONTO20') {
        discount = totalPrice * 0.20;
      }
      totalPrice -= discount;
      document.getElementById('coupon-info').textContent = `Coupon applicato: ${couponData.code} - Sconto: €${discount.toFixed(2)}`;
    } else {
      document.getElementById('coupon-info').textContent = 'Nessun coupon applicato.';
    }
    
    // Mostra il totale
    document.getElementById('total-price').textContent = totalPrice.toFixed(2);
  }
  
  document.getElementById('confirm-checkout').addEventListener('click', () => {
    // Azione di conferma (personalizza come desideri)
    alert('Acquisto confermato!');
    
    // Opzionale: svuota il carrello e il coupon dopo il checkout
    localStorage.removeItem('cart');
    localStorage.removeItem('coupon');
    
    // Ricarica la pagina o reindirizza altrove
    window.location.reload();
  });
  
  // Carica i dati al caricamento della pagina
  window.onload = loadCheckoutData;
  