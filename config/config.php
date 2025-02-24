<?php

return [

    'webhook' => [
        'path' => env('AWS_SNS_WEBHOOK_PATH', 'aws/sns/webhook'),
    ],

];
