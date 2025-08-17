<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BasketCourt;

class CheckCourtHoliday extends Command
{
    protected $signature = 'court:check-holiday';
    protected $description = 'Check and update court status based on holiday dates';

    public function handle()
    {
        BasketCourt::checkAndUpdateHolidayStatus();
        $this->info('Court holiday status has been checked and updated.');
    }
} 