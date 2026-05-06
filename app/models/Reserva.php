<?php
declare(strict_types=1);

namespace Models;

use Core\Database;
use PDO;
use RuntimeException;

final class Reserva extends Model
{
    public const ESTAT_PRE_RESERVA = 'PRE_RESERVA';
    public const ESTAT_ACCEPTADA = 'ACCEPTADA';
    public const ESTAT_REBUTJADA = 'REBUTJADA';
    public const ESTAT_FORMALITZADA = 'FORMALITZADA';

    public function create(array $clientData, array $travelers, array $promocio): int
    {
        return (int)Database::transaction(function (PDO $pdo) use ($clientData, $travelers, $promocio) {
            $clientId = $this->findOrCreateClient($pdo, $clientData);
            $total = $this->calculateTotal($travelers, $promocio);

            $stmt = $pdo->prepare(
                "INSERT INTO reserves
                    (id_client, id_promocio, estat, data_reserva, data_inici, data_fi, total_reserva, total_pagat, observacions)
                 VALUES
                    (:id_client, :id_promocio, :estat, NOW(), :data_inici, :data_fi, :total_reserva, 0, :observacions)"
            );
            $stmt->execute([
                'id_client' => $clientId,
                'id_promocio' => $promocio['id_promocio'],
                'estat' => self::ESTAT_PRE_RESERVA,
                'data_inici' => $promocio['data_inici_viatge'],
                'data_fi' => $promocio['data_fi_viatge'],
                'total_reserva' => $total,
                'observacions' => $clientData['observacions'] ?? null,
            ]);

            $reservaId = (int)$pdo->lastInsertId();
            $lineStmt = $pdo->prepare(
                "INSERT INTO viatgers
                    (id_reserva, nom, cognoms, adult, habitacio_individual, categoria_superior,
                     document_identitat, nacionalitat, data_naixement, preferencies, preu_calculat)
                 VALUES
                    (:id_reserva, :nom, :cognoms, :adult, :habitacio_individual, :categoria_superior,
                     :document_identitat, :nacionalitat, :data_naixement, :preferencies, :preu_calculat)"
            );

            foreach ($travelers as $traveler) {
                $lineStmt->execute([
                    'id_reserva' => $reservaId,
                    'nom' => $traveler['nom'],
                    'cognoms' => $traveler['cognoms'],
                    'adult' => $traveler['adult'] ? 1 : 0,
                    'habitacio_individual' => $traveler['habitacio_individual'] ? 1 : 0,
                    'categoria_superior' => $traveler['categoria_superior'] ? 1 : 0,
                    'document_identitat' => $traveler['document_identitat'],
                    'nacionalitat' => $traveler['nacionalitat'],
                    'data_naixement' => $traveler['data_naixement'] ?: null,
                    'preferencies' => $traveler['preferencies'] ?: null,
                    'preu_calculat' => $this->calculateTravelerPrice($traveler, $promocio),
                ]);
            }

            return $reservaId;
        });
    }

    public function calculateTotal(array $travelers, array $promocio): float
    {
        $total = 0.0;
        foreach ($travelers as $traveler) {
            $total += $this->calculateTravelerPrice($traveler, $promocio);
        }
        return $total;
    }

    public function calculateTravelerPrice(array $traveler, array $promocio): float
    {
        $price = !empty($traveler['adult'])
            ? (float)$promocio['preu_base_adult']
            : (float)$promocio['preu_base_nen'];

        if (!empty($traveler['habitacio_individual'])) {
            $price += (float)$promocio['preu_extra_individual'];
        }

        if (!empty($traveler['categoria_superior'])) {
            $price += (float)$promocio['preu_extra_categoria_superior'];
        }

        return $price;
    }

