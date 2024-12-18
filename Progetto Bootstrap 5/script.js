// Inizializza i tooltip di Bootstrap per gli elementi con l'attributo 'data-bs-toggle="tooltip"'
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
  return new bootstrap.Tooltip(tooltipTriggerEl); // Crea un nuovo tooltip per ogni elemento
});