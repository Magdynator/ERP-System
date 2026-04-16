<?php

declare(strict_types=1);

namespace Erp\Core\Services;

use Erp\Core\Models\AuditLog;
use Erp\Core\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(
        protected AuditLogService $auditLogService
    ) {}

    public function attemptLogin(string $email, string $password, bool $remember = false): bool
    {
        if (! Auth::attempt(['email' => $email, 'password' => $password], $remember)) {
            return false;
        }

        request()->session()->regenerate();

        $this->auditLogService->logAction(
            'login',
            get_class(Auth::user()),
            Auth::id(),
            Auth::id(),
            ['ip_address' => request()->ip(), 'user_agent' => request()->userAgent()]
        );

        return true;
    }

    public function logout(): void
    {
        Auth::guard('web')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }

    public function apiLogin(string $email, string $password, ?string $deviceName = null): ?array
    {
        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            return null;
        }

        $token = $user->createToken($deviceName ?? 'api-token')->plainTextToken;

        return [
            'token' => $token,
            'user'  => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role,
            ],
        ];
    }

    public function apiLogout(): void
    {
        request()->user()->currentAccessToken()->delete();
    }

    public function register(string $name, string $email, string $password): User
    {
        return User::create([
            'name'     => $name,
            'email'    => $email,
            'password' => Hash::make($password),
        ]);
    }
}
