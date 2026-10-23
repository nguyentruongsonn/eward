<?php

namespace App\Console\Commands;

use App\Services\Search\ApplicationSearchService;
use Illuminate\Console\Command;

class ReindexSearchApplications extends Command
{
    protected $signature = 'search:reindex-applications';

    protected $description = 'Rebuild the Elasticsearch index for applications';

    public function handle(ApplicationSearchService $search): int
    {
        if (! config('elasticsearch.enabled', false)) {
            $this->error('Elasticsearch is disabled. Set ELASTICSEARCH_ENABLED=true first.');

            return self::FAILURE;
        }

        $this->info('Reindexing applications...');
        try {
            $count = $search->reindex();
        } catch (\Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }
        $this->info("Indexed {$count} applications.");

        return self::SUCCESS;
    }
}
