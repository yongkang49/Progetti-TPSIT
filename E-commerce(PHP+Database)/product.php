<?php
if(session_status() == PHP_SESSION_NONE)
{
    session_start();
}
$config = require 'database/databaseconf.php';
try {
    $db = new PDO($config['dns'], $config['username'], $config['pass'], $config['options']);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connessione al database fallita: " . $e->getMessage();
    exit;
}

// Recupera l'ID del prodotto dalla query string
$productId = $_GET['id'] ?? null;
if (!$productId) {
    echo "Nessun prodotto selezionato.";
    exit;
}

$query = "SELECT * FROM forx_web.prodotti WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->execute(['id' => $productId]);
$prodotto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$prodotto) {
    echo "Prodotto non trovato.";
    exit;
}

// Definisci un array di mapping per tradurre l'ID della taglia
$tagliaMapping = [
    1 => 'xs',
    2 => 's',
    3 => 'm',
    4 => 'l',
    5 => 'xl',
    6 => '35',
    7 => '36',
    8 => '37',
    9 => '38',
    10 => '39',
    11 => '40',
    12 => '41',
    13 => '42',
    14 => '43'
];

// Recupera tutte le varianti con lo stesso nome per ottenere tutte le taglie e i colori
$queryVariants = "SELECT * FROM forx_web.prodotti WHERE nome = :nome ORDER BY taglia_id";
$stmtVariants = $db->prepare($queryVariants);
$stmtVariants->execute(['nome' => $prodotto['nome']]);
$varianti = $stmtVariants->fetchAll(PDO::FETCH_ASSOC);

// Accumula in array univoci tutte le taglie e i colori
$taglie = [];
$colori = [];
foreach ($varianti as $var) {
    if (!empty($var['taglia_id'])) {
        $taglia = isset($tagliaMapping[$var['taglia_id']]) ? $tagliaMapping[$var['taglia_id']] : $var['taglia_id'];
        if (!in_array($taglia, $taglie)) {
            $taglie[] = $taglia;
        }
    }
    if (!empty($var['colore'])) {
        if (!in_array($var['colore'], $colori)) {
            $colori[] = $var['colore'];
        }
    }
}

// Imposta il prezzo base
$basePrice = (float)$prodotto['prezzo'];

