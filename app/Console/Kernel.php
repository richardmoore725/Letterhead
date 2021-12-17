<?php

namespace App\Console;

use App\Task\PublishLetters;
use App\Task\SyncMailchimpList;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        /**
         * Every minute, we will seek Letters that have been scheduled to publish at that exact time.
         * @uses PublishLetters
         */
        $publishLettersTask = \app()->make(PublishLetters::class);
        $schedule->call($publishLettersTask)
            ->everyMinute()
            ->name('publish-letters')
            ->withoutOverlapping();

        /**
         * @uses SyncMailchimpList
         */
        $syncMailchimpListTask = \app()->make(SyncMailchimpList::class);

        $schedule->call($syncMailchimpListTask)
            ->weekly()
            ->name('sync-mailchimp-list')
            ->withoutOverlapping();
    }
}
