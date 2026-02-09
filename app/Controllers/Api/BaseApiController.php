<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Shield\Entities\User;

abstract class BaseApiController extends BaseController
{
    use ResponseTrait;

    /**
     * @return User|null
     */
    protected function currentUser(): ?User
    {
        /** @var User|null $user */
        $user = auth()->user();

        return $user;
    }

    /**
     * @param array<string, string> $errors
     */
    protected function respondValidationErrors(array $errors)
    {
        return $this->failValidationErrors($errors);
    }

    /**
     * @return array<string, mixed>
     */
    protected function getJsonBody(): array
    {
        $json = $this->request->getJSON(true);

        if (is_array($json)) {
            return $json;
        }

        return $this->request->getPost() ?? [];
    }
}
