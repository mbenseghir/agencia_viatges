<?php
declare(strict_types=1);

namespace Models;

final class Promocio extends Model
{
    public function active(): array
    {
        $sql = "SELECT pr.*, p.nom AS paquet_nom, p.descripcio, p.continent, p.pais_ruta, p.punt_origen,
                       p.numero_dies, p.galeria_url, p.pdf_circuit, pv.nom AS proveidor_nom
                FROM promocions pr
                INNER JOIN paquets p ON p.id_paquet = pr.id_paquet
                INNER JOIN proveidors pv ON pv.id_proveidor = p.id_proveidor
                WHERE pr.activa = 1
                  AND CURDATE() BETWEEN pr.data_inici_promocio AND pr.data_fi_promocio
                ORDER BY pr.data_inici_viatge ASC";

        return $this->db()->query($sql)->fetchAll();
    }

    public function all(): array
    {
        $sql = "SELECT pr.*, p.nom AS paquet_nom, p.continent, p.pais_ruta, pv.nom AS proveidor_nom
                FROM promocions pr
                INNER JOIN paquets p ON p.id_paquet = pr.id_paquet
                INNER JOIN proveidors pv ON pv.id_proveidor = p.id_proveidor
                ORDER BY pr.data_inici_viatge ASC";

        return $this->db()->query($sql)->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db()->prepare(
            "SELECT pr.*, p.nom AS paquet_nom, p.descripcio, p.continent, p.pais_ruta, p.punt_origen,
                    p.numero_dies, p.galeria_url, p.pdf_circuit, p.id_proveidor,
                    pv.nom AS proveidor_nom, pv.correu AS proveidor_correu, pv.telefon AS proveidor_telefon
             FROM promocions pr
             INNER JOIN paquets p ON p.id_paquet = pr.id_paquet
             INNER JOIN proveidors pv ON pv.id_proveidor = p.id_proveidor
             WHERE pr.id_promocio = :id"
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ?: null;
    }
}
