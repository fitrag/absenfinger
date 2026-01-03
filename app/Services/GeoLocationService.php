<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class GeoLocationService
{
    /**
     * Get location from IP address using free API.
     * Caches result for 24 hours to minimize API calls.
     */
    public static function getLocationFromIp($ipAddress)
    {
        // Don't lookup local/private IPs
        if (self::isLocalIp($ipAddress)) {
            return 'Local Network';
        }

        // Check cache first
        $cacheKey = 'geo_ip_' . md5($ipAddress);

        return Cache::remember($cacheKey, 60 * 60 * 24, function () use ($ipAddress) {
            try {
                // Using ip-api.com (free, no API key required, 45 req/min limit)
                $response = Http::timeout(5)->get("http://ip-api.com/json/{$ipAddress}", [
                    'fields' => 'status,city,regionName,country',
                ]);

                if ($response->successful()) {
                    $data = $response->json();

                    if ($data['status'] === 'success') {
                        $parts = array_filter([
                            $data['city'] ?? null,
                            $data['regionName'] ?? null,
                            $data['country'] ?? null,
                        ]);

                        return implode(', ', $parts) ?: 'Unknown';
                    }
                }
            } catch (\Exception $e) {
                // Log error but don't fail
                \Log::warning('GeoLocation lookup failed: ' . $e->getMessage());
            }

            return 'Unknown';
        });
    }

    /**
     * Check if IP is local/private.
     */
    private static function isLocalIp($ip)
    {
        if (empty($ip) || $ip === '127.0.0.1' || $ip === '::1') {
            return true;
        }

        // Check for private IP ranges
        $privateRanges = [
            '10.',
            '172.16.',
            '172.17.',
            '172.18.',
            '172.19.',
            '172.20.',
            '172.21.',
            '172.22.',
            '172.23.',
            '172.24.',
            '172.25.',
            '172.26.',
            '172.27.',
            '172.28.',
            '172.29.',
            '172.30.',
            '172.31.',
            '192.168.',
        ];

        foreach ($privateRanges as $range) {
            if (strpos($ip, $range) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Parse device info from User-Agent string.
     */
    public static function parseDeviceInfo($userAgent)
    {
        if (empty($userAgent)) {
            return 'Unknown Device';
        }

        $browser = 'Unknown Browser';
        $os = 'Unknown OS';

        // Detect Browser
        if (preg_match('/Edg\/([0-9.]+)/', $userAgent)) {
            $browser = 'Microsoft Edge';
        } elseif (preg_match('/OPR\/([0-9.]+)/', $userAgent) || preg_match('/Opera\/([0-9.]+)/', $userAgent)) {
            $browser = 'Opera';
        } elseif (preg_match('/Chrome\/([0-9.]+)/', $userAgent)) {
            $browser = 'Chrome';
        } elseif (preg_match('/Firefox\/([0-9.]+)/', $userAgent)) {
            $browser = 'Firefox';
        } elseif (preg_match('/Safari\/([0-9.]+)/', $userAgent) && !preg_match('/Chrome/', $userAgent)) {
            $browser = 'Safari';
        } elseif (preg_match('/MSIE ([0-9.]+)/', $userAgent) || preg_match('/Trident\//', $userAgent)) {
            $browser = 'Internet Explorer';
        }

        // Detect OS
        if (preg_match('/Windows NT 10/', $userAgent)) {
            $os = 'Windows 10/11';
        } elseif (preg_match('/Windows NT 6\.3/', $userAgent)) {
            $os = 'Windows 8.1';
        } elseif (preg_match('/Windows NT 6\.2/', $userAgent)) {
            $os = 'Windows 8';
        } elseif (preg_match('/Windows NT 6\.1/', $userAgent)) {
            $os = 'Windows 7';
        } elseif (preg_match('/Mac OS X/', $userAgent)) {
            $os = 'macOS';
        } elseif (preg_match('/Android ([0-9.]+)/', $userAgent, $matches)) {
            $os = 'Android ' . $matches[1];
        } elseif (preg_match('/iPhone|iPad/', $userAgent)) {
            $os = 'iOS';
        } elseif (preg_match('/Linux/', $userAgent)) {
            $os = 'Linux';
        }

        // Detect if mobile
        $isMobile = preg_match('/Mobile|Android|iPhone|iPad/', $userAgent);
        $deviceType = $isMobile ? '📱' : '💻';

        return "{$deviceType} {$browser} / {$os}";
    }
}
