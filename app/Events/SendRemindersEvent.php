<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendRemindersEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */

    public $email;

    public $name;

    public $organizationName;

    public $countPendingDonationRequests;

    public $pendingAmount;

    public function __construct($email, $name, $organizationName, $countPendingDonationRequests, $pendingAmount)
    {
        $this->email = $email;
        $this->name = $name;
        $this->organizationName = $organizationName;
        $this->countPendingDonationRequests = $countPendingDonationRequests;
        $this->pendingAmount = $pendingAmount;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
