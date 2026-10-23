<?php

namespace App\Services\Search;

use App\Contracts\Search\ProcedureSearchGateway;
use Elastic\Elasticsearch\Client;

class ElasticsearchProcedureSearchGateway implements ProcedureSearchGateway
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
                        'id' => ['type' => 'integer'],
                        'code' => ['type' => 'keyword'],
                        'name' => ['type' => 'search_as_you_type'],
                        'field_id' => ['type' => 'integer'],
                        'field_name' => ['type' => 'text'],
                        'is_public' => ['type' => 'boolean'],
                        'updated_at' => ['type' => 'date'],
                    ],
                ],
            ],
        ]);
    }

    public function index(int $id, array $document): void
    {
        $this->client->index([
            'index' => $this->indexName(),
            'id' => (string) $id,
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

    public function delete(int $id): void
    {
        try {
            $this->client->delete([
                'index' => $this->indexName(),
                'id' => (string) $id,
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
        return (string) config('elasticsearch.index_prefix', 'eward_').'procedures_v1';
    }
}
