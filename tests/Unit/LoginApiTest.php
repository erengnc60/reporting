<?php

namespace Tests\Unit;
use Tests\TestCase;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
class LoginApiTest extends TestCase
{
    public function valid()
    {
        $request = new Request([
            'email' => 'merchant@test.com',
            'password' => '123*-+"',
        ]);
        $apiResponse = [
            'token' => 'token',
        ];
        Http::fake([
            env('BASE_URL') . '/merchant/user/login' => Http::response(json_encode($apiResponse), 200),
        ]);

        $controller = new AuthenticationController();
        $response = $controller->loginApi($request);

        Http::assertSent(function ($request) use ($request) {
            return
                $request->url() === env('BASE_URL') . '/merchant/user/login' &&
                $request->json() == [
                    'email' => 'merchant@test.com',
                    'password' => '123*-+"',
                ];
        });

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue(Cache::has('token'));
        $this->assertEquals('token', Cache::get('token'));
    }
    public function invalid()
    {
        $request = new Request([
            'email' => 'dadadad@dadada.com',
            'password' => 'dadadadada',
        ]);


        $apiResponse = [
            'code' => 0,
            'status' = "DECLINED",
            'message' => 'Error: Merchant User credentials is not valid',
        ];

        Http::fake([
            env('BASE_URL') . '/merchant/user/login' => Http::response(json_encode($apiResponse), 401),
        ]);

        $controller = new AuthenticationController();
        $response = $controller->loginApi($request);

        Http::assertSent(function ($request) use ($request) {
            return
                $request->url() === env('BASE_URL') . '/merchant/user/login' &&
                $request->json() == [
                    'email' => 'dadadad@dadada.com',
                    'password' => 'dadadadada',
                ];
        });


        $this->assertEquals(200, $response->getStatusCode());
        $this->assertFalse(Cache::has('token'));
        $this->assertEquals(0, $apiResponse['code']);
        $this->assertEquals('Geçersiz kimlik bilgileri', $apiResponse['message']);
    }
}
