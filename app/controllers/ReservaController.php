<?php
declare(strict_types=1);

namespace Controllers;

use Core\View;
use Models\Promocio;
use Models\Reserva;
use Services\ProveedorNotifier;
use Services\Validator;

final class ReservaController
{
    public function create(int $promocioId): void
    {
        $promocio = (new Promocio())->find($promocioId);
        if (!$promocio) {
            http_response_code(404);
            View::render('error', [
                'title' => 'Promoció no trobada',
                'message' => 'No es pot reservar una promoció inexistent.',
            ]);
            return;
        }

        View::render('public/reserva_form', [
            'title' => 'Fer pre-reserva',
            'promocio' => $promocio,
            'errors' => [],
            'old' => [],
        ]);
    }

    public function store(int $promocioId): void
    {
        \verify_csrf();

        $promocio = (new Promocio())->find($promocioId);
        if (!$promocio) {
            http_response_code(404);
            View::render('error', [
                'title' => 'Promoció no trobada',
                'message' => 'No es pot reservar una promoció inexistent.',
            ]);
            return;
        }

        [$clientData, $travelers] = $this->sanitizePayload();
        $errors = $this->validatePayload($clientData, $travelers);

        if (!empty($errors)) {
            View::render('public/reserva_form', [
                'title' => 'Fer pre-reserva',
                'promocio' => $promocio,
                'errors' => $errors,
                'old' => $_POST,
            ]);
            return;
        }

        $model = new Reserva();
        $reservaId = $model->create($clientData, $travelers, $promocio);
        $reserva = $model->find($reservaId);

        if ($reserva) {
            (new ProveedorNotifier())->notifyPreReserva($reserva);
        }

        \flash('success', 'Pre-reserva registrada. Hem notificat el proveïdor.');
        \redirect('reserva/gracies', ['id' => $reservaId]);
    }

    public function success(int $id): void
    {
        $reserva = (new Reserva())->find($id);
        if (!$reserva) {
            http_response_code(404);
            View::render('error', [
                'title' => 'Reserva no trobada',
                'message' => 'No hem trobat aquesta reserva.',
            ]);
            return;
        }

        View::render('public/reserva_success', [
            'title' => 'Pre-reserva registrada',
            'reserva' => $reserva,
        ]);
    }

    private function sanitizePayload(): array
    {
        $clientData = [
            'nom' => \post_string('client_nom', 100),
            'cognoms' => \post_string('client_cognoms', 150),
            'telefon' => \post_string('client_telefon', 50),
            'correu' => \post_string('client_correu', 190),
            'adreca' => \post_string('client_adreca', 255),
            'document_identitat' => \post_string('client_document', 50),
            'nacionalitat' => \post_string('client_nacionalitat', 80),
            'observacions' => \post_string('observacions', 500),
        ];

        $rawTravelers = $_POST['viatgers'] ?? [];
        $travelers = [];
        if (is_array($rawTravelers)) {
            foreach ($rawTravelers as $item) {
                if (!is_array($item)) {
                    continue;
                }
                $travelers[] = [
                    'nom' => trim((string)($item['nom'] ?? '')),
                    'cognoms' => trim((string)($item['cognoms'] ?? '')),
                    'adult' => isset($item['adult']) && (string)$item['adult'] === '1',
                    'habitacio_individual' => isset($item['habitacio_individual']) && (string)$item['habitacio_individual'] === '1',
                    'categoria_superior' => isset($item['categoria_superior']) && (string)$item['categoria_superior'] === '1',
                    'document_identitat' => trim((string)($item['document_identitat'] ?? '')),
                    'nacionalitat' => trim((string)($item['nacionalitat'] ?? '')),
                    'data_naixement' => trim((string)($item['data_naixement'] ?? '')),
                    'preferencies' => trim((string)($item['preferencies'] ?? '')),
                ];
            }
        }

        return [$clientData, $travelers];
    }

    private function validatePayload(array $clientData, array $travelers): array
    {
        $validator = new Validator();
        $validator
            ->required('client_nom', $clientData['nom'], 'nom del client')
            ->required('client_cognoms', $clientData['cognoms'], 'cognoms del client')
            ->required('client_telefon', $clientData['telefon'], 'telèfon')
            ->required('client_correu', $clientData['correu'], 'correu')
            ->email('client_correu', $clientData['correu'], 'correu')
            ->required('client_document', $clientData['document_identitat'], 'DNI o passaport')
            ->required('client_nacionalitat', $clientData['nacionalitat'], 'nacionalitat');

        $errors = $validator->errors();

        if (count($travelers) === 0) {
            $errors['viatgers'][] = 'Cal informar com a mínim un viatger.';
        }

        foreach ($travelers as $index => $traveler) {
            $line = $index + 1;
            if ($traveler['nom'] === '') {
                $errors['viatgers'][] = "El nom del viatger {$line} és obligatori.";
            }
            if ($traveler['cognoms'] === '') {
                $errors['viatgers'][] = "Els cognoms del viatger {$line} són obligatoris.";
            }
            if ($traveler['document_identitat'] === '') {
                $errors['viatgers'][] = "El document del viatger {$line} és obligatori.";
            }
            if ($traveler['nacionalitat'] === '') {
                $errors['viatgers'][] = "La nacionalitat del viatger {$line} és obligatòria.";
            }
            if ($traveler['data_naixement'] !== '') {
                $dt = \DateTime::createFromFormat('Y-m-d', $traveler['data_naixement']);
                if (!$dt || $dt->format('Y-m-d') !== $traveler['data_naixement']) {
                    $errors['viatgers'][] = "La data de naixement del viatger {$line} no és vàlida.";
                }
            }
        }

        return $errors;
    }
}
