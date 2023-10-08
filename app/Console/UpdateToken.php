<?php

namespace App\Console;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class UpdateToken
{

    public function __construct()
    {
        parent::__construct();
    }
    public function handle()
    {
        $client = new Client();
        $response = $client->post(env('BASE_URL') . '/merchant/user/login', [
            'json' => [
                'email' => 'demo@financialhouse.io',
                'password' => 'cjaiU8CV',
            ],
        ]);
        $token = json_decode($response->getBody(), true)['token'];
        Cache::put('TOKEN_CACHE_KEY', $token, now()->addMinutes(9));

        $this->info('güncellendi.');
    }
}
