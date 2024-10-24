<?php

namespace RedlineCms\Entity\Enums;

enum UserRole:int {
    case GUEST = 0;
    case USER = 1;
    case ADMIN = 2;
}