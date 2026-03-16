<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Gateway;
use App\Models\Gateways;

class GatewaySeeder extends Seeder
{
    public function run(): void
    {
        // Criando o Gateway A (Maior Prioridade)
        Gateways::create([
            'name' => 'Gateway 1',
            'priority' => 1,
            'api_key' => 'chave-teste-a', // Se o mock exigir
            'is_active' => true
        ]);

        // Criando o Gateway B (Menor Prioridade)
        Gateways::create([
            'name' => 'Gateway 2',
            'priority' => 2,
            'api_key' => 'chave-teste-b',
            'is_active' => true
        ]);
    }
}