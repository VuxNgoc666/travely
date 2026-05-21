<?php

class ContactMessage
{
    public static function create(array $data)
    {
        Database::query(
            'INSERT INTO contact_messages (name, phone, email, subject, message, status)
             VALUES (:name, :phone, :email, :subject, :message, "new")',
            [
                ':name' => trim($data['name'] ?? ''),
                ':phone' => trim($data['phone'] ?? ''),
                ':email' => trim($data['email'] ?? ''),
                ':subject' => trim($data['subject'] ?? ''),
                ':message' => trim($data['message'] ?? ''),
            ]
        );

        return Database::lastInsertId();
    }

    public static function all()
    {
        return Database::query('SELECT * FROM contact_messages ORDER BY created_at DESC')->fetchAll();
    }

    public static function count()
    {
        return (int) Database::query('SELECT COUNT(*) AS total FROM contact_messages')->fetch()['total'];
    }

    public static function unreadCount()
    {
        return (int) Database::query('SELECT COUNT(*) AS total FROM contact_messages WHERE status = "new"')->fetch()['total'];
    }

    public static function updateStatus($id, $status)
    {
        $allowed = ['new', 'read', 'resolved'];
        if (!in_array($status, $allowed, true)) {
            $status = 'new';
        }

        Database::query('UPDATE contact_messages SET status = :status WHERE id = :id', [
            ':id' => (int) $id,
            ':status' => $status,
        ]);
    }
}
