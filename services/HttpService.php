<?php

namespace Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class HttpService
{
    private Client $client;
    private string $apiKey;
    private string $publicKey;
    private $defaultOptions = [
        'headers' => [
            'Accept'       => 'application/json',
            'Content-Type' => 'application/json',
        ],
        'query' => [
            'accountId' => 512322,
            'currency' => 'ARS',
        ]
    ];

    public function __construct(string $baseUri = '')
    {
        $this->client = new Client([
            'base_uri' => $baseUri,
            'timeout'  => 10.0,
            'verify'   => false,
        ]);

        $this->publicKey = 'PKaC6H4cEDJD919n705L544kSU';
        $this->apiKey    = '4Vj8eK4rloUd272L48hsrarnUA';
    }

    public function get(string $uri, array $options = []): array
    {
        $options = $this->prepareOptions('GET', $uri, $options);
        return $this->request('GET', $uri, $options);
    }

    public function post(string $uri, array $options = []): array
    {
        return $this->request('POST', $uri, $options);
    }

    public function put(string $uri, array $options = []): array
    {
        return $this->request('PUT', $uri, $options);
    }

    public function delete(string $uri, array $options = []): array
    {
        return $this->request('DELETE', $uri, $options);
    }

    private function request(string $method, string $uri, array $options = []): array
    {
        try {
            $response = $this->client->request($method, $uri, $options);
            $body = (string) $response->getBody();
            $data = json_decode($body, true);
            return $data !== null ? $data : ['raw' => $body];
        } catch (RequestException $e) {
            return [
                'error' => true,
                'message' => $e->getMessage(),
                'status' => $e->hasResponse() ? $e->getResponse()->getStatusCode() : null,
            ];
        }
    }

    private function prepareOptions(string $method, string $uri, array $options): array
    {
        // Merge defaults
        $options = array_merge_recursive($this->defaultOptions, $options);

        // Generar fecha UTC
        $date = gmdate('D, d M Y H:i:s \G\M\T');

        // Crear string a firmar
        $contentToSign = $method . "\n\n\n" . $date . "\n" . $uri;

        // HMAC-SHA256 y base64
        $hash = hash_hmac('sha256', $contentToSign, $this->apiKey, true);
        $signature = base64_encode($hash);

        error_log($this->publicKey);
        // Añadir headers HMAC
        $options['headers']['Date'] = $date;
        $options['headers']['Authorization'] = "Hmac {$this->publicKey}:{$signature}";

        return $options;
    }
}
