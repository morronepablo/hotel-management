<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run()
    {
        $clients = [
            [
                'name' => 'Pablo Martín',
                'lastname' => 'Morrone',
                'tipo_id' => 1,
                'nro_documento' => '22362590',
                'nro_matricula' => 'AD123QP',
                'email' => 'pablomoronepablo@gmail.com',
                'phone' => '113869097',
                'address' => 'Justin García 6 A',
                'created_at' => '2025-04-20 19:26:23',
                'updated_at' => '2025-04-20 19:26:23',
            ],
            [
                'name' => 'Ana',
                'lastname' => 'Lopez',
                'tipo_id' => 1,
                'nro_documento' => '87654321',
                'nro_matricula' => 'AC123XL',
                'email' => 'ana.lopez@gmail.com',
                'phone' => '112345678',
                'address' => 'Av. Siempre Viva 123',
                'created_at' => '2025-04-20 19:26:23',
                'updated_at' => '2025-04-20 19:26:23',
            ],
            [
                'name' => 'Carlos',
                'lastname' => 'Gomez',
                'tipo_id' => 1,
                'nro_documento' => '45678912',
                'nro_matricula' => 'AB123CD',
                'email' => 'carlos.gomez@gmail.com',
                'phone' => '114567890',
                'address' => 'Calle Falsa 456',
                'created_at' => '2025-04-20 19:26:23',
                'updated_at' => '2025-04-20 19:26:23',
            ],
            [
                'name' => 'Ernesto',
                'lastname' => 'Sabato',
                'tipo_id' => 1,
                'nro_documento' => '30569887',
                'nro_matricula' => 'AB125CD',
                'email' => 'ernesto@gmail.com',
                'phone' => '114562258',
                'address' => 'Calle Falsa 456',
                'created_at' => '2025-04-20 19:26:23',
                'updated_at' => '2025-04-20 19:26:23',
            ],
            [
                'name' => 'Hernan',
                'lastname' => 'Oviedo',
                'tipo_id' => 1,
                'nro_documento' => '27366477',
                'nro_matricula' => 'AB129TB',
                'email' => 'hernan@gmail.com',
                'phone' => '1133669874',
                'address' => 'Calle Falsa 456',
                'created_at' => '2025-04-20 19:26:23',
                'updated_at' => '2025-04-20 19:26:23',
            ],
        ];

        foreach ($clients as $client) {
            Client::create($client);
        }
    }
}
