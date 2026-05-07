<?php
declare(strict_types=1);

use DI\ContainerBuilder;

$containerBuilder = new ContainerBuilder();

$containerBuilder->addDefinitions([
    // Aquí podrem definir dependències si calen fàbriques específiques.
    // PHP-DI autowiring s'encarregarà de la majoria de classes automàticament.
]);

return $containerBuilder->build();
