<?php

namespace Tests\Unit;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Tests\TestCase;

class GetClientTest extends TestCase
{
    public function test_getClientApi_method_with_valid_request()
    {
        //kullanici giris yapmis olsun
        Cache::put('token',  60);

        $request = new Request([
            'transactionId' => '1-1444392550-1',
        ]);

        $apiResponse = [
            'id' => '1',
            'created_at' => '2015-10-09 12:09:10',
            'updated_at' => '2015-10-09 12:09:10',
            'deleted_at' => null,
            'number' => '401288XXXXXX1881',
            'expiryMonth' => '6',
            'expiryYear' => '2017',
            'startMonth' => null
        ];

        Http::fake([
            env('BASE_URL') . '/client' => Http::response(json_encode($apiResponse), 200),
        ]);
        $controller = new ClientController();
        $response = $controller->getClientApi($request);
        Http::assertSent(function ($request) {
            return
                $request->url() === env('BASE_URL') . '/client' &&
                $request->header('Authorization') === 'VALID_TOKEN' &&
                $request->json() == [
                    'id' => '1',
                    'created_at' => '2015-10-09 12:09:10',
                    'updated_at' => '2015-10-09 12:09:10',
                    'deleted_at' => null,
                    'number' => '401288XXXXXX1881',
                    'expiryMonth' => '6',
                    'expiryYear' => '2017',
                    'startMonth' => null
                ];
        });
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertViewHas('result', $apiResponse);
    }
    public function test_getClientApi_method_with_api_failure()
    {
        Cache::put('token', 'VALID_TOKEN', 60);

        $request = new Request([
            'transactionId' => '123456789',
        ]);
        $apiErrorResponse = [
        ];

        Http::fake([
            env('BASE_URL') . '/client' => Http::response(json_encode($apiErrorResponse), 500),
        ]);
        $controller = new ClientController();
        $response = $controller->getClientApi($request);
        Http::assertSent(function ($request) {
            return true;
        });
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $this->assertJsonStringEqualsJson(
            '{"error": "API isteği başarısız."}',
            $response->getContent()
        );
    }
}
