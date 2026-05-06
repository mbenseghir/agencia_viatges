<section class="panel narrow">
    <span class="eyebrow">Administració</span>
    <h1>Accés intern</h1>
    <p>Usuari de prova: <strong>admin@agencia.test</strong> · Contrasenya: <strong>admin123</strong></p>

    <form method="post" action="<?= url('auth/login') ?>" class="login-form">
        <?= csrf_field() ?>
        <label>Correu
            <input type="email" name="email" value="admin@agencia.test" required>
        </label>
        <label>Contrasenya
            <input type="password" name="password" value="admin123" required>
        </label>
        <button class="btn full" type="submit">Entrar</button>
    </form>
</section>
