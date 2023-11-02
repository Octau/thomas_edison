<?php

namespace App\Http\Controllers\User;

use App\Http\Resources\UserResource;
use App\Models\Passport\OauthClient;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Validation\ValidationException;
use Laravel\Passport\Exceptions\OAuthServerException;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Laravel\Passport\RefreshTokenRepository;
use Laravel\Passport\TokenRepository;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @group UserAuth
 */
class AuthController extends AccessTokenController
{
    public function getMe(Request $request): UserResource
    {
        return new UserResource($request->user());
    }

    /**
     * @bodyParam username string required
     * @bodyParam password string required
     */
    public function login(ServerRequestInterface $request): array
    {
        $body = $request->getParsedBody();
        $body['client_id'] = config('passport.clients.users.id');
        $body['client_secret'] = config('passport.clients.users.secret');
        $body['grant_type'] = 'password';
        $body['scope'] = '';
        $body['username'] = $body['email'];

        try {
            $result = json_decode($this->issueToken($request->withParsedBody($body))->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (OAuthServerException | Exception $exception) {
            throw new BadRequestHttpException(__('error.incorrect_credentials'));
        }

        return [
            'data' => $result,
            'message' => __('success.login_success')
        ];
    }

    /**
     * @bodyParam old_password string required
     * @bodyParam password string required
     * @bodyParam password_confirmation string required
     */
    public function changePassword(Request $request): UserResource
    {
        $input = $request->validate([
            'old_password' => 'required|string|min:8',
            'password'     => 'required|regex:/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/|confirmed',
        ]);
        $user = $request->user();

        if (!Hash::check($input['old_password'], $user->password)) {
            throw ValidationException::withMessages([
                'old_password' => [__('error.incorrect_password')],
            ]);
        }

        $user->password = Hash::make($input['password']);
        $user->save();

        return (new UserResource($request->user()))
            ->additional([
                'message' => __('success.change_password_success'),
            ]);
    }

    /**
     * @bodyParam refresh_token string required
     */
    public function refresh(ServerRequestInterface $request): array
    {
        $body = $request->getParsedBody();
        $body['client_id'] = config('passport.clients.users.id');
        $body['client_secret'] = config('passport.clients.users.secret');
        $body['grant_type'] = 'refresh_token';
        $body['scope'] = '';

        $key = 'refresh_token:'.$body['refresh_token'];

        $result = null;

        if (Redis::get($key) !== null) {
            $result = json_decode(Redis::get('refresh_token:' . $body['refresh_token']), true, 512, JSON_THROW_ON_ERROR);
        } else {
            Cache::lock($key, 2)->block(3, function () use ($key, $body, $request, &$result) {
                if (Redis::get($key) !== null) {
                    $result = json_decode(Redis::get('refresh_token:' . $body['refresh_token']), true, 512, JSON_THROW_ON_ERROR);
                } else {
                    try {
                        $result = json_decode($this->issueToken($request->withParsedBody($body))->getContent(), true, 512, JSON_THROW_ON_ERROR);
                        Redis::set($key, json_encode($result, JSON_THROW_ON_ERROR, 512), 'EX', 45);
                    } catch (OAuthServerException | \Exception $exception) {
                        $result = null;
                    }
                }
            });
        }

        if ($result === null) {
            throw new BadRequestHttpException(__('error.incorrect_refresh_token'));
        }

        return [
            'data' => $result,
            'message' => __('success.refresh_token_success')
        ];
    }

    public function revoke(): array
    {
        try {
            $tokenRepository = new TokenRepository();
            $refreshTokenRepository = new RefreshTokenRepository();
            $tokenId = auth()->user()->token()->id;
            $tokenRepository->revokeAccessToken($tokenId);
            $refreshTokenRepository->revokeRefreshTokensByAccessTokenId($tokenId);
        } catch (\Exception $e) {
            throw new BadRequestHttpException(__('error.logout_failed'));
        }

        return [
            'message' => __('success.logout_success')
        ];
    }

    public function getPermission(): array
    {
        return [
            'data' => auth()->user()->getPermissionsViaRoles()->map(fn ($x) => $x['id']),
        ];
    }
}
