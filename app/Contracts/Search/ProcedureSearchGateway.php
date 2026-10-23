<?php

namespace App\Contracts\Search;

interface ProcedureSearchGateway
{
    public function search(array $body): array;

    public function createIndex(): void;

    public function index(int $id, array $document): void;

    public function bulk(array $operations): void;

    public function delete(int $id): void;
}
