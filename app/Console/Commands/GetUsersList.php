<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class GetUsersList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-users-list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'shows a table with users names and emails list';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // $usersNames = User::all()->pluck('name')->toArray();
        $this->info('list of users:');
        $this->table(
            ['Name', 'Email'],
            User::all(['name', 'email'])->toArray()
        );
    }
}
