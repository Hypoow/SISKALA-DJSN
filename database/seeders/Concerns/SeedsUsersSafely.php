<?php

namespace Database\Seeders\Concerns;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

trait SeedsUsersSafely
{
    protected function seedUser(array $identity, array $attributes, string $defaultPassword = 'password'): User
    {
        $user = User::firstOrNew($identity);
        $isNew = !$user->exists;

        $user->fill($attributes);

        if ($isNew && blank($user->password)) {
            $user->password = Hash::make($defaultPassword);
        }

        $user->save();

        return $user;
    }
}
