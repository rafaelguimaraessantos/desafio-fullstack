<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Plan;
use App\Models\Contract;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreditCalculationTest extends TestCase
{
    use RefreshDatabase;

    public function test_credit_calculation_for_plan_upgrade()
    {
        // Simular cenário: usuário contratou plano de R$ 100 no dia 1
        // No dia 15 (metade do mês) quer trocar para plano de R$ 200
        // Deve pagar R$ 150 (R$ 200 - R$ 50 de crédito)
        
        $user = User::factory()->create();
        $oldPlan = Plan::factory()->create(['price' => 100.00]);
        $newPlan = Plan::factory()->create(['price' => 200.00]);
        
        // Contrato antigo (15 dias atrás)
        $oldContract = Contract::create([
            'user_id' => $user->id,
            'plan_id' => $oldPlan->id,
            'active' => true,
            'contract_date' => Carbon::now()->subDays(15),
        ]);

        // Simular troca de plano
        $response = $this->postJson('/api/contracts', [
            'user_id' => $user->id,
            'plan_id' => $newPlan->id,
        ]);

        $response->assertStatus(200);
        
        $data = $response->json();
        
        // Verificar se o desconto foi aplicado corretamente
        $this->assertLessThan(200.00, $data['payment_amount']);
        $this->assertGreaterThan(0, $data['discount']);
    }

    public function test_credit_calculation_for_plan_downgrade()
    {
        // Simular cenário: usuário contratou plano de R$ 200 no dia 1
        // No dia 15 quer trocar para plano de R$ 100
        // Deve pagar R$ 0 (crédito de R$ 100 cobre o novo plano)
        
        $user = User::factory()->create();
        $oldPlan = Plan::factory()->create(['price' => 200.00]);
        $newPlan = Plan::factory()->create(['price' => 100.00]);
        
        // Contrato antigo (15 dias atrás)
        $oldContract = Contract::create([
            'user_id' => $user->id,
            'plan_id' => $oldPlan->id,
            'active' => true,
            'contract_date' => Carbon::now()->subDays(15),
        ]);

        // Simular troca de plano
        $response = $this->postJson('/api/contracts', [
            'user_id' => $user->id,
            'plan_id' => $newPlan->id,
        ]);

        $response->assertStatus(200);
        
        $data = $response->json();
        
        // Verificar se o valor final é menor que o preço do plano
        $this->assertLessThanOrEqual(100.00, $data['payment_amount']);
        $this->assertGreaterThan(0, $data['discount']);
    }

    public function test_no_credit_for_first_contract()
    {
        // Primeiro contrato não deve ter desconto
        
        $user = User::factory()->create();
        $plan = Plan::factory()->create(['price' => 100.00]);

        $response = $this->postJson('/api/contracts', [
            'user_id' => $user->id,
            'plan_id' => $plan->id,
        ]);

        $response->assertStatus(200);
        
        $data = $response->json();
        
        // Primeiro contrato deve pagar valor integral
        $this->assertEquals(100.00, $data['payment_amount']);
        $this->assertEquals(0, $data['discount']);
    }
}