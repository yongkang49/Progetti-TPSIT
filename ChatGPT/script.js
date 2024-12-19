document.addEventListener("DOMContentLoaded", () => {
  const currentPage = window.location.pathname.split("/").pop();

  if (currentPage.startsWith("question")) {
    const questionNumber = currentPage.match(/\d+/)[0];
    document.body.innerHTML = `
            <div class="container">
                <h1>Question ${questionNumber}</h1>
                <p>Provide your answer below:</p>
                <textarea id="answer" rows="5" cols="40">${
                  localStorage.getItem(`answer${questionNumber}`) || ""
                }</textarea>
                <br><br>
                <button id="saveAnswer">Save Answer</button>
                <a href="index.html" class="button">Back to Home</a>
            </div>
        `;

    document.getElementById("saveAnswer").addEventListener("click", () => {
      const answer = document.getElementById("answer").value;
      localStorage.setItem(`answer${questionNumber}`, answer);
      alert("Answer saved!");
    });
  }
});

// Timer configuration
let countdownTime = localStorage.getItem("remainingTime") || 3600; // In seconds
const display = document.getElementById("timer-display");
let timerInterval; // Variabile per l'intervallo

// Update timer every second
function updateTimer() {
  if (countdownTime > 0) {
    countdownTime--;
    const minutes = Math.floor(countdownTime / 60);
    const seconds = countdownTime % 60;
    display.textContent = `${minutes.toString().padStart(2, "0")}:${seconds
      .toString()
      .padStart(2, "0")}`;
    localStorage.setItem("remainingTime", countdownTime);
  } else {
    display.textContent = "Tempo scaduto!";
    clearInterval(timerInterval); // Stop the timer
    showModal("Tempo scaduto!"); // Show the modal when time is up
  }
}

// Start timer
function startTimer() {
  timerInterval = setInterval(updateTimer, 1000);
  updateTimer();
}

// Stop timer
function stopTimer() {
  clearInterval(timerInterval);
  // Show modal
  document.getElementById("modal").style.display = "flex";
}

// Reset timer
function resetTimer() {
  countdownTime = 3600;
  localStorage.removeItem("remainingTime");
  saveLocalStorageToFile();
  startTimer();
}

// Add event listener to Stop Timer button
const stopTimerButton = document.getElementById("stop-timer");
if (stopTimerButton) {
  stopTimerButton.addEventListener("click", stopTimer);
}

// Modal buttons
const retryButton = document.getElementById("retry-button");

if (retryButton) {
  retryButton.addEventListener("click", () => {
    document.getElementById("modal").style.display = "none"; // Nasconde il modal
    resetTimer(); // Resetta il timer
    localStorage.clear();
  });
}

function saveLocalStorageToFile() {
  // Ottieni tutti i dati dal localStorage
  const localStorageData = {};
  for (let key in localStorage) {
    if (localStorage.hasOwnProperty(key)) {
      localStorageData[key] = localStorage.getItem(key);
    }
  }

  // Converti i dati in formato stringa (JSON)
  const dataString = JSON.stringify(localStorageData, null, 2); // Formattato per leggibilità

  // Crea un blob con i dati
  const blob = new Blob([dataString], { type: "text/plain" });

  // Crea un link per il download
  const link = document.createElement("a");
  link.href = URL.createObjectURL(blob);
  link.download = "localStorageData.txt";

  // Avvia il download
  link.click();

  // Libera la memoria utilizzata dal blob
  URL.revokeObjectURL(link.href);
}
// Start the timer on page load
startTimer();
