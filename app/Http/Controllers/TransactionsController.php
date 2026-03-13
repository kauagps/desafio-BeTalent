<?php

namespace App\Http\Controllers;

use App\Models\Clients;
use App\Models\Product;
use App\Models\Transactions;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transactions = Transactions::with(['client', 'products'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'message' => 'Histórico de transações recuperado com sucesso',
            'data' => $transactions
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, PaymentService $paymentService)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.amount' => 'required|integer|min:1',
            'cardNumber' => 'required|string|size:16',
            'cvv' => 'required|string|size:3'
        ]);

        return DB::transaction(function () use ($validated, $paymentService) {

            $totalAmount = 0;
            $pivotData = [];

            foreach ($validated['products'] as $item){
                $product = Product::find($item['id']);

                $totalAmount += $product->price * $item['amount'];

                $pivotData[$product->id] = [
                    'quantity' => $item['amount'],
                    'unit_price' => $product->price
                ];
            }

            $transaction = Transactions::create([
                'client_id' => $validated['client_id'],
                'total_amount' => $totalAmount,
                'status' => 'pending'
            ]);

            $transaction->products()->attach($pivotData);

            try {
                $transaction->load('client');

                $paymentResponse = $paymentService->processPayment($transaction, [
                    'cardNumber' => $validated['cardNumber'],
                    'cvv' => $validated['cvv']
                ]);

                $transaction->update([
                    'status' => 'paid',
                    'external_id' => $paymentResponse['external_id'],
                    'gateway_id' => $paymentResponse['gateway'] === 'Gateway 1' ? 1 : 2
                ]);

                return response()->json([
                    'message' => 'Pagamento aprovado via ' . $paymentResponse['gateway'],
                    'transaction' => $transaction->load('products'),
                ], 201);
            } catch (\Exception $e) {
                $transaction->update(['status' => 'failed']);

                return response()->json([
                    'message' => 'Erro no pagamento: ' . $e->getMessage(),
                    'transaction' => $transaction
                ], 402);
            }
            // TODO:

            // return response()->json([
            //     'message' => 'Transação registrada com sucesso!!!',
            //     'transaction' => $transaction->load('products'),
            // ], 201);
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(Transactions $transactions)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transactions $transactions)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transactions $transactions)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transactions $transactions)
    {
        //
    }
}
