<?php

declare(strict_types=1);

namespace Erp\Core\Services;

use Erp\Core\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function getPaginatedUsers(int $perPage = 15): LengthAwarePaginator
    {
        return User::paginate($perPage);
    }

    public function createUser(string $name, string $email, string $password, string $role): User
    {
        $user = User::create([
            'name'     => $name,
            'email'    => $email,
            'password' => Hash::make($password),
        ]);

        $user->role = $role;
        $user->save();

        return $user;
    }

    public function updateUser(User $user, string $name, string $email, ?string $password, string $role): User
    {
        $user->name  = $name;
        $user->email = $email;
        if (! empty($password)) {
            $user->password = Hash::make($password);
        }
        $user->role = $role;
        $user->save();

        return $user;
    }

    public function deleteUser(User $user): void
    {
        $user->delete();
    }
}
