<?php

namespace App\Gateways;

interface GatewayInterface {
    public function pay($transaction, array $cardData);
}