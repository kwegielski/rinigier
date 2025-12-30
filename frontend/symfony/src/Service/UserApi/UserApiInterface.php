<?php

declare(strict_types = 1);

namespace App\Service\UserApi;

use Symfony\Contracts\HttpClient\HttpClientInterface;

interface UserApiInterface
{
    public function list(array $filters = []): array;

    public function get(int $id): array;

    public function create(array $data): void;

    public function update(int $id, array $data): void;

    public function delete(int $id): void;

    public function import(): void;

}
