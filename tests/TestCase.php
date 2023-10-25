<?php

namespace Descom\AwsSnsNotification\Tests;

use Descom\AwsSnsNotification\AwsSnsNotificationServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            AwsSnsNotificationServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('aws_sns_notification.webhook', [
            'path' => 'aws/sns/webhook',
        ]);
    }
}
