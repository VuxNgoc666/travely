<?php

class Favorite
{
    public static function toggle($userId, $tourId)
    {
        $existing = Database::query(
            'SELECT id FROM favorites WHERE user_id = :user_id AND tour_id = :tour_id',
            [':user_id' => $userId, ':tour_id' => $tourId]
        )->fetch();

        if ($existing) {
            Database::query('DELETE FROM favorites WHERE id = :id', [':id' => $existing['id']]);
            return false;
        }

        Database::query(
            'INSERT INTO favorites (user_id, tour_id) VALUES (:user_id, :tour_id)',
            [':user_id' => $userId, ':tour_id' => $tourId]
        );

        return true;
    }

    public static function exists($userId, $tourId)
    {
        return (bool) Database::query(
            'SELECT id FROM favorites WHERE user_id = :user_id AND tour_id = :tour_id',
            [':user_id' => $userId, ':tour_id' => $tourId]
        )->fetch();
    }

    public static function forUser($userId)
    {
        return Database::query(
            'SELECT tours.*
             FROM favorites
             JOIN tours ON tours.id = favorites.tour_id
             WHERE favorites.user_id = :user_id
             ORDER BY favorites.created_at DESC',
            [':user_id' => $userId]
        )->fetchAll();
    }
}
