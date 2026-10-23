<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OsrmService
{
    /**
     * Jarak tempuh jalan raya dari OSRM (di-cache per koordinat), dengan fallback jarak garis lurus.
     *
     * @return array{km: float, method: 'osrm'|'haversine'}
     */
    public function getDistance(float $fromLat, float $fromLng, float $toLat, float $toLng): array
    {
        $km = $this->getDistanceKm($fromLat, $fromLng, $toLat, $toLng);

        return $km !== null
            ? ['km' => $km, 'method' => 'osrm']
            : ['km' => static::haversineKm($fromLat, $fromLng, $toLat, $toLng), 'method' => 'haversine'];
    }

    public function getDistanceKm(float $fromLat, float $fromLng, float $toLat, float $toLng): ?float
    {
        // Koordinat dibulatkan (~11 m) agar hasil bisa dipakai ulang tanpa memanggil server lagi
        $key = sprintf('osrm:%.4f,%.4f:%.4f,%.4f', $fromLat, $fromLng, $toLat, $toLng);

        $cached = Cache::get($key);
        if ($cached !== null) {
            return (float) $cached;
        }

        try {
            $response = Http::timeout(config('services.osrm.timeout', 4))->get(
                rtrim(config('services.osrm.url', 'https://router.project-osrm.org'), '/')
                    . "/route/v1/driving/{$fromLng},{$fromLat};{$toLng},{$toLat}",
                ['overview' => 'false']
            );

            $meters = $response->ok() ? ($response->json('routes.0.distance')) : null;
            if ($meters !== null) {
                $km = round($meters / 1000, 2);
                Cache::put($key, $km, now()->addDays(7));

                return $km;
            }
        } catch (\Exception $e) {
            Log::warning('OSRM request failed: ' . $e->getMessage());
        }

        return null;
    }

    public static function haversineKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $r = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return round($r * 2 * atan2(sqrt($a), sqrt(1 - $a)), 2);
    }

    public function calculateOngkir(float $distanceKm, float $perKm, float $freeKm): int
    {
        if ($distanceKm <= $freeKm) {
            return 0;
        }

        return (int) ceil(($distanceKm - $freeKm) * $perKm);
    }
}
