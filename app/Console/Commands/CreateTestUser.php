<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CreateTestUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create-test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a test user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Admin Test',
                'password' => bcrypt('123456')
            ]
        );

        $this->info('Usuario creado/encontrado: ' . $user->email);
        return 0;
    }
}
