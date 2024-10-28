<?php

namespace RedlineCms\Request;

use RedlineCms\Core\Support\App;
use RedlineCms\Repository\UserRepository;

class UserUpdateRequest extends FormRequest
{
    protected function keys(): array
    {
        return ["password", "email", "username"];
    }

    protected function rules(): array
    {
        $required = [];

        foreach ($this->keys() as $key) {
            if ($key === "password" && $this->has($key) && !$this->getBody("password")) {
                $this->remove($key);
                continue;
            }

            if ($this->has($key)) {
                $required[] = $key;
            }
        }

        return [
            'required' => $required,
            'lengthMin' => [
                ['password', 6],
                ['username', 4]
            ],
            'lengthMax' => [
                ['password', 255],
                ['username', 255]
            ],
            'email' => 'email',
            'regex' => [['username', '/^[a-zA-Z]+$/']]
        ];
    }

    protected function validate(): array
    {
        $errors = parent::validate();

        if (count($errors)) {
            return $errors;
        }

        // check for unique email
        /** @var UserRepository */
        $repo = App::resolve(UserRepository::class);

        if ($this->has("email") && $repo->emailExists($this->getBody("email"), (int) $this->getParam("id"))) {
            return ["email" => "The email is taken"];
        }

        if ($this->has("username") && $repo->usernameExists($this->getBody("username"), (int) $this->getParam("id"))) {
            return ["username" => "The username is taken"];
        }

        return [];
    }
}
