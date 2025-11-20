<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Existing seeders
            UnitsSeeder::class,
            PositionsSeeder::class,
            RanksSeeder::class,
            EchelonsSeeder::class,
            OrgSettingsSeeder::class,
            
            // Location seeders
            ProvincesSeeder::class,
            CitiesSeeder::class,
            DistrictsSeeder::class,
            OrgPlacesSeeder::class,
            
            // Transport & Route seeders
            TransportModesSeeder::class,
            TravelRoutesSeeder::class,
            
            // Travel Grade seeders
            TravelGradesSeeder::class,
            
            // Perdiem Rate seeders
            PerdiemRatesSeeder::class,
            
            // Lodging Cap seeders
            LodgingCapsSeeder::class,
            
            // Representation Rate seeders
            RepresentationRatesSeeder::class,
            
            // Intra Province Transport Reference seeders
            IntraProvinceTransportRefsSeeder::class,
            
            // Intra District Transport Reference seeders
            IntraDistrictTransportRefsSeeder::class,
            
            // Official Vehicle Transport Reference seeders
            OfficialVehicleTransportRefsSeeder::class,
            
            // At-Cost Components seeders
            AtCostComponentsSeeder::class,
            
            // Airfare Reference seeders
            AirfareRefsSeeder::class,
            NotaDinasSeeder::class,
            DocNumberFormatSeeder::class,
            
            // User seeders (harus dijalankan terakhir karena bergantung pada data referensi)
            UsersSeeder::class,
            UserTravelGradeMapSeeder::class,
            EmployeesSeeder::class,
        ]);
    }
}
