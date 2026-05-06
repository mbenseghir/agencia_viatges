<section class="section-head">
    <div>
        <span class="eyebrow">Catàleg</span>
        <h1>Promocions disponibles</h1>
    </div>
</section>

<div class="grid cards-grid">
    <?php foreach ($promocions as $promocio): ?>
        <article class="card travel-card compact">
            <div class="card-body">
                <div class="badges">
                    <span class="badge"><?= e($promocio['pais_ruta']) ?></span>
                    <span class="badge muted"><?= e($promocio['numero_dies']) ?> dies</span>
                </div>
                <h3><?= e($promocio['paquet_nom']) ?></h3>
                <p><?= e($promocio['punt_origen']) ?> · <?= e($promocio['proveidor_nom']) ?></p>
                <div class="price-row"><span>Adult</span><strong><?= money($promocio['preu_base_adult']) ?></strong></div>
                <a class="btn full" href="<?= url('promocio', ['id' => $promocio['id_promocio']]) ?>">Veure detall</a>
            </div>
        </article>
    <?php endforeach; ?>
</div>
