<?php

namespace Tests\Unit;

use App\Models\Contract;
use App\Models\User;
use App\Models\Plan;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContractTest extends TestCase
{
    use RefreshDatabase;

    public function test_contract_belongs_to_user()
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();
        
        $contract = Contract::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'active' => true,
            'contract_date' => now(),
        ]);

        $this->assertInstanceOf(User::class, $contract->user);
        $this->assertEquals($user->id, $contract->user->id);
    }

    public function test_contract_belongs_to_plan()
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();
        
        $contract = Contract::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'active' => true,
            'contract_date' => now(),
        ]);

        $this->assertInstanceOf(Plan::class, $contract->plan);
        $this->assertEquals($plan->id, $contract->plan->id);
    }

    public function test_contract_can_have_payments()
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();
        
        $contract = Contract::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'active' => true,
            'contract_date' => now(),
        ]);

        $payment = Payment::create([
            'contract_id' => $contract->id,
            'amount' => 100.00,
            'discount' => 0.00,
            'status' => 'pending',
            'due_date' => now(),
        ]);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $contract->payments);
        $this->assertEquals(1, $contract->payments->count());
        $this->assertEquals($payment->id, $contract->payments->first()->id);
    }
}