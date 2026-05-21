<?php

class User
{
    public static function find($id)
    {
        return Database::query('SELECT * FROM users WHERE id = :id', [':id' => $id])->fetch();
    }

    public static function findByEmail($email)
    {
        return Database::query('SELECT * FROM users WHERE email = :email', [':email' => $email])->fetch();
    }

    public static function create(array $data)
    {
        Database::query(
            'INSERT INTO users (name, email, phone, password, role) VALUES (:name, :email, :phone, :password, :role)',
            [
                ':name' => trim($data['name'] ?? ''),
                ':email' => trim($data['email'] ?? ''),
                ':phone' => trim($data['phone'] ?? ''),
                ':password' => password_hash($data['password'] ?? '', PASSWORD_DEFAULT),
                ':role' => $data['role'] ?? 'user',
            ]
        );

        return Database::lastInsertId();
    }

    public static function all()
    {
        return Database::query('SELECT id, name, email, phone, role, created_at FROM users ORDER BY created_at DESC')->fetchAll();
    }

    public static function updateRole($id, $role)
    {
        Database::query('UPDATE users SET role = :role WHERE id = :id', [
            ':id' => $id,
            ':role' => $role === 'admin' ? 'admin' : 'user',
        ]);
    }

    public static function delete($id)
    {
        Database::query('DELETE FROM users WHERE id = :id AND role <> "admin"', [':id' => $id]);
    }

    public static function count()
    {
        return (int) Database::query('SELECT COUNT(*) AS total FROM users')->fetch()['total'];
    }
}

