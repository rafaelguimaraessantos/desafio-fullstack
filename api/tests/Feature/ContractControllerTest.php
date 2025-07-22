<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Plan;
use App\Models\Contract;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContractControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_contract()
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create(['price' => 100.00]);

        $response = $this->postJson('/api/contracts', [
            'user_id' => $user->id,
            'plan_id' => $plan->id,
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'contract' => [
                        'id',
                        'user_id',
                        'plan_id',
                        'active',
                        'contract_date',
                        'plan'
                    ],
                    'payment_id',
                    'payment_amount',
                    'discount'
                ]);

        $this->assertDatabaseHas('contracts', [
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'active' => true,
        ]);
    }

    public function test_can_get_active_contract()
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();
        
        $contract = Contract::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'active' => true,
            'contract_date' => now(),
        ]);

        $response = $this->getJson("/api/contracts/{$user->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'id' => $contract->id,
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                    'active' => true,
                ]);
    }

    public function test_returns_404_when_no_active_contract()
    {
        $user = User::factory()->create();

        $response = $this->getJson("/api/contracts/{$user->id}");

        $response->assertStatus(404)
                ->assertJson([
                    'message' => 'No active contract found'
                ]);
    }

    public function test_can_get_contract_history()
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();
        
        // Criar múltiplos contratos
        Contract::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'active' => false,
            'contract_date' => now()->subDays(10),
        ]);

        Contract::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'active' => true,
            'contract_date' => now(),
        ]);

        $response = $this->getJson("/api/contracts/{$user->id}/history");

        $response->assertStatus(200)
                ->assertJsonCount(2);
    }

    public function test_contract_creation_requires_valid_data()
    {
        $response = $this->postJson('/api/contracts', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['plan_id', 'user_id']);
    }
}