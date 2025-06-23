<?php

namespace App\Listeners;

use App\Events\NewBenefitUserWithLeaderEvent;
use App\Mail\BenefitUserCreated;
use App\Mail\NotifyNewBenefitRequestToLeader;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class NewBenefitUserWithLeaderListener implements ShouldQueue
{

    private User $user;
    private array $data;

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
    public function handle(NewBenefitUserWithLeaderEvent $event): void
    {
        $this->user = $event->user;
        $this->data = $event->data;
        Mail::to($this->user->leader_user->email)->queue(new NotifyNewBenefitRequestToLeader($this->data));
        Mail::to($this->user->email)->queue(new BenefitUserCreated($this->data));
    }
}
