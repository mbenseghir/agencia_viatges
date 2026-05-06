<?php
declare(strict_types=1);

namespace Controllers;

use Core\View;
use Models\Promocio;
use Models\Reserva;
use Services\ProveedorNotifier;

final class AdminController
{
    public function __construct(
        private Promocio $promocioModel,
        private Reserva $reservaModel,
        private ProveedorNotifier $notifier
    ) {}

    public function dashboard(): void
    {
        View::render('admin/dashboard', [
            'title' => 'Panell de control',
            'counts' => $this->reservaModel->countByStatus(),
            'sales' => $this->reservaModel->totalSales(),
            'latest' => $this->reservaModel->latest(8),
            'promocions' => $this->promocioModel->all(),
        ]);
    }

    public function reservas(): void
    {
        $estat = isset($_GET['estat']) ? (string)$_GET['estat'] : null;
        View::render('admin/reservas', [
            'title' => 'Gestió de reserves',
            'reservas' => $this->reservaModel->all($estat),
            'estat' => $estat,
        ]);
    }

    public function reserva(int $id): void
    {
        $reserva = $this->reservaModel->find($id);
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
        $this->reservaModel->updateStatus($id, Reserva::ESTAT_ACCEPTADA);
        $reserva = $this->reservaModel->find($id);
        if ($reserva) {
            $this->notifier->notifyAcceptedOrRejected($reserva, Reserva::ESTAT_ACCEPTADA);
        }
        \flash('success', 'Reserva acceptada. Ara el client podria procedir al pagament.');
        \redirect('admin/reserva/' . $id);
    }

    public function rebutjar(int $id): void
    {
        $this->reservaModel->updateStatus($id, Reserva::ESTAT_REBUTJADA);
        $reserva = $this->reservaModel->find($id);
        if ($reserva) {
            $this->notifier->notifyAcceptedOrRejected($reserva, Reserva::ESTAT_REBUTJADA);
        }
        \flash('success', 'Reserva rebutjada.');
        \redirect('admin/reserva/' . $id);
    }

    public function formalitzar(int $id): void
    {
        $reserva = $this->reservaModel->find($id);
        if (!$reserva) {
            \flash('danger', 'Reserva no trobada.');
            \redirect('admin/reservas');
        }

        if ($reserva['estat'] !== Reserva::ESTAT_ACCEPTADA) {
            \flash('warning', 'Només es pot formalitzar una reserva acceptada.');
            \redirect('admin/reserva', ['id' => $id]);
        }

        $this->reservaModel->updateStatus($id, Reserva::ESTAT_FORMALITZADA, (float)$reserva['total_reserva']);
        $updated = $this->reservaModel->find($id);
        if ($updated) {
            $this->notifier->notifyFormalitzada($updated);
        }
        \flash('success', 'Reserva formalitzada i notificada al proveïdor.');
        \redirect('admin/reserva/' . $id);
    }
}
