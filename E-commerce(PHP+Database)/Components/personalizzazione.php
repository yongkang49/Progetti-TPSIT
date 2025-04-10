<div class="mt-4">
    <div class="bg-white p-4 rounded shadow">
        <h3>Personalizzazione</h3>
        <div class="mb-3">
            <label for="customization-type" class="form-label">Tipo di personalizzazione</label>
            <select class="form-select" id="customization-type">
                <option value="">Nessuna personalizzazione</option>
                <option value="text" data-price="5">Testo (+5€)</option>
                <option value="small-image" data-price="8">Disegno piccolo (+8€)</option>
                <option value="large-image" data-price="12">Disegno grande (+12€)</option>
            </select>
        </div>
        <div id="text-customization" class="customization-options mb-3" style="display: none">
            <label for="custom-text" class="form-label">Il tuo testo</label>
            <input type="text" class="form-control mb-3" id="custom-text" maxlength="20"
                   placeholder="Massimo 20 caratteri"/>
            <label for="text-position" class="form-label">Posizione del testo</label>
            <select class="form-select mb-2" id="text-position">
                <option value="front-center">Centro frontale</option>
                <option value="front-top">Alto frontale</option>
                <option value="front-bottom">Basso frontale</option>
                <option value="back-center">Centro posteriore</option>
                <option value="back-top">Alto posteriore</option>
                <option value="sleeve-left">Manica sinistra</option>
                <option value="sleeve-right">Manica destra</option>
            </select>
            <div class="form-text mb-3">
                Inserisci il testo che vuoi aggiungere al prodotto e scegli dove posizionarlo
            </div>
        </div>
        <div id="image-customization" class="customization-options mb-3" style="display: none">
            <label class="form-label">Scegli il disegno</label>
            <div class="d-flex flex-wrap gap-2">
                <label class="design-option">
                    <input type="radio" name="design" value="cuore"/>
                    <span>❤️ Cuore</span>
                </label>
                <label class="design-option">
                    <input type="radio" name="design" value="stella"/>
                    <span>⭐ Stella</span>
                </label>
                <label class="design-option">
                    <input type="radio" name="design" value="fiore"/>
                    <span>🌸 Fiore</span>
                </label>
            </div>
        </div>
    </div>
</div>