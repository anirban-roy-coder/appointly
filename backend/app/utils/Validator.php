<?php

class Validator {
    private array $errors = [];
    private array $data;

    public function __construct(array $data) {
        $this->data = $data;
    }

    public function required(string $field, string $label = ''): self {
        $label = $label ?: ucfirst(str_replace('_', ' ', $field));
        if (!isset($this->data[$field]) || trim((string)$this->data[$field]) === '') {
            $this->errors[$field] = "$label is required.";
        }
        return $this;
    }

    public function email(string $field): self {
        if (isset($this->data[$field]) && !filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = "Invalid email format.";
        }
        return $this;
    }

    public function minLength(string $field, int $min): self {
        if (isset($this->data[$field]) && strlen((string)$this->data[$field]) < $min) {
            $label = ucfirst(str_replace('_', ' ', $field));
            $this->errors[$field] = "$label must be at least $min characters.";
        }
        return $this;
    }

    public function date(string $field): self {
        if (isset($this->data[$field]) && $this->data[$field] !== '') {
            $d = DateTime::createFromFormat('Y-m-d', $this->data[$field]);
            if (!$d || $d->format('Y-m-d') !== $this->data[$field]) {
                $this->errors[$field] = "Invalid date format (YYYY-MM-DD expected).";
            }
        }
        return $this;
    }

    public function time(string $field): self {
        if (isset($this->data[$field]) && $this->data[$field] !== '') {
            $t = DateTime::createFromFormat('H:i', $this->data[$field]);
            if (!$t) {
                $this->errors[$field] = "Invalid time format (HH:MM expected).";
            }
        }
        return $this;
    }

    public function futureDate(string $dateField, string $timeField = ''): self {
        if (isset($this->data[$dateField]) && $this->data[$dateField] !== '') {
            $dateStr = $this->data[$dateField];
            if ($timeField && isset($this->data[$timeField]) && $this->data[$timeField] !== '') {
                $dateStr .= ' ' . $this->data[$timeField];
                $dt = DateTime::createFromFormat('Y-m-d H:i', $dateStr);
            } else {
                $dt = DateTime::createFromFormat('Y-m-d', $dateStr);
            }
            if ($dt && $dt <= new DateTime()) {
                $this->errors[$dateField] = "Appointment must be scheduled in the future.";
            }
        }
        return $this;
    }

    public function fails(): bool {
        return !empty($this->errors);
    }

    public function getErrors(): array {
        return $this->errors;
    }

    public function getFirstError(): string {
        return array_values($this->errors)[0] ?? 'Validation failed.';
    }
}
