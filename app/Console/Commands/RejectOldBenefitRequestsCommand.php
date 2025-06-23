<?php

namespace App\Console\Commands;

use App\Jobs\RejectOldBenefitRequestsJob;
use App\Services\BenefitUserService;
use Illuminate\Console\Command;

class RejectOldBenefitRequestsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reject-old-benefit-requests';

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
        RejectOldBenefitRequestsJob::dispatch(new BenefitUserService());
    }
}
