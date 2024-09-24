<?php

namespace App\Console\Commands;

use App\Jobs\SendWelcomeEmail;
use App\Models\User;
use Illuminate\Console\Command;

class SendTestWelcomeEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:send-test-welcome-email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send the welcome email to a factory generated dummy user';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Creating dummy user...');
        $user = User::factory()->create();

        $this->info('Dispatching job...');
        SendWelcomeEmail::dispatchSync($user);
        $this->info('Test welcome email dispatched successfully.');

        $user->delete();
        $this->info('Dummy user deleted.');

        return 0;
    }
}
