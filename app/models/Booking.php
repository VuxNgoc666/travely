<?php

class Booking
{
    public static function create(array $data)
    {
        Database::query(
            'INSERT INTO bookings (tour_id, user_id, start_date, guests, full_name, phone, email, notes, total_price, status)
             VALUES (:tour_id, :user_id, :start_date, :guests, :full_name, :phone, :email, :notes, :total_price, "pending")',
            [
                ':tour_id' => (int) $data['tour_id'],
                ':user_id' => (int) $data['user_id'],
                ':start_date' => $data['start_date'],
                ':guests' => (int) $data['guests'],
                ':full_name' => trim($data['full_name']),
                ':phone' => trim($data['phone']),
                ':email' => trim($data['email']),
                ':notes' => trim($data['notes'] ?? ''),
                ':total_price' => (float) $data['total_price'],
            ]
        );

        return Database::lastInsertId();
    }

    public static function all()
    {
        return Database::query(
            'SELECT bookings.*, tours.title AS tour_title, users.name AS user_name
             FROM bookings
             JOIN tours ON tours.id = bookings.tour_id
             LEFT JOIN users ON users.id = bookings.user_id
             ORDER BY bookings.created_at DESC'
        )->fetchAll();
    }

    public static function forUser($userId)
    {
        return Database::query(
            'SELECT bookings.*, tours.title AS tour_title, tours.slug, tours.thumbnail, tours.destination
             FROM bookings
             JOIN tours ON tours.id = bookings.tour_id
             WHERE bookings.user_id = :user_id
             ORDER BY bookings.created_at DESC',
            [':user_id' => $userId]
        )->fetchAll();
    }

    public static function find($id)
    {
        return Database::query('SELECT * FROM bookings WHERE id = :id', [':id' => (int) $id])->fetch();
    }

    public static function updateStatus($id, $status)
    {
        $allowed = ['pending', 'confirmed', 'completed', 'cancelled'];
        if (!in_array($status, $allowed, true)) {
            $status = 'pending';
        }

        Database::query('UPDATE bookings SET status = :status WHERE id = :id AND status NOT IN ("completed", "cancelled")', [
            ':id' => $id,
            ':status' => $status,
        ]);
    }

    public static function count()
    {
        return (int) Database::query('SELECT COUNT(*) AS total FROM bookings')->fetch()['total'];
    }

    public static function countByStatus($status)
    {
        return (int) Database::query(
            'SELECT COUNT(*) AS total FROM bookings WHERE status = :status',
            [':status' => $status]
        )->fetch()['total'];
    }

    public static function revenue()
    {
        $row = Database::query('SELECT COALESCE(SUM(total_price), 0) AS total FROM bookings WHERE status IN ("confirmed", "completed")')->fetch();
        return (float) $row['total'];
    }

    public static function recent($limit = 5)
    {
        return Database::query(
            'SELECT bookings.*, tours.title AS tour_title
             FROM bookings
             JOIN tours ON tours.id = bookings.tour_id
             ORDER BY bookings.created_at DESC
             LIMIT ' . (int) $limit
        )->fetchAll();
    }
}
