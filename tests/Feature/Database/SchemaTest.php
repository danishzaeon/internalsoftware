<?php

namespace Tests\Feature\Database;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_business_tables_exist(): void
    {
        $tables = [
            'users', 'services', 'leads', 'lead_activities', 'clients', 'client_contacts',
            'client_services', 'projects', 'project_members', 'tasks', 'task_comments',
            'task_attachments', 'content_items', 'content_attachments', 'content_approvals',
            'password_reset_tokens', 'sessions', 'cache', 'jobs',
        ];

        foreach ($tables as $table) {
            $this->assertTrue(Schema::hasTable($table), "Missing table: {$table}");
        }
    }

    public function test_approved_design_changes_are_present(): void
    {
        $this->assertTrue(Schema::hasColumn('users', 'client_id'));
        $this->assertTrue(Schema::hasColumns('content_items', ['assigned_to', 'version']));
        $this->assertTrue(Schema::hasColumn('content_approvals', 'content_version'));
        $this->assertTrue(Schema::hasTable('content_attachments'));
    }

    public function test_users_table_has_no_email_verified_at(): void
    {
        $this->assertFalse(Schema::hasColumn('users', 'email_verified_at'));
    }
}
