<?php

namespace App\Jobs;

use App\Enums\BenefitDecisionEnum;
use App\Models\BenefitUser;
use App\Services\BenefitUserService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RejectOldBenefitRequestsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public BenefitUserService $benefitUserService
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $rejectMessage = "Tu solicitud de beneficio ha sido rechazada automáticamente por el sistema porque ha estado pendiente durante más de 15 días.";
        BenefitUser::where('is_approved', '=', BenefitDecisionEnum::PENDING)
            ->where('created_at', '<', now()->subDays(15))
            ->get()
            ->each(function ($benefitUser) use ($rejectMessage) {
                $this->benefitUserService->decideBenefitUser('reject', $rejectMessage, $benefitUser);
            });
    }

    public function tries(): int
    {
        return 5;
    }
}
