<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OsrmService
{
    public function getDistanceKm(float $fromLat, float $fromLng, float $toLat, float $toLng): ?float
    {
        try {
            $response = Http::timeout(5)->get(
                "http://router.project-osrm.org/route/v1/driving/{$fromLng},{$fromLat};{$toLng},{$toLat}",
                ['overview' => 'false']
            );

            if ($response->ok()) {
                $data = $response->json();
                if (isset($data['routes'][0]['distance'])) {
                    return round($data['routes'][0]['distance'] / 1000, 2);
                }
            }
        } catch (\Exception $e) {
            Log::warning('OSRM request failed: ' . $e->getMessage());
        }

        return null;
    }

    public function calculateOngkir(float $distanceKm, float $perKm, float $freeKm): int
    {
        if ($distanceKm <= $freeKm) {
            return 0;
        }

        return (int) ceil(($distanceKm - $freeKm) * $perKm);
    }
}
