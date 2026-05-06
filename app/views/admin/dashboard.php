<section class="section-head">
    <div>
        <span class="eyebrow">Backoffice</span>
        <h1>Panell de control</h1>
    </div>
    <a class="btn" href="<?= url('admin/reservas') ?>">Gestionar reserves</a>
</section>

<div class="stats-grid">
    <a class="stat-card" href="<?= url('admin/reservas', ['estat' => 'PRE_RESERVA']) ?>">
        <span>Pre-reserves</span>
        <strong><?= e($counts['PRE_RESERVA'] ?? 0) ?></strong>
    </a>
    <a class="stat-card" href="<?= url('admin/reservas', ['estat' => 'ACCEPTADA']) ?>">
        <span>Acceptades</span>
        <strong><?= e($counts['ACCEPTADA'] ?? 0) ?></strong>
    </a>
    <a class="stat-card" href="<?= url('admin/reservas', ['estat' => 'FORMALITZADA']) ?>">
        <span>Formalitzades</span>
        <strong><?= e($counts['FORMALITZADA'] ?? 0) ?></strong>
    </a>
    <div class="stat-card">
        <span>Total cobrat</span>
        <strong><?= money($sales) ?></strong>
    </div>
</div>

<section class="panel">
    <div class="section-inline">
        <div>
            <h2>Últimes reserves</h2>
            <p>Seguiment operatiu de les peticions rebudes.</p>
        </div>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Client</th>
                <th>Paquet</th>
                <th>Estat</th>
                <th>Total</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($latest as $reserva): ?>
                <tr>
                    <td>#<?= e($reserva['id_reserva']) ?></td>
                    <td><?= e($reserva['client_nom'] . ' ' . $reserva['client_cognoms']) ?></td>
                    <td><?= e($reserva['paquet_nom']) ?></td>
                    <td><span class="status status-<?= e(strtolower($reserva['estat'])) ?>"><?= e($reserva['estat']) ?></span></td>
                    <td><?= money($reserva['total_reserva']) ?></td>
                    <td><a href="<?= url('admin/reserva', ['id' => $reserva['id_reserva']]) ?>">Veure</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<section class="panel">
    <h2>Promocions carregades</h2>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Paquet</th><th>Proveïdor</th><th>Viatge</th><th>Adult</th><th>Activa</th></tr></thead>
            <tbody>
            <?php foreach ($promocions as $promocio): ?>
                <tr>
                    <td><?= e($promocio['paquet_nom']) ?></td>
                    <td><?= e($promocio['proveidor_nom']) ?></td>
                    <td><?= e($promocio['data_inici_viatge']) ?> - <?= e($promocio['data_fi_viatge']) ?></td>
                    <td><?= money($promocio['preu_base_adult']) ?></td>
                    <td><?= $promocio['activa'] ? 'Sí' : 'No' ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
