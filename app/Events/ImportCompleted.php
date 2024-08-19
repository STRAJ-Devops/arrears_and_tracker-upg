<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ImportCompleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $staffId;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($message, $staffId)
    {
        $this->message = $message;
        $this->staffId = $staffId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        \Log::info('Broadcasting on channel:', ['channel' => "import-status.{$this->staffId}"]);

        return [
            new PrivateChannel("import-status.{$this->staffId}")
        ];
    }
}
