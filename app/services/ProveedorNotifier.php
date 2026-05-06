<?php
declare(strict_types=1);

namespace Services;

final class ProveedorNotifier
{
    public function notifyPreReserva(array $reserva): void
    {
        $subject = 'Nova pre-reserva #' . $reserva['id_reserva'];
        $body = $this->buildBody($reserva, 'S’ha registrat una nova pre-reserva pendent de validació del proveïdor.');
        $this->sendOrLog((string)$reserva['proveidor_correu'], $subject, $body);
    }

    public function notifyFormalitzada(array $reserva): void
    {
        $subject = 'Reserva formalitzada #' . $reserva['id_reserva'];
        $body = $this->buildBody($reserva, 'El client ha completat el pagament i la reserva ha quedat formalitzada.');
        $this->sendOrLog((string)$reserva['proveidor_correu'], $subject, $body);
    }

    public function notifyAcceptedOrRejected(array $reserva, string $status): void
    {
        $subject = 'Actualització reserva #' . $reserva['id_reserva'] . ' - ' . $status;
        $body = $this->buildBody($reserva, 'La reserva ha canviat d’estat a: ' . $status . '.');
        $this->sendOrLog((string)$reserva['proveidor_correu'], $subject, $body);
    }

    private function buildBody(array $reserva, string $intro): string
    {
        $lines = [];
        $lines[] = $intro;
        $lines[] = '';
        $lines[] = 'Reserva: #' . $reserva['id_reserva'];
        $lines[] = 'Estat: ' . $reserva['estat'];
        $lines[] = 'Paquet: ' . $reserva['paquet_nom'];
        $lines[] = 'Client: ' . $reserva['client_nom'] . ' ' . $reserva['client_cognoms'];
        $lines[] = 'Email client: ' . $reserva['correu'];
        $lines[] = 'Dates: ' . $reserva['data_inici'] . ' - ' . $reserva['data_fi'];
        $lines[] = 'Total reserva: ' . number_format((float)$reserva['total_reserva'], 2, ',', '.') . ' €';
        $lines[] = '';
        $lines[] = 'Viatgers:';

        foreach (($reserva['viatgers'] ?? []) as $traveler) {
            $lines[] = '- ' . $traveler['nom'] . ' ' . $traveler['cognoms'] . ' | ' . ($traveler['adult'] ? 'Adult' : 'Nen') . ' | ' . number_format((float)$traveler['preu_calculat'], 2, ',', '.') . ' €';
        }

        return implode(PHP_EOL, $lines);
    }

    private function sendOrLog(string $to, string $subject, string $body): void
    {
        $mode = \config('app', 'provider_mail_mode', 'log');

        if ($mode === 'mail') {
            @mail($to, $subject, $body, 'Content-Type: text/plain; charset=UTF-8');
        }

        $dir = STORAGE_PATH . '/logs';
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $entry = "\n==============================\n";
        $entry .= '[' . date('Y-m-d H:i:s') . "]\n";
        $entry .= "TO: {$to}\nSUBJECT: {$subject}\n\n{$body}\n";
        file_put_contents($dir . '/provider-mails.log', $entry, FILE_APPEND);
    }
}
