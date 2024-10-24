<?php

namespace RedlineCms\Request;

use RedlineCms\Core\Http\UploadFile;
use RedlineCms\Core\Support\App;
use RedlineCms\Entity\Enums\PostStatus;
use RedlineCms\Repository\PostRepository;
use RedlineCms\Service\Str;

class StorePostRequest extends FormRequest
{
    protected function validate(): array
    {
        $errors = [];

        if (!$this->getBody("title")) {
            $errors["title"] = "Title is required";
            return $errors;
        }

        if (!$this->getBody("content")) {
            $errors["content"] = "Content is required";
            return $errors;
        }

        $status = $this->getBody("status");

        if (ctype_digit($status) && !PostStatus::tryFrom((int) $status)) {
            $errors["status"] = "Invalid status";
            return $errors;
        } elseif (ctype_digit($status)) {
            $this->setBody("status", PostStatus::from((int) $status));
        } else {
            $this->setBody("status", PostStatus::PUBLISHED);
        }

        if ($slug = $this->getBody("slug")) {
            $slug = Str::url_safe_string(trim($slug));

            $isTaken = App::resolve(PostRepository::class)->slugExists($slug, $this->getParam("id"));
            if ($isTaken) {
                $errors["slug"] = "The slug is taken";
                return $errors;
            }

            $this->setBody("slug", $slug);
        }

        if($categoryId = $this->getBody("category_id")) {
            $this->setBody("category_id", (int) $categoryId);
        }

        $image = UploadFile::get("image");

        if ($image && !in_array($image->ext, ["jpg", "jpeg", "png"])) {
            $errors["image"] = "Image format should be in jpg, jpeg, and png";
            return $errors;
        }

        return $errors;
    }
}
