<?php

namespace Tests\Feature;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTeamTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_endpoint_returns_teams()
    {
        $user = User::factory()->create();
        $team = Team::factory()->create();
        $team->users()->attach($user);

        $this->actingAs($user, 'sanctum');

        $response = $this->getJson('/api/user');

        $response->assertOk();
        $response->assertJsonPath('id', $user->id);
        $response->assertJsonCount(1, 'teams');
        $response->assertJsonPath('teams.0.id', $team->id);
    }
}