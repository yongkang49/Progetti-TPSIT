<?php
session_start(); // Avvia la sessione
// Distrugge tutte le variabili di sessione
session_unset();
// Distrugge la sessione
session_destroy();
// Reindirizza alla home page o alla pagina di login
header("Location: index.php");
exit();

