<?php
declare(strict_types=1);

namespace Controllers;

use Core\View;
use Models\Promocio;
use Models\Reserva;
use Services\ProveedorNotifier;

final class AdminController
{
    public function dashboard(): void
    {
        \require_admin();

        $reservaModel = new Reserva();
        View::render('admin/dashboard', [
            'title' => 'Panell de control',
            'counts' => $reservaModel->countByStatus(),
            'sales' => $reservaModel->totalSales(),
            'latest' => $reservaModel->latest(8),
            'promocions' => (new Promocio())->all(),
        ]);
    }

    public function reservas(): void
    {
        \require_admin();
        $estat = isset($_GET['estat']) ? (string)$_GET['estat'] : null;
        View::render('admin/reservas', [
            'title' => 'Gestió de reserves',
            'reservas' => (new Reserva())->all($estat),
            'estat' => $estat,
        ]);
    }

    public function reserva(int $id): void
    {
        \require_admin();
        $reserva = (new Reserva())->find($id);
        if (!$reserva) {
            http_response_code(404);
            View::render('error', [
                'title' => 'Reserva no trobada',
                'message' => 'No hem trobat aquesta reserva.',
            ]);
            return;
        }

        View::render('admin/reserva_detail', [
            'title' => 'Reserva #' . $id,
            'reserva' => $reserva,
        ]);
    }

    public function acceptar(int $id): void
    {
        \require_admin();
        \verify_csrf();
        $model = new Reserva();
        $model->updateStatus($id, Reserva::ESTAT_ACCEPTADA);
        $reserva = $model->find($id);
        if ($reserva) {
            (new ProveedorNotifier())->notifyAcceptedOrRejected($reserva, Reserva::ESTAT_ACCEPTADA);
        }
        \flash('success', 'Reserva acceptada. Ara el client podria procedir al pagament.');
        \redirect('admin/reserva', ['id' => $id]);
    }

    public function rebutjar(int $id): void
    {
        \require_admin();
        \verify_csrf();
        $model = new Reserva();
        $model->updateStatus($id, Reserva::ESTAT_REBUTJADA);
        $reserva = $model->find($id);
        if ($reserva) {
            (new ProveedorNotifier())->notifyAcceptedOrRejected($reserva, Reserva::ESTAT_REBUTJADA);
        }
        \flash('success', 'Reserva rebutjada.');
        \redirect('admin/reserva', ['id' => $id]);
    }

    public function formalitzar(int $id): void
    {
        \require_admin();
        \verify_csrf();
        $model = new Reserva();
        $reserva = $model->find($id);
        if (!$reserva) {
            \flash('danger', 'Reserva no trobada.');
            \redirect('admin/reservas');
        }

        if ($reserva['estat'] !== Reserva::ESTAT_ACCEPTADA) {
            \flash('warning', 'Només es pot formalitzar una reserva acceptada.');
            \redirect('admin/reserva', ['id' => $id]);
        }

        $model->updateStatus($id, Reserva::ESTAT_FORMALITZADA, (float)$reserva['total_reserva']);
        $updated = $model->find($id);
        if ($updated) {
            (new ProveedorNotifier())->notifyFormalitzada($updated);
        }
        \flash('success', 'Reserva formalitzada i notificada al proveïdor.');
        \redirect('admin/reserva', ['id' => $id]);
    }
}
