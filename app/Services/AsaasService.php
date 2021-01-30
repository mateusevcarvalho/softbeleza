<?php


namespace App\Services;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class AsaasService
{
    private Client $client;

    public function __construct()
    {
        $client = new Client([
            'base_uri' => env('ASAAS_URL_BASE'),
            'headers' => [
                'access_token' => env('ASAAS_TOKEN'),
                'Accept' => 'application/json'
            ]
        ]);

        $this->client = $client;
    }

    public function createClient(array $data)
    {
        $response = $this->client->request('POST', 'customers', ['json' => $data]);
        return json_decode($response->getBody(), true);
    }

    public function payment(array $data)
    {
        try {
            $response = $this->client->request('POST', 'payments', ['json' => $data]);
            return json_decode($response->getBody(), true);
        } catch (ClientException $exception) {
            return json_decode($exception->getResponse()->getBody(), true);
        }
    }


    public function listPayment($id)
    {
        $response = $this->client->request('GET', "payments/$id");
        return json_decode($response->getBody(), true);
    }
}
