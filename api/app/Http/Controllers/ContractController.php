<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::find($request->user_id);
        $plan = Plan::find($request->plan_id);
        
        // Desativar contrato anterior se existir
        $activeContract = $user->activeContract();
        if ($activeContract) {
            $this->deactivateContract($activeContract);
        }

        // Criar novo contrato
        $contract = Contract::create([
            'user_id' => $request->user_id,
            'plan_id' => $request->plan_id,
            'active' => true,
            'contract_date' => Carbon::now()->toDateString(),
        ]);

        // Calcular valor do pagamento considerando créditos
        $paymentAmount = $this->calculatePaymentAmount($activeContract, $plan);

        // Criar pagamento
        $payment = Payment::create([
            'contract_id' => $contract->id,
            'amount' => $paymentAmount['amount'],
            'discount' => $paymentAmount['discount'],
            'status' => 'pending',
            'due_date' => Carbon::now()->toDateString(),
        ]);

        return response()->json([
            'contract' => $contract->load('plan'),
            'payment_id' => $payment->id,
            'payment_amount' => $paymentAmount['amount'],
            'discount' => $paymentAmount['discount'],
        ]);
    }

    public function show($userId)
    {
        $user = User::find($userId);
        $activeContract = $user->activeContract();
        
        if (!$activeContract) {
            return response()->json(['message' => 'No active contract found'], 404);
        }

        return response()->json($activeContract);
    }

    public function history($userId)
    {
        $user = User::find($userId);
        $contracts = $user->contracts()
            ->with(['plan', 'payments'])
            ->orderBy('active', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($contracts);
    }

    private function deactivateContract($contract)
    {
        $contract->update(['active' => false]);
    }

    private function calculatePaymentAmount($previousContract, $newPlan)
    {
        if (!$previousContract) {
            return [
                'amount' => $newPlan->price,
                'discount' => 0
            ];
        }

        $contractDate = Carbon::parse($previousContract->contract_date);
        $today = Carbon::now();
        $daysInMonth = $contractDate->daysInMonth;
        $daysUsed = $contractDate->diffInDays($today);
        
        // Calcular crédito proporcional
        $previousPlanPrice = $previousContract->plan->price;
        $dailyRate = $previousPlanPrice / $daysInMonth;
        $daysRemaining = $daysInMonth - $daysUsed;
        $credit = $dailyRate * $daysRemaining;

        $finalAmount = $newPlan->price - $credit;
        
        return [
            'amount' => max(0, $finalAmount), // Não pode ser negativo
            'discount' => min($credit, $newPlan->price) // Desconto não pode ser maior que o preço
        ];
    }
}
