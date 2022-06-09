<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\ProjectReminder::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        /* Project Remainder - For Induction meeting scheduling */
        $schedule->command('project:reminder')->daily()->timezone('Asia/Kolkata')->runInBackground();
        /* Design Submission Remainder */
        $schedule->command('designsubmission:remainder')->daily()->runInBackground();
        /* Handover meeting Reamainder */
        $schedule->command('handovermeeting:remainder')->daily()->runInBackground();
        /* Insurance Expiry Reamainder */
        $schedule->command('insuranceexpiry:remainder')->daily()->runInBackground();
        /* Project Completion Remainder - for 1 & 2 week */
        $schedule->command('projectcompletion:remainder')->daily()->runInBackground();
        /* License Remainder */
         $schedule->command('license:remainder')->daily()->runInBackground();
         /*Work Permit Expiry Remainder */
         $schedule->command('workpermitexpiry:remainder')->daily()->runInBackground();
         /*Insurance Submission Remainder */
         $schedule->command('insurancesubmission:remainder')->daily()->runInBackground();
         /*Inspection Snags Remainder */
         $schedule->command('inspectionsnags:remainder')->weeklyOn(1, '6:00')->runInBackground();
         /*Rejected Designs Remainder - weekly */
         $schedule->command('rejecteddesigns:remainder')->weeklyOn(1, '7:00')->runInBackground();
          /*Design Actions Remainder */
         $schedule->command('desingactions:remainder')->daily()->runInBackground();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
