<?php

declare(strict_types=1);

namespace Erp\Core\Http\Controllers\Web;

use Erp\Core\Http\Controllers\Controller;
use Erp\Core\Http\Requests\LoginRequest;
use Erp\Core\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}

    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {

        if (! $this->authService->attemptLogin($request->email, $request->password, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $this->authService->logout();

        return redirect('/');
    }
}
