<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class SuperAdminSeeder extends Seeder
{
    /**
     * Creates the first super admin from SEED_SUPERADMIN_* values (read via config/zaeon.php so it
     * also works when the config is cached). Does nothing if a super admin already exists.
     * Remove SEED_SUPERADMIN_PASSWORD from .env after the first run.
     */
    public function run(): void
    {
        if (User::where('role', UserRole::SuperAdmin->value)->exists()) {
            $this->command?->info('A super admin already exists. Skipping SuperAdminSeeder.');

            return;
        }

        $name = trim((string) config('zaeon.super_admin.name'));
        $email = strtolower(trim((string) config('zaeon.super_admin.email')));
        $password = (string) config('zaeon.super_admin.password');

        if ($name === '' || $email === '' || $password === '') {
            throw new RuntimeException(
                'Set SEED_SUPERADMIN_NAME, SEED_SUPERADMIN_EMAIL and SEED_SUPERADMIN_PASSWORD in .env before seeding.'
            );
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('SEED_SUPERADMIN_EMAIL is not a valid email address.');
        }

        if (strlen($password) < 10) {
            throw new RuntimeException('SEED_SUPERADMIN_PASSWORD must be at least 10 characters.');
        }

        if (User::withTrashed()->where('email', $email)->exists()) {
            throw new RuntimeException("A (possibly deleted) user with email {$email} already exists. Restore it or use another email.");
        }

        $user = new User(['name' => $name, 'email' => $email, 'password' => $password]);
        $user->forceFill([
            'role' => UserRole::SuperAdmin,
            'status' => UserStatus::Active,
        ])->save();

        $this->command?->info("Super admin created: {$email}");
    }
}
