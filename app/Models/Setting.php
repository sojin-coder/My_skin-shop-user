<?php

class Setting extends Model
{
    protected string $table = 'settings';

    // ទាញយកទិន្នន័យទាំងអស់
    public function all(): array
    {
        $stmt = Database::connection()->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ទាញយកតាម Key ជាក់លាក់
    public function findByKey(string $key)
    {
        $stmt = Database::connection()->prepare("SELECT * FROM {$this->table} WHERE setting_key = :key LIMIT 1");
        $stmt->execute(['key' => $key]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}