<?php

namespace Tests\Feature;

use App\Models\Position;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PositionTest extends TestCase
{
    use RefreshDatabase;

    private function authHeaders(): array
    {
        $user  = User::factory()->create();
        $token = $user->createToken('api-token')->plainTextToken;

        return ['Authorization' => "Bearer {$token}"];
    }

    private function createPosition(string $title = 'Software Engineer'): Position
    {
        return Position::create([
            'title'       => $title,
            'description' => 'Test description',
        ]);
    }

    public function test_can_get_all_positions(): void
    {
        $this->createPosition();

        $response = $this->withHeaders($this->authHeaders())
                         ->getJson('/api/v1/positions');

        $response->assertStatus(200);
    }

    public function test_can_create_position(): void
    {
        $response = $this->withHeaders($this->authHeaders())
                         ->postJson('/api/v1/positions', [
                             'title'       => 'Software Engineer',
                             'description' => 'Develops software',
                         ]);

        $response->assertStatus(201)
                 ->assertJsonFragment(['title' => 'Software Engineer']);

        $this->assertDatabaseHas('positions', ['title' => 'Software Engineer']);
    }

    public function test_cannot_create_duplicate_position(): void
    {
        $this->createPosition();

        $response = $this->withHeaders($this->authHeaders())
                         ->postJson('/api/v1/positions', [
                             'title' => 'Software Engineer',
                         ]);

        $response->assertStatus(422);
    }

    public function test_can_get_single_position(): void
    {
        $position = $this->createPosition();

        $response = $this->withHeaders($this->authHeaders())
                         ->getJson("/api/v1/positions/{$position->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment(['title' => 'Software Engineer']);
    }

    public function test_can_update_position(): void
    {
        $position = $this->createPosition();

        $response = $this->withHeaders($this->authHeaders())
                         ->putJson("/api/v1/positions/{$position->id}", [
                             'title' => 'Senior Software Engineer',
                         ]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['title' => 'Senior Software Engineer']);
    }

    public function test_can_delete_position(): void
    {
        $position = $this->createPosition();

        $response = $this->withHeaders($this->authHeaders())
                         ->deleteJson("/api/v1/positions/{$position->id}");

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Position deleted successfully.']);

        $this->assertDatabaseMissing('positions', ['id' => $position->id]);
    }

    public function test_returns_404_for_missing_position(): void
    {
        $response = $this->withHeaders($this->authHeaders())
                         ->getJson('/api/v1/positions/999');

        $response->assertStatus(404);
    }
}