<?php
$oldTravelers = $old['viatgers'] ?? [[
    'nom' => '', 'cognoms' => '', 'adult' => '1', 'habitacio_individual' => '0', 'categoria_superior' => '0',
    'document_identitat' => '', 'nacionalitat' => '', 'data_naixement' => '', 'preferencies' => ''
]];
?>
<section class="section-head">
    <div>
        <span class="eyebrow">Pre-reserva</span>
        <h1><?= e($promocio['paquet_nom']) ?></h1>
    </div>
    <p>El total es calcula en servidor segons adults, nens i suplements.</p>
</section>

<?php require VIEW_PATH . '/partials/errors.php'; ?>

<form class="booking-form" method="post" action="<?= url('reserva/crear', ['promocio_id' => $promocio['id_promocio']]) ?>">
    <?= csrf_field() ?>

    <section class="panel">
        <h2>Dades del client</h2>
        <div class="form-grid">
            <label>Nom
                <input name="client_nom" value="<?= e($old['client_nom'] ?? '') ?>" required>
            </label>
            <label>Cognoms
                <input name="client_cognoms" value="<?= e($old['client_cognoms'] ?? '') ?>" required>
            </label>
            <label>Telèfon
                <input name="client_telefon" value="<?= e($old['client_telefon'] ?? '') ?>" required>
            </label>
            <label>Correu electrònic
                <input type="email" name="client_correu" value="<?= e($old['client_correu'] ?? '') ?>" required>
            </label>
            <label>DNI o passaport
                <input name="client_document" value="<?= e($old['client_document'] ?? '') ?>" required>
            </label>
            <label>Nacionalitat
                <input name="client_nacionalitat" value="<?= e($old['client_nacionalitat'] ?? '') ?>" required>
            </label>
            <label class="wide">Adreça
                <input name="client_adreca" value="<?= e($old['client_adreca'] ?? '') ?>">
            </label>
            <label class="wide">Observacions generals
                <textarea name="observacions" rows="3"><?= e($old['observacions'] ?? '') ?></textarea>
            </label>
        </div>
    </section>

    <section class="panel">
        <div class="section-inline">
            <div>
                <h2>Viatgers</h2>
                <p>Afegeix les dades de cada persona que viatja.</p>
            </div>
            <button class="btn btn-secondary" type="button" data-add-traveler>Afegir viatger</button>
        </div>

        <div id="travelers" data-adult-price="<?= e($promocio['preu_base_adult']) ?>" data-child-price="<?= e($promocio['preu_base_nen']) ?>" data-single-price="<?= e($promocio['preu_extra_individual']) ?>" data-superior-price="<?= e($promocio['preu_extra_categoria_superior']) ?>">
            <?php foreach ($oldTravelers as $i => $traveler): ?>
                <div class="traveler-card" data-traveler>
                    <div class="traveler-head">
                        <strong>Viatger <span data-traveler-number><?= $i + 1 ?></span></strong>
                        <button class="link-button" type="button" data-remove-traveler>Eliminar</button>
                    </div>
                    <div class="form-grid">
                        <label>Nom
                            <input name="viatgers[<?= $i ?>][nom]" value="<?= e($traveler['nom'] ?? '') ?>" required>
                        </label>
                        <label>Cognoms
                            <input name="viatgers[<?= $i ?>][cognoms]" value="<?= e($traveler['cognoms'] ?? '') ?>" required>
                        </label>
                        <label>Tipus
                            <select name="viatgers[<?= $i ?>][adult]" data-price-trigger>
                                <option value="1" <?= (($traveler['adult'] ?? '1') == '1') ? 'selected' : '' ?>>Adult</option>
                                <option value="0" <?= (($traveler['adult'] ?? '1') == '0') ? 'selected' : '' ?>>Nen</option>
                            </select>
                        </label>
                        <label>DNI o passaport
                            <input name="viatgers[<?= $i ?>][document_identitat]" value="<?= e($traveler['document_identitat'] ?? '') ?>" required>
                        </label>
                        <label>Nacionalitat
                            <input name="viatgers[<?= $i ?>][nacionalitat]" value="<?= e($traveler['nacionalitat'] ?? '') ?>" required>
                        </label>
                        <label>Data naixement
                            <input type="date" name="viatgers[<?= $i ?>][data_naixement]" value="<?= e($traveler['data_naixement'] ?? '') ?>">
                        </label>
                        <label class="check-row">
                            <input type="checkbox" name="viatgers[<?= $i ?>][habitacio_individual]" value="1" data-price-trigger <?= !empty($traveler['habitacio_individual']) && $traveler['habitacio_individual'] != '0' ? 'checked' : '' ?>>
                            Habitació individual
                        </label>
                        <label class="check-row">
                            <input type="checkbox" name="viatgers[<?= $i ?>][categoria_superior]" value="1" data-price-trigger <?= !empty($traveler['categoria_superior']) && $traveler['categoria_superior'] != '0' ? 'checked' : '' ?>>
                            Categoria superior
                        </label>
                        <label class="wide">Preferències
                            <textarea name="viatgers[<?= $i ?>][preferencies]" rows="2"><?= e($traveler['preferencies'] ?? '') ?></textarea>
                        </label>
                    </div>
                    <div class="traveler-price">Preu estimat: <strong data-traveler-price>0,00 €</strong></div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <aside class="summary-bar">
        <div>
            <span>Total estimat</span>
            <strong data-booking-total>0,00 €</strong>
        </div>
        <button class="btn" type="submit">Enviar pre-reserva</button>
    </aside>
</form>