// Decidi se mostrare la personalizzazione in base alla categoria
$showCustomization = ($prodotto['categoria_id'] != 1);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Dettagli Prodotto - <?php echo htmlspecialchars($prodotto['nome']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css"/>
    <link rel="stylesheet" href="css/product.css"/>
</head>
<body>

<?php require "Components/header.php" ?>

<div class="container my-4">
    <div class="row">
        <!-- Dettagli del prodotto -->
        <div class="col-md-6">
            <div class="bg-white p-4 rounded shadow" style="max-width: 600px; margin-left: 2%">
                <img id="product-image"
                     src="<?php echo htmlspecialchars($prodotto['immagine']); ?>"
                     alt="<?php echo htmlspecialchars($prodotto['nome']); ?>"
                     class="product-img img-fluid"
                     style="object-fit: contain; max-height: 400px;"/>
                <h1 id="product-name" class="mt-3"><?php echo htmlspecialchars($prodotto['nome']); ?></h1>
                <p id="product-price" class="fw-bold text-success fs-4">
                    €<?php echo number_format($basePrice, 2, ',', '.'); ?></p>
                <p id="product-description" class="text-muted"></p>
                <button id="add-to-cart" class="btn btn-primary w-100">Aggiungi al Carrello</button>
            </div>
        </div>
        <!-- Dati aggiuntivi sul prodotto -->
        <div class="col-md-6">
            <div class="bg-white p-4 rounded shadow" style="max-width: 600px">
                <h3>Dettagli Extra</h3>
                <ul class="list-group">
                    <li class="list-group-item">
                        <strong>Materiale:</strong> <span
                                id="product-materiale"><?php echo htmlspecialchars($prodotto['materiale']); ?></span>
                    </li>
                    <li class="list-group-item">
                        <strong>Taglia:</strong>
                        <span id="product-taglio">
                            <?php if (!empty($taglie)): ?>
                                <?php foreach ($taglie as $taglia): ?>
                                    <button type="button"
                                            class="btn btn-outline-primary size-button"><?php echo htmlspecialchars($taglia); ?></button>
                                <?php endforeach; ?>
                            <?php else: ?>
                                Taglia unica
                            <?php endif; ?>
                        </span>
                    </li>
                    <li class="list-group-item">
                        <strong>Colore:</strong>
                        <div id="product-colore" class="d-flex flex-wrap">
                            <?php if (!empty($colori)): ?>
                                <?php foreach ($colori as $colore): ?>
                                    <?php
                                    // Trova l'immagine per la variante di quel colore
                                    $immagineColore = '';
                                    foreach ($varianti as $var) {
                                        if (!empty($var['colore']) && strtolower($var['colore']) === strtolower($colore)) {
                                            $immagineColore = $var['immagine'];
                                            break;
                                        }
                                    }
                                    ?>
                                    <button type="button" class="color-button"
                                            style="background-color: <?php echo htmlspecialchars($colore); ?>;"
                                            title="<?php echo htmlspecialchars($colore); ?>"
                                            data-image="<?php echo htmlspecialchars($immagineColore); ?>"></button>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span class="text-muted">Colore unico</span>
                            <?php endif; ?>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <strong>Disponibilità:</strong>
                        <span id="product-quantità"><?php echo htmlspecialchars($prodotto['quantita']); ?></span>
                    </li>
                </ul>
                <!-- Sezione Personalizzazione -->
                <?php require "Components/personalizzazione.php" ?>
            </div>
        </div>
    </div>
</div>

<script src="js/carrello.js"></script>
<script>
    let basePrice = <?= $basePrice ?>;
    let customizationPrice = 0;
    // Aggiungi queste righe
    const productId = <?= json_encode($prodotto['id']) ?>;
    const productName = <?= json_encode($prodotto['nome']) ?>;
    const showCustomization = <?= json_encode($showCustomization) ?>;

    // Funzioni helper per ottenere le selezioni
    function getSelectedSize() {
        const activeSize = document.querySelector('.size-button.active');
        return activeSize ? activeSize.textContent.trim() : null;
    }

    function getSelectedColor() {
        const activeColor = document.querySelector('.color-button.active');
        return activeColor ? activeColor.getAttribute('title') : null;
    }

    function getCustomization() {
        const type = document.getElementById('customization-type').value;
        const customization = {type};

        if (type === 'text') {
            customization.text = document.getElementById('custom-text').value.trim();
        } else if (type === 'image') {
            const selectedDesign = document.querySelector('.design-option.selected');
            if (selectedDesign) {
                customization.design = selectedDesign.dataset.designUrl; // Adatta al tuo attributo dati effettivo
            }
        }

        return customization;
    }

    // Gestione click sul pulsante "Aggiungi al carrello"
    document.getElementById('add-to-cart').addEventListener('click', function () {
        // Validazione selezioni
        const errorMessages = [];

        if (document.querySelectorAll('.size-button').length > 0 && !getSelectedSize()) {
            errorMessages.push('Seleziona una taglia');
        }

        if (document.querySelectorAll('.color-button').length > 0 && !getSelectedColor()) {
            errorMessages.push('Seleziona un colore');
        }

        if (showCustomization) {
            const customization = getCustomization();
            if (customization.type !== 'none' && !customization.design && !customization.text) {
                errorMessages.push('Completa la personalizzazione');
            }
        }

        if (errorMessages.length > 0) {
            showMessage(errorMessages.join(' | '), 3000, '#dc3545');
            return;
        }

        // Crea oggetto prodotto
        const product = {
            id: productId,
            name: productName,
            image: document.getElementById('product-image').src,
            selectedSize: getSelectedSize(),
            selectedColor: getSelectedColor(),
            customization: getCustomization(),
            price: basePrice + customizationPrice
        };
        showMessage('Prodotto aggiunto con successo', 3000, 'green');
        addToCart(product);
    });

    // Funzione per mostrare un messaggio al centro dello schermo per un certo periodo (in millisecondi)
    function showMessage(message, duration, colore) {
        const messageDiv = document.createElement("div");
        messageDiv.innerText = message;
        messageDiv.style.position = "fixed";
        messageDiv.style.top = "50%";
        messageDiv.style.left = "50%";
        messageDiv.style.transform = "translate(-50%, -50%)";
        messageDiv.style.backgroundColor = colore;
        messageDiv.style.color = "#fff";
        messageDiv.style.padding = "20px 30px";
        messageDiv.style.borderRadius = "5px";
        messageDiv.style.zIndex = "9999";
        messageDiv.style.textAlign = "center";
        document.body.appendChild(messageDiv);

        setTimeout(() => {
            messageDiv.style.transition = "opacity 0.5s ease-out";
            messageDiv.style.opacity = "0";
            setTimeout(() => {
                document.body.removeChild(messageDiv);
            }, 500);
        }, duration);
    }

    function updateTotalPrice() {
        const total = basePrice + customizationPrice;
        document.getElementById("product-price").textContent = `€${total.toFixed(2)}`;
    }

    function initializeCustomization() {
        const customizationType = document.getElementById("customization-type");
        const textCustomization = document.getElementById("text-customization");
        const imageCustomization = document.getElementById("image-customization");
        const customText = document.getElementById("custom-text");
        const maxLength = 20;

        // Aggiungi il contatore di caratteri
        const charCounter = document.createElement("small");
        charCounter.className = "text-muted ms-2";
        charCounter.textContent = `0/${maxLength}`;
        customText.parentNode.insertBefore(charCounter, customText.nextSibling);

        // Gestione dei design radio buttons
        document.querySelectorAll(".design-option").forEach((option) => {
            option.addEventListener("click", () => {
                document.querySelectorAll(".design-option").forEach((opt) => opt.classList.remove("selected"));
                option.classList.add("selected");
            });
        });

        // Aggiorna il contatore mentre si digita il testo
        customText.addEventListener("input", (e) => {
            const length = e.target.value.length;
            charCounter.textContent = `${length}/${maxLength}`;

            if (length >= maxLength) {
                charCounter.className = "text-danger ms-2";
            } else if (length >= maxLength - 5) {
                charCounter.className = "text-warning ms-2";
            } else {
                charCounter.className = "text-muted ms-2";
            }
        });

        customizationType.addEventListener("change", (e) => {
            const selected = e.target.value;
            const price = parseFloat(e.target.selectedOptions[0].dataset.price || 0);
            customizationPrice = price;
            updateTotalPrice();

            textCustomization.style.display = selected === "text" ? "block" : "none";
            imageCustomization.style.display = selected.includes("image") ? "block" : "none";

            if (selected !== "text") {
                customText.value = "";
                charCounter.textContent = `0/${maxLength}`;
                charCounter.className = "text-muted ms-2";
            }
        });
    }

    document.addEventListener("DOMContentLoaded", () => {
        initializeCustomization();

        // Gestione della selezione della taglia
        document.querySelectorAll(".size-button").forEach(btn => {
            btn.addEventListener("click", () => {
                document.querySelectorAll(".size-button").forEach(b => b.classList.remove("active"));
                btn.classList.add("active");
            });
        });

        // Gestione della selezione del colore: aggiornamento immagine
        document.querySelectorAll('.color-button').forEach(button => {
            button.addEventListener('click', () => {
                // Rimuovi selezione precedente
                document.querySelectorAll('.color-button').forEach(b => {
                    b.classList.remove('active');
                    b.style.border = 'none';
                });

                // Aggiorna immagine e stato
                button.classList.add('active');
                button.style.border = '2px solid #007bff';
                const newImage = button.getAttribute('data-image');
                if (newImage) document.getElementById('product-image').src = newImage;
            });
        });
    });

</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
