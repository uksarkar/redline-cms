<?php

namespace RedlineCms\Request;

use RedlineCms\Core\Support\App;
use RedlineCms\Repository\UserRepository;

class UserStoreRequest extends FormRequest
{
    protected function keys(): array
    {
        return ["password", "email", "username"];
    }

    protected function rules(): array
    {
        return [
            'required' => $this->keys(),
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
        if(count($errors = parent::validate())) {
            return $errors;
        }

        // check for unique email
        /** @var UserRepository */
        $repo = App::resolve(UserRepository::class);

        if ($repo->emailExists($this->getBody("email"))) {
            return ["email" => "The email is taken"];
        }

        if ($repo->usernameExists($this->getBody("username"))) {
            return ["username" => "The username is taken"];
        }

        return [];
    }
}
