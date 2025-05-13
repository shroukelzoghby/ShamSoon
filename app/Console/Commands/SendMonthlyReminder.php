<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\FirebaseNotificationService;
use Illuminate\Console\Command;

class SendMonthlyReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:monthly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send monthly reminders for solar panel maintenance';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $users = User::whereNotNull('fcm_token')->get();

        foreach ($users as $user) {
            $title = 'Monthly Reminder';
            $body = 'Time for solar panel maintenance.';

            try {
                (new FirebaseNotificationService)->sendNotification(
                    $user->fcm_token,
                    $title,
                    $body,
                    [],
                    $user->id 
                );
            } catch (\Exception $e) {
                $this->error('Failed to send notification to user ' . $user->id . ': ' . $e->getMessage());
            }
        }

        $this->info('Monthly reminders sent successfully.');
    }
}
