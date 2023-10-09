<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Illuminate\Support\Facades\Auth;

class TransactionReportController extends Controller
{
    public function transactionView(Request $request)
    {
        return view('transaction-report');
    }
    public function Transaction(Request $request)
    {
            try{
                $token = $this->getToken();
                if (!cache()->has('token')) {
                    return redirect('/login');
                }
                $http = new \GuzzleHttp\Client;
                $fromDate = $request->input('fromDate');
                $toDate = $request->input('toDate');
                $merchant = $request->input('merchant');
                $acquirer = $request->input('acquirer');
                $response = $http->post(env('BASE_URL') . '/transactions/report',[
                    'headers' => [
                        'Authorization' => $token,
                    ],
                    'json' =>[
                        'fromDate' => $fromDate,
                        'toDate' => $toDate,
                        'merchant' => $merchant,
                        'acquirer' => $acquirer,
                    ]
                ]);
                $result = json_decode($response->getBody(),true);

                /*$transaction = new Transaction();
                    $transaction->count = $count;
                    $transaction->total = $total;          Örnek = //Gelen datayı veri tabanına kaydetme
                    $transaction->currency = $currency;
                    $transaction->save(); */

                if (isset($result['count , total , currency'])) {
                    $count = $result['count'];
                    $total = $result['total'];
                    $currency = $result['currency'];
                } else {
                    return response()->json(['error' => 'API yanıtında eksik datalar var.'], 400);
                }

            } catch (\GuzzleHttp\Exception\RequestException $e){
                $statusCode = $e->getResponse()->getStatusCode();
                $errorBody = json_decode($e->getResponse()->getBody(), true);
                return response()->json(['error' => 'API isteği başarısız.'], $statusCode);
            }
        return view('welcome' , compact('result'));
    }
}
