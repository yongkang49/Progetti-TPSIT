// Recupera l'ID del prodotto dalla query string
const urlParams = new URLSearchParams(window.location.search);
const productId = urlParams.get("id");

let basePrice = 0;
let customizationPrice = 0;

function updateTotalPrice() {
  const total = basePrice + customizationPrice;
  document.getElementById("product-price").textContent = `€${total.toFixed(2)}`;
}

function initializeCustomization() {
  const customizationType = document.getElementById("customization-type");
  const textCustomization = document.getElementById("text-customization");
  const imageCustomization = document.getElementById("image-customization");
  const customText = document.getElementById("custom-text");
  const maxLength = 20;

  // Aggiungi il contatore di caratteri
  const charCounter = document.createElement("small");
  charCounter.className = "text-muted ms-2";
  charCounter.textContent = `0/${maxLength}`;
  customText.parentNode.insertBefore(charCounter, customText.nextSibling);

  // Gestione dei design radio buttons
  document.querySelectorAll(".design-option").forEach((option) => {
    option.addEventListener("click", () => {
      document
        .querySelectorAll(".design-option")
        .forEach((opt) => opt.classList.remove("selected"));
      option.classList.add("selected");
    });
  });

  // Aggiorna l'anteprima quando si scrive il testo
  customText.addEventListener("input", (e) => {
    const length = e.target.value.length;
    charCounter.textContent = `${length}/${maxLength}`;

    if (length >= maxLength) {
      charCounter.className = "text-danger ms-2";
    } else if (length >= maxLength - 5) {
      charCounter.className = "text-warning ms-2";
    } else {
      charCounter.className = "text-muted ms-2";
    }
  });

  customizationType.addEventListener("change", (e) => {
    const selected = e.target.value;
    const price = parseFloat(e.target.selectedOptions[0].dataset.price || 0);

    customizationPrice = price;
    updateTotalPrice();

    textCustomization.style.display = selected === "text" ? "block" : "none";
    imageCustomization.style.display = selected.includes("image")
      ? "block"
      : "none";

    if (selected !== "text") {
      customText.value = "";
      charCounter.textContent = `0/${maxLength}`;
      charCounter.className = "text-muted ms-2";
    }
  });
}

if (productId) {
  fetch("prodotti.json")
    .then((response) => response.json())
    .then((products) => {
      const product = products.find((p) => p.id == productId);
      if (product) {
        // Aggiorna l'immagine e gli altri dettagli
        document.getElementById("product-image").src = product.image;
        document.getElementById("product-name").innerText = product.nome;
        basePrice = product.prezzo;
        updateTotalPrice();
        // Se esiste una descrizione, puoi gestirla (qui non presente nel JSON)
        // document.getElementById('product-description').innerText = product.descrizione;
        document.getElementById("product-materiale").textContent =
          product.materiale;

        // Variabili per tenere traccia delle selezioni dell'utente
        let selectedSize = null;
        let selectedColor = product.colori ? product.colori[0].name : null;

        // Modifica per creare bottoni per le taglie
        const taglioContainer = document.getElementById("product-taglio");
        taglioContainer.innerHTML = "";

        const taglie = product.taglio.split(", ");
        taglie.forEach((taglia) => {
          const btn = document.createElement("button");
          btn.classList.add("size-button");
          btn.textContent = taglia;
          btn.addEventListener("click", () => {
            document
              .querySelectorAll(".size-button")
              .forEach((b) => b.classList.remove("active"));
            btn.classList.add("active");
            selectedSize = taglia;
          });
          taglioContainer.appendChild(btn);
        });

        // Per il campo "colore", crea dei bottoni per ogni colore disponibile
        const colorContainer = document.getElementById("product-colore");
        colorContainer.innerHTML = "";

        if (product.colori && Array.isArray(product.colori)) {
          product.colori.forEach((color) => {
            const btn = document.createElement("button");
            btn.classList.add("color-button");
            btn.style.backgroundColor = color.name;
            btn.title = color.name;
            btn.addEventListener("click", () => {
              document.getElementById("product-image").src = color.image;
              selectedColor = color.name;
            });
            colorContainer.appendChild(btn);
          });
        } else {
          const colorMessage = document.createElement("span");
          colorMessage.classList.add("text-muted", "ms-2");
          colorMessage.textContent = "Colore unico disponibile";
          colorContainer.appendChild(colorMessage);
        }

        // Aggiorna la disponibilità (campo "quantità" nel JSON)
        document.getElementById("product-quantità").textContent =
          product.quantità || "Non disponibile";

        // Controlla se il prodotto è una scarpa
        const customizationSection = document.querySelector(".mt-4");
        if (product.categoria === "scarpe") {
          // Nascondi la sezione di personalizzazione per le scarpe
          customizationSection.style.display = "none";
        } else {
          // Mostra la sezione di personalizzazione per altri prodotti
          customizationSection.style.display = "block";
        }

        // Imposta il listener per il pulsante "Aggiungi al Carrello"
        document.getElementById("add-to-cart").addEventListener("click", () => {
          const customization =
            product.categoria !== "scarpe"
              ? {
                  type: document.getElementById("customization-type").value,
                  text: document.getElementById("custom-text").value,
                  textPosition: document.getElementById("text-position").value,
                  design: document.querySelector('input[name="design"]:checked')
                    ?.value,
                }
              : null;

          // Validazione della personalizzazione solo per prodotti non scarpe
          if (product.categoria !== "scarpe") {
            if (customization.type === "text" && !customization.text.trim()) {
              alert("Per favore inserisci il testo per la personalizzazione");
              return;
            }

            if (customization.type.includes("image") && !customization.design) {
              alert("Per favore seleziona un disegno");
              return;
            }
          }

          // Verifica la selezione della taglia usando la classe active
          const selectedSizeButton = document.querySelector(
            ".size-button.active"
          );
          if (!selectedSizeButton) {
            alert("Per favore seleziona una taglia");
            return;
          }

          const productToAdd = {
            ...product,
            prezzo:
              basePrice +
              (product.categoria !== "scarpe" ? customizationPrice : 0),
            selectedSize: selectedSizeButton.textContent,
            selectedColor: selectedColor,
            customization: customization,
            quantita: 1,
          };

          addToCart(productToAdd);
        });
      } else {
        alert("Prodotto non trovato");
      }
    })
    .catch((error) =>
      console.error("Errore nel caricamento del prodotto:", error)
    );
} else {
  alert("Nessun prodotto selezionato");
}

// Aggiungi alla fine del file
document.addEventListener("DOMContentLoaded", () => {
  initializeCustomization();
});
