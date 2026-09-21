<?php

namespace App\Console\Commands;

use App\Services\Search\ProcedureSearchService;
use Illuminate\Console\Command;

class ReindexSearchProcedures extends Command
{
    protected $signature = 'search:reindex {index=procedures}';

    protected $description = 'Rebuild the Elasticsearch index for public procedures';

    public function handle(ProcedureSearchService $search): int
    {
        if ($this->argument('index') !== 'procedures') {
            $this->error('Only the procedures index is supported.');

            return self::INVALID;
        }

        if (! config('elasticsearch.enabled', false)) {
            $this->error('Elasticsearch is disabled. Set ELASTICSEARCH_ENABLED=true first.');

            return self::FAILURE;
        }

        try {
            $count = $search->reindex();
        } catch (\Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->info("Indexed {$count} procedures.");

        return self::SUCCESS;
    }
}
