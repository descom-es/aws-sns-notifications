<?php

namespace Descom\AwsSnsNotification\Events;

use Aws\Sns\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AwsSnsSubscriptionConfirmationReceived
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public readonly Message $messageSns
    ) {
    }

    public function topicArn(): string
    {
        return $this->messageSns->offsetGet('TopicArn');
    }

    public function subscribeUrl(): string
    {
        return $this->messageSns->offsetGet('SubscribeURL');
    }
}
