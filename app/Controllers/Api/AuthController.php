<?php

namespace App\Controllers\Api;

use CodeIgniter\Shield\Authentication\Authenticators\Session;
use CodeIgniter\Shield\Entities\User;

class AuthController extends BaseApiController
{
    public function login()
    {
        $data = $this->getJsonBody();

        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|string',
        ];

        if (! $this->validateData($data, $rules)) {
            return $this->respondValidationErrors($this->validator->getErrors());
        }

        $credentials = [
            'email'    => (string) $data['email'],
            'password' => (string) $data['password'],
        ];

        /** @var Session $authenticator */
        $authenticator = auth('session')->getAuthenticator();
        $result        = $authenticator->attempt($credentials);

        if (! $result->isOK()) {
            return $this->failUnauthorized($result->reason() ?? 'Invalid credentials');
        }

        /** @var User $user */
        $user = $result->extraInfo();

        $token = $user->generateAccessToken('react');

        return $this->respond([
            'token_type' => 'Bearer',
            'token'      => $token->raw_token,
            'user'       => [
                'id'       => $user->id,
                'username' => $user->username,
                'email'    => $user->email,
            ],
        ]);
    }

    public function me()
    {
        $user = $this->currentUser();

        if ($user === null) {
            return $this->failUnauthorized('Unauthorized');
        }

        return $this->respond([
            'id'       => $user->id,
            'username' => $user->username,
            'email'    => $user->email,
        ]);
    }

    public function logout()
    {
        $user = $this->currentUser();

        if ($user === null) {
            return $this->failUnauthorized('Unauthorized');
        }

        // MVP approach: revoke all tokens for this user.
        $user->revokeAllAccessTokens();

        return $this->respond(['ok' => true]);
    }
}
