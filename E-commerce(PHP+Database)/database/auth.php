<?php
session_start();
$config = require 'databaseconf.php';
$db = new PDO($config['dns'], $config['username'], $config['pass'], $config['options']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ottieni i dati del form
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Verifica se l'utente esiste
    $stmt = $db->prepare("SELECT id, nome, email, password FROM utente WHERE email = ?");
    $stmt->execute([$email]);
    $utente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($utente && password_verify($password, $utente['password'])) {
        // Password corretta, avvia la sessione
        $_SESSION['user_id'] = $utente['id'];
        $_SESSION['user_nome'] = $utente['nome'];
        $_SESSION['user_email'] = $utente['email'];

        // Redirect alla pagina principale o dashboard
        header("Location: ../index.php");
        exit;
    } else {
        // Se le credenziali sono errate, mostra un messaggio di errore
        $_SESSION['error'] = 'Credenziali non valide. Riprova.';
        header("Location: ../login.php");
        exit;
    }
} else {
    // Se non è un POST, fai un redirect alla pagina di login
    header("Location: ../login.php");
    exit;
}
