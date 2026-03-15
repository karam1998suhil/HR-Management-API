<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EmployeeTest extends TestCase
{
    use RefreshDatabase;

    // helper: returns authenticated headers
    private function authHeaders(): array
    {
        $user  = User::factory()->create();
        $token = $user->createToken('api-token')->plainTextToken;

        return ['Authorization' => "Bearer {$token}"];
    }

    // helper: creates a founder
    private function createFounder(): Employee
    {
        return Employee::create([
            'name'       => 'John Founder',
            'email'      => 'founder@company.com',
            'salary'     => 10000,
            'manager_id' => null,
            'is_founder' => true,
        ]);
    }

    // helper: creates a regular employee under a manager
    private function createEmployee(int $managerId, string $name = 'Jane Employee'): Employee
    {
        return Employee::create([
            'name'       => $name,
            'email'      => strtolower(str_replace(' ', '.', $name)) . '@company.com',
            'salary'     => 5000,
            'manager_id' => $managerId,
            'is_founder' => false,
        ]);
    }

    // ── INDEX ────────────────────────────────────────────────────────

    public function test_can_get_all_employees(): void
    {
        $this->createFounder();

        $response = $this->withHeaders($this->authHeaders())
                         ->getJson('/api/v1/employees');

        $response->assertStatus(200)
                 ->assertJsonStructure(['data', 'total']);
    }

    public function test_unauthenticated_user_cannot_get_employees(): void
    {
        $response = $this->getJson('/api/v1/employees');

        $response->assertStatus(401);
    }

    // ── STORE ────────────────────────────────────────────────────────

    public function test_can_create_founder(): void
    {
        $response = $this->withHeaders($this->authHeaders())
                         ->postJson('/api/v1/employees', [
                             'name'       => 'John Founder',
                             'email'      => 'founder@company.com',
                             'salary'     => 10000,
                             'is_founder' => true,
                         ]);

        $response->assertStatus(201)
                 ->assertJsonFragment(['name' => 'John Founder', 'is_founder' => true]);

        $this->assertDatabaseHas('employees', ['email' => 'founder@company.com']);
    }

    public function test_cannot_create_second_founder(): void
    {
        $this->createFounder();

        $response = $this->withHeaders($this->authHeaders())
                         ->postJson('/api/v1/employees', [
                             'name'       => 'Second Founder',
                             'email'      => 'second@company.com',
                             'salary'     => 5000,
                             'is_founder' => true,
                         ]);

        $response->assertStatus(422)
                 ->assertJson(['message' => 'A founder already exists.']);
    }

    public function test_can_create_employee_with_manager(): void
    {
        Mail::fake();

        $founder = $this->createFounder();

        $response = $this->withHeaders($this->authHeaders())
                         ->postJson('/api/v1/employees', [
                             'name'       => 'Jane Employee',
                             'email'      => 'jane@company.com',
                             'salary'     => 5000,
                             'manager_id' => $founder->id,
                         ]);

        $response->assertStatus(201)
                 ->assertJsonFragment(['name' => 'Jane Employee']);

        $this->assertDatabaseHas('employees', ['email' => 'jane@company.com']);
    }

    public function test_cannot_create_employee_without_manager(): void
    {
        $response = $this->withHeaders($this->authHeaders())
                         ->postJson('/api/v1/employees', [
                             'name'   => 'Jane Employee',
                             'email'  => 'jane@company.com',
                             'salary' => 5000,
                         ]);

        $response->assertStatus(422);
    }

    // ── SHOW ─────────────────────────────────────────────────────────

    public function test_can_get_single_employee(): void
    {
        $founder = $this->createFounder();

        $response = $this->withHeaders($this->authHeaders())
                         ->getJson("/api/v1/employees/{$founder->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => 'John Founder']);
    }

    public function test_returns_404_for_missing_employee(): void
    {
        $response = $this->withHeaders($this->authHeaders())
                         ->getJson('/api/v1/employees/999');

        $response->assertStatus(404);
    }

    // ── UPDATE ───────────────────────────────────────────────────────

    public function test_can_update_employee(): void
    {
        $founder = $this->createFounder();

        $response = $this->withHeaders($this->authHeaders())
                         ->putJson("/api/v1/employees/{$founder->id}", [
                             'name' => 'Updated Founder',
                         ]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => 'Updated Founder']);
    }

    public function test_can_update_employee_salary(): void
    {
        $founder = $this->createFounder();
    
        $response = $this->withHeaders($this->authHeaders())
                         ->putJson("/api/v1/employees/{$founder->id}", [
                             'salary' => 99999,
                         ]);
    
        $response->assertStatus(200)
                 ->assertJsonFragment(['salary' => '99999.00']); // ← string not float
    }

    // ── DESTROY ──────────────────────────────────────────────────────

    public function test_can_delete_employee(): void
    {
        $founder  = $this->createFounder();
        $employee = $this->createEmployee($founder->id);

        $response = $this->withHeaders($this->authHeaders())
                         ->deleteJson("/api/v1/employees/{$employee->id}");

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Employee deleted successfully.']);

        $this->assertSoftDeleted('employees', ['id' => $employee->id]);
    }

    // ── SEARCH ───────────────────────────────────────────────────────

    public function test_can_search_employees_by_name(): void
    {
        $founder = $this->createFounder();
        $this->createEmployee($founder->id, 'Alice Smith');

        $response = $this->withHeaders($this->authHeaders())
                         ->getJson('/api/v1/employees/search?name=Alice');

        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => 'Alice Smith']);
    }

    public function test_search_returns_all_when_no_params(): void
    {
        $founder = $this->createFounder();
        $this->createEmployee($founder->id);

        $response = $this->withHeaders($this->authHeaders())
                         ->getJson('/api/v1/employees/search');

        $response->assertStatus(200);
        $this->assertEquals(2, $response->json('count'));
    }

    // ── HIERARCHY ────────────────────────────────────────────────────

    public function test_can_get_employee_hierarchy(): void
    {
        $founder  = $this->createFounder();
        $employee = $this->createEmployee($founder->id);

        $response = $this->withHeaders($this->authHeaders())
                         ->getJson("/api/v1/employees/{$employee->id}/hierarchy");

        $response->assertStatus(200)
                 ->assertJsonStructure(['hierarchy'])
                 ->assertJsonFragment(['hierarchy' => ['John Founder', 'Jane Employee']]);
    }

    public function test_can_get_hierarchy_with_salary(): void
    {
        $founder  = $this->createFounder();
        $employee = $this->createEmployee($founder->id);

        $response = $this->withHeaders($this->authHeaders())
                         ->getJson("/api/v1/employees/{$employee->id}/hierarchy-with-salary");

        $response->assertStatus(200)
                 ->assertJsonStructure(['hierarchy']);
    }

    // ── NO SALARY CHANGE ─────────────────────────────────────────────

    public function test_can_get_employees_without_recent_salary_change(): void
    {
        $founder = $this->createFounder();
        $this->createEmployee($founder->id);

        $response = $this->withHeaders($this->authHeaders())
                         ->getJson('/api/v1/employees/no-salary-change/3');

        $response->assertStatus(200)
                 ->assertJsonStructure(['months', 'cutoff', 'count', 'employees']);
    }

    // ── EXPORT CSV ───────────────────────────────────────────────────

    public function test_can_export_employees_to_csv(): void
    {
        $this->createFounder();

        $response = $this->withHeaders($this->authHeaders())
                         ->get('/api/v1/employees/export/csv');

        $response->assertStatus(200)
                 ->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    // ── IMPORT CSV ───────────────────────────────────────────────────

    public function test_can_import_employees_from_csv(): void
    {
        $csvContent = "name,email,salary,manager_id,is_founder\n";
        $csvContent .= "\"Import Founder\",import@company.com,10000,,1\n";

        $file = UploadedFile::fake()->createWithContent('employees.csv', $csvContent);

        $response = $this->withHeaders($this->authHeaders())
                         ->post('/api/v1/employees/import/csv', [
                             'file' => $file,
                         ]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['imported' => 1]);
    }

    // ── LOGS ─────────────────────────────────────────────────────────

    public function test_can_get_employee_logs(): void
    {
        $founder = $this->createFounder();

        $response = $this->withHeaders($this->authHeaders())
                         ->getJson("/api/v1/employees/{$founder->id}/logs");

        $response->assertStatus(200)
                 ->assertJsonStructure(['employee', 'count', 'logs']);
    }
}