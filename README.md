# AWS SNS Notifications for Laravel Apps

[![tests](https://github.com/descom-es/aws-sns-notifications/actions/workflows/tests.yml/badge.svg)](https://github.com/descom-es/aws-sns-notifications/actions/workflows/tests.yml)
[![analyze](https://github.com/descom-es/aws-sns-notifications/actions/workflows/analyze.yml/badge.svg)](https://github.com/descom-es/aws-sns-notifications/actions/workflows/analyze.yml)
[![style](https://github.com/descom-es/aws-sns-notifications/actions/workflows/style_fix.yml/badge.svg)](https://github.com/descom-es/aws-sns-notifications/actions/workflows/style_fix.yml)

## Install

```bash
composer require descom/aws-sns-notifications
```

## Usage

### Create en SNS a subscription HTTPS to a topic

`https://localhost:8000/aws/sns/webhook`

### Capture Events

#### AwsSnsSubscriptionConfirmationReceived

```php
use Descom\AwsSnsNotification\Events\AwsSnsSubscriptionConfirmationReceived;
use Illuminate\Support\Facades\Http;

class SnsSubscriptionConfirmation
{
    public function handle(AwsSnsSubscriptionConfirmationReceived $event): void
    {
        // Confirm the subscription by sending a GET request to the SubscribeURL

        Http::get($event->subscribeUrl());
    }
}
```

#### AwsSnsNotificationReceived

```php
use Descom\AwsSnsNotification\Events\AwsSnsNotificationReceived;
use Illuminate\Support\Facades\Http;

class SnsNotificationLogger
{
    public function handle(AwsSnsNotificationReceived $event): void
    {
        logger()->info('SNS Notification received', [
            'topic' => $event->topicArn(),
            'subject' => $event->subject(),
            'message' => $event->toJson(),
        ]);
    }
}
```
