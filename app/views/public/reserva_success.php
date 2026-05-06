<section class="panel narrow success-panel">
    <span class="eyebrow">Pre-reserva registrada</span>
    <h1>Reserva #<?= e($reserva['id_reserva']) ?></h1>
    <p>Hem registrat la teva pre-reserva i s’ha notificat el proveïdor. Quan el proveïdor l’accepti, es podrà continuar amb el pagament.</p>

    <div class="facts vertical">
        <div><span>Estat</span><strong><?= e($reserva['estat']) ?></strong></div>
        <div><span>Paquet</span><strong><?= e($reserva['paquet_nom']) ?></strong></div>
        <div><span>Total</span><strong><?= money($reserva['total_reserva']) ?></strong></div>
    </div>

    <a class="btn" href="<?= url('home') ?>">Tornar a promocions</a>
</section>
