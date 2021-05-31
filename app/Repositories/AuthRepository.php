<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;

class AuthRepository
{
    /**
     * Procura um usuário pelo email e o autentica pela senha
     *
     * @param string $data
     * @return User|null
     */
    public function authenticate(array $data): array
    {
        $user = User::where('email', $data['email'])->first();
        if (!$user) {
            throw new AuthorizationException('Wrong credentials', 401);
        }

        if (!Hash::check($data['password'], $user->password)) {
            throw new AuthorizationException('Wrong credentials', 401);
        }

        $token = $user->createToken('myapp');

        return [
            'access_token' => $token->accessToken,
            'expires_at' => $token->token->expires_at
        ];
    }
}
