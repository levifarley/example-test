<?php

namespace App\Console\Commands;

use App\Http\Controllers\UserController;
use Illuminate\Console\Command;

class UpdateUsers extends Command
{
    /**
     * @var string
     */
    protected $signature = 'update:users';

    /**
     * @var string
     */
    protected $description = 'Update users data from external source';

    /**
     * Handle command logic
     */
    public function handle()
    {
        $this->info('Updating users...');

        $this->info((new UserController())->update());

        $this->call('cache:clear');
    }
}
