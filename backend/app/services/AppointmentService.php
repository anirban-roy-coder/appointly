<?php

require_once __DIR__ . '/../models/AppointmentModel.php';
require_once __DIR__ . '/../utils/Validator.php';

class AppointmentService {
    private AppointmentModel $model;

    public function __construct() {
        $this->model = new AppointmentModel();
    }

    public function listForUser(int $userId): array {
        return $this->model->getAllByUser($userId);
    }

    /**
     * Validates, checks conflicts, then creates a booking.
     * Returns ['ok' => true, 'data' => [...]] or ['ok' => false, 'error' => '...', 'code' => 422]
     */
    public function book(int $userId, array $input): array {
        $v = new Validator($input);
        $v->required('title', 'Title')
          ->required('appointment_date', 'Date')
          ->required('appointment_time', 'Time')
          ->minLength('title', 3)
          ->date('appointment_date')
          ->time('appointment_time')
          ->futureDate('appointment_date', 'appointment_time');

        if ($v->fails()) {
            return ['ok' => false, 'error' => $v->getFirstError(), 'errors' => $v->getErrors(), 'code' => 422];
        }

        if ($this->model->isSlotTaken($input['appointment_date'], $input['appointment_time'])) {
            return ['ok' => false, 'error' => 'That slot is already taken — pick a different time.', 'code' => 409];
        }

        $id = $this->model->create($userId, $input);
        return ['ok' => true, 'data' => $this->model->findById($id)];
    }

    public function edit(int $appointmentId, int $userId, array $input): array {
        $existing = $this->model->findById($appointmentId);

        if (!$existing || (int)$existing['user_id'] !== $userId) {
            return ['ok' => false, 'error' => 'Appointment not found.', 'code' => 404];
        }
        if ($existing['status'] !== 'pending') {
            return ['ok' => false, 'error' => 'Only pending appointments can be changed.', 'code' => 400];
        }

        $v = new Validator($input);
        $v->required('title', 'Title')
          ->required('appointment_date', 'Date')
          ->required('appointment_time', 'Time')
          ->minLength('title', 3)
          ->date('appointment_date')
          ->time('appointment_time')
          ->futureDate('appointment_date', 'appointment_time');

        if ($v->fails()) {
            return ['ok' => false, 'error' => $v->getFirstError(), 'errors' => $v->getErrors(), 'code' => 422];
        }

        if ($this->model->isSlotTaken($input['appointment_date'], $input['appointment_time'], $appointmentId)) {
            return ['ok' => false, 'error' => 'That slot is already taken — pick a different time.', 'code' => 409];
        }

        $this->model->update($appointmentId, $userId, $input);
        return ['ok' => true, 'data' => $this->model->findById($appointmentId)];
    }

    public function cancel(int $appointmentId, int $userId): array {
        $existing = $this->model->findById($appointmentId);

        if (!$existing || (int)$existing['user_id'] !== $userId) {
            return ['ok' => false, 'error' => 'Appointment not found.', 'code' => 404];
        }
        if ($existing['status'] === 'cancelled') {
            return ['ok' => false, 'error' => 'This appointment is already cancelled.', 'code' => 400];
        }

        $this->model->cancel($appointmentId, $userId);
        return ['ok' => true, 'data' => null];
    }
}
