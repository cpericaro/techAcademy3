<?php

declare(strict_types=1);

namespace Carlos\TechAcademy3\Model\Enum;

enum AccountType: int
    {
    case TEACHER = 1;
    case PARENT = 2;
    Case ADMIN = 3;
    }

    