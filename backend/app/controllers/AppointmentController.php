<?php

require_once __DIR__ . '/../services/AppointmentService.php';
require_once __DIR__ . '/../middlewares/AuthMiddleware.php';
require_once __DIR__ . '/../utils/Response.php';

class AppointmentController {
    private AppointmentService $service;

    public function __construct() {
        $this->service = new AppointmentService();
    }

    public function index(): void {
        $payload = AuthMiddleware::handle();
        $items = $this->service->listForUser($payload['user_id']);
        Response::success($items, 'Appointments fetched.');
    }

    public function store(): void {
        $payload = AuthMiddleware::handle();
        $input   = $this->getJsonInput();

        $result = $this->service->book($payload['user_id'], $input);
        if (!$result['ok']) {
            Response::error($result['error'], $result['code'], $result['errors'] ?? null);
        }
        Response::success($result['data'], 'Appointment booked.', 201);
    }

    public function update(int $id): void {
        $payload = AuthMiddleware::handle();
        $input   = $this->getJsonInput();

        $result = $this->service->edit($id, $payload['user_id'], $input);
        if (!$result['ok']) {
            Response::error($result['error'], $result['code'], $result['errors'] ?? null);
        }
        Response::success($result['data'], 'Appointment updated.');
    }

    public function cancel(int $id): void {
        $payload = AuthMiddleware::handle();

        $result = $this->service->cancel($id, $payload['user_id']);
        if (!$result['ok']) {
            Response::error($result['error'], $result['code']);
        }
        Response::success(null, 'Appointment cancelled.');
    }

    private function getJsonInput(): array {
        $raw = file_get_contents('php://input');
        return json_decode($raw, true) ?? [];
    }
}
