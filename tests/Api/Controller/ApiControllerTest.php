<?php

declare(strict_types=1);

namespace App\Tests\Api\Controller;

use App\Api\Controller\ApiController;
use App\Tests\_tools\JsonApiRequest;
use App\Tests\ApiTestCase;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class ApiControllerTest extends ApiTestCase
{
    /**
     * Отправка заявки в банк
     */
    public function testItSendMessageToRabbit(): void
    {
        $this->mockClockOn(new \DateTimeImmutable('2020-01-01 10:20:30'));

        /** @see ApiController::sendLoadRequestToBank() */
        $this->requestJsonApi(
            JsonApiRequest::post('request/create')
                ->jsonBody([
                    "messageId" => "21ba13b4-b185-4882-ac6f-d147355987eb",
                    "bank" => "otp",
                    "blankId" => 5,
                    "request" => [
                        "url" => "https://otp.ru/soap/",
                        "body" => "payload",
                        "metadata" => ["action" => "send"]
                    ],
                    "callback" => [
                        "url" => "http://callback.url",
                        "returnData" => ["additionalField" => 555]
                    ]
                ])
        );

        $published = $this->rabbitConnection()->getFirstMessageForReceiver('otp');

        self::assertNotNull($published);
        $this->assertMatchesJsonSnapshot($published);
    }

    /**
     * Если в системе нет банка с переданным кодом, возвращается ошибка с кодом 100
     */
    public function testItReturnsErrorOnUnsupportedBankCode(): void
    {
        /** @see ApiController::sendLoadRequestToBank() */
        $response = $this->requestJsonApi(
            JsonApiRequest::post('request/create')
                ->expectingStatusCode(422)
                ->jsonBody([
                    "messageId" => "21ba13b4-b185-4882-ac6f-d147355987eb",
                    "bank" => "unsopported",
                    "blankId" => 5,
                    "request" => ["url" => "u", "body" => "b"],
                    "callback" => ["url" => "u"]
                ])
        );

        self::assertEquals(100, $response['code'] ?? 0);
    }
}
