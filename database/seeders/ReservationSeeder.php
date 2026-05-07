<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Reservation;
use App\Models\ReservationStatus;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReservationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reservedBy = DB::table('users')->value('user_id');

        $pendingStatusId = ReservationStatus::whereRaw('LOWER(status_name) = ?', ['pending'])->value('status_id');
        $confirmedStatusId = ReservationStatus::whereRaw('LOWER(status_name) = ?', ['confirmed'])->value('status_id');
        $cancelledStatusId = ReservationStatus::whereRaw('LOWER(status_name) = ?', ['cancelled'])->value('status_id');

        $customers = Customer::orderBy('customer_id')->take(5)->get();
        if ($customers->count() < 5 || !$reservedBy) {
            return;
        }

        $baseDate = Carbon::today();

        $records = [
            [
                'customer_id' => $customers[0]->customer_id,
                'status_id' => $confirmedStatusId,
                'reservation_date' => $baseDate->copy()->subDays(4)->toDateString(),
                'start_date' => $baseDate->copy()->subDays(2)->toDateString(),
                'end_date' => $baseDate->copy()->addDays(1)->toDateString(),
            ],
            [
                'customer_id' => $customers[1]->customer_id,
                'status_id' => $pendingStatusId,
                'reservation_date' => $baseDate->copy()->subDay()->toDateString(),
                'start_date' => $baseDate->copy()->addDays(5)->toDateString(),
                'end_date' => $baseDate->copy()->addDays(8)->toDateString(),
            ],
            [
                'customer_id' => $customers[2]->customer_id,
                'status_id' => $confirmedStatusId,
                'reservation_date' => $baseDate->copy()->subDays(2)->toDateString(),
                'start_date' => $baseDate->copy()->addDays(1)->toDateString(),
                'end_date' => $baseDate->copy()->addDays(4)->toDateString(),
            ],
            [
                'customer_id' => $customers[3]->customer_id,
                'status_id' => $cancelledStatusId,
                'reservation_date' => $baseDate->copy()->subDays(10)->toDateString(),
                'start_date' => $baseDate->copy()->subDays(7)->toDateString(),
                'end_date' => $baseDate->copy()->subDays(4)->toDateString(),
            ],
            [
                'customer_id' => $customers[4]->customer_id,
                'status_id' => $confirmedStatusId,
                'reservation_date' => $baseDate->copy()->subDays(1)->toDateString(),
                'start_date' => $baseDate->copy()->addDays(10)->toDateString(),
                'end_date' => $baseDate->copy()->addDays(12)->toDateString(),
            ],
        ];

        foreach ($records as $record) {
            Reservation::updateOrCreate(
                [
                    'customer_id' => $record['customer_id'],
                    'start_date' => $record['start_date'],
                    'end_date' => $record['end_date'],
                ],
                [
                    'reserved_by' => $reservedBy,
                    'status_id' => $record['status_id'],
                    'reservation_date' => $record['reservation_date'],
                ]
            );
        }
    }
}
