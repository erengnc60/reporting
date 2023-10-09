<?php

namespace App\Http\Controllers;

use App\Models\Users;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class LoginController extends Controller
{
    /**
     * @throws ContainerExceptionInterface
     * @throws GuzzleException
     * @throws NotFoundExceptionInterface
     */
    public function login(Request $request)
  {

      return view('login');

  }
  public function loginApi(Request $request)
  {
      $http = new \GuzzleHttp\Client;
      $email = $request->get('email');
      $password = $request->get('password');
      $response = $http->post(env('BASE_URL') . '/merchant/user/login',[
          'json' =>[
              'email' => $email,
              'password' => $password
          ]
      ]);
      $result = json_decode((string) $response->getBody(),true);
      if ($response->getStatusCode() === 200)
      {
          $accessToken = $result ['token'];
          Cache::put('token',$accessToken, now()->addMinutes(10));
      } else {
          $error = $result['code'];
          $errorMessage = $data['message'];
      }
      return view('welcome');
  }
  public function updateToken(Request $request)
  {
      /*   $token = cache(env('TOKEN_CACHE_KEY'));
         $http = new Client();

         try {
             $response = $http->post(env('BASE_URL') . '/merchant/user/login', [
                 'headers' => [
                     'Authorization' => 'Bearer ' . $token,
                     'Accept' => 'application/json',
                 ],
             ]);
             $data = json_decode($response->getBody(), true);
             return response()->json($data);
         } catch (\GuzzleHttp\Exception\RequestException $e) {
             $statusCode = $e->getResponse()->getStatusCode();
             $errorBody = json_decode($e->getResponse()->getBody(), true);
             return response()->json(['error' => 'Token Yenileme İşlemi başarısız.'], $statusCode);
         }
     } */
  }
}
