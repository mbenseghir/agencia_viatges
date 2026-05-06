<?php
declare(strict_types=1);

namespace Models;

final class Usuari extends Model
{
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db()->prepare('SELECT * FROM usuaris WHERE email = :email AND actiu = 1 LIMIT 1');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }
}
