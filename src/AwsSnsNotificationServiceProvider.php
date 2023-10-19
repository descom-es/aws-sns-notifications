<?php

namespace Descom\AwsSnsNotification;

use Illuminate\Support\ServiceProvider;

class AwsSnsNotificationServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'aws_sns_notification');
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
              __DIR__.'/../config/config.php' => config_path('aws_sns_notification.php'),
            ], 'config');
        }
    }
}
