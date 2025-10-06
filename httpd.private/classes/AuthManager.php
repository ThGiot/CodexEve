<?php
class AuthManager {
    public static function checkAuth() {
        if (!isset($_SESSION['user'])) {
            exit('Non authentifié');
        }
        return true;
    }

    public static function getUserId() {
        return $_SESSION['user']['id'] ?? null;
    }
}
