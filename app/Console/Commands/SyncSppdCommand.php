<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\NotaDinas;
use App\Services\SppdManagementService;

class SyncSppdCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sppd:sync {--nota-dinas-id= : Sync specific Nota Dinas by ID} {--all : Sync all Nota Dinas}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync SPPD documents for Nota Dinas participants';

    protected $sppdManagementService;

    public function __construct(SppdManagementService $sppdManagementService)
    {
        parent::__construct();
        $this->sppdManagementService = $sppdManagementService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $notaDinasId = $this->option('nota-dinas-id');
        $syncAll = $this->option('all');

        if ($notaDinasId) {
            $this->syncSpecificNotaDinas($notaDinasId);
        } elseif ($syncAll) {
            $this->syncAllNotaDinas();
        } else {
            $this->error('Please specify --nota-dinas-id or --all option');
            return 1;
        }

        return 0;
    }

    protected function syncSpecificNotaDinas($notaDinasId)
    {
        $notaDinas = NotaDinas::with(['spt.sppds', 'participants'])->find($notaDinasId);

        if (!$notaDinas) {
            $this->error("Nota Dinas with ID {$notaDinasId} not found");
            return;
        }

        $this->info("Syncing SPPD for Nota Dinas ID: {$notaDinasId}");
        
        $syncStatus = $this->sppdManagementService->getSyncStatus($notaDinas);
        $this->info("Status: " . $syncStatus['message']);

        if ($syncStatus['needs_sync']) {
            try {
                $this->sppdManagementService->syncSppdForNotaDinas($notaDinas);
                $this->info("SPPD sync completed successfully");
            } catch (\Exception $e) {
                $this->error("Failed to sync SPPD: " . $e->getMessage());
            }
        } else {
            $this->info("No sync needed");
        }
    }

    protected function syncAllNotaDinas()
    {
        $this->info("Starting sync for all Nota Dinas...");

        $notaDinasList = NotaDinas::with(['spt.sppds', 'participants'])
            ->whereHas('spt')
            ->get();

        $bar = $this->output->createProgressBar($notaDinasList->count());
        $bar->start();

        $syncedCount = 0;
        $errorCount = 0;

        foreach ($notaDinasList as $notaDinas) {
            try {
                if ($this->sppdManagementService->needsSppdSync($notaDinas)) {
                    $this->sppdManagementService->syncSppdForNotaDinas($notaDinas);
                    $syncedCount++;
                }
            } catch (\Exception $e) {
                $errorCount++;
                $this->newLine();
                $this->error("Error syncing Nota Dinas ID {$notaDinas->id}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Sync completed:");
        $this->info("- Total Nota Dinas processed: " . $notaDinasList->count());
        $this->info("- Successfully synced: " . $syncedCount);
        $this->info("- Errors: " . $errorCount);
    }
}
