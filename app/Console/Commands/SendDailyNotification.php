<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FcmService;

class SendDailyNotification extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'notify:daily';

    /**
     * The console command description.
     */
    protected $description = 'Send daily push notification to all users';

    /**
     * Execute the console command.
     */
    public function handle(FcmService $fcm)
    {
        $title = 'Pengingat Harian';
        $body  = 'Jangan lupa absen hari ini 🚀';

        $fcm->sendToAll($title, $body);

        $this->info('Daily notification sent successfully.');
    }
}
