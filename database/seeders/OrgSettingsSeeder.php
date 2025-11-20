<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrgSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if org_settings already has data
        $existingCount = DB::table('org_settings')->count();
        
        if ($existingCount === 0) {
            DB::table('org_settings')->insert([
                'name' => 'Badan Pengelola Keuangan dan Aset Daerah',
                'short_name' => 'BPKAD',
                'address' => 'Jl. Raya Bengkalis',
                'city' => 'Bengkalis',
                'province' => 'Riau',
                'phone' => '0766-123456',
                'email' => 'info@bpkad.bengkalis.go.id',
                'website' => 'https://bpkad.bengkalis.go.id',
                'head_title' => 'Kepala Badan Pengelola Keuangan dan Aset Daerah',
                'ym_separator' => '/',
                'qr_footer_text' => 'BPKAD Kabupaten Bengkalis',
                'show_left_logo' => true,
                'show_right_logo' => false,
                'singleton' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            $this->command->info('Organization settings created successfully!');
        } else {
            $this->command->info('Organization settings already exists, skipping...');
        }
    }
}