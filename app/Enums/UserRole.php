<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class UserRole extends Enum
{
    const SuperAdministrator = 1;
    const Administrator = 2;
    const Accountant = 3;
    const Consultant = 4;
    const Storekeeper = 5;
}
