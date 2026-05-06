<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <strong>Revisa el formulari:</strong>
        <ul>
            <?php foreach ($errors as $items): ?>
                <?php foreach ((array)$items as $error): ?>
                    <li><?= e($error) ?></li>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
