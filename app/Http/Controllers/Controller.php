<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use function PHPUnit\Framework\isNull;

class Controller extends BaseController
{

    use AuthorizesRequests, ValidatesRequests;
    public function getToken()
    {
            $token = Cache::remember('TOKEN_CACHE_KEY',540,function (){
            $http = new \GuzzleHttp\Client;
            $response = $http->post(env('BASE_URL') . '/merchant/user/login',[
                'json' =>[
                    'email' => 'demo@financialhouse.io',
                    'password' => 'cjaiU8CV'
                ]
            ]);
            $result = json_decode((string) $response->getBody(),true);
            if ($response->getStatusCode() === 200) {
                return $result ['token'];
            }
            return null;
        });
        if(is_null($token)) {
            Cache::forget('TOKEN_CACHE_KEY');
        }
        return $token;
    }

}
