<section class="detail-hero">
    <div class="detail-image" style="background-image: linear-gradient(135deg, rgba(15,23,42,.25), rgba(15,23,42,.75)), url('<?= e($promocio['galeria_url']) ?>');"></div>
    <div class="detail-content">
        <span class="eyebrow"><?= e($promocio['continent']) ?> · <?= e($promocio['pais_ruta']) ?></span>
        <h1><?= e($promocio['paquet_nom']) ?></h1>
        <p><?= e($promocio['descripcio']) ?></p>
        <div class="facts">
            <div><span>Origen</span><strong><?= e($promocio['punt_origen']) ?></strong></div>
            <div><span>Durada</span><strong><?= e($promocio['numero_dies']) ?> dies</strong></div>
            <div><span>Viatge</span><strong><?= e($promocio['data_inici_viatge']) ?> / <?= e($promocio['data_fi_viatge']) ?></strong></div>
            <div><span>Proveïdor</span><strong><?= e($promocio['proveidor_nom']) ?></strong></div>
        </div>
    </div>
</section>

<div class="two-columns">
    <section class="panel">
        <h2>Preus de la promoció</h2>
        <div class="price-list">
            <div><span>Preu base adult</span><strong><?= money($promocio['preu_base_adult']) ?></strong></div>
            <div><span>Preu base nen</span><strong><?= money($promocio['preu_base_nen']) ?></strong></div>
            <div><span>Suplement habitació individual</span><strong><?= money($promocio['preu_extra_individual']) ?></strong></div>
            <div><span>Suplement categoria superior</span><strong><?= money($promocio['preu_extra_categoria_superior']) ?></strong></div>
        </div>
    </section>

    <aside class="panel sticky-card">
        <h2>Pre-reserva</h2>
        <p>La reserva quedarà inicialment pendent fins que el proveïdor l’accepti.</p>
        <a class="btn full" href="<?= url('reserva/crear', ['promocio_id' => $promocio['id_promocio']]) ?>">Fer pre-reserva</a>
        <?php if (!empty($promocio['pdf_circuit'])): ?>
            <a class="btn btn-secondary full" href="<?= e($promocio['pdf_circuit']) ?>" target="_blank">Veure circuit PDF</a>
        <?php endif; ?>
    </aside>
</div>
