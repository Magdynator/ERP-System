<?php

declare(strict_types=1);

namespace Erp\Core\Http\Controllers\Web;

use Erp\Core\Http\Controllers\Controller;
use Erp\Core\Http\Requests\RegisterRequest;
use Erp\Core\Services\AuthService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}

    public function create(): View
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = $this->authService->register($validated['name'], $validated['email'], $validated['password']);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
