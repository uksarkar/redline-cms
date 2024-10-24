<?php

namespace RedlineCms\Service;

use DateTimeImmutable;

class AppDate
{
    public function __construct(private DateTimeImmutable $date) {}

    public function __toString()
    {
        return $this->date->format("Y-m-d h:i:s A");
    }
}
