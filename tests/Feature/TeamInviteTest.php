<?php

namespace Tests\Feature;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamInviteTest extends TestCase
{
  use RefreshDatabase;

  public function test_team_member_can_invite_user(): void
  {
    $user = User::factory()->create();
    $team = Team::factory()->create();
    $team->users()->attach($user);

    $this->actingAs($user, 'sanctum');

    $response = $this->postJson("/api/teams/{$team->id}/invite", [
        'name' => 'Invited User',
        'email' => 'invite@example.com',
    ]);

    $response->assertCreated();
    $this->assertDatabaseHas('users', ['email' => 'invite@example.com']);
    $this->assertDatabaseHas('team_user', [
        'team_id' => $team->id,
        'user_id' => User::where('email', 'invite@example.com')->first()->id,
    ]);
  }
}