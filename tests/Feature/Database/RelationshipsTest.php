<?php

namespace Tests\Feature\Database;

use App\Enums\ActivityType;
use App\Enums\ApprovalStatus;
use App\Enums\ClientServiceStatus;
use App\Models\Client;
use App\Models\ContentItem;
use App\Models\Lead;
use App\Models\Project;
use App\Models\Service;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RelationshipsTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_services_projects_and_members_resolve(): void
    {
        $service = Service::factory()->create();
        $manager = User::factory()->manager()->create();
        $staff = User::factory()->create();
        $client = Client::factory()->create(['account_manager_id' => $manager->id]);

        $client->clientServices()->create(['service_id' => $service->id, 'status' => ClientServiceStatus::Active]);

        $project = Project::factory()->for($client)->create([
            'service_id' => $service->id,
            'manager_id' => $manager->id,
        ]);
        $project->members()->attach($staff->id, ['role' => 'SEO executive']);

        $this->assertTrue($client->accountManager->is($manager));
        $this->assertTrue($client->hasActiveService($service->id));
        $this->assertCount(1, $client->activeClientServices);
        $this->assertTrue($project->client->is($client));
        $this->assertTrue($project->service->is($service));
        $this->assertTrue($project->manager->is($manager));
        $this->assertSame('SEO executive', $project->members->first()->pivot->role);
        $this->assertTrue($staff->projects->first()->is($project));
        $this->assertTrue($project->isAssignedTo($manager));
        $this->assertTrue($project->isAssignedTo($staff));
        $this->assertFalse($project->isAssignedTo(User::factory()->create()));
    }

    public function test_client_does_not_have_a_service_that_is_paused(): void
    {
        $service = Service::factory()->create();
        $client = Client::factory()->create();
        $client->clientServices()->create(['service_id' => $service->id, 'status' => ClientServiceStatus::Paused]);

        $this->assertFalse($client->hasActiveService($service->id));
    }

    public function test_tasks_subtasks_comments_and_attachments_resolve(): void
    {
        $manager = User::factory()->manager()->create();
        $staff = User::factory()->create();
        $project = Project::factory()->create(['manager_id' => $manager->id]);

        $task = Task::factory()->create([
            'project_id' => $project->id,
            'created_by' => $manager->id,
            'assigned_to' => $staff->id,
        ]);
        $subtask = Task::factory()->create([
            'project_id' => $project->id,
            'created_by' => $manager->id,
            'parent_task_id' => $task->id,
        ]);

        $task->comments()->forceCreate(['user_id' => $staff->id, 'comment' => 'Started work']);
        $task->attachments()->forceCreate([
            'uploaded_by' => $staff->id,
            'file_name' => 'report.pdf',
            'file_path' => 'tasks/abc123.pdf',
            'file_type' => 'application/pdf',
            'file_size' => 2048,
        ]);

        $this->assertTrue($task->project->is($project));
        $this->assertTrue($task->assignee->is($staff));
        $this->assertTrue($task->creator->is($manager));
        $this->assertTrue($subtask->parent->is($task));
        $this->assertTrue($subtask->isSubtask());
        $this->assertCount(1, $task->subtasks);
        $this->assertTrue($task->comments->first()->author->is($staff));
        $this->assertTrue($task->attachments->first()->uploader->is($staff));
        $this->assertSame('2.0 KB', $task->attachments->first()->humanSize());
        $this->assertArrayNotHasKey('file_path', $task->attachments->first()->toArray());
    }

    public function test_content_assignment_attachments_and_approvals_resolve(): void
    {
        $manager = User::factory()->manager()->create();
        $staff = User::factory()->create();
        $client = Client::factory()->create();
        $clientUser = User::factory()->forClient($client)->create();
        $project = Project::factory()->for($client)->create(['manager_id' => $manager->id]);

        $content = ContentItem::factory()->create([
            'project_id' => $project->id,
            'created_by' => $manager->id,
            'assigned_to' => $staff->id,
        ]);

        $content->attachments()->forceCreate([
            'uploaded_by' => $staff->id,
            'file_name' => 'creative.png',
            'file_path' => 'content/xyz.png',
            'file_type' => 'image/png',
            'file_size' => 1024,
        ]);
        $content->approvals()->forceCreate([
            'reviewer_id' => $clientUser->id,
            'content_version' => 1,
            'status' => ApprovalStatus::Approved,
            'comment' => 'Looks good',
            'reviewed_at' => now(),
        ]);

        $this->assertTrue($content->assignee->is($staff));
        $this->assertTrue($content->isAssignedTo($staff));
        $this->assertFalse($content->isAssignedTo($manager));
        $this->assertTrue($staff->assignedContentItems->first()->is($content));
        $this->assertTrue($manager->createdContentItems->first()->is($content));
        $this->assertTrue($content->project->is($project));
        $this->assertTrue($content->attachments->first()->isImage());
        $this->assertTrue($content->attachments->first()->content->is($content));
        $this->assertSame(ApprovalStatus::Approved, $content->latestApproval->status);
        $this->assertTrue($content->approvals->first()->reviewer->is($clientUser));
        $this->assertTrue($clientUser->client->is($client));
        $this->assertTrue($client->users->first()->is($clientUser));
    }

    public function test_lead_activities_and_conversion_link_resolve(): void
    {
        $service = Service::factory()->create();
        $manager = User::factory()->manager()->create();
        $client = Client::factory()->create();
        $lead = Lead::factory()->create([
            'service_id' => $service->id,
            'assigned_to' => $manager->id,
            'converted_client_id' => $client->id,
        ]);

        $lead->activities()->forceCreate([
            'user_id' => $manager->id,
            'type' => ActivityType::Call,
            'description' => 'Intro call',
        ]);

        $this->assertTrue($lead->service->is($service));
        $this->assertTrue($lead->assignee->is($manager));
        $this->assertTrue($lead->isConverted());
        $this->assertTrue($lead->convertedClient->is($client));
        $this->assertTrue($client->convertedLeads->first()->is($lead));
        $this->assertSame(ActivityType::Call, $lead->activities->first()->type);
        $this->assertTrue($manager->assignedLeads->first()->is($lead));
    }
}
