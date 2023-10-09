<?php

namespace App\Http\Controllers;

use GuzzleHttp\Exception\RequestException;
use http\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Illuminate\Support\Facades\Auth;

class TransactionQueryController extends Controller
{
    public function queryView()
    {
        return view('transaction-query');
    }
    public function query(Request $request)
    {
        try {
            $token = $this->getToken();
            if(is_null($token)){
                return response()->json(['message' => ' Oturumunuz sona erdi.'], 401);
            }
            $params = [];
            if ($request->has('page'))
            {
                $params['page'] = $request->get('page');
            }else{
                $params['page']=1;
            }
            if ($request->get('fromDate') !== 'all' )
            {
                $params['fromDate'] = $request->get('fromDate');
            }
            if ($request->get('toDate') !== 'all' )
            {
                $params['toDate'] = $request->get('toDate');
            }
            if ($request->get('status') !== 'all' )
            {
                $params['status'] = $request->get('status');
            }
            if ($request->get('operation') !== 'all' )
            {
                $params['operation'] = $request->get('operation');
            }
            if ($request->get('paymentMethod') !== 'all' )
            {
                $params['paymentMethod'] = $request->get('paymentMethod');
            }
            if ($request->get('errorCode') !== 'all' )
            {
                $params['errorCode'] = $request->get('errorCode');
            }
            $http = new \GuzzleHttp\Client;
            $response = $http->post(env('BASE_URL') . '/transaction/list',[
                'headers' => [
                    'Authorization' => $token,
                ],
                'json' =>[
                    $params
                ]
            ]);
            $result = json_decode($response->getBody(),true);
            }catch (\GuzzleHttp\Exception\RequestException $e){
                $statusCode = $e->getResponse()->getStatusCode();
                $errorBody = json_decode($e->getResponse()->getBody(), true);
                return response()->json(['error' => 'API isteği başarısız.'], $statusCode);
            }
        return view('transaction-query' , compact('result'));
    }
}


