<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Both seeders are idempotent and safe to re-run in production
     * (php artisan db:seed --force).
     */
    public function run(): void
    {
        $this->call([
            ServiceSeeder::class,
            SuperAdminSeeder::class,
        ]);
    }
}
