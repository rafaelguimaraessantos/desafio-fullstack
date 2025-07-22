<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Plan;
use App\Models\Contract;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_generate_pix()
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create(['price' => 100.00]);
        
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

        $response = $this->postJson('/api/payments/pix', [
            'payment_id' => $payment->id,
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'qr_code',
                    'pix_key',
                    'amount',
                    'description',
                    'expiration'
                ]);
    }

    public function test_can_confirm_payment()
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create(['price' => 100.00]);
        
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

        $response = $this->postJson('/api/payments/confirm', [
            'payment_id' => $payment->id,
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Payment confirmed successfully'
                ]);

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'paid',
        ]);
    }

    public function test_can_get_payment_details()
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create(['price' => 100.00]);
        
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

        $response = $this->getJson("/api/payments/{$payment->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'id' => $payment->id,
                    'amount' => '100.00',
                    'status' => 'pending',
                ]);
    }
}