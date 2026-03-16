<?php
namespace App\Gateways;

use Illuminate\Support\Facades\Http;
use Exception;

class GatewayOne implements GatewayInterface {
    public function pay($transaction, array $cardData) {
        $response = Http::timeout(5)->post('http://host.docker.internal:3001/transactions', [
            'amount'     => $transaction->total_amount * 100, // Centavos
            'name'       => $transaction->client->name,
            'email'      => $transaction->client->email,
            'cardNumber' => $cardData['cardNumber'],
            'cvv'        => $cardData['cvv'],
        ]);

        if ($response->failed()) {
            throw new Exception("Erro no Gateway 1");
        }

        return [
            'success' => true,
            'external_id' => $response->json('id') ?? uniqid(),
            'gateway_name' => 'Gateway 1'
        ];
    }
}