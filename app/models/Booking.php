<?php

class Booking
{
    public static function create(array $data)
    {
        $paymentReference = self::generatePaymentReference();
        Database::query(
            'INSERT INTO bookings (tour_id, user_id, start_date, guests, full_name, phone, email, notes, total_price, status, payment_method, payment_status, payment_reference, transaction_code, paid_at)
             VALUES (:tour_id, :user_id, :start_date, :guests, :full_name, :phone, :email, :notes, :total_price, "pending", NULL, "unpaid", :payment_reference, NULL, NULL)',
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
                ':payment_reference' => $paymentReference,
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

    public static function markPaid($id, array $data)
    {
        Database::query(
            'UPDATE bookings
             SET payment_method = :payment_method,
                 payment_status = "paid",
                 transaction_code = :transaction_code,
                 paid_at = CURRENT_TIMESTAMP,
                 status = "confirmed"
             WHERE id = :id AND status NOT IN ("completed", "cancelled")',
            [
                ':id' => (int) $id,
                ':payment_method' => in_array(($data['payment_method'] ?? ''), ['bank_transfer', 'card', 'ewallet'], true)
                    ? $data['payment_method']
                    : 'bank_transfer',
                ':transaction_code' => trim((string) ($data['transaction_code'] ?? '')),
            ]
        );
    }

    public static function paymentReferenceExists($reference)
    {
        return (bool) Database::query(
            'SELECT id FROM bookings WHERE payment_reference = :payment_reference',
            [':payment_reference' => $reference]
        )->fetch();
    }

    private static function generatePaymentReference()
    {
        do {
            $reference = 'TVY' . strtoupper(bin2hex(random_bytes(4)));
        } while (self::paymentReferenceExists($reference));

        return $reference;
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
        $row = Database::query('SELECT COALESCE(SUM(total_price), 0) AS total FROM bookings WHERE payment_status = "paid"')->fetch();
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
