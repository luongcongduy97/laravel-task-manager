<?php

namespace Tests\Feature;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_delete_team(): void
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        $team = Team::factory()->create();

        $this->actingAs($admin, 'sanctum');

        $response = $this->deleteJson("/api/teams/{$team->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('teams', ['id' => $team->id]);
    }
}