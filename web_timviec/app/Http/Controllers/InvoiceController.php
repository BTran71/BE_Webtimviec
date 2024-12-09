<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\Log;

class InvoiceController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $data = $request->all();
        $user=Auth::guard('employer')->user();
        // Lưu hóa đơn
        if(!$user){
            return response()->json(['error' => 'User not authenticated'], 401);
        }
        try {
            Invoice::create([
                'transaction_id' => $request->transaction_id,
                'employer_id' => $user->id,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'status' => $request->status,
                'paid_at' => $request->paid_at ?? now(),
            ]);
    
            return response()->json(['message' => 'Invoice created successfully'], 200);
        } catch (\Exception $e) {
            \Log::error('Invoice creation failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to create invoice'], 500);
        }
    }
}
