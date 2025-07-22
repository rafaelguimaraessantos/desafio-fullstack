<?php

namespace Tests\Unit;

use App\Models\Payment;
use App\Models\Contract;
use App\Models\User;
use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_belongs_to_contract()
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

        $this->assertInstanceOf(Contract::class, $payment->contract);
        $this->assertEquals($contract->id, $payment->contract->id);
    }

    public function test_payment_amount_is_cast_to_decimal()
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
            'amount' => '100.50',
            'discount' => '10.25',
            'status' => 'pending',
            'due_date' => now(),
        ]);

        $this->assertEquals('100.50', $payment->amount);
        $this->assertEquals('10.25', $payment->discount);
    }

    public function test_payment_status_enum_values()
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();
        
        $contract = Contract::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'active' => true,
            'contract_date' => now(),
        ]);

        $validStatuses = ['pending', 'paid', 'cancelled'];

        foreach ($validStatuses as $status) {
            $payment = Payment::create([
                'contract_id' => $contract->id,
                'amount' => 100.00,
                'discount' => 0.00,
                'status' => $status,
                'due_date' => now(),
            ]);

            $this->assertEquals($status, $payment->status);
        }
    }
}