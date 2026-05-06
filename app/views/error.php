<section class="panel narrow">
    <span class="eyebrow">Error</span>
    <h1><?= e($title ?? 'S’ha produït un error') ?></h1>
    <p><?= e($message ?? 'No s’ha pogut completar la petició.') ?></p>
    <a class="btn" href="<?= url('home') ?>">Tornar a l’inici</a>
</section>
