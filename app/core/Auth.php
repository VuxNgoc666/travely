<?php

class Auth
{
    private static $cachedUser = false;

    public static function user()
    {
        if (self::$cachedUser !== false) {
            return self::$cachedUser;
        }

        if (empty($_SESSION['user_id'])) {
            self::$cachedUser = null;
            return null;
        }

        self::$cachedUser = User::find($_SESSION['user_id']);
        return self::$cachedUser;
    }

    public static function check()
    {
        return self::user() !== null;
    }

    public static function isAdmin()
    {
        $user = self::user();
        return $user && $user['role'] === 'admin';
    }

    public static function attempt($email, $password)
    {
        $identifier = trim((string) $email);
        if ($identifier === 'admin@travely.local') {
            $identifier = 'admin';
        }

        $user = User::findByEmail($identifier);
        if (!$user && $identifier === 'admin') {
            $user = User::findByEmail('admin@travely.local');
        }

        if (!$user || !password_verify($password, $user['password'])) {
            return false;
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        self::$cachedUser = $user;
        return true;
    }

    public static function loginById($id)
    {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $id;
        self::$cachedUser = User::find($id);
    }

    public static function logout()
    {
        unset($_SESSION['user_id']);
        self::$cachedUser = null;
    }
}
