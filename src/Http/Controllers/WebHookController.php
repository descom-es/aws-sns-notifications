<?php

namespace Descom\AwsSnsNotification\Http\Controllers;

use Aws\Sns\Message;
use Aws\Sns\MessageValidator;
use Descom\AwsSnsNotification\Events\AwsSnsNotificationReceived;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class WebHookController extends Controller
{
    private static ?\Closure $callableCertClient = null;

    public static function fake(string $certificate)
    {
        static::$callableCertClient = function () use ($certificate) {
            return $certificate;
        };
    }

    public function __invoke(Request $request)
    {
        $message = new Message(json_decode($request->getContent(), true) ?? []);

        if (! (new MessageValidator(static::$callableCertClient))->isValid($message)) {
            return response()->json([
                "message" => 'signature not valid',
            ], 401);
        }

        if ($message['Type'] === 'Notification') {
            event(new AwsSnsNotificationReceived($message));
        }

        return response()->json([
            "message" => 'ok',
        ]);
    }
}
