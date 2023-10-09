<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class TransactionTest extends TestCase
{
    public function valid_request()
    {
        // Kullanıcı oturumu açık olsun
        Cache::put('token', 'VALID_TOKEN', 60);

        $request = new Request([
            'fromDate' => '2023-01-01',
            'toDate' => '2023-12-31',
            'merchant' => '1',
            'acquirer' => '1',
        ]);

        $apiResponse = [
            'count' => '283',
            'total' => '28300',
            'currency' => 'USD',
        ];

        Http::fake([
            env('BASE_URL') . '/transactions/report' => Http::response(json_encode($apiResponse), 200),
        ]);

        $controller = new TransactionController();
        $response = $controller->Transaction($request);

        Http::assertSent(function ($request) use ($apiResponse) {
            return
                $request->url() === env('BASE_URL') . '/transactions/report' &&
                $request->header('Authorization') === 'VALID_TOKEN' && // Token kontrolü
                $request->json() == [
                    'fromDate' => '2023-01-01',
                    'toDate' => '2023-12-31',
                    'merchant' => 'example_merchant',
                    'acquirer' => 'example_acquirer',
                ];
        });
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertViewHas('result', $apiResponse);
    }

    public function api_response()
    {
        // Kullanıcı oturumu açık olsun
        Cache::put('token', 'VALID_TOKEN', 60);

        $request = new Request([
            'fromDate' => '2023-01-01',
            'toDate' => '2023-12-31',
            'merchant' => '1',
            'acquirer' => '1',
        ]);

        $apiResponse = [
            'count' => '283',
        ];


        Http::fake([
            env('BASE_URL') . '/transactions/report' => Http::response(json_encode($apiResponse), 200),
        ]);

        $controller = new TransactionController();
        $response = $controller->Transaction($request);

        Http::assertSent(function ($request) {
            return true;
        });

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $this->assertJsonStringEqualsJson(
            '{"error": "API yanıtında eksik datalar var."}',
            $response->getContent()
        );
    }

    public function no_user_token()
    {
        // Kullanıcı oturumu kapalı olsun (token yok)
        Cache::forget('token');
        $request = new Request([
            'fromDate' => '2023-01-01',
            'toDate' => '2023-12-31',
            'merchant' => 'example_merchant',
            'acquirer' => 'example_acquirer',
        ]);

        $controller = new TransactionController();
        $response = $controller->Transaction($request);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('/login', $response->getTargetUrl());
    }
}



