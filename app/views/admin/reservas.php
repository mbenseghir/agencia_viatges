<section class="section-head">
    <div>
        <span class="eyebrow">Backoffice</span>
        <h1>Gestió de reserves</h1>
    </div>
    <div class="filter-bar">
        <a class="chip <?= !$estat ? 'active' : '' ?>" href="<?= url('admin/reservas') ?>">Totes</a>
        <a class="chip <?= $estat === 'PRE_RESERVA' ? 'active' : '' ?>" href="<?= url('admin/reservas', ['estat' => 'PRE_RESERVA']) ?>">Pre-reserva</a>
        <a class="chip <?= $estat === 'ACCEPTADA' ? 'active' : '' ?>" href="<?= url('admin/reservas', ['estat' => 'ACCEPTADA']) ?>">Acceptada</a>
        <a class="chip <?= $estat === 'FORMALITZADA' ? 'active' : '' ?>" href="<?= url('admin/reservas', ['estat' => 'FORMALITZADA']) ?>">Formalitzada</a>
    </div>
</section>

<section class="panel">
    <div class="table-wrap">
        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Data</th>
                <th>Client</th>
                <th>Paquet</th>
                <th>Proveïdor</th>
                <th>Estat</th>
                <th>Total</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($reservas as $reserva): ?>
                <tr>
                    <td>#<?= e($reserva['id_reserva']) ?></td>
                    <td><?= e($reserva['data_reserva']) ?></td>
                    <td><?= e($reserva['client_nom'] . ' ' . $reserva['client_cognoms']) ?><br><small><?= e($reserva['correu']) ?></small></td>
                    <td><?= e($reserva['paquet_nom']) ?></td>
                    <td><?= e($reserva['proveidor_nom']) ?></td>
                    <td><span class="status status-<?= e(strtolower($reserva['estat'])) ?>"><?= e($reserva['estat']) ?></span></td>
                    <td><?= money($reserva['total_reserva']) ?></td>
                    <td><a class="btn btn-small" href="<?= url('admin/reserva', ['id' => $reserva['id_reserva']]) ?>">Gestionar</a></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($reservas)): ?>
                <tr><td colspan="8">No hi ha reserves amb aquest filtre.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
