<?php

namespace RedlineCms\Request;

use RedlineCms\Core\Support\App;
use RedlineCms\Repository\CategoryRepository;
use RedlineCms\Service\Str;

class CategoryStoreRequest extends FormRequest
{
    protected function keys(): array
    {
        return ["name"];
    }

    protected function rules(): array
    {
        return [
            "required" => $this->keys(),
            'lengthMax' => [
                ['name', 255]
            ],
        ];
    }

    protected function validate(): array
    {
        if(count($errors = parent::validate())) {
            return $errors;
        }

        if ($slug = $this->getBody("slug")) {
            $slug = Str::url_safe_string(trim($slug));
            
            $isTaken = App::resolve(CategoryRepository::class)->slugExists($slug, $this->getParam("id"));
            if ($isTaken) {
                $errors["slug"] = "The slug is taken";
                return $errors;
            }
            
            $this->setBody("slug", $slug);
        }

        return $errors;
    }
}
