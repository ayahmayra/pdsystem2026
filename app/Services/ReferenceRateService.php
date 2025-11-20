<?php

namespace App\Services;

use App\Models\AirfareRef;
use App\Models\LodgingCap;
use App\Models\PerdiemRate;
use App\Models\RepresentationRate;
use App\Models\IntraProvinceTransportRef;
use App\Models\IntraDistrictTransportRef;
use App\Models\City;
use App\Models\Province;
use App\Models\OrgPlace;

class ReferenceRateService
{
    /**
     * Get airfare reference rate for a specific route
     */
    public function getAirfareRate($originCityId, $destinationCityId, $class = 'ECONOMY')
    {
        $airfare = AirfareRef::where('origin_city_id', $originCityId)
            ->where('destination_city_id', $destinationCityId)
            ->where('class', $class)
            ->first();

        return $airfare ? $airfare->pp_estimate : null;
    }

    /**
     * Get lodging cap for a specific province and travel grade
     */
    public function getLodgingCap($provinceId, $travelGradeId)
    {
        $lodgingCap = LodgingCap::where('province_id', $provinceId)
            ->where('travel_grade_id', $travelGradeId)
            ->first();

        return $lodgingCap ? $lodgingCap->cap_amount : null;
    }

    /**
     * Get perdiem rate for a specific province and travel grade
     */
    public function getPerdiemRate($provinceId, $travelGradeId, $tripType = 'luar_kota')
    {
        $perdiemRate = PerdiemRate::where('province_id', $provinceId)
            ->where('travel_grade_id', $travelGradeId)
            ->first();

        if (!$perdiemRate) {
            return null;
        }

        switch ($tripType) {
            case 'luar_kota':
                return $perdiemRate->luar_kota;
            case 'dalam_kota_gt8h':
                return $perdiemRate->dalam_kota_gt8h;
            case 'diklat':
                return $perdiemRate->diklat;
            default:
                return $perdiemRate->luar_kota;
        }
    }

    /**
     * Get representation rate for a specific travel grade
     */
    public function getRepresentationRate($travelGradeId, $tripType = 'luar_kota')
    {
        $representationRate = RepresentationRate::where('travel_grade_id', $travelGradeId)->first();

        if (!$representationRate) {
            return null;
        }

        switch ($tripType) {
            case 'luar_kota':
                return $representationRate->luar_kota;
            case 'dalam_kota_gt8h':
                return $representationRate->dalam_kota_gt8h;
            default:
                return $representationRate->luar_kota;
        }
    }

    /**
     * Get intra-province transport rate
     */
    public function getIntraProvinceTransportRate($originPlaceId, $destinationCityId)
    {
        $transportRate = IntraProvinceTransportRef::where('origin_place_id', $originPlaceId)
            ->where('destination_city_id', $destinationCityId)
            ->first();

        return $transportRate ? $transportRate->pp_amount : null;
    }

    /**
     * Get intra-district transport rate
     */
    public function getIntraDistrictTransportRate($originPlaceId, $destinationDistrictId)
    {
        $transportRate = IntraDistrictTransportRef::where('origin_place_id', $originPlaceId)
            ->where('destination_district_id', $destinationDistrictId)
            ->first();

        return $transportRate ? $transportRate->pp_amount : null;
    }

    /**
     * Calculate total perdiem amount for a trip
     */
    public function calculateTotalPerdiem($provinceId, $travelGradeId, $tripType, $daysCount)
    {
        $dailyRate = $this->getPerdiemRate($provinceId, $travelGradeId, $tripType);
        
        if ($dailyRate === null) {
            return null;
        }

        return $dailyRate * $daysCount;
    }

    /**
     * Get default origin city (Pekanbaru)
     */
    public function getDefaultOriginCity()
    {
        return City::where('name', 'Pekanbaru')->first();
    }

    /**
     * Get trip type from nota dinas
     */
    public function getTripType($notaDinas)
    {
        if (!$notaDinas) {
            return 'luar_kota';
        }

        switch ($notaDinas->trip_type) {
            case 'LUAR_DAERAH':
                return 'luar_kota';
            case 'DALAM_DAERAH_GT8H':
                return 'dalam_kota_gt8h';
            case 'DALAM_DAERAH_LE8H':
                return 'dalam_kota_gt8h';
            case 'DIKLAT':
                return 'diklat';
            default:
                return 'luar_kota';
        }
    }
}
