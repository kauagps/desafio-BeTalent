<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class PaymentService{
    private $url1 = 'http://localhost:3001/transactions';
    private $url2 = 'http//localhost:3002/transacoes';

    public function processPayment($transaction, $cardData){
        
        try {
            $response1 = Http::timeout(5)->post($this->url1, [
                'amount' => $transaction->total_amount * 100,
                'name' => $transaction->client->name,
                'email' => $transaction->client->email,
                'cardNumber' => $cardData['cardNumber'],
                'cvv' => $cardData['cvv'],
            ]);

            if ($response1->successful()){
                return [
                    'success' => true,
                    'gateway' => 'Gateway 1',
                    'external_id' => $response1->json('id')
                ];
            }
        } catch (Exception $e) { }

        try {
            $response2 = Http::timeout(5)
                ->withHeaders([
                    'Gateway-Auth-Token' => 'tk_f2198cc671b5289fa856',
                    'Gateway-Auth-Secret' => '3d15e8ed6131446ea7e3456728b1211f'
                ])
                ->post($this->url2, [
                    'valor' => $transaction->total_amount * 100,
                    'nome' => $transaction->client->name,
                    'email' => $transaction->client->email,
                    'numeroCartao' => $cardData['cardNumber'],
                    'cvv' => $cardData['cvv'],
                ]);
            
            if ($response2->successful()){
                return [
                    'success' => true,
                    'gateway' => 'Gateway 2',
                    'external_id' => $response2->json('id')
                ];
            }
        } catch (Exception $e) { }

        throw new Exception("Falha ao processar pagamento em todos os gateways!!!");
    }
}