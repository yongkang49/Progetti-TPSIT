<?php
if(session_status() == PHP_SESSION_NONE)
{
    session_start();
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Login - FORX</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-card {
            width: 100%;
            max-width: 400px;
            padding: 2rem;
            border-radius: 1rem;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>

<div class="login-card">
    <h3 class="text-center mb-4">Accedi a FORX</h3>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger text-center">
            <?= htmlspecialchars($_SESSION['error']) ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form method="POST" action="database/auth.php">
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input
                    type="email"
                    class="form-control"
                    id="email"
                    name="email"
                    required
                    placeholder="nome@esempio.com"
            />
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input
                    type="password"
                    class="form-control"
                    id="password"
                    name="password"
                    required
                    placeholder="••••••••"
            />
        </div>
        <div class="d-grid">
            <button type="submit" class="btn btn-primary">Accedi</button>
        </div>
    </form>
    <!-- Link per tornare alla home page -->
    <p class="text-center mt-3 mb-0">
        <a href="index.php" class="text-decoration-none">Torna alla home page</a>
    </p>

    <p class="text-center mt-3 mb-0">
        Non hai un account? <a href="registra.php">Registrati</a>
    </p>
</div>

</body>
</html>
