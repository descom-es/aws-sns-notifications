<?php

namespace Descom\AwsSnsNotification\Http\Controllers;

use Aws\Sns\Message;
use Aws\Sns\MessageValidator;
use Descom\AwsSnsNotification\Events\AwsSnsNotificationReceived;
use Descom\AwsSnsNotification\Events\AwsSnsSubscriptionConfirmationReceived;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class WebHookController extends Controller
{
    private static ?\Closure $callableCertClient = null;

    public static function fake(string $certificate)
    {
        self::$callableCertClient = function () use ($certificate) {
            return $certificate;
        };
    }

    public function __invoke(Request $request)
    {
        $message = new Message(json_decode($request->getContent(), true) ?? []);

        $this->validate($message);

        if ($message['Type'] === 'Notification') {
            event(new AwsSnsNotificationReceived($message));
        }

        if ($message['Type'] === 'SubscriptionConfirmation') {
            event(new AwsSnsSubscriptionConfirmationReceived($message));
        }

        return response()->json([
            "message" => 'ok',
        ]);
    }

    private function validate(Message $message): void
    {
        if (! (new MessageValidator(self::$callableCertClient))->isValid($message)) {
            abort(response()->json([
                "message" => 'signature not valid',
            ], 401));
        }
    }
}
