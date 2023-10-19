<?php

namespace Descom\AwsSnsNotification\Tests\Feature;

use Aws\Sns\Message;
use Aws\Sns\MessageValidator;
use Descom\AwsSnsNotification\Events\AwsSnsNotificationReceived;
use Descom\AwsSnsNotification\Http\Controllers\WebHookController;
use Descom\AwsSnsNotification\Tests\TestCase;
use Illuminate\Support\Facades\Event;

class AwsNotificationReceivedTest extends TestCase
{
    public function testIfBadSignedResponseUnauthorized()
    {
        Event::fake();
        WebHookController::fake(str_replace('5hAyUS', '5hAyuS', $this->getCertificate()));

        $this->postJson(config('aws_sns_notification.webhook.path'), $this->generateNotification([
            'type' => 'NullAction',
        ]))->assertUnauthorized();

        Event::assertNotDispatched(AwsSnsNotificationReceived::class);
    }

    public function testDispatchEventAwsSnsNotificationReceived()
    {
        Event::fake();
        WebHookController::fake($this->getCertificate());

        $this->postJson(config('aws_sns_notification.webhook.path'), $this->generateNotification([
            'type' => 'NullAction',
        ]))->assertOk();

        Event::assertDispatched(AwsSnsNotificationReceived::class, function (AwsSnsNotificationReceived $event) {
            return $event->toJson()->type === 'NullAction';
        });
    }

    private function generateNotification(array $message): array
    {
        $data = [
            'Type' => "Notification",
            'MessageId' => "792cda85-518f-5dd3-9163-81d851212f3a",
            'TopicArn' => "arn:aws:sns:us-east-2:295079676684:publish-and-verify-892f85fe-4836-424d-8188-ab85bef0f362",
            'Message' => json_encode($message),
            'Timestamp' => "2022-07-28T21:23:58.317Z",
            'SignatureVersion' => "1",
            'Signature' => "ghtf+deOBAzHJJZ6s6CdRLfTQAlcGzq9naoFM1wi0CJiq//uVRuZnamrkWNF0fhouMFvuLVRwcz8PZLUMSfnmd5VpdTKpTyiKmy1qJAZXma0w+yi7G+I33hD1Jyk1Nbym2n0kqp3fVu2aoooiN2ZeLAT2bH0/BtjLSfN1yAOKNoprco4qV9gGUZinXJdj9a1YdNhDR2jKi33ldlsVtEXAtiaDklGEk7DgRKX38GerBPiLg3FdtgY6KC7cdeGpU/dGK+4hjc83Ive1HoFkAwqhpgInM2sMytBosoiXfCmOKmU4xeGD0gHDNZTlJUJQDlzw8Eag0H9f/5zXF9d3uy0YQ==",
            'SigningCertURL' => "https://sns.us-east-2.amazonaws.com/SimpleNotificationService-7ff5318490ec183fbaddaa2a969abfda.pem",
            'UnsubscribeURL' => "https://sns.us-east-2.amazonaws.com/?Action=Unsubscribe&SubscriptionArn=arn:aws:sns:us-east-2:295079676684:publish-and-verify-892f85fe-4836-424d-8188-ab85bef0f362:2296bc94-7992-4be1-b15f-b97229b5c1d8",
        ];

        $message = new Message($data);

        $stringToSign = (new MessageValidator(fn () => $this->getCertificate()))->getStringToSign($message);

        $key = openssl_get_privatekey($this->getCertificatePrivateKey());

        openssl_sign($stringToSign, $signature, $key, OPENSSL_ALGO_SHA1);

        $data['Signature'] = base64_encode($signature);

        return $data;
    }

    private function getCertificatePrivateKey(): string
    {
        return
'-----BEGIN RSA PRIVATE KEY-----
MIICXAIBAAKBgQDGdbpzSBNL8o+jBOpLFIWOKze48xyAQryBr7CcEo5hAyUSz5Ca
4COVcGBv3Ux4CrkvCuQHCd57HfamCojWVJQq8jGClogKwXh7n5MOldHHk9ax0LEA
LWOsP4Mreh/ADhEUBk53GtbWpZb/8EjV4V8REeQ+Rbb3bKdfY6J1gypT5QIDAQAB
AoGAAnohs6KoqwACDvTWv80nxZiAf4x5RJiQpcW9nJUWtdtGMS/qDCTUDN90NLE8
bRslKJMoOXZEEqFTHMtw3cv8dW8fqtXV5yuDBYa7a2n12tJ+k7tck3LU0xfIoWtv
9AeEVsnBWWUF4IuatGYE2XzxxBJyZbQGQBm+/UlfsPcTgRkCQQDzkBtUVTnOUC9+
TYH9z6RngiVzJLYSJmCkwMmU6c+HVDEoqsUztjrldiesogD/VwttOwZgK8qjKZ1Q
qL5YZZ2XAkEA0JgHBeEh4FHor69IkBmtZDmQI94H8dfrphzDhGPMY5I6dvIzWeXo
Rk8paDCwBLCzlN2/lrFRF2PFt0kZ0l4B4wJAb9lts3yv3x7TsJzHZVdFmIMbz5S8
R/l3yDEAYXI961eue45woR6+TUHFVnHPI3NFvBOvCdsSY3to0vGq980yYQJADYsB
UblnOKg4wkdQH8L7BfnSyPFedK4/J37QluTf3UseLqDVAq4xoXb8Sj52/yb10eF/
0enbSPh6WscNPSJLXwJBAKehg78qorS4e0thmCLKqqeGB2vKLNQV3pCni8Huqa2w
oWN728YqB+t2eGtzkWvuSyn8U4qq9NtzvqXuiAC6CYE=
-----END RSA PRIVATE KEY-----';
    }

    private function getCertificate(): string
    {
        return
'-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDGdbpzSBNL8o+jBOpLFIWOKze4
8xyAQryBr7CcEo5hAyUSz5Ca4COVcGBv3Ux4CrkvCuQHCd57HfamCojWVJQq8jGC
logKwXh7n5MOldHHk9ax0LEALWOsP4Mreh/ADhEUBk53GtbWpZb/8EjV4V8REeQ+
Rbb3bKdfY6J1gypT5QIDAQAB
-----END PUBLIC KEY-----';
    }
}
