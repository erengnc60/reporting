<?php

namespace Tests\Unit;
use Tests\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class TransactionControllerTest extends TestCase
{
    public function valid_request()
    {

        $request = new Request([
            'page' => 2,
            'fromDate' => '2023-01-01',
            'toDate' => '2023-12-31',
            'status' => 'APPROVED',
            'operation' => 'DIRECT',
            'paymentMethod' => 'CREDITCARD'
        ]);

        $apiResponse = [
            'result' => [
                'per_page' => '50',
                'current_page' => '1',
                'next_page_url' => "http://reporting.rpdpymnt.com/api/v3/transaction/list/?page=2",
                'prev_page_url' => null,
                'from' => '1',
                'to' => '50',
            ],
        ];

        Http::fake([
            env('BASE_URL') . '/transaction/list' => Http::response($apiResponse, 200),
        ]);

        $controller = new TransactionController();
        $response = $controller->query($request);

        Http::assertSent(function ($request) use ($apiResponse) {
            return
                $request->url() === env('BASE_URL') . '/transaction/list' &&
                $request->header('Authorization') === 'YOUR_TOKEN' &&
                $request->json() == [
                    'page' => 2,
                    'fromDate' => '2015-07-01',
                    'toDate' => '2015-10-01',
                    'status' => 'APPROVED',
                    'operation' => 'DIRECT',
                    'paymentMethod' => 'CREDITCARD ',
                    'errorCode' => '0',
                ];
        });
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertViewHas('result', $apiResponse['result']);
    }

}
