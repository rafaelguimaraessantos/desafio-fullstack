<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Contract;
use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_have_contracts()
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();
        
        $contract = Contract::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'active' => true,
            'contract_date' => now(),
        ]);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $user->contracts);
        $this->assertEquals(1, $user->contracts->count());
        $this->assertEquals($contract->id, $user->contracts->first()->id);
    }

    public function test_user_can_get_active_contract()
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();
        
        // Criar contrato inativo
        Contract::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'active' => false,
            'contract_date' => now()->subDays(10),
        ]);

        // Criar contrato ativo
        $activeContract = Contract::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'active' => true,
            'contract_date' => now(),
        ]);

        $result = $user->activeContract();
        
        $this->assertNotNull($result);
        $this->assertEquals($activeContract->id, $result->id);
        $this->assertTrue($result->active);
    }

    public function test_user_returns_null_when_no_active_contract()
    {
        $user = User::factory()->create();
        
        $result = $user->activeContract();
        
        $this->assertNull($result);
    }
}