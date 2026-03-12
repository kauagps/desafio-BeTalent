<?php

namespace App\Http\Controllers;

use App\Models\Clients;
use App\Models\Product;
use App\Models\Transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.amount' => 'required|integer|min:1'
        ]);

        return DB::transaction(function () use ($validated) {

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

            TODO:

            return response()->json([
                'message' => 'Transação registrada com sucesso!!!',
                'transaction' => $transaction->load('products'),
            ], 201);
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
