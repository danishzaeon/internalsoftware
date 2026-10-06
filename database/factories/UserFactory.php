<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    protected static ?string $password = null;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'phone' => fake()->numerify('9#########'),
            'role' => UserRole::Staff,
            'status' => UserStatus::Active,
            'remember_token' => Str::random(10),
        ];
    }

    public function superAdmin(): static
    {
        return $this->state(['role' => UserRole::SuperAdmin]);
    }

    public function admin(): static
    {
        return $this->state(['role' => UserRole::Admin]);
    }

    public function manager(): static
    {
        return $this->state(['role' => UserRole::Manager]);
    }

    public function staff(): static
    {
        return $this->state(['role' => UserRole::Staff]);
    }

    /** A client-portal login linked to the given client (or a freshly created one). */
    public function forClient(Client|int|null $client = null): static
    {
        return $this->state(fn () => [
            'role' => UserRole::Client,
            'client_id' => $client ?? Client::factory(),
        ]);
    }

    public function inactive(): static
    {
        return $this->state(['status' => UserStatus::Inactive]);
    }

    public function suspended(): static
    {
        return $this->state(['status' => UserStatus::Suspended]);
    }
}
