<?php

namespace Tests\Feature\Database;

use App\Enums\ApprovalStatus;
use App\Enums\ContentStatus;
use App\Enums\UserRole;
use App\Models\Client;
use App\Models\ContentItem;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use LogicException;
use Tests\TestCase;

class IntegrityTest extends TestCase
{
    use RefreshDatabase;

    public function test_duplicate_project_member_is_rejected_by_the_database(): void
    {
        $project = Project::factory()->create();
        $user = User::factory()->create();

        $project->members()->attach($user->id);

        $this->expectException(QueryException::class);
        $project->members()->attach($user->id);
    }

    public function test_client_with_projects_cannot_be_hard_deleted(): void
    {
        $client = Client::factory()->create();
        Project::factory()->for($client)->create();

        $this->expectException(QueryException::class);
        $client->forceDelete();
    }

    public function test_project_with_tasks_cannot_be_hard_deleted(): void
    {
        $project = Project::factory()->create();
        Task::factory()->create(['project_id' => $project->id]);

        $this->expectException(QueryException::class);
        $project->forceDelete();
    }

    public function test_client_role_user_requires_a_client(): void
    {
        $this->expectException(InvalidArgumentException::class);
        User::factory()->create(['role' => UserRole::Client]);
    }

    public function test_non_client_user_cannot_be_linked_to_a_client(): void
    {
        $client = Client::factory()->create();

        $this->expectException(InvalidArgumentException::class);
        User::factory()->create(['client_id' => $client->id]);
    }

    public function test_valid_client_user_can_be_created(): void
    {
        $user = User::factory()->forClient()->create();

        $this->assertTrue($user->isClient());
        $this->assertNotNull($user->client);
    }

    public function test_role_status_and_client_id_are_not_mass_assignable(): void
    {
        $user = new User(['name' => 'X', 'role' => 'super_admin', 'status' => 'suspended', 'client_id' => 5]);

        $this->assertSame(UserRole::Staff, $user->role);
        $this->assertNull($user->client_id);
    }

    public function test_content_status_and_version_are_not_mass_assignable(): void
    {
        $content = new ContentItem(['title' => 'Post', 'status' => 'published', 'version' => 9]);

        $this->assertSame(ContentStatus::Draft, $content->status);
        $this->assertSame(1, $content->version);
    }

    public function test_content_defaults_to_draft_version_one(): void
    {
        $content = ContentItem::factory()->create()->refresh();

        $this->assertSame(ContentStatus::Draft, $content->status);
        $this->assertSame(1, $content->version);
        $this->assertNull($content->assigned_to);
    }

    public function test_content_approvals_are_append_only(): void
    {
        $reviewer = User::factory()->forClient()->create();
        $content = ContentItem::factory()->create();
        $approval = $content->approvals()->forceCreate([
            'reviewer_id' => $reviewer->id,
            'content_version' => 1,
            'status' => ApprovalStatus::Revision,
            'comment' => 'Change the headline',
            'reviewed_at' => now(),
        ]);

        try {
            $approval->update(['comment' => 'edited']);
            $this->fail('Approval update should have been blocked.');
        } catch (LogicException) {
            $this->assertTrue(true);
        }

        $this->expectException(LogicException::class);
        $approval->delete();
    }

    public function test_user_email_stays_reserved_after_soft_delete(): void
    {
        $user = User::factory()->create(['email' => 'same@example.com']);
        $user->delete();

        $this->expectException(QueryException::class);
        User::factory()->create(['email' => 'same@example.com']);
    }
}
