<?php

namespace Database\Factories;

use App\Enums\ClientStatus;
use App\Enums\ClientType;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Client>
 */
class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition(): array
    {
        return [
            'name' => 'Dr. '.fake()->name(),
            'company_name' => fake()->company(),
            'client_type' => fake()->randomElement(ClientType::cases()),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->numerify('9#########'),
            'whatsapp' => fake()->numerify('9#########'),
            'website' => 'https://'.fake()->domainName(),
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'state' => fake()->state(),
            'status' => ClientStatus::Active,
            'account_manager_id' => null,
            'notes' => null,
        ];
    }

    public function prospect(): static
    {
        return $this->state(['status' => ClientStatus::Prospect]);
    }

    public function inactive(): static
    {
        return $this->state(['status' => ClientStatus::Inactive]);
    }
}
