<?php

namespace App\Contracts\Search;

interface ApplicationSearchGateway
{
    public function search(array $body): array;

    public function createIndex(): void;

    public function index(string $id, array $document): void;

    public function bulk(array $operations): void;

    public function delete(string $id): void;
}
