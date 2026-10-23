<?php

namespace App\Services\Search;

use App\Contracts\Search\ApplicationSearchGateway;
use App\Models\HoSoXuLy;
use Illuminate\Support\Facades\Log;

class ApplicationSearchService
{
    public function __construct(private readonly ApplicationSearchGateway $gateway) {}

    public function searchApplicationIds(string $query, int $limit = 1000): ?array
    {
        if (! config('elasticsearch.enabled', false) || trim($query) === '') {
            return null;
        }

        try {
            $response = $this->gateway->search([
                'size' => max(1, min($limit, 1000)),
                'track_total_hits' => true,
                'query' => [
                    'bool' => [
                        'must' => [[
                            'multi_match' => [
                                'query' => trim($query),
                                'type' => 'bool_prefix',
                                'fields' => [
                                    'id',
                                    'name',
                                    'name._2gram',
                                    'name._3gram',
                                    'email',
                                    'phone',
                                    'procedure_name',
                                ],
                            ],
                        ]],
                    ],
                ],
            ]);
        } catch (\Throwable $exception) {
            Log::warning('Elasticsearch application search failed; falling back to MySQL.', [
                'message' => $exception->getMessage(),
            ]);

            return null;
        }

        $total = $response['hits']['total'] ?? 0;
        $total = is_array($total) ? ($total['value'] ?? 0) : $total;
        $hits = $response['hits']['hits'] ?? [];

        if ((int) $total > $limit) {
            return null;
        }

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

    public function sync(HoSoXuLy $application): void
    {
        if (! config('elasticsearch.enabled', false)) {
            return;
        }

        try {
            $application->loadMissing('tthc');
            $this->gateway->createIndex();
            $this->gateway->index((string) $application->getKey(), $this->document($application));
        } catch (\Throwable $exception) {
            Log::warning('Elasticsearch application indexing failed.', [
                'application_id' => $application->getKey(),
                'message' => $exception->getMessage(),
            ]);
        }
    }

    public function remove(string $applicationId): void
    {
        if (! config('elasticsearch.enabled', false)) {
            return;
        }

        try {
            $this->gateway->delete($applicationId);
        } catch (\Throwable $exception) {
            Log::warning('Elasticsearch application deletion failed.', [
                'application_id' => $applicationId,
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

        HoSoXuLy::query()->with('tthc')->orderBy('maHSXL')->chunkById(200, function ($applications) use (&$count): void {
            $operations = [];
            foreach ($applications as $application) {
                $operations[] = ['index' => ['_id' => (string) $application->getKey()]];
                $operations[] = $this->document($application);
                $count++;
            }

            $this->gateway->bulk($operations);
        }, 'maHSXL');

        return $count;
    }

    private function document(HoSoXuLy $application): array
    {
        return [
            'id' => (string) $application->getKey(),
            'name' => (string) $application->tenChuHoSo,
            'email' => (string) $application->email,
            'phone' => (string) $application->soDienThoai,
            'procedure_name' => $application->tthc?->tenTTHC,
            'status_id' => $application->maTrangThai !== null ? (int) $application->maTrangThai : null,
            'citizen_id' => $application->IDCD !== null ? (int) $application->IDCD : null,
            'received_at' => $application->ngayTiepNhan?->toDateString(),
        ];
    }
}