    public function latest(int $limit = 10): array
    {
        $stmt = $this->db()->prepare(
            "SELECT r.*, c.nom AS client_nom, c.cognoms AS client_cognoms, c.correu,
                    p.nom AS paquet_nom, pr.data_inici_viatge, pr.data_fi_viatge
             FROM reserves r
             INNER JOIN clients c ON c.id_client = r.id_client
             INNER JOIN promocions pr ON pr.id_promocio = r.id_promocio
             INNER JOIN paquets p ON p.id_paquet = pr.id_paquet
             ORDER BY r.data_reserva DESC
             LIMIT :limit"
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function all(?string $estat = null): array
    {
        $params = [];
        $where = '';
        if ($estat !== null && $estat !== '') {
            $where = 'WHERE r.estat = :estat';
            $params['estat'] = $estat;
        }

        $stmt = $this->db()->prepare(
            "SELECT r.*, c.nom AS client_nom, c.cognoms AS client_cognoms, c.correu,
                    p.nom AS paquet_nom, pv.nom AS proveidor_nom
             FROM reserves r
             INNER JOIN clients c ON c.id_client = r.id_client
             INNER JOIN promocions pr ON pr.id_promocio = r.id_promocio
             INNER JOIN paquets p ON p.id_paquet = pr.id_paquet
             INNER JOIN proveidors pv ON pv.id_proveidor = p.id_proveidor
             {$where}
             ORDER BY r.data_reserva DESC"
        );
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db()->prepare(
            "SELECT r.*, c.nom AS client_nom, c.cognoms AS client_cognoms, c.telefon, c.correu, c.adreca,
                    c.document_identitat AS client_document, c.nacionalitat AS client_nacionalitat,
                    pr.*, p.nom AS paquet_nom, p.punt_origen, p.pais_ruta, p.numero_dies,
                    pv.nom AS proveidor_nom, pv.correu AS proveidor_correu, pv.telefon AS proveidor_telefon
             FROM reserves r
             INNER JOIN clients c ON c.id_client = r.id_client
             INNER JOIN promocions pr ON pr.id_promocio = r.id_promocio
             INNER JOIN paquets p ON p.id_paquet = pr.id_paquet
             INNER JOIN proveidors pv ON pv.id_proveidor = p.id_proveidor
             WHERE r.id_reserva = :id"
        );
        $stmt->execute(['id' => $id]);
        $reserva = $stmt->fetch();

        if (!$reserva) {
            return null;
        }

        $reserva['viatgers'] = $this->travelers($id);
        return $reserva;
    }

    public function travelers(int $idReserva): array
    {
        $stmt = $this->db()->prepare('SELECT * FROM viatgers WHERE id_reserva = :id ORDER BY id_viatger ASC');
        $stmt->execute(['id' => $idReserva]);
        return $stmt->fetchAll();
    }

    public function updateStatus(int $idReserva, string $newStatus, ?float $amountPaid = null): void
    {
        $allowed = [self::ESTAT_PRE_RESERVA, self::ESTAT_ACCEPTADA, self::ESTAT_REBUTJADA, self::ESTAT_FORMALITZADA];
        if (!in_array($newStatus, $allowed, true)) {
            throw new RuntimeException('Estat de reserva no vàlid.');
        }

        $fields = ['estat = :estat'];
        $params = ['estat' => $newStatus, 'id' => $idReserva];

        if ($newStatus === self::ESTAT_FORMALITZADA) {
            $fields[] = 'data_formalitzacio = NOW()';
            $fields[] = 'total_pagat = :total_pagat';
            $params['total_pagat'] = $amountPaid;
        }

        $sql = 'UPDATE reserves SET ' . implode(', ', $fields) . ' WHERE id_reserva = :id';
        $stmt = $this->db()->prepare($sql);
        $stmt->execute($params);
    }

    public function countByStatus(): array
    {
        $rows = $this->db()->query('SELECT estat, COUNT(*) total FROM reserves GROUP BY estat')->fetchAll();
        $result = [
            self::ESTAT_PRE_RESERVA => 0,
            self::ESTAT_ACCEPTADA => 0,
            self::ESTAT_REBUTJADA => 0,
            self::ESTAT_FORMALITZADA => 0,
        ];

        foreach ($rows as $row) {
            $result[$row['estat']] = (int)$row['total'];
        }

        return $result;
    }

    public function totalSales(): float
    {
        $row = $this->db()->query("SELECT COALESCE(SUM(total_pagat), 0) total FROM reserves WHERE estat = 'FORMALITZADA'")->fetch();
        return (float)($row['total'] ?? 0);
    }

    private function findOrCreateClient(PDO $pdo, array $clientData): int
    {
        $stmt = $pdo->prepare('SELECT id_client FROM clients WHERE correu = :correu LIMIT 1');
        $stmt->execute(['correu' => $clientData['correu']]);
        $existing = $stmt->fetch();

        if ($existing) {
            $update = $pdo->prepare(
                "UPDATE clients SET nom = :nom, cognoms = :cognoms, telefon = :telefon, adreca = :adreca,
                    document_identitat = :document_identitat, nacionalitat = :nacionalitat
                 WHERE id_client = :id_client"
            );
            $update->execute([
                'nom' => $clientData['nom'],
                'cognoms' => $clientData['cognoms'],
                'telefon' => $clientData['telefon'],
                'adreca' => $clientData['adreca'],
                'document_identitat' => $clientData['document_identitat'],
                'nacionalitat' => $clientData['nacionalitat'],
                'id_client' => $existing['id_client'],
            ]);
            return (int)$existing['id_client'];
        }

        $insert = $pdo->prepare(
            "INSERT INTO clients (nom, cognoms, telefon, correu, adreca, document_identitat, nacionalitat)
             VALUES (:nom, :cognoms, :telefon, :correu, :adreca, :document_identitat, :nacionalitat)"
        );
        $insert->execute([
            'nom' => $clientData['nom'],
            'cognoms' => $clientData['cognoms'],
            'telefon' => $clientData['telefon'],
            'correu' => $clientData['correu'],
            'adreca' => $clientData['adreca'],
            'document_identitat' => $clientData['document_identitat'],
            'nacionalitat' => $clientData['nacionalitat'],
        ]);

        return (int)$pdo->lastInsertId();
    }
}
