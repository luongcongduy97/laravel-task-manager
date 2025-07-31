<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_team_member_can_create_task()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create();
        $project->team->users()->attach($user);

        $this->actingAs($user, 'sanctum');

        $response = $this->postJson("/api/projects/{$project->id}/tasks", [
            'name' => 'New Task',
            'status' => 'pending',
            'priority' => 'high',
            'due_date' => now()->addWeek()->toDateString(),
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('tasks', [
            'name' => 'New Task',
            'project_id' => $project->id,
        ]);
    }

    public function test_team_member_can_update_task()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create();
        $task->project->team->users()->attach($user);

        $this->actingAs($user, 'sanctum');

        $response = $this->putJson("/api/tasks/{$task->id}", [
            'name' => 'Updated Task',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'name' => 'Updated Task']);
    }

    public function test_team_member_can_delete_task()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create();
        $task->project->team->users()->attach($user);

        $this->actingAs($user, 'sanctum');

        $response = $this->deleteJson("/api/tasks/{$task->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }
}