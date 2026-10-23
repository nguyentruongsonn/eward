<?php

namespace App\Services\Search;

use App\Contracts\Search\ApplicationSearchGateway;
use Elastic\Elasticsearch\Client;

class ElasticsearchApplicationSearchGateway implements ApplicationSearchGateway
{
    public function __construct(private readonly Client $client) {}

    public function search(array $body): array
    {
        return $this->client->search([
            'index' => $this->indexName(),
            'body' => $body,
        ])->asArray();
    }

    public function createIndex(): void
    {
        $indices = $this->client->indices();
        $index = $this->indexName();

        if ($indices->exists(['index' => $index])->asBool()) {
            return;
        }

        $indices->create([
            'index' => $index,
            'body' => [
                'settings' => [
                    'number_of_shards' => 1,
                    'number_of_replicas' => 0,
                ],
                'mappings' => [
                    'properties' => [
                        'id' => ['type' => 'keyword'],
                        'name' => ['type' => 'search_as_you_type'],
                        'email' => ['type' => 'keyword'],
                        'phone' => ['type' => 'keyword'],
                        'procedure_name' => ['type' => 'text'],
                        'status_id' => ['type' => 'integer'],
                        'citizen_id' => ['type' => 'integer'],
                        'received_at' => ['type' => 'date'],
                    ],
                ],
            ],
        ]);
    }

    public function index(string $id, array $document): void
    {
        $this->client->index([
            'index' => $this->indexName(),
            'id' => $id,
            'body' => $document,
            'refresh' => 'wait_for',
        ]);
    }

    public function bulk(array $operations): void
    {
        if ($operations === []) {
            return;
        }

        $this->client->bulk([
            'index' => $this->indexName(),
            'body' => $operations,
            'refresh' => 'wait_for',
        ]);
    }

    public function delete(string $id): void
    {
        try {
            $this->client->delete([
                'index' => $this->indexName(),
                'id' => $id,
                'refresh' => 'wait_for',
            ]);
        } catch (\Throwable $exception) {
            if (! str_contains($exception->getMessage(), '404')) {
                throw $exception;
            }
        }
    }

    private function indexName(): string
    {
        return (string) config('elasticsearch.index_prefix', 'eward_').'applications_v1';
    }
}
