<?php

namespace App\Gateways;

use Illuminate\Support\Facades\Http;
use Exception;

class GatewayTwo implements GatewayInterface {
    public function pay($transaction, array $cardData) {
        $response = Http::timeout(5)
            ->withHeaders([
                'Gateway-Auth-Token' => 'tk_f2198cc671b5289fa856',
                'Gateway-Auth-Secret' => '3d15e8ed6131446ea7e3456728b1211f'
            ])
            ->post('http://localhost:3002/transacoes', [
                'valor'        => $transaction->total_amount * 100,
                'nome'         => $transaction->client->name,
                'email'        => $transaction->client->email,
                'numeroCartao' => $cardData['cardNumber'],
                'cvv'          => $cardData['cvv'],
            ]);

        if ($response->failed()) {
            throw new Exception("Erro no Gateway 2");
        }

        return [
            'success' => true,
            'external_id' => $response->json('id'),
            'gateway_name' => 'Gateway 2'
        ];
    }
}