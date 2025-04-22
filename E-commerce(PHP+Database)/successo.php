<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="5;url=index.php">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ordine Completato - Forx</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .success-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="success-container">
    <h1 class="text-success display-4">✅ Ordine completato con successo!</h1>
    <p class="lead">Grazie per il tuo acquisto su <strong>Forx</strong>.</p>
    <p>Verrai reindirizzato alla <a href="index.php">homepage</a> tra 5 secondi...</p>
</div>

</body>
</html>
