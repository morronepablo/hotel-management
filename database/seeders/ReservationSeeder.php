<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Reservation;
use App\Models\Client;
use App\Models\Room;
use Carbon\Carbon;

class ReservationSeeder extends Seeder
{
    public function run()
    {
        $clients = Client::all();
        $rooms = Room::all();

        $reservations = [
            [
                'client_id' => $clients->where('email', 'pablomoronepablo@gmail.com')->first()->id,
                'room_id' => $rooms->where('room_number', '101')->first()->id,
                'check_in' => Carbon::today()->subDays(2),
                'check_in_time' => '09:00:00',
                'check_out' => Carbon::today()->addDays(1),
                'check_out_time' => '10:00:00',
                'status' => 'Confirmada',
            ],
            [
                'client_id' => $clients->where('email', 'ana.lopez@gmail.com')->first()->id,
                'room_id' => $rooms->where('room_number', '102')->first()->id,
                'check_in' => Carbon::today()->subDays(1),
                'check_in_time' => '09:00:00',
                'check_out' => Carbon::today()->addDays(2),
                'check_out_time' => '10:00:00',
                'status' => 'Confirmada',
            ],
        ];

        foreach ($reservations as $reservation) {
            Reservation::firstOrCreate(
                [
                    'client_id' => $reservation['client_id'],
                    'room_id' => $reservation['room_id'],
                    'check_in' => $reservation['check_in'],
                ],
                $reservation
            );
        }
    }
}
