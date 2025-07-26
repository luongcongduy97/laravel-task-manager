<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_team_member_can_create_project()
    {
        $user = User::factory()->create();
        $team = Team::factory()->create();
        $team->users()->attach($user);

        $this->actingAs($user, 'sanctum');

        $response = $this->postJson("/api/teams/{$team->id}/projects", [
            'name' => 'New Project',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('projects', ['name' => 'New Project', 'team_id' => $team->id]);
    }

    public function test_team_member_can_update_project()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create();
        $project->team->users()->attach($user);

        $this->actingAs($user, 'sanctum');

        $response = $this->putJson("/api/projects/{$project->id}", [
            'name' => 'Updated Project',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('projects', ['id' => $project->id, 'name' => 'Updated Project']);
    }

    public function test_team_member_can_delete_project()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create();
        $project->team->users()->attach($user);

        $this->actingAs($user, 'sanctum');

        $response = $this->deleteJson("/api/projects/{$project->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }
}