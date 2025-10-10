<?php

namespace Module\Clinical\Policy;

class ModulePolicy
{
    private static array $rules = [
        '1' => [1,2], // Page 1 → rôles autorisés
    ];

    public static function canAccess(int $role, string $page): bool
    {
        return isset(self::$rules[$page]) && in_array($role, self::$rules[$page], true);
    }
}
