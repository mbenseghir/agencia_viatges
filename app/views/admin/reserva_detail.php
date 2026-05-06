<section class="section-head">
    <div>
        <span class="eyebrow">Reserva #<?= e($reserva['id_reserva']) ?></span>
        <h1><?= e($reserva['paquet_nom']) ?></h1>
    </div>
    <span class="status status-<?= e(strtolower($reserva['estat'])) ?> big"><?= e($reserva['estat']) ?></span>
</section>

<div class="two-columns">
    <section class="panel">
        <h2>Dades de la reserva</h2>
        <div class="facts vertical">
            <div><span>Client</span><strong><?= e($reserva['client_nom'] . ' ' . $reserva['client_cognoms']) ?></strong></div>
            <div><span>Contacte</span><strong><?= e($reserva['correu']) ?> · <?= e($reserva['telefon']) ?></strong></div>
            <div><span>Document client</span><strong><?= e($reserva['client_document']) ?></strong></div>
            <div><span>Viatge</span><strong><?= e($reserva['data_inici']) ?> - <?= e($reserva['data_fi']) ?></strong></div>
            <div><span>Proveïdor</span><strong><?= e($reserva['proveidor_nom']) ?> · <?= e($reserva['proveidor_correu']) ?></strong></div>
            <div><span>Total reserva</span><strong><?= money($reserva['total_reserva']) ?></strong></div>
            <div><span>Total pagat</span><strong><?= money($reserva['total_pagat']) ?></strong></div>
        </div>
    </section>

    <aside class="panel">
        <h2>Accions</h2>
        <p>El flux recomanat és: pre-reserva → acceptada → formalitzada.</p>
        <div class="action-stack">
            <?php if ($reserva['estat'] === 'PRE_RESERVA'): ?>
                <form method="post" action="<?= url('admin/reserva/acceptar') ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id_reserva" value="<?= e($reserva['id_reserva']) ?>">
                    <button class="btn full" type="submit">Acceptar reserva</button>
                </form>
                <form method="post" action="<?= url('admin/reserva/rebutjar') ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id_reserva" value="<?= e($reserva['id_reserva']) ?>">
                    <button class="btn btn-danger full" type="submit">Rebutjar reserva</button>
                </form>
            <?php elseif ($reserva['estat'] === 'ACCEPTADA'): ?>
                <form method="post" action="<?= url('admin/reserva/formalitzar') ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id_reserva" value="<?= e($reserva['id_reserva']) ?>">
                    <button class="btn full" type="submit">Marcar com pagada i formalitzar</button>
                </form>
            <?php else: ?>
                <div class="alert alert-info">No hi ha accions pendents per a aquest estat.</div>
            <?php endif; ?>
        </div>
        <a class="btn btn-secondary full" href="<?= url('admin/reservas') ?>">Tornar al llistat</a>
    </aside>
</div>

<section class="panel">
    <h2>Viatgers</h2>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Nom</th><th>Tipus</th><th>Document</th><th>Nacionalitat</th><th>Suplements</th><th>Preu</th></tr></thead>
            <tbody>
            <?php foreach ($reserva['viatgers'] as $traveler): ?>
                <tr>
                    <td><?= e($traveler['nom'] . ' ' . $traveler['cognoms']) ?><br><small><?= e($traveler['preferencies']) ?></small></td>
                    <td><?= $traveler['adult'] ? 'Adult' : 'Nen' ?></td>
                    <td><?= e($traveler['document_identitat']) ?></td>
                    <td><?= e($traveler['nacionalitat']) ?></td>
                    <td>
                        <?= $traveler['habitacio_individual'] ? 'Hab. individual ' : '' ?>
                        <?= $traveler['categoria_superior'] ? 'Categoria superior' : '' ?>
                    </td>
                    <td><?= money($traveler['preu_calculat']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
