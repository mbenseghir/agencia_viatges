<?php
declare(strict_types=1);

namespace Models;

use Core\Database;
use PDO;

abstract class Model
{
    protected function db(): PDO
    {
        return Database::connection();
    }
}
