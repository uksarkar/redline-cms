<?php

namespace RedlineCms\Entity\Enums;

enum UserStatus:int {
    case ACTIVE = 1;
    case DISABLED = 0;
}