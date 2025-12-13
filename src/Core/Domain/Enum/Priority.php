<?php

namespace TechFix\Core\Domain\Enum;

enum Priority: string
{
    case HIGH = 'HIGH';
    case MEDIUM = 'MEDIUM';
    case LOW = 'LOW';

    public static function getPriorityByName(string $name): self
    {
        $priority = self::tryFrom($name);
        if ($priority === null) {
            throw new \InvalidArgumentException("Invalid priority name: $name");
        }

        return $priority;
    }

}
  