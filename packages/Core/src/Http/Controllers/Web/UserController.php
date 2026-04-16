<?php

namespace Erp\Core\Http\Controllers\Web;

use Erp\Core\Http\Controllers\Controller;
use Erp\Core\Http\Requests\StoreUserRequest;
use Erp\Core\Http\Requests\UpdateUserRequest;
use Erp\Core\Models\User;
use Erp\Core\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {}

    public function index()
    {
        $users = $this->userService->getPaginatedUsers(15);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = [
            (object) ['name' => 'super_admin'],
            (object) ['name' => 'admin'],
            (object) ['name' => 'user'],
        ];

        return view('users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();

        $this->userService->createUser($validated['name'], $validated['email'], $validated['password'], $validated['role']);

        return redirect()->route('web.users.index')->with('success', 'User added successfully.');
    }

    public function edit(User $user)
    {
        $roles = [
            (object) ['name' => 'super_admin'],
            (object) ['name' => 'admin'],
            (object) ['name' => 'user'],
        ];

        return view('users.edit', compact('user', 'roles'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $validated = $request->validated();

        $this->userService->updateUser($user, $validated['name'], $validated['email'], $validated['password'] ?? null, $validated['role']);

        return redirect()->route('web.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $this->userService->deleteUser($user);

        return redirect()->route('web.users.index')->with('success', 'User deleted successfully.');
    }
}
