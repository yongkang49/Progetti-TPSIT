<?php
$config = require 'database/databaseconf.php';
$db = new PDO($config['dns'], $config['username'], $config['pass'], $config['options']);

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $passwordConfirm = $_POST['password_confirm'];

    // Controlla che la password e la conferma siano uguali
    if ($password !== $passwordConfirm) {
        $error = "Le password non coincidono.";
    } elseif ($nome && $email && $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Sicuro!
        try {
            // Verifica se l'email è già registrata
            $stmt = $db->prepare("SELECT COUNT(*) FROM utente WHERE email = ?");
            $stmt->execute([$email]);
            $emailExist = $stmt->fetchColumn();

            if ($emailExist) {
                $error = "Email già registrata.";
            } else {
                // Inserisci i dati nel database
                $stmt = $db->prepare("INSERT INTO utente (nome, email, password) VALUES (?, ?, ?)");
                $stmt->execute([$nome, $email, $hashedPassword]);
                $success = true;
            }
        } catch (PDOException $e) {
            $error = "Errore nella registrazione: " . $e->getMessage();
        }
    } else {
        $error = "Compila tutti i campi.";
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Registrazione</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="height: 100vh;">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="mb-4 text-center">Registrati</h4>

                    <?php if ($success): ?>
                        <div class="alert alert-success text-center">Registrazione avvenuta con successo!</div>
                        <div class="text-center"><a href="login.php" class="btn btn-primary">Vai al login</a></div>
                    <?php else: ?>
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Nome</label>
                                <input type="text" name="nome" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Conferma Password</label>
                                <input type="password" name="password_confirm" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-success w-100">Registrati</button>
                        </form>
                        <!-- Link per tornare alla home page -->
                        <p class="text-center mt-3 mb-0">
                            <a href="index.php" class="text-decoration-none">Torna alla home page</a>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
            <p class="text-center mt-3 small text-muted">© FORX 2025</p>
        </div>
    </div>
</div>
</body>
</html>
