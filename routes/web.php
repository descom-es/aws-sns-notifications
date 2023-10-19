<?php

use Descom\AwsSnsNotification\Http\Controllers\WebHookController;
use Illuminate\Support\Facades\Route;

Route::post(config('aws_sns_notification.webhook.path', 'aws/sns/webhook'), WebHookController::class);
