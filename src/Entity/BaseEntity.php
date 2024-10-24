<?php

namespace RedlineCms\Entity;

use RedlineCms\Core\Http\Response;

abstract class BaseEntity
{
    protected array $hidden = [];

    public function toResponse(): Response
    {
        return Response::fromArray($this->toArray());
    }

    public function toArray(): array
    {
        $arr = [];

        $class = new \ReflectionClass($this);
        foreach ($class->getProperties() as $property) {
            $name = $property->getName();

            if($name !== "hidden" && $name[0] !== "_" && !in_array($name, $this->hidden)) {
                $arr[$name] = $property->getValue($this);
            }
        }

        return $arr;
    }
}
