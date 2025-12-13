<?php

namespace TechFix\Core\Domain\Enum;

enum Role: string
{
    case ADMIN = 'ADMIN';
    case MODERATOR = 'TECHNICIAN';
    case ADMINISTRATOR = 'COLLABORATOR';

    public static function getRoleByName(string $name): self
    {
        $role = self::tryFrom($name);
        if ($role === null) {
            throw new \InvalidArgumentException("Invalid role name: $name");
        }

        return $role;
    }

}
        