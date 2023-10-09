<?php

namespace App\Http\Controllers;

use http\Env\Request;

class GetClientController
{
 public function ClientView (Request $request)
 {
     return view('get-client');
 }

 public function getClientApi(Request $request)
 {

     try{
         $token = $this->getToken();
         if (!cache()->has('token')) {
             return redirect('/login');
         }
         $http = new \GuzzleHttp\Client;
         $transactionId = $request->input('transactionId');
         $response = $http->post(env('BASE_URL') . '/transactions/report',[
             'headers' => [
                 'Authorization' => $token,
             ],
             'json' =>[
                 'fromDate' => $transactionId,
             ]
         ]);
         $result = json_decode($response->getBody(),true);

     } catch (\GuzzleHttp\Exception\RequestException $e){
         $statusCode = $e->getResponse()->getStatusCode();
         $errorBody = json_decode($e->getResponse()->getBody(), true);
         return response()->json(['error' => 'API isteği başarısız.'], $statusCode);
     }
     return view('get-client' , compact('result'));
 }

}
