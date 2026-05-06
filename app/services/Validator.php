<?php
declare(strict_types=1);

namespace Services;

final class Validator
{
    private array $errors = [];

    public function required(string $field, mixed $value, string $label): self
    {
        if (trim((string)$value) === '') {
            $this->errors[$field][] = "El camp {$label} és obligatori.";
        }
        return $this;
    }

    public function email(string $field, mixed $value, string $label): self
    {
        if (trim((string)$value) !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field][] = "El camp {$label} ha de ser un correu vàlid.";
        }
        return $this;
    }

    public function date(string $field, mixed $value, string $label): self
    {
        if (trim((string)$value) !== '') {
            $dt = \DateTime::createFromFormat('Y-m-d', (string)$value);
            if (!$dt || $dt->format('Y-m-d') !== $value) {
                $this->errors[$field][] = "El camp {$label} ha de tenir format AAAA-MM-DD.";
            }
        }
        return $this;
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function fails(): bool
    {
        return !empty($this->errors);
    }
}
