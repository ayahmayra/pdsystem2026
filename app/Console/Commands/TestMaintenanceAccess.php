<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\OrgSettings;
use App\Models\User;

class TestMaintenanceAccess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'maintenance:test {user_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test maintenance mode access for a user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id') ?? 1;
        $user = User::find($userId);
        
        if (!$user) {
            $this->error("User with ID {$userId} not found!");
            return 1;
        }

        $orgSettings = OrgSettings::getInstance();

        $this->line('');
        $this->info('=== MAINTENANCE MODE TEST ===');
        $this->line('');

        // Test User Info
        $this->line('👤 User Information:');
        $this->line("   ID: {$user->id}");
        $this->line("   Name: {$user->name}");
        $this->line("   Email: {$user->email}");
        $this->line('');

        // Test Roles
        $this->line('🎭 User Roles:');
        $roles = $user->getRoleNames();
        if ($roles->isEmpty()) {
            $this->warn('   No roles assigned');
        } else {
            foreach ($roles as $role) {
                $isSuperadmin = $role === 'superadmin';
                $icon = $isSuperadmin ? '⭐' : '•';
                $this->line("   {$icon} {$role}");
            }
        }
        $this->line('');

        // Test Superadmin Check
        $this->line('🔐 Superadmin Check:');
        $hasSuperadmin = $user->hasRole('superadmin') || $user->hasRole('super-admin');
        if ($hasSuperadmin) {
            $this->info('   ✅ User HAS superadmin role');
        } else {
            $this->error('   ❌ User DOES NOT have superadmin role');
        }
        $this->line('');

        // Maintenance Mode Status
        $this->line('🔧 Maintenance Mode Status:');
        if ($orgSettings->maintenance_mode) {
            $this->warn('   🔒 ENABLED');
            $this->line("   Message: {$orgSettings->maintenance_message}");
        } else {
            $this->info('   🔓 DISABLED');
        }
        $this->line('');

        // Access Prediction
        $this->line('🎯 Access Prediction:');
        if ($orgSettings->maintenance_mode) {
            if ($hasSuperadmin) {
                $this->info('   ✅ This user WILL BYPASS maintenance mode');
                $this->info('   ✅ Full system access granted');
            } else {
                $this->error('   ❌ This user WILL BE BLOCKED');
                $this->error('   ❌ Will see maintenance page');
            }
        } else {
            $this->info('   ✅ Maintenance mode is OFF');
            $this->info('   ✅ All users have normal access');
        }
        $this->line('');

        // Recommendations
        if ($orgSettings->maintenance_mode && !$hasSuperadmin) {
            $this->warn('⚠️  RECOMMENDATION:');
            $this->line('   To grant access during maintenance:');
            $this->line("   php artisan tinker");
            $this->line("   User::find({$userId})->assignRole('superadmin');");
            $this->line('');
        }

        return 0;
    }
}
