<?php

namespace App\Listeners;

use App\Events\NewBenefitUserWithoutLeaderEvent;
use App\Mail\BenefitDecision;
use App\Models\BenefitUser;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class NewBenefitUserWithoutLeaderListener implements ShouldQueue
{

    public User $user;
    public BenefitUser $benefitUserData;

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
    public function handle(NewBenefitUserWithoutLeaderEvent $event): void
    {
        $this->user = $event->user;
        $this->benefitUserData = $event->benefitUserData;
        Mail::to($this->user->email)->queue(new BenefitDecision($this->benefitUserData));
    }
}
