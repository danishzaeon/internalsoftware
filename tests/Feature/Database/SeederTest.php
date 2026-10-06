<?php

namespace Tests\Feature\Database;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Service;
use App\Models\User;
use Database\Seeders\ServiceSeeder;
use Database\Seeders\SuperAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use RuntimeException;
use Tests\TestCase;

class SeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_seeder_creates_the_eight_services_and_is_idempotent(): void
    {
        (new ServiceSeeder)->run();
        (new ServiceSeeder)->run();

        $this->assertSame(8, Service::count());
        $this->assertTrue(Service::where('name', 'Google Business Profile')->exists());
    }

    public function test_service_seeder_does_not_overwrite_admin_edits(): void
    {
        (new ServiceSeeder)->run();
        Service::where('name', 'SEO')->update(['description' => 'Edited by admin']);

        (new ServiceSeeder)->run();

        $this->assertSame('Edited by admin', Service::where('name', 'SEO')->value('description'));
    }

    public function test_super_admin_seeder_creates_user_from_config(): void
    {
        config()->set('zaeon.super_admin', [
            'name' => 'Owner',
            'email' => 'Owner@Example.com',
            'password' => 'a-long-secret-1',
        ]);

        (new SuperAdminSeeder)->run();
        (new SuperAdminSeeder)->run(); // second run must not duplicate

        $user = User::where('role', UserRole::SuperAdmin->value)->sole();
        $this->assertSame('owner@example.com', $user->email);
        $this->assertSame(UserStatus::Active, $user->status);
        $this->assertNull($user->client_id);
        $this->assertNotSame('a-long-secret-1', $user->password);
        $this->assertTrue(Hash::check('a-long-secret-1', $user->password));
    }

    public function test_super_admin_seeder_fails_without_credentials(): void
    {
        config()->set('zaeon.super_admin', ['name' => null, 'email' => null, 'password' => null]);

        $this->expectException(RuntimeException::class);
        (new SuperAdminSeeder)->run();
    }

    public function test_super_admin_seeder_rejects_a_weak_password(): void
    {
        config()->set('zaeon.super_admin', ['name' => 'Owner', 'email' => 'o@example.com', 'password' => 'short']);

        $this->expectException(RuntimeException::class);
        (new SuperAdminSeeder)->run();
    }
}
