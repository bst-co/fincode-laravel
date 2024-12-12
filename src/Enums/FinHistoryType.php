<?php

namespace Fincode\Laravel\Enums;

enum FinHistoryType: int
{
    case INSERT = 1;
    case UPDATE = 2;
    case DELETE = 3;
    case RESTORE = 4;
    case FORCE_DELETE = 5;
}
