<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\OrgSettings;

class MaintenanceModeToggle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'maintenance:toggle {action? : on/off/status}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Toggle maintenance mode on or off';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');
        $orgSettings = OrgSettings::getInstance();

        if (!$action) {
            // Show current status and ask what to do
            $currentStatus = $orgSettings->maintenance_mode ? 'ON' : 'OFF';
            $this->info("Current maintenance mode status: {$currentStatus}");
            
            $action = $this->choice(
                'What would you like to do?',
                ['status', 'on', 'off'],
                0
            );
        }

        switch (strtolower($action)) {
            case 'on':
                $orgSettings->update(['maintenance_mode' => true]);
                $this->info('✅ Maintenance mode is now ENABLED');
                $this->warn('⚠️  All users (except superadmin) will see maintenance page');
                break;

            case 'off':
                $orgSettings->update(['maintenance_mode' => false]);
                $this->info('✅ Maintenance mode is now DISABLED');
                $this->info('🎉 All users can access the system normally');
                break;

            case 'status':
                $status = $orgSettings->maintenance_mode ? 'ENABLED' : 'DISABLED';
                $emoji = $orgSettings->maintenance_mode ? '🔒' : '🔓';
                $this->line('');
                $this->line("  {$emoji} Maintenance Mode: <fg=yellow>{$status}</>");
                
                if ($orgSettings->maintenance_mode) {
                    $this->line("  📝 Message: {$orgSettings->maintenance_message}");
                    $this->line('');
                    $this->warn('  To disable: php artisan maintenance:toggle off');
                }
                $this->line('');
                break;

            default:
                $this->error("Invalid action. Use: on, off, or status");
                return 1;
        }

        // Clear cache after toggling
        $this->call('cache:clear');
        $this->call('config:clear');

        return 0;
    }
}
