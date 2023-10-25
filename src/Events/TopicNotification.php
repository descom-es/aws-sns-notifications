<?php

namespace Descom\AwsSnsNotification\Events;

use Aws\Sns\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TopicNotification
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public readonly Message $messageSns,
    ) {
    }

    public function topicArn(): string
    {
        return $this->messageSns->offsetGet('TopicArn');
    }

    public function subject(): ?string
    {
        return $this->messageSns->offsetGet('Subject');
    }

    public function toJson(): object
    {
        return json_decode($this->messageSns->offsetGet('Message'), false, 512, JSON_THROW_ON_ERROR);
    }

    public function toArray(): array
    {
        return json_decode($this->messageSns->offsetGet('Message'), true, 512, JSON_THROW_ON_ERROR);
    }

    public function __toString()
    {
        return $this->messageSns->offsetGet('Message');
    }
}
