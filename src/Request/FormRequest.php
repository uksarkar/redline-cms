<?php

namespace RedlineCms\Request;

use RedlineCms\Core\Http\Request;
use Valitron\Validator;

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

    private function formatErrors(array $errors): array
    {
        $formattedErrors = [];
        foreach ($errors as $field => $messages) {
            $formattedErrors[$field] = $messages[0];
        }
        return $formattedErrors;
    }

    protected function validate(): array
    {
        $v = new Validator($this->only($this->keys()));
        $v->rules($this->rules());

        if (!$v->validate()) {
            return $this->formatErrors($v->errors());
        }

        return [];
    }

    protected abstract function keys(): array;
    protected abstract function rules(): array;
}
