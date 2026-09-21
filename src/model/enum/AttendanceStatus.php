<?php

declare(strict_types=1);

namespace Carlos\TechAcademy3\Model\Enum;

enum AttendanceStatus: int
{
    case PRESENT = 1;
    case ABSENT = 2;
}
