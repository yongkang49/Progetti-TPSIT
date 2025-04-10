<nav class="navbar navbar-light bg-light sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">FORX</a>
        <div>
            <button id="bundle-info"
                    class="btn btn-outline-info me-2"
                    data-bs-toggle="modal"
                    data-bs-target="#bundleModal">
                <i class="fas fa-info-circle"></i> Info Bundle
            </button>
            <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#cart-modal">
                Carrello (<?= array_sum(array_column($_SESSION['cart'], 'quantity')) ?>)
            </button>
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="login.php" class="btn btn-outline-primary ms-2">Accedi</a>
            <?php else: ?>
                <a href="logout.php" class="btn btn-outline-secondary ms-2">Logout</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
<!-- Modal Info Bundle -->
<div class="modal fade" id="bundleModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">🎁 Offerta Bundle Speciale!</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <p class="mb-0">Acquista prodotti da 3 categorie diverse e ottieni uno sconto del 10% su un'unità di ciascun prodotto!</p>
                </div>

                <div class="card mb-3">
                    <div class="card-header bg-warning">
                        <strong>Come funziona</strong>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex align-items-start">
                                <i class="fas fa-tag me-2 text-success"></i>
                                Aggiungi al carrello almeno un prodotto da tre categorie diverse (vestiti, scarpe, gonne)
                            </li>
                            <li class="list-group-item d-flex align-items-start">
                                <i class="fas fa-percent me-2 text-success"></i>
                                Lo sconto verrà applicato automaticamente sui 3 prodotti più economici di categorie diverse
                            </li>
                            <li class="list-group-item d-flex align-items-start">
                                <i class="fas fa-info-circle me-2 text-success"></i>
                                Lo sconto si applica a una sola unità per prodotto
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="alert alert-success">
                    <strong>Esempio:</strong> Se acquisti un vestito, una gonna e un paio di scarpe, riceverai automaticamente lo sconto del 10% su questi tre prodotti!
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal del carrello -->
<div class="modal fade" id="cart-modal" tabindex="-1">
    <?php require_once 'carrello.php'; ?>
</div>