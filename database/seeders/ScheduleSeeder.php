<?php

namespace Database\Seeders;

use App\Models\Schedule;
use App\Models\BasketCourt;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $courts = BasketCourt::all();
        $today = Carbon::today();

        // Generate jadwal untuk 7 hari ke depan
        for ($day = 0; $day < 7; $day++) {
            $date = $today->copy()->addDays($day);

            foreach ($courts as $court) {
                // Jadwal dari jam 8 pagi sampai 9 malam
                $startHour = 8;
                $endHour = 21;

                for ($hour = $startHour; $hour < $endHour; $hour++) {
                    Schedule::create([
                        'court_id' => $court->id,
                        'schedule_date' => $date->format('Y-m-d'),
                        'start_time' => sprintf('%02d:00:00', $hour),
                        'end_time' => sprintf('%02d:00:00', $hour + 1),
                        'status' => 'available'
                    ]);
                }
            }
        }
    }
}
