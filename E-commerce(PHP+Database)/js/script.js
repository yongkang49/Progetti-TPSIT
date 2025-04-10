let currentSlide = 0;
const slides = document.querySelectorAll(".slide");

function showSlide(index) {
    slides.forEach((slide) => slide.classList.remove("active"));
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