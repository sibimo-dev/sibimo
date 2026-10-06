<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationAvailable implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly string $type,
        public readonly int $id,
        public readonly string $action,
        public readonly string $occurredAt,
    ) {
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('admin-notifications')];
    }

    public function broadcastAs(): string
    {
        return 'notification.available';
    }

    public function broadcastWith(): array
    {
        return [
            'type' => $this->type,
            'id' => $this->id,
            'action' => $this->action,
            'occurred_at' => $this->occurredAt,
        ];
    }
}
