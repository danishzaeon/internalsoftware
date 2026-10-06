<?php

namespace Database\Seeders;

use App\Enums\ServiceStatus;
use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            'SEO' => 'Search engine optimization.',
            'Google Business Profile' => 'Google Business Profile setup, optimization and management.',
            'Google Ads' => 'Google Ads campaign setup and management.',
            'Meta Ads' => 'Facebook and Instagram advertising campaigns.',
            'Social Media' => 'Social media content and page management.',
            'Website Development' => 'Website design, development and maintenance.',
            'Video' => 'Video production and editing.',
            'AI Automation' => 'AI-powered workflow and business automation.',
        ];

        foreach ($services as $name => $description) {
            // firstOrCreate (not updateOrCreate) so re-seeding never overwrites edits made in the admin UI.
            // withTrashed so a soft-deleted service is not re-created and does not break the unique name index.
            Service::withTrashed()->firstOrCreate(
                ['name' => $name],
                ['description' => $description, 'status' => ServiceStatus::Active],
            );
        }
    }
}
