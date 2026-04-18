<?php

require_once __DIR__ . '/../../config/database.php';

class AppointmentModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAllByUser(int $userId): array {
        $stmt = $this->db->prepare(
            "SELECT * FROM appointments WHERE user_id = :user_id ORDER BY appointment_date ASC, appointment_time ASC"
        );
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM appointments WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function isSlotTaken(string $date, string $time, ?int $excludeId = null): bool {
        $sql = "SELECT COUNT(*) FROM appointments WHERE appointment_date = :date AND appointment_time = :time AND status != 'cancelled'";
        $params = [':date' => $date, ':time' => $time];
        if ($excludeId !== null) {
            $sql .= " AND id != :exclude_id";
            $params[':exclude_id'] = $excludeId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function create(int $userId, array $data): int {
        $stmt = $this->db->prepare(
            "INSERT INTO appointments (user_id, title, description, appointment_date, appointment_time, status, created_at)
             VALUES (:user_id, :title, :description, :date, :time, 'pending', NOW())"
        );
        $stmt->execute([
            ':user_id'     => $userId,
            ':title'       => $data['title'],
            ':description' => $data['description'] ?? '',
            ':date'        => $data['appointment_date'],
            ':time'        => $data['appointment_time'],
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function cancel(int $id, int $userId): bool {
        $stmt = $this->db->prepare(
            "UPDATE appointments SET status = 'cancelled' WHERE id = :id AND user_id = :user_id AND status != 'cancelled'"
        );
        $stmt->execute([':id' => $id, ':user_id' => $userId]);
        return $stmt->rowCount() > 0;
    }

    public function update(int $id, int $userId, array $data): bool {
        $stmt = $this->db->prepare(
            "UPDATE appointments SET title = :title, description = :description, appointment_date = :date, appointment_time = :time
             WHERE id = :id AND user_id = :user_id AND status = 'pending'"
        );
        $stmt->execute([
            ':title'       => $data['title'],
            ':description' => $data['description'] ?? '',
            ':date'        => $data['appointment_date'],
            ':time'        => $data['appointment_time'],
            ':id'          => $id,
            ':user_id'     => $userId,
        ]);
        return $stmt->rowCount() > 0;
    }
}
