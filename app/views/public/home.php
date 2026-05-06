<section class="hero">
    <div>
        <span class="eyebrow">Paquets tancats amb proveïdor</span>
        <h1>Reserva el teu viatge amb un procés clar i controlat.</h1>
        <p>Consulta les promocions actives, envia una pre-reserva i espera la validació del proveïdor abans del pagament.</p>
        <div class="hero-actions">
            <a class="btn" href="#promocions">Veure promocions</a>
            <?php if (!is_admin()): ?>
                <a class="btn btn-secondary" href="<?= url('auth/login') ?>">Entrar al panell intern</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="hero-card">
        <strong>Flux de reserva</strong>
        <ol class="steps">
            <li>Client envia pre-reserva</li>
            <li>Notificació al proveïdor</li>
            <li>Acceptació o rebuig</li>
            <li>Pagament i formalització</li>
        </ol>
    </div>
</section>

<section id="promocions" class="section-head">
    <div>
        <span class="eyebrow">Promocions actives</span>
        <h2>Viatges disponibles</h2>
    </div>
    <p><?= count($promocions) ?> promocions disponibles ara mateix.</p>
</section>

<?php if (empty($promocions)): ?>
    <div class="panel">No hi ha promocions actives en aquest moment.</div>
<?php else: ?>
    <div class="grid cards-grid">
        <?php foreach ($promocions as $promocio): ?>
            <article class="card travel-card">
                <div class="image-placeholder" style="background-image: linear-gradient(135deg, rgba(15,23,42,.30), rgba(15,23,42,.75)), url('<?= e($promocio['galeria_url']) ?>');"></div>
                <div class="card-body">
                    <div class="badges">
                        <span class="badge"><?= e($promocio['continent']) ?></span>
                        <span class="badge muted"><?= e($promocio['numero_dies']) ?> dies</span>
                    </div>
                    <h3><?= e($promocio['paquet_nom']) ?></h3>
                    <p><?= e(mb_strimwidth($promocio['descripcio'], 0, 120, '...')) ?></p>
                    <div class="price-row">
                        <span>Adult des de</span>
                        <strong><?= money($promocio['preu_base_adult']) ?></strong>
                    </div>
                    <a class="btn full" href="<?= url('promocio', ['id' => $promocio['id_promocio']]) ?>">Veure detall</a>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
