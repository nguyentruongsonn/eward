<?php

namespace App\Services\Search;

use App\Contracts\Search\ProcedureSearchGateway;
use App\Models\TTHC;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProcedureSearchService
{
    public function __construct(private readonly ProcedureSearchGateway $gateway) {}

    public function searchPublicProcedureIds(string $query, ?int $fieldId, int $page, int $perPage): ?array
    {
        if (! config('elasticsearch.enabled', false) || trim($query) === '') {
            return null;
        }

        $filters = [
            ['term' => ['is_public' => true]],
        ];
        if ($fieldId !== null) {
            $filters[] = ['term' => ['field_id' => $fieldId]];
        }

        $search = trim($query);
        $normalizedSearch = trim(Str::ascii(Str::lower($search))) ?: $search;

        try {
            $response = $this->gateway->search([
                'from' => max(0, $page - 1) * $perPage,
                'size' => $perPage,
                'track_total_hits' => true,
                'query' => [
                    'bool' => [
                        'must' => [[
                            'multi_match' => [
                                'query' => $normalizedSearch,
                                'type' => 'bool_prefix',
                                'fields' => [
                                    'name_normalized',
                                    'name_normalized._2gram',
                                    'name_normalized._3gram',
                                    'code',
                                    'field_name_normalized',
                                ],
                            ],
                        ]],
                        'filter' => $filters,
                    ],
                ],
            ]);
        } catch (\Throwable $exception) {
            Log::warning('Elasticsearch procedure search failed; falling back to MySQL.', [
                'message' => $exception->getMessage(),
            ]);

            return null;
        }

        $total = $response['hits']['total'] ?? 0;
        $total = is_array($total) ? ($total['value'] ?? 0) : $total;
        $hits = $response['hits']['hits'] ?? [];

        return [
            'ids' => array_values(array_filter(array_map(
                static fn (array $hit): string => (string) ($hit['_id'] ?? ''),
                $hits,
            ))),
            'total' => max(0, (int) $total),
        ];
    }

    public function createIndex(): void
    {
        $this->gateway->createIndex();
    }

    public function sync(TTHC $procedure): void
    {
        if (! config('elasticsearch.enabled', false)) {
            return;
        }

        try {
            $this->gateway->createIndex();
            $this->gateway->index((int) $procedure->getKey(), $this->document($procedure));
        } catch (\Throwable $exception) {
            Log::warning('Elasticsearch procedure indexing failed.', [
                'procedure_id' => $procedure->getKey(),
                'message' => $exception->getMessage(),
            ]);
        }
    }

    public function remove(int $procedureId): void
    {
        if (! config('elasticsearch.enabled', false)) {
            return;
        }

        try {
            $this->gateway->delete($procedureId);
        } catch (\Throwable $exception) {
            Log::warning('Elasticsearch procedure deletion failed.', [
                'procedure_id' => $procedureId,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    public function reindex(): int
    {
        if (! config('elasticsearch.enabled', false)) {
            return 0;
        }

        $this->gateway->createIndex();
        $count = 0;
        $operations = [];

        TTHC::query()->with('linhVuc')->orderBy('maTTHC')->chunkById(200, function ($procedures) use (&$count, &$operations): void {
            foreach ($procedures as $procedure) {
                $operations[] = ['index' => ['_id' => (string) $procedure->getKey()]];
                $operations[] = $this->document($procedure);
                $count++;
            }

            $this->gateway->bulk($operations);
            $operations = [];
        }, 'maTTHC');

        return $count;
    }

    private function document(TTHC $procedure): array
    {
        $name = (string) $procedure->tenTTHC;
        $fieldName = $procedure->linhVuc?->tenLinhVuc;

        return [
            'id' => (int) $procedure->getKey(),
            'code' => (string) $procedure->getKey(),
            'name' => $name,
            'name_normalized' => Str::ascii(Str::lower($name)),
            'field_id' => $procedure->maLinhVuc !== null ? (int) $procedure->maLinhVuc : null,
            'field_name' => $fieldName,
            'field_name_normalized' => $fieldName !== null ? Str::ascii(Str::lower($fieldName)) : null,
            'is_public' => $procedure->trangThai === null || $procedure->trangThai === 'Công khai',
            'updated_at' => now()->toIso8601String(),
        ];
    }
}
