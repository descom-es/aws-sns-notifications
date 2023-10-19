<?php

namespace Descom\AwsSnsNotification\Events;

use Aws\Sns\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AwsSnsNotificationReceived
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public readonly object $message;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public readonly Message $messageSns,
    ) {
    }

    public function getTopicArn(): string
    {
        return $this->messageSns->offsetGet('TopicArn');
    }

    public function toJson(): object
    {
        return json_decode($this->messageSns->offsetGet('Message'), false, 512, JSON_THROW_ON_ERROR);
    }

    public function toArray(): array
    {
        return json_decode($this->messageSns->offsetGet('Message'), true);
    }

    public function __toString()
    {
        return $this->messageSns->offsetGet('Message');
    }
}
