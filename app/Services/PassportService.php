<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;

class PassportService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client;
    }

    public function passwordGrantToken(array $input, $scope = '')
    {
        $response = $this->client->post(env('APP_URL') . '/oauth/token', [
            'form_params' => [
                'grant_type' => 'password',
                'client_id' => env('FPOLL_CLIENT_ID'),
                'client_secret' => env('FPOLL_CLIENT_SECRET'),
                'username' => $input['email'],
                'password' => $input['password'],
                'scope' => $scope,
            ],
        ]);

        return json_decode((string)$response->getBody(), true);
    }

    public function refreshGrantToken($refreshTokenKey)
    {
        $response = $this->client->post(env('APP_URL') . '/oauth/token', [
            'form_params' => [
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshTokenKey,
                'client_id' => env('API_CLIENT_ID'),
                'client_secret' => env('API_CLIENT_SECRET'),
            ],
        ]);

        return json_decode((string)$response->getBody(), true);
    }

    public function getTokenByUser($user)
    {
        return $user->createToken('myToken')->accessToken;
    }
}
