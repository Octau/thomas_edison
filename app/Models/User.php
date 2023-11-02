<?php

namespace App\Models;

use App\Models\Abstract\BaseUuidUser;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Laravel\Passport\RefreshTokenRepository;
use Laravel\Passport\TokenRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Watson\Validating\ValidatingTrait;
use Exception;

class User extends BaseUuidUser
{
    use Notifiable;
    use ValidatingTrait;
    use HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function setEmailAttribute(string $value): void
    {
        $this->attributes['email'] = strtolower($value);
    }

    public function findForPassport(string $username): self
    {
        return $this->where('email', $username)->firstOrFail();
    }

    public function getRules(): array
    {
        return [
            'email'     => 'required|email|unique:users,email,'.$this->id,
            'password'  => 'required|string|min:6',
            'name'      => 'required|string',
        ];
    }

    public function purchase() {
        return $this->hasMany('App\Models\Purchase');
    }

    public function revoke(): void
    {
        try {
            $tokenRepository = new TokenRepository();
            $refreshTokenRepository = new RefreshTokenRepository();
            foreach ($this->tokens()->get()->where('revoked', false) ?? [] as $token) {
                $tokenRepository->revokeAccessToken($token->id);
                $refreshTokenRepository->revokeRefreshTokensByAccessTokenId($token->id);
            }
        } catch (Exception $e) {
            throw new BadRequestHttpException(__('error.revoke_failed'));
        }
    }
}
