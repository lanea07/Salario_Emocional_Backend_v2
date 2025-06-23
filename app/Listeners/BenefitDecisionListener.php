<?php

namespace App\Listeners;

use App\Events\BenefitDecisionEvent;
use App\Mail\BenefitDecision;
use App\Models\BenefitUser;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class BenefitDecisionListener implements ShouldQueue
{

    private BenefitUser $benefitUser;
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(BenefitDecisionEvent $event): void
    {
        $this->benefitUser = $event->benefitUser;
        Mail::to($this->benefitUser->user->email)->queue(new BenefitDecision($this->benefitUser));
    }
}
