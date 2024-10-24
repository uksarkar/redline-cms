<?php

namespace RedlineCms\Entity\Enums;

enum PostStatus: int
{
    case DRAFTED = 0;
    case PUBLISHED = 1;
    case SCHEDULED = 2;
}
