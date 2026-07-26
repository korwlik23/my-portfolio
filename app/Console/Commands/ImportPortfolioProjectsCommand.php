<?php

namespace App\Console\Commands;

use App\Services\PortfolioProjectImporter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\ValidationException;

class ImportPortfolioProjectsCommand extends Command
{
    protected $signature = 'portfolio:import-projects
        {path : Path to the JSON import file}
        {--dry-run : Validate and summarize without writing to the database}
        {--force : Allow writes while the application is running in production}';

    protected $description = 'Import portfolio projects from an AI-generated JSON file';

    public function handle(PortfolioProjectImporter $importer): int
    {
        if (app()->isProduction() && ! $this->option('force') && ! $this->option('dry-run')) {
            $this->error('Refusing to import in production without --force. Run with --dry-run first.');

            return self::FAILURE;
        }

        $argumentPath = (string) $this->argument('path');
        $path = preg_match('/^(?:[A-Za-z]:[\\\\\/]|[\\\\\/])/', $argumentPath)
            ? $argumentPath
            : base_path($argumentPath);
        if (! File::exists($path)) {
            $this->error("Import file not found: {$path}");

            return self::FAILURE;
        }

        $payload = json_decode(File::get($path), true);
        if (! is_array($payload) || json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Import file must be valid JSON: '.json_last_error_msg());

            return self::FAILURE;
        }

        try {
            $summary = $importer->import($payload, (bool) $this->option('dry-run'));
        } catch (ValidationException $exception) {
            $this->error('Import validation failed.');
            foreach ($exception->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $this->line("- {$field}: {$message}");
                }
            }

            return self::FAILURE;
        }

        $this->info($summary['dry_run'] ? 'Dry-run passed. No records were written.' : 'Portfolio projects imported.');
        $this->table(['Slug', 'Name', 'Action'], $summary['projects']);
        $this->line(sprintf(
            'Total: %d, Created: %d, Updated: %d, Restored: %d',
            $summary['total'],
            $summary['created'],
            $summary['updated'],
            $summary['restored'],
        ));

        return self::SUCCESS;
    }
}
