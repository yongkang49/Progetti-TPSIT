// Inizializza i tooltip di Bootstrap per gli elementi con l'attributo 'data-bs-toggle="tooltip"'
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
  return new bootstrap.Tooltip(tooltipTriggerEl); // Crea un nuovo tooltip per ogni elemento
});

// Carica la navbar quando il documento è completamente caricato
document.addEventListener("DOMContentLoaded", () => {
  // Effettua una richiesta per caricare il contenuto di 'navbar.html'
  fetch("navbar.html")
    .then(response => {
      if (!response.ok) {
        throw new Error("Errore nel caricamento del file navbar.html"); // Gestione degli errori
      }
      return response.text(); // Restituisce il contenuto del file come testo
    })
    .then(data => {
      // Inserisce il contenuto della navbar nel div con id "navbar-placeholder"
      document.getElementById("navbar-placeholder").innerHTML = data;
    })
    .catch(error => console.error("Errore:", error)); // Stampa l'errore in caso di fallimento
});

// Carica i dati dal file JSON
fetch('dati.json')
  .then(response => {
    if (!response.ok) {
      throw new Error('Errore nel caricamento del file JSON'); // Gestione degli errori
    }
    return response.json(); // Restituisce i dati in formato JSON
  })
  .then(data => {
    // Identifica la pagina corrente basandosi sul percorso dell'URL
    const page = window.location.pathname.split('/').pop().split('.')[0];
    console.log(page); // Stampa il nome della pagina nella console

    // Verifica il tipo di pagina e carica i dati relativi
    if (page === 'index') {
      // Caricamento dati per la Home Page
      const homeData = data.paginaHome;
      document.getElementById('titolo').innerHTML = homeData.titoloHome; // Inserisce il titolo
      document.getElementById('contenuto').innerHTML = homeData.introduzioneHome; // Inserisce l'introduzione
      const container = document.getElementById('blocks-container');

      // Aggiunge i blocchi dinamici alla pagina
      homeData.blocks.forEach(block => {
        const blockHtml = `
          <div class="container mt-5">
            <div class="row g-0 bg-light position-relative">
              <div class="col-md-6 mb-md-0 p-md-4" style="text-align: center;">
                <img src="${block.image}" class="img-fluid" alt="${block.alt}">
              </div>
              <div class="col-md-6 p-4 ps-md-0">
                <h5 class="mt-0">${block.titolo}</h5>
                <p>${block.descrizione}</p>
                <a href="${block.link}" type="button" class="btn btn-info">Approfondisci</a>
              </div>
            </div>
          </div>`;
        container.innerHTML += blockHtml; // Aggiunge il blocco HTML al container
      });
    } else if (page === 'osi') {
      // Caricamento dati per la pagina OSI
      const osiData = data.paginaOSI;
      document.getElementById('titolo').innerHTML = osiData.titoloOSI;
      document.getElementById('introduzione').innerHTML = osiData.introduzioneOSI;

      // Caricamento dei livelli OSI (accordion)
      const accordion = document.getElementById('accordionLivelli');
      Object.keys(osiData.livelli).forEach(key => {
        const livello = osiData.livelli[key];
        accordion.innerHTML += `
          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-${key}" aria-expanded="false" aria-controls="collapse-${key}">
                ${livello.titolo}
              </button>
            </h2>
            <div id="collapse-${key}" class="accordion-collapse collapse">
              <div class="accordion-body">
                ${livello.contenuto}
              </div>
            </div>
          </div>
        `;
      });
    } else if (page === 'socket') {
      // Caricamento dati per la pagina Socket
      const socketData = data.paginaSocket;
      document.getElementById('titolo').innerHTML = socketData.titolo;
      document.getElementById('introduzione').innerHTML = socketData.introduzione;
      document.getElementById('titolo2').innerHTML = socketData.titolo2;
      document.getElementById('titolo3').innerHTML = socketData.titolo3;

      // Tipologie di socket
      const tipologieList = document.querySelector('.container ul');
      tipologieList.innerHTML = socketData.tipologie
        .map(tipo => `<li><strong>${tipo.titolo}</strong>: ${tipo.descrizione}</li>`)
        .join('');

      // Processo di comunicazione tramite tab
      const tabList = document.querySelector('#list-tab');
      const tabContent = document.querySelector('#nav-tabContent');
      tabList.innerHTML = '';
      tabContent.innerHTML = '';
      socketData.processo.forEach((step, index) => {
        const id = `step-${index}`;
        tabList.innerHTML += `
        <a class="list-group-item list-group-item-action ${index === 0 ? 'active' : ''}" id="list-${id}" data-bs-toggle="list" href="#${id}" role="tab" aria-controls="${id}">
          ${step.passo}
        </a>`;
        tabContent.innerHTML += `
        <div class="tab-pane fade ${index === 0 ? 'show active' : ''}" id="${id}" role="tabpanel" aria-labelledby="list-${id}">
          ${step.contenuto}
        </div>`;
      });
    } else if (page === 'tcpUdp') {
      // Caricamento dati per la pagina TCP/UDP
      const tcpUdpData = data.paginaTcpUdp;

      document.getElementById('titolo').innerHTML = tcpUdpData.titolo;
      document.getElementById('introduzione').innerHTML = tcpUdpData.introduzione;

      // Caratteristiche TCP
      const tcpList = document.querySelector('.col-md-6 ul');
      tcpList.innerHTML = tcpUdpData.tcp.caratteristiche
        .map(caratteristica => `
          <li class="list-group-item">
            <strong>${caratteristica.nome}</strong>: ${caratteristica.descrizione}
          </li>
        `)
        .join('');

      // Caratteristiche UDP
      const udpList = document.querySelector('.col-md-5 ul');
      udpList.innerHTML = tcpUdpData.udp.caratteristiche
        .map(caratteristica => `
          <li class="list-group-item">
            <strong>${caratteristica.nome}</strong>: ${caratteristica.descrizione}
          </li>
        `)
        .join('');
    } else if (page === 'glossario') {
      // Caricamento dati per la pagina Glossario
      const glossarioData = data.paginaGlossario;

      if (!glossarioData) {
        console.error('Dati per il glossario non trovati.'); // Gestione degli errori
        return;
      }

      document.querySelector('h1').textContent = glossarioData.titolo;
      const glossarioContainer = document.querySelector('.list-group');
      // Carica categorie e termini
      glossarioData.categorie.forEach(categoria => {
        categoria.termini.forEach(termine => {
          const a = document.createElement('a');
          a.className = 'list-group-item list-group-item-action';
          a.href = categoria.link; // Link unico per la categoria
          a.innerHTML = `<strong>${termine.termine}</strong>: ${termine.definizione}`;
          glossarioContainer.appendChild(a); // Aggiunge i termini al container
        });
      });
    }

    // Aggiorna il footer (comune a tutte le pagine)
    document.getElementById('footer').innerHTML = data.footer;
  })
  .catch(error => {
    console.error('Errore:', error); // Stampa l'errore in caso di fallimento
  });