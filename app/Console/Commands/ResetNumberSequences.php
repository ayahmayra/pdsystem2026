<?php

namespace App\Console\Commands;

use App\Models\NumberSequence;
use Illuminate\Console\Command;

class ResetNumberSequences extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'number-sequences:reset 
                            {--doc-type= : Reset hanya untuk tipe dokumen tertentu (ND, SPT, SPPD, KWT, LAP)}
                            {--year= : Reset hanya untuk tahun tertentu}
                            {--all : Reset semua sequences ke 0}
                            {--confirm : Skip confirmation prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset semua atau sebagian number sequences ke 0 untuk memulai penomoran dari awal';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $docType = $this->option('doc-type');
        $year = $this->option('year');
        $all = $this->option('all');
        $confirm = $this->option('confirm');

        // Build query
        $query = NumberSequence::query();

        if ($docType) {
            $query->where('doc_type', $docType);
        }

        if ($year) {
            $query->where('year_scope', $year);
        }

        $count = $query->count();

        if ($count === 0) {
            $this->info('Tidak ada sequence yang ditemukan dengan kriteria yang diberikan.');
            return 0;
        }

        // Show summary
        $this->info("Akan direset {$count} sequence(s):");
        $this->line("  - Doc Type: " . ($docType ?: 'Semua'));
        $this->line("  - Tahun: " . ($year ?: 'Semua'));
        $this->line("  - Total: {$count} sequence(s)");

        // Confirmation
        if (!$confirm && !$all) {
            if (!$this->confirm('Apakah Anda yakin ingin mereset semua sequences ke 0?', false)) {
                $this->info('Operasi dibatalkan.');
                return 0;
            }
        }

        // Reset sequences
        $updated = $query->update([
            'current_value' => 0,
            'last_generated_at' => now(),
        ]);

        $this->info("✓ Berhasil mereset {$updated} sequence(s) ke 0.");
        $this->line("Dokumen berikutnya akan dimulai dari nomor 001.");

        return 0;
    }
}
