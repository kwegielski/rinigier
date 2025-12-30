<?php

declare(strict_types=1);

namespace App\Service\UserApi;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;

class PhoenixUserApi implements UserApiInterface
{
    public function __construct(
        private HttpClientInterface $client,
        private string $baseUrl
    ) {}

    public function list(array $filters = []): array
    {
        return $this->request('GET', '/users', $filters);
    }

    public function get(int $id): array
    {
        return $this->request('GET', "/users/$id")['data'];
    }

    public function create(array $data): void
    {
        $this->request('POST', '/users', ['user' => $data]);
    }

    public function update(int $id, array $data): void
    {
        $this->request('PUT', "/users/$id", ['user' => $data]);
    }

    public function delete(int $id): void
    {
        $this->client->request('DELETE', $this->baseUrl."/users/$id");
    }

    private function request(string $method, string $uri, array $payload = []): array
    {
        $options = $method === 'GET' ? ['query' => $payload] : ['json' => $payload];

        try {
            return $this->client->request($method, $this->baseUrl.$uri, $options)->toArray();
        } catch (ClientExceptionInterface $e) {
            $response = $e->getResponse();
            $statusCode = $response->getStatusCode();
            $content = $response->toArray(false);

            if ($statusCode === 422 && isset($content['errors'])) {
                throw new ValidationException($content['errors']);
            }

            throw $e;
        }
    }

    public function import(): void
    { 
        $response = $this->client->request(
            'POST', 
            $this->baseUrl. '/import', 
            [
                'json' => [
                    'token' => 'supersecret123',
                ],
            ]
            );
  
        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException('Import failed with status ' . $response->getStatusCode());
        }
    }
}
