<?php

namespace App\Http\Controllers;

class ApiController extends Controller
{
  public function index()
  {
      return view('index');
  }

  public function getData(Request $request)
  {
      $filterValue = $request->input('filter');
      $token = $this->getToken();
      $http = new \GuzzleHttp\Client;
      $response = $http->post(env('BASE_URL') . '/transactions/report',[
          'headers' => [
              'Authorization' => $token,
          ],
          'json' =>[
              'filter' => $filterValue
          ]
      ]);
  }
}
