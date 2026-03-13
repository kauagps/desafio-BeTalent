<?php
namespace App\Services;

use App\Models\Gateways; 
use Exception;

class PaymentService {

    public function processPayment($transaction, $cardData) {
        
        $gatewaysDoBanco = Gateways::where('is_active', true)
                                   ->orderBy('priority', 'asc')
                                   ->get();
        
        foreach ($gatewaysDoBanco as $gatewayModel) {
            try {
                
                $adapter = $this->getAdapter($gatewayModel->name);
                
                
                $result = $adapter->pay($transaction, $cardData);
                
                
                $result['gateway_database_id'] = $gatewayModel->id;
                
                return $result;
            } catch (Exception $e) {
                continue;
            }
        }

        throw new Exception("Nenhum gateway conseguiu processar o pagamento.");
    }

    private function getAdapter($name) {
        
        return match($name) {
            'Gateway 1' => new \App\Gateways\GatewayOne(),
            'Gateway 2' => new \App\Gateways\GatewayTwo(),
            default => throw new Exception("Gateway [$name] não implementado no código."),
        };
    }
}