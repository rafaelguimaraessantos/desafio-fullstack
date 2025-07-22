<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function generatePix(Request $request)
    {
        $request->validate([
            'payment_id' => 'required|exists:payments,id',
        ]);

        $payment = Payment::with('contract.plan')->find($request->payment_id);

        // Simular dados do PIX
        $pixData = [
            'qr_code' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==',
            'pix_key' => '12345678901',
            'amount' => $payment->amount,
            'description' => 'Pagamento do plano: ' . $payment->contract->plan->description,
            'expiration' => Carbon::now()->addMinutes(30)->toISOString(),
        ];

        return response()->json($pixData);
    }

    public function confirmPayment(Request $request)
    {
        $request->validate([
            'payment_id' => 'required|exists:payments,id',
        ]);

        $payment = Payment::find($request->payment_id);
        
        $payment->update([
            'status' => 'paid',
            'paid_at' => Carbon::now()->toDateString(),
        ]);

        return response()->json([
            'message' => 'Payment confirmed successfully',
            'payment' => $payment
        ]);
    }

    public function show($paymentId)
    {
        $payment = Payment::with('contract.plan')->find($paymentId);
        
        if (!$payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        return response()->json($payment);
    }
}
