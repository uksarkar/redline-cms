<?php

namespace RedlineCms\Request;

use RedlineCms\Core\Http\Request;

abstract class FormRequest extends Request
{
    private array $errors;

    public function __construct()
    {
        $this->errors = $this->validate();
    }

    public function isValid(): bool
    {
        return count($this->errors) === 0;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    protected abstract function validate(): array;
}
