<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ResetRequiredEmailTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cx:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->call('cache:clear');
        $this->call('config:clear');
        $this->call('view:clear');
        $this->call('queue:restart');
        $this->line('  <bg=yellow> WARNING </> Remember to init queues again');
        $this->line('');
    }
}
